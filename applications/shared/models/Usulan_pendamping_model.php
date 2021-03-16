<?php

/**
 * Class Pwmi_model
 * @property CI_DB_query_builder $db
 * @property int $id
 * @property int $kegiatan_id
 * @property int $perguruan_tinggi_id
 * @property int $dosen_id
 * @property string $created_at
 * @property string $updated_at
 */
class Usulan_pendamping_model extends CI_Model
{
	/**
	 * @return int
	 */
	public function count_usulan($tahun, $pt_id)
	{
		return $this->db
			->from('usulan_pendamping up')
			->join('kegiatan k', 'k.id = up.kegiatan_id')
			->where([
				'k.tahun' => $tahun,
				'up.perguruan_tinggi_id' => $pt_id
			])
			->count_all_results();
	}

	/**
	 * @param Usulan_pendamping_model $model
	 */
	public function add($model)
	{
		$result = $this->db->insert('usulan_pendamping', $model);
		$model->id = $this->db->insert_id();
		return $result;
	}

	public function list_all($perguruan_tinggi_id)
	{
		$jumlah_syarat = $this->db->select('count(*)')->from('syarat')
			->where('kegiatan_id = k.id')
			->where('is_wajib = 1')
			->get_compiled_select();

		$jumlah_upload = $this->db->select('count(*)')->from('file_usulan_pendamping')
			->where('usulan_pendamping_id = up.id')
			->get_compiled_select();

		return $this->db
			->select('up.id, k.tahun, d.nidn, d.nama, ps.nama as nama_program_studi')
			->select("($jumlah_syarat) as jumlah_syarat")
			->select("($jumlah_upload) as jumlah_upload")
			->from('usulan_pendamping up')
			->join('kegiatan k', 'k.id = up.kegiatan_id')
			->join('perguruan_tinggi pt', 'pt.id = up.perguruan_tinggi_id')
			->join('dosen d', 'd.id = up.dosen_id')
			->join('program_studi ps', 'ps.id = d.program_studi_id')
			->where('pt.id', $perguruan_tinggi_id)
			->order_by('k.tahun desc')
			->get()->result();
	}

	public function list_by_kegiatan($kegiatan_id)
	{
		$jumlah_syarat = $this->db->select('count(*)')->from('syarat')
			->where('kegiatan_id = k.id')
			->where('is_wajib = 1')
			->get_compiled_select();

		$jumlah_upload = $this->db->select('count(*)')->from('file_usulan_pendamping')
			->where('usulan_pendamping_id = up.id')
			->get_compiled_select();

		return $this->db
			->select('up.id, pt.nama_pt, k.tahun, d.nidn, d.nama, ps.nama as nama_program_studi')
			->select("($jumlah_syarat) as jumlah_syarat")
			->select("($jumlah_upload) as jumlah_upload")
			->from('usulan_pendamping up')
			->join('kegiatan k', 'k.id = up.kegiatan_id')
			->join('perguruan_tinggi pt', 'pt.id = up.perguruan_tinggi_id')
			->join('dosen d', 'd.id = up.dosen_id')
			->join('program_studi ps', 'ps.id = d.program_studi_id')
			->where('up.kegiatan_id', $kegiatan_id)
			->order_by('k.tahun desc')
			->get()->result();
	}

	/**
	 * @param int $id
	 * @param int $pt_id
	 * @return Usulan_pendamping_model|null
	 */
	public function get_single($id, $pt_id)
	{
		return $this->db->get_where('usulan_pendamping', ['id' => $id, 'perguruan_tinggi_id' => $pt_id])->first_row();
	}

	/**
	 * @param Usulan_pendamping_model $model
	 */
	public function delete($model)
	{
		$this->db->trans_start();
		$this->db->delete('file_usulan_pendamping', ['usulan_pendamping_id', $model->id]);
		$this->db->delete('usulan_pendamping', ['id' => $model->id]);
		$this->db->trans_complete();
	}
}
