<?php
use GuzzleHttp\Client;

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db
 * @property GuzzleHttp\Client $client
 * @property int $id
 * @property string $nama
 * @property int $perguruan_tinggi_id
 * @property int $program_studi_id
 */
class Dosen_model extends CI_Model
{
	/**
	 * @param int $id
	 * @return Dosen_model
	 */
	public function get($id)
	{
		return $this->db->get_where('dosen', ['id' => $id], 1)->row();
	}
	
	/**
	 * @param string $npsn
	 * @param int $program_studi_id
	 * @param string $nidn
	 * @return Dosen_model
	 * @throws Exception
	 */
	public function get_by_nidn($npsn, $program_studi_id, $nidn)
	{
		$dosen = $this->db
			->select('d.*, ps.nama as nama_program_studi')
			->from('dosen d')
			->join('perguruan_tinggi pt', 'pt.id = d.perguruan_tinggi_id')
			->join('program_studi ps', 'ps.id = d.program_studi_id')
			->where('pt.npsn', $npsn)
			->where('d.nidn', $nidn)
			->get()->row();
		
		// Jika tidak ada dalam DB -> ambil dari Forlap
		if ($dosen == NULL)
		{
			// Ambil konfigurasi
			$this->config->load('pddikti');
			$pddikti_url = $this->config->item('pddikti_url');
			$pddikti_auth = $this->config->item('pddikti_auth');
			
			$this->client = new Client([
				'base_uri' => $pddikti_url,
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . $pddikti_auth
				],
				'verify' => FALSE	// Disable CA Verification !
			]); 

			$this->load->model(MODEL_PROGRAM_STUDI, 'program_studi_model');
			$program_studi = $this->program_studi_model->get($program_studi_id);
			$program_studi->kode_prodi = trim($program_studi->kode_prodi);
			
			// Cari dari Forlap
			$response = $this->client->get("pt/{$npsn}/prodi/{$program_studi->kode_prodi}/dosen/{$nidn}");
			
			if ($response->getStatusCode() == 200)
			{
				$body = $response->getBody();
				$dosen_pddikti = json_decode($body);
				
				if ( ! isset($dosen_pddikti[0]))
				{
					throw new Exception("Dosen tidak ditemukan");
				}
				else
				{
					// Insert dosen dari Pddikti
					$this->insert_from_pddikti($dosen_pddikti[0], $program_studi_id);

					// di query ulang
					$dosen = $this->get_by_nidn($npsn, $program_studi_id, $nidn);
				}
			}
		}
		
		return $dosen;
	}
	
	private function insert_from_pddikti($param, $program_studi_id)
	{
		// get perguruan tinggi id
		$pt = $this->db->select('id')->get_where('perguruan_tinggi', ['npsn' => $param->kode_pt])->row();
		
		return $this->db->insert('dosen', [
			'perguruan_tinggi_id' => $pt->id,
			'nidn' => trim($param->nidn),
			'nama' => $param->nama,
			'gelar_depan' => $param->gelar_depan,
			'gelar_belakang' => $param->gelar_belakang,
			'program_studi_id' => $program_studi_id,
			'id_pdpt' => strtolower($param->id),
			'created_at' => date('Y-m-d H:i:s')
		]);
	}
}
