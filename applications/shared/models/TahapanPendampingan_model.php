<?php

/**
 * Class TahapanPendampingan_model
 * @property CI_DB_query_builder $db
 */
class TahapanPendampingan_model extends CI_Model
{
	public function get_aktif($kegiatan_id)
	{
		$data = $this->db
			->where('kegiatan_id', $kegiatan_id)
			->where('CURRENT_TIMESTAMP BETWEEN tgl_awal_laporan AND tgl_akhir_laporan', NULL, FALSE)
			->get('tahapan_pendampingan')->first_row();

		if ($data)
		{
			$data->tgl_awal_laporan_dmy = strftime('%d %B %Y %H:%M', strtotime($data->tgl_awal_laporan));
			$data->tgl_akhir_laporan_dmy = strftime('%d %B %Y %H:%M', strtotime($data->tgl_akhir_laporan));
		}

		return $data;
	}
}
