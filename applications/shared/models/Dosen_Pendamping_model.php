<?php

/**
 * Class DosenPendamping_model
 * @property CI_DB_query_builder $db
 */
class Dosen_Pendamping_model extends CI_Model
{
	public function get_from_dosen($dosen_id, $kegiatan_id)
	{
		return $this->db->get_where('dosen_pendamping', [
			'dosen_id' => $dosen_id,
			'kegiatan_id' => $kegiatan_id
		])->first_row();
	}
}
