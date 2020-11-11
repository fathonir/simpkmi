<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db
 * @property int $id
 * @property int $mahasiswa_id
 * @property Mahasiswa_model $mahasiswa
 */
class Anggota_proposal_model extends CI_Model
{	
	public function add(stdClass $model)
	{
		$result = $this->db->insert('anggota_proposal', $model);
		
		if ($result)
		{
			$model->id = $this->db->insert_id();
		}
		
		return $result;
	}

	/**
	 * @param $id
	 * @return Anggota_proposal_model|null
	 */
	public function get($id)
	{
		return $this->db->get_where('anggota_proposal', ['id' => $id], 1)->row();
	}
	
	public function update(stdClass $model)
	{
		return $this->db->update('anggota_proposal', $model, ['id' => $model->id]);
	}
	
	public function is_sudah_terdaftar($mahasiswa_id, $kegiatan_id)
	{
		$count = $this->db
			->from('anggota_proposal a')
			->join('proposal p', 'p.id = a.proposal_id')
			->where('a.mahasiswa_id', $mahasiswa_id)
			->where('p.kegiatan_id', $kegiatan_id)
			->count_all_results();
		
		return ($count > 0);
	}

	/**
	 * @param $proposal_id
	 * @return Anggota_proposal_model|null
	 */
	public function get_ketua($proposal_id)
	{
		return $this->db->get_where('anggota_proposal', ['proposal_id' => $proposal_id, 'no_urut' => 1], 1)->row();
	}
	
	/**
	 * @param int $proposal_id
	 * @return Anggota_proposal_model[] 
	 */
	public function list_by_proposal($proposal_id)
	{
		return $this->db
			->select('ap.id, ap.mahasiswa_id, coalesce(m.nim, ap.nim) as nim, coalesce(m.nama, ap.nama) as nama, m.program_studi_id, ps.nama as nama_program_studi')
			->from('anggota_proposal ap')
			->join('mahasiswa m', 'm.id = ap.mahasiswa_id', 'LEFT')
			->join('program_studi ps', 'ps.id = m.program_studi_id', 'LEFT')
			->where('ap.proposal_id', $proposal_id)
			->order_by('ap.no_urut')
			->get()->result();
	}
	
	public function delete($id)
	{
		return $this->db->delete('anggota_proposal', ['id' => $id], 1);
	}
	
	public function delete_by_proposal($proposal_id)
	{
		return $this->db->delete('anggota_proposal', ['proposal_id' => $proposal_id]);
	}
}
