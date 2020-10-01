<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db
 * @property CI_Input $input
 * @property int $id
 * @property int $kegiatan_id
 * @property string $syarat
 * @property string $keterangan
 * @property bool $is_wajib
 * @property int $urutan
 * @property bool $is_aktif
 */
class Syarat_model extends CI_Model
{
	/**
	 * @param int $kegiatan_id
	 * @return Syarat_model[]
	 */
	public function list_by_kegiatan($kegiatan_id, $proposal_id = 0)
	{
		if ($proposal_id != 0)
		{
			return $this->db
				->select('s.id, s.syarat, s.keterangan, s.is_wajib, s.allowed_types, s.max_size, s.is_aktif, s.is_upload')
				->select('fp.id as file_proposal_id, fp.nama_file, fp.nama_asli')
				->from('syarat s')->join('file_proposal fp', 'fp.syarat_id = s.id AND fp.proposal_id = '.$proposal_id, 'LEFT')
				->where(['s.kegiatan_id' => $kegiatan_id])->order_by('urutan')
				->get()->result();
		}
		else
		{
			return $this->db->from('syarat')->where(['kegiatan_id' => $kegiatan_id])->order_by('urutan')->get()->result();
		}
		
	}
	
	public function is_deletable($id)
	{
		return ($this->db->from('file_proposal')->where(['syarat_id' => $id])->count_all_results() === 0);
	}
	
	public function add()
	{
		$post = $this->input->post();
		
		$syarat					= new stdClass();
		$syarat->kegiatan_id	= $post['kegiatan_id'];
		$syarat->urutan			= $post['urutan'];
		$syarat->syarat			= $post['syarat'];
		$syarat->keterangan		= $post['keterangan'];
		$syarat->is_wajib		= $post['is_wajib'];
		$syarat->is_upload		= $post['is_upload'];
		$syarat->allowed_types	= $post['allowed_types'];
		
		return $this->db->insert('syarat', $syarat);
	}
	
	public function get_single($id)
	{
		return $this->db->get_where('syarat', ['id' => $id], 1)->row();
	}

	public function get_by_nama($kegiatan_id, $syarat, $proposal_id)
	{
		return $this->db
			->select('s.id, s.syarat, s.keterangan, s.is_wajib, s.allowed_types, s.max_size, s.is_aktif, s.is_upload')
			->select('fp.id as file_proposal_id, fp.nama_file, fp.nama_asli')
			->from('syarat s')
			->join('file_proposal fp', 'fp.syarat_id = s.id AND fp.proposal_id = '.$proposal_id, 'LEFT')
			->where(['s.kegiatan_id' => $kegiatan_id, 's.syarat' => $syarat])
			->get()->row();
	}
	
	public function update($id)
	{
		$post = $this->input->post();
		
		$syarat					= $this->get_single($id);
		$syarat->urutan			= $post['urutan'];
		$syarat->syarat			= $post['syarat'];
		$syarat->keterangan		= $post['keterangan'];
		$syarat->is_wajib		= $post['is_wajib'];
		$syarat->is_upload		= $post['is_upload'];
		$syarat->allowed_types	= $post['allowed_types'];
		
		return $this->db->update('syarat', $syarat, ['id' => $id]);
	}
}
