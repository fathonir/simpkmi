<?php

/**
 * Class TahapanPendampingan_model
 * @property CI_DB_query_builder $db
 * @property int $id
 * @property int $kegiatan_id
 * @property string $nama_tahapan
 * @property string $tgl_awal_laporan
 * @property string $tgl_akhir_laporan
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

	public function list_by_kegiatan($kegiatan_id)
	{
		return $this->db
			->order_by('tgl_awal_laporan')
			->get_where('tahapan_pendampingan', ['kegiatan_id' => $kegiatan_id])
			->result();
	}

	/**
	 * @param $id
	 * @return TahapanPendampingan_model|null
	 */
	public function get_single($id)
	{
		return $this->db->get_where('tahapan_pendampingan', ['id' => $id], 1)->row();
	}

	public function update($id)
	{
		$post = $this->input->post();
		$tahapan_pendampingan = $this->get_single($id);
		$tahapan_pendampingan->nama_tahapan = $post['nama_tahapan'];
		$tahapan_pendampingan->tgl_awal_laporan =
			"{$post['awal_laporan_Year']}-" .
			"{$post['awal_laporan_Month']}-" .
			"{$post['awal_laporan_Day']} " .
			"{$post['awal_laporan_time']}";
		$tahapan_pendampingan->tgl_akhir_laporan =
			"{$post['akhir_laporan_Year']}-" .
			"{$post['akhir_laporan_Month']}-" .
			"{$post['akhir_laporan_Day']} " .
			"{$post['akhir_laporan_time']}";
		$tahapan_pendampingan->updated_at = date('Y-m-d H:i:s');

		return $this->db->update('tahapan_pendampingan', $tahapan_pendampingan, ['id' => $id]);
	}
}
