<?php

/**
 * Class Isian_model
 * @property CI_DB_query_builder $db
 * @property int $id
 * @property int $kegiatan_id
 * @property int $kelompok_isian_id
 * @property int $isian_ke
 * @property string $judul_isian
 */
class Isian_model extends CI_Model
{
	/**
	 * @param $kegiatan_id
	 * @param $isian_ke
	 * @return Isian_model|null
	 */
	function get_single($kegiatan_id, $isian_ke)
	{
		$isian = $this->db->get_where('isian', ['kegiatan_id' => $kegiatan_id, 'isian_ke' => $isian_ke])->row();

		if ($isian != null)
		{
			$isian->kelompok_isian = $this->db->get_where('kelompok_isian', ['id' => $isian->kelompok_isian_id])->row();
		}

		return $isian;
	}

	function get_count_isian($kegiatan_id)
	{
		return $this->db->where('kegiatan_id', $kegiatan_id)->count_all_results('isian');
	}

	function get_kelompok_isian($kelompok_isian_id)
	{
		return $this->db->get_where('kelompok_isian', ['id' => $kelompok_isian_id])->row();
	}
}
