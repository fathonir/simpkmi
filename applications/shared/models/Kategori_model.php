<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db 
 * @property CI_Input $input
 * 
 * @property int $id
 * @property int $program_id
 * @property string $nama_kategori
 */
class Kategori_model extends CI_Model
{
	function get_single($id)
	{
		return $this->db->get_where('kategori', ['id' => $id])->row();
	}

	function list_kategori_by_program($program_id)
	{
		return $this->db->get_where('kategori', ['program_id' => $program_id])->result();
	}
}
