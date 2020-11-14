<?php

/**
 * Class LaporanPendampingan_model
 * @property CI_DB_query_builder $db
 */
class LaporanPendampingan_model extends CI_Model
{
	public function list_by_proposal($proposal_id, $kegiatan_id, $dosen_id)
	{
		//select tp.id, tp.nama_tahapan, tp.tgl_awal_laporan, tp.tgl_akhir_laporan,
		//    case when lp.id is not null then 1 else 0 end laporan_pendampingan
		//from tahapan_pendampingan tp
		//left join laporan_pendampingan lp on lp.tahapan_pendampingan_id = tp.id and lp.dosen_pendamping_id = 1 and lp.proposal_id = 14414
		//where tp.kegiatan_id = 14

		$data_set = $this->db
			->select('tp.id as tahapan_pendampingan_id, tp.nama_tahapan, tp.tgl_awal_laporan, tp.tgl_akhir_laporan')
			->select('case when lp.id is not null then 1 else 0 end laporan_pendampingan', FALSE)
			->from('tahapan_pendampingan tp')
			->join('dosen_pendamping dp', 'dp.kegiatan_id = tp.kegiatan_id AND dp.dosen_id = ' . $dosen_id)
			->join('laporan_pendampingan lp', 'lp.tahapan_pendampingan_id = tp.id AND lp.proposal_id = ' . $proposal_id, 'LEFT')
			->where('tp.kegiatan_id = ', $kegiatan_id)
			->get()->result();

		foreach ($data_set as $data)
		{
			$data->tgl_awal_laporan_dmy = strftime('%d %B %Y %H:%M', strtotime($data->tgl_awal_laporan));
			$data->tgl_akhir_laporan_dmy = strftime('%d %B %Y %H:%M', strtotime($data->tgl_akhir_laporan));
		}

		return $data_set;
	}

	/**
	 * Mendapatkan judul-judul yang membutuhkan pelaporan pendampingan
	 * @param $kegiatan_id
	 * @return array|array[]|object|object[]
	 */
	public function list_by_kegiatan($kegiatan_id)
	{
		return $this->db
			->select('p.id, pt.nama_pt, p.judul, dp.id as dosen_pendamping_id, d.nama as nama_dosen')
			->from('proposal p')
			->join('kegiatan k', 'k.id = p.kegiatan_id')
			->join('perguruan_tinggi pt', 'pt.id = p.perguruan_tinggi_id')
			->join('dosen_pendamping dp', 'dp.kegiatan_id = k.id and dp.perguruan_tinggi_id = pt.id', 'LEFT')
			->join('dosen d', 'd.id = dp.dosen_id', 'LEFT')
			->where('p.is_didanai', 1)
			->where('p.kegiatan_id', $kegiatan_id)
			->get()->result();
	}

	public function list_data($kegiatan_id)
	{

	}

	public function get_single($dosen_id, $proposal_id, $tahapan_pendampingan_id)
	{
		return $this->db
			->select('lp.*')
			->from('laporan_pendampingan lp')
			->join('dosen_pendamping dp', 'dp.id = lp.dosen_pendamping_id')
			->where('dp.dosen_id', $dosen_id)
			->where('lp.proposal_id', $proposal_id)
			->where('lp.tahapan_pendampingan_id', $tahapan_pendampingan_id)
			->get()->first_row();
	}

	public function add($laporan_pendampingan)
	{
		return $this->db->insert('laporan_pendampingan', $laporan_pendampingan);
	}

	public function update($laporan_pendampingan)
	{
		return $this->db->update('laporan_pendampingan', $laporan_pendampingan, ['id' => $laporan_pendampingan->id]);
	}
}
