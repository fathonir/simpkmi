<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db
 */
class File_proposal_model extends CI_Model
{
	public $id;
	public $proposal_id;
	public $syarat_id;
	public $nama_file;
	public $nama_asli;
	public $created_at;
	public $updated_at;
	
	/**
	 * @param int $proposal_id
	 * @param array $filter_include
	 * @param array $filter_exclude
	 * @return File_proposal_model[]
	 */
	public function list_by_proposal($proposal_id, $filter_include = null, $filter_exclude = null)
	{
		if (is_array($filter_include))
		{
			$this->db->where_in('s.syarat', $filter_include);
		}

		if (is_array($filter_exclude))
		{
			$this->db->where_not_in('s.syarat', $filter_exclude);
		}

		return $this->db
			->select('fp.*, s.syarat, s.is_upload')
			->join('syarat s', 's.id = fp.syarat_id')
			->get_where('file_proposal fp', ['fp.proposal_id' => $proposal_id])
			->result();
	}
	
	public function get_single($id)
	{
		return $this->db->get_where('file_proposal', ['id' => $id], 1)->row();
	}
	
	public function insert(stdClass $model)
	{
		return $this->db->insert('file_proposal', $model);
	}
	
	public function delete_by_proposal($proposal_id)
	{
		return $this->db->delete('file_proposal', ['proposal_id' => $proposal_id]);
	}
}
