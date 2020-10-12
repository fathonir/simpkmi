<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db
 * @property int $id
 * @property int $perguruan_tinggi_id
 * @property string $judul
 * @property string $mulai_berjalan
 * @property int $kegiatan_id
 * @property int $dosen_id
 * @property bool $is_submited
 * @property bool $is_reviewed
 * @property string $updated_at
 * @property Anggota_proposal_model $ketua
 * @property File_proposal_model[] $file_proposal_set
 * @property Anggota_proposal_model[] $anggota_proposal_set
 * @property Dosen_model $dosen
 * @property Syarat_model $syarat_model
 */
class Proposal_model extends CI_Model
{
	/**
	 * Mendapatkan list data semua proposal per kegiatan (versi 2018 kebawah)
	 * @param int $kegiatan_id
	 * @return array
	 */
	public function list_all_per_kegiatan($kegiatan_id)
	{
		return $this->db
			->select("p.id, p.judul, pt.nama_pt, ktg.nama_kategori, p.nim_ketua, p.nama_ketua, p.created_at")
			->select('count(s.id) as jumlah_syarat, sum(s.is_wajib) as syarat_wajib', FALSE)
			->select('count(fp.id) as syarat_terupload, sum(if(s.is_wajib = 1 AND fp.id IS NOT NULL, 1,0)) as syarat_wajib_terupload', FALSE)
			->from('proposal p')
			->join('kegiatan k', 'k.id = p.kegiatan_id')
			->join('perguruan_tinggi pt', 'pt.id = p.perguruan_tinggi_id')
			->join('kategori ktg', 'ktg.id = p.kategori_id')
			->join('syarat s', 's.kegiatan_id = k.id', 'LEFT')
			->join('file_proposal fp', 'fp.proposal_id = p.id AND fp.syarat_id = s.id', 'LEFT')
			->where(['p.kegiatan_id' => $kegiatan_id])
			->group_by("p.id, p.judul, pt.nama_pt, ktg.nama_kategori, p.nim_ketua, p.nama_ketua, p.created_at")
			->get()->result();
	}
	
	/**
	 * Mendapatkan list data semua proposal per kegiatan (versi 2019) untuk datatables
	 * @param int $kegiatan_id
	 * @return array Array untuk diformat json kebutuhan datatables
	 */
	public function list_all_per_kegiatan_v2_dt($kegiatan_id, $tampilan, $dt_params, $debug_query = false)
	{
		$result = new stdClass();
		
		// Match query draw
		$result->draw = $dt_params['draw'];
		
		// Basic Query
		$this->db
			->select('p.id, p.judul, m.nama, pt.nama_pt')
			->select('case p.is_submited when 1 then p.updated_at when 0 then null end as updated_at', false)
			->from('proposal p')
			->join('anggota_proposal ap', 'ap.proposal_id = p.id AND ap.no_urut = 1')
			->join('mahasiswa m', 'm.id = ap.mahasiswa_id')
			->join('perguruan_tinggi pt', 'pt.id = p.perguruan_tinggi_id')
			->where('p.kegiatan_id', $kegiatan_id);
		
		if ($tampilan == 'submited')
		{
			$this->db->where('p.is_submited', 1);
		}
		
		// Total records
		$result->recordsTotal = $this->db->count_all_results('', FALSE);
		
		if ($debug_query)
		{
			$result->sql = array();
			array_push($result->sql, $this->db->last_query());
		}
		
		// Sorting
		if (count($dt_params['order']) > 0)
		{
			if ($dt_params['order'][0]['column'] == 0) $this->db->order_by('p.judul', $dt_params['order'][0]['dir']);
			if ($dt_params['order'][0]['column'] == 1) $this->db->order_by('m.nama', $dt_params['order'][0]['dir']);
			if ($dt_params['order'][0]['column'] == 2) $this->db->order_by('pt.nama_pt', $dt_params['order'][0]['dir']);
			if ($dt_params['order'][0]['column'] == 3) $this->db->order_by('p.updated_at', $dt_params['order'][0]['dir']);
		}
		
		// Search
		if ($dt_params['search']['value'] != '')
		{
			$this->db->group_start();
			$this->db->like('lower(p.judul)', $dt_params['search']['value'], 'both', FALSE);
			$this->db->or_like('lower(m.nama)', $dt_params['search']['value'], 'both', FALSE);
			$this->db->or_like('lower(pt.nama_pt)', $dt_params['search']['value'], 'both', FALSE);
			$this->db->group_end();
			
			$result->recordsFiltered = $this->db->count_all_results('', FALSE);
			
			if ($debug_query)
			{
				array_push($result->sql, $this->db->last_query());
			}
		}
		else
		{
			$result->recordsFiltered = $result->recordsTotal;
		}
		
		// Paging
		$this->db->limit($dt_params['length'], $dt_params['start']);
		
		// Get Data
		$result->data = $this->db->get()->result();
		
		if ($debug_query)
		{
			array_push($result->sql, $this->db->last_query());
		}
		
		return $result;
	}
	
	public function list_by_perguruan_tinggi($perguruan_tinggi_id, $kegiatan_id)
	{
		$select_file_pitchdeck = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Pitchdeck\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		$select_link_presentasi = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Presentasi\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		$select_link_produk = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Produk\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		return $this->db
			->select('p.id, p.judul, ap.mahasiswa_id, m.nim, m.nama, ps.nama as nama_program_studi, d.nama as nama_dosen, p.is_submited, p.is_reviewed, count(ip.id) as jumlah_isian')
			->select("({$select_file_pitchdeck}) as file_pitchdeck", FALSE)
			->select("({$select_link_presentasi}) as link_presentasi", FALSE)
			->select("({$select_link_produk}) as link_produk", FALSE)
			->from('proposal p')
			->join('anggota_proposal ap', 'ap.proposal_id = p.id AND ap.no_urut = 1') // Ketua di No Urut 1
			->join('mahasiswa m', 'm.id = ap.mahasiswa_id')
			->join('program_studi ps', 'ps.id = m.program_studi_id')
			->join('dosen d', 'd.id = p.dosen_id', 'LEFT')
			->join('isian_proposal ip', 'ip.proposal_id = p.id AND ip.isian_ke > 0', 'LEFT')
			->where(['p.perguruan_tinggi_id' => $perguruan_tinggi_id, 'kegiatan_id' => $kegiatan_id])
			->group_by('p.id, p.judul, ap.mahasiswa_id, m.nim, m.nama, ps.nama, d.nama, p.is_submited')
			->get()->result();
	}
	
	public function list_by_mahasiswa($mahasiswa_id, $program_id)
	{
		$select_isian_proposal_count = $this->db
			->select('count(ip.id)')->from('isian_proposal ip')
			->where('ip.isian is not null', NULL, FALSE)
			->where('ip.proposal_id = p.id')
			->get_compiled_select();

		$select_file_pitchdeck = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Pitchdeck\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		$select_link_presentasi = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Presentasi\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		$select_link_produk = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Produk\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();

		$select_file_pitchdeck_2 = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Pitchdeck Tahap 2\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		return $this->db
			->select('p.id, k.tahun, p.judul, p.is_submited, p.is_didanai')
			->select("({$select_isian_proposal_count}) as isian_proposal", FALSE)
			->select("({$select_file_pitchdeck}) as file_pitchdeck", FALSE)
			->select("({$select_link_presentasi}) as link_presentasi", FALSE)
			->select("({$select_link_produk}) as link_produk", FALSE)
			->select("({$select_file_pitchdeck_2}) as file_pitchdeck_2", FALSE)
			->from('mahasiswa m')
			->join('anggota_proposal ap', 'ap.mahasiswa_id = m.id AND ap.no_urut = 1') // Ketua di No Urut 1
			->join('proposal p', 'p.id = ap.proposal_id')
			->join('kegiatan k', 'k.id = p.kegiatan_id')
			->join('program pr', 'pr.id = k.program_id')
			->where(['m.id' => $mahasiswa_id, 'pr.id' => $program_id])
			->get()->result();
	}

	/**
	 * @param int $kegiatan_id
	 * @param int $dosen_id
	 */
	public function list_by_dosen_pendamping($kegiatan_id, $dosen_id, $tahapan_pendampingan_id = null)
	{
		if ($tahapan_pendampingan_id == null)
		{
			$tahapan_pendampingan_id = 'NULL';
		}

		return $this->db
			->select('p.id, k.tahun, p.judul, m.nama as ketua, m.nim as nim_ketua')
			->select('db.nama as pembimbing, db.nidn')
			->select('case when lp.id is not null then 1 else 0 end as laporan_pendampingan', FALSE)
			->from('proposal p')
			->join('kegiatan k', 'k.id = p.kegiatan_id')
			->join('perguruan_tinggi pt', 'pt.id = p.perguruan_tinggi_id')
			->join('dosen_pendamping dp','dp.kegiatan_id = k.id AND dp.perguruan_tinggi_id = pt.id', '', FALSE)
			->join('laporan_pendampingan lp', 'lp.dosen_pendamping_id = dp.id ' .
				'AND lp.proposal_id = p.id ' .
				'AND lp.tahapan_pendampingan_id = '.$tahapan_pendampingan_id, 'LEFT', FALSE)
			->join('anggota_proposal ap','ap.proposal_id = p.id AND ap.no_urut = 1', '', FALSE)
			->join('mahasiswa m', 'm.id = ap.mahasiswa_id')
			->join('dosen db', 'db.id = p.dosen_id', 'LEFT')
			->where('p.kegiatan_id', $kegiatan_id)
			->where('p.is_didanai', 1)
			->where('dp.dosen_id', $dosen_id)
			->get()->result();
	}

	/**
	 * Mengambil satu data Proposal
	 * @param int $id
	 * @param int $perguruan_tinggi_id Harus ada untuk tampilan frontend sebagai pengaman
	 * @return Proposal_model 
	 */
	public function get_single($id, $perguruan_tinggi_id = NULL)
	{
		$this->db->where(['id' => $id]);
		if ($perguruan_tinggi_id != NULL) $this->db->where(['perguruan_tinggi_id' => $perguruan_tinggi_id]);
		return $this->db->get('proposal', 1)->row();
	}
	
	/**
	 * @param int $mahasiswa_id
	 * @return Proposal_model
	 */
	public function get_by_ketua($kegiatan_id, $mahasiswa_id)
	{
		return $this->db
			->select('p.*')
			->from('proposal p')
			->join('anggota_proposal ap', 'ap.proposal_id = p.id AND ap.no_urut = 1')
			->where('p.kegiatan_id', $kegiatan_id)
			->where('ap.mahasiswa_id', $mahasiswa_id)
			->get()->row();
	}
	
	/**
	 * @param Proposal_model $model
	 * @return bool
	 */
	public function add(&$model)
	{
		$result = $this->db->insert('proposal', $model);
		$model->id = $this->db->insert_id();
		return $result;
	}
	
	/**
	 * @param int $id
	 * @param Proposal_model $model
	 * @return bool
	 */
	public function update($id, $model)
	{
		return $this->db->update('proposal', $model, ['id' => $id]);
	}
	
	/**
	 * @param int $id
	 * @param int $perguruan_tinggi_id
	 * @return bool
	 */
	public function delete($id, $perguruan_tinggi_id = NULL)
	{
		if ($perguruan_tinggi_id != NULL)
		{
			$delete1 = $this->db->delete('isian_proposal', ['proposal_id' => $id]);
			
			$delete2 = $this->db->delete('proposal', array(
				'id' => $id,
				'perguruan_tinggi_id' => $perguruan_tinggi_id
			), 1);
			
			return $delete1 && $delete2;
		}

		return FALSE;
	}
	
	/**
	 * Jumlah proposal per kegiatan per perguruan tinggi
	 * @param int $kegiatan_id
	 * @param int $perguruan_tinggi_id
	 * @return int 
	 */
	public function get_jumlah_per_pt($kegiatan_id, $perguruan_tinggi_id)
	{
		return $this->db->from('proposal p')
			->join('kegiatan k', 'k.id = p.kegiatan_id')
			->where(array(
				'kegiatan_id' => $kegiatan_id,
				'perguruan_tinggi_id' => $perguruan_tinggi_id
			))
			->count_all_results();
	}
	
	public function list_proposal_per_reviewer($kegiatan_id, $tahapan_id, $reviewer_id, $order_by = 'judul')
	{
		switch ($order_by)
		{
			case 'judul':
				$order_column = 'p.judul asc'; break;
			case 'pt':
				$order_column = 'pt.nama_pt asc'; break;
			case 'nilai':
				$order_column = 'pr.nilai_reviewer desc'; break;
			case 'rekomendasi':
				$order_column = 'pr.biaya_rekomendasi desc'; break;
			default: 
				$order_column = 'p.judul asc'; break;
		}
		
		$sql = 
			"select pr.id, p.judul, coalesce(m.nama, ap.nama) as nama_ketua, pt.nama_pt, pr.biaya_rekomendasi, pr.nilai_reviewer
			from plot_reviewer pr
			join tahapan_proposal tp on tp.id = pr.tahapan_proposal_id
			join proposal p on p.id = tp.proposal_id
			join perguruan_tinggi pt on pt.id = p.perguruan_tinggi_id
			left join anggota_proposal ap on ap.proposal_id = p.id and ap.no_urut = 1
			left join mahasiswa m on m.id = ap.mahasiswa_id
			where tp.kegiatan_id = ? and tp.tahapan_id = ? and pr.reviewer_id = ?
			order by {$order_column}";
		
		return $this->db->query($sql, array(
			$kegiatan_id, $tahapan_id, $reviewer_id
		))->result();
	}
	
	public function list_proposal_expo($perguruan_tinggi_id)
	{
		// Kegiatan Expo Aktif
		$kegiatan_id = $this->db->get_where('kegiatan', ['program_id' => PROGRAM_EXPO, 'is_aktif' => 1], 1)->row()->id;
		
		return $this->db
			->select('proposal.id, judul, nama_kategori, is_submited, is_didanai, is_ditolak, is_kmi_award')
			->from('proposal')
			->join('kategori', 'kategori.id = kategori_id')
			->where(['perguruan_tinggi_id' => $perguruan_tinggi_id, 'kegiatan_id' => $kegiatan_id])
			->get()->result();
	}
	
	public function list_all_proposal_expo()
	{
		// Kegiatan Expo Aktif
		$kegiatan_id = $this->db->get_where('kegiatan', ['program_id' => PROGRAM_EXPO, 'is_aktif' => 1], 1)->row()->id;
		
		return $this->db
			->select('proposal.id, nama_pt, nama_kategori, judul, is_submited, is_didanai, is_ditolak, keterangan_ditolak, is_kmi_award, fe.nama_file')
			->from('proposal')
			->join('kategori', 'kategori.id = kategori_id')
			->join('perguruan_tinggi', 'perguruan_tinggi.id = perguruan_tinggi_id')
			->join('file_expo fe', 'fe.kegiatan_id = proposal.kegiatan_id and fe.perguruan_tinggi_id = proposal.perguruan_tinggi_id', 'LEFT')
			->where(['proposal.kegiatan_id' => $kegiatan_id, 'is_submited' => 1])
			->order_by('nama_pt', 'asc')
			->get()->result();
	}
	
	public function submit($id)
	{
		return $this->db->update('proposal', [
			'is_submited' => 1,
			'updated_at' => date('Y-m-d H:i:s')
		], ['id' => $id]);
	}
	
	public function has_kmi_award($kegiatan_id, $perguruan_tinggi_id, $kategori_id = null)
	{
		$this->db->where(array(
			'kegiatan_id' => $kegiatan_id, 
			'perguruan_tinggi_id' => $perguruan_tinggi_id,
			'is_kmi_award' => 1
		));
		
		if ($kategori_id != null)
		{
			$this->db->where('kategori_id', $kategori_id);
		}
		
		$count = $this->db->count_all_results('proposal');
		
		return ($count > 0);
	}
	
	public function list_rekap_expo_pt()
	{
		// Kegiatan Expo Aktif
		$kegiatan_id = $this->db->get_where('kegiatan', ['program_id' => PROGRAM_EXPO, 'is_aktif' => 1], 1)->row()->id;
		
		return $this->db
			->select('npsn, nama_pt, sum(is_submited) as jumlah_submit, sum(is_didanai) as jumlah_lolos, sum(is_ditolak) as jumlah_ditolak')
			->from('proposal')
			->join('perguruan_tinggi', 'perguruan_tinggi.id = perguruan_tinggi_id')
			->where(['proposal.kegiatan_id' => $kegiatan_id, 'is_submited' => 1])
			->group_by('npsn, nama_pt')
			->order_by('3 desc, npsn asc', null, FALSE)
			->get()->result();
	}
	
	public function list_rekap_expo_kategori()
	{
		// Kegiatan Expo Aktif
		$kegiatan_id = $this->db->get_where('kegiatan', ['program_id' => PROGRAM_EXPO, 'is_aktif' => 1], 1)->row()->id;
		
		return $this->db
			->select('nama_kategori, count(distinct if(is_didanai = 1, perguruan_tinggi_id, null)) as jumlah_pt, sum(is_submited) as jumlah_submit, sum(is_didanai) as jumlah_lolos, sum(is_ditolak) as jumlah_ditolak')
			->from('proposal')
			->join('kategori', 'kategori.id = kategori_id')
			->where(['proposal.kegiatan_id' => $kegiatan_id, 'is_submited' => 1])
			->group_by('nama_kategori')
			->get()->result();
	}
	
	function update_dosen($perguruan_tinggi_id, $proposal_id, $dosen_id)
	{
		return $this->db->update('proposal', [
			'dosen_id' => $dosen_id,
			'updated_at' => date('Y-m-d H:i:s')
		], [
			'perguruan_tinggi_id' => $perguruan_tinggi_id,
			'id' => $proposal_id
		]);
	}
	
	function get_isian_proposal($proposal_id, $isian_ke)
	{
		$isian_proposal = $this->db->get_where('isian_proposal', ['proposal_id' => $proposal_id, 'isian_ke' => $isian_ke], 1)->row();
		
		if ($isian_proposal == NULL)
		{
			$this->db->insert('isian_proposal', [
				'proposal_id' => $proposal_id,
				'isian_ke' => $isian_ke,
				'created_at' => date('Y-m-d H:i:s')
			]);
			
			$isian_proposal = $this->db->get_where('isian_proposal', ['proposal_id' => $proposal_id, 'isian_ke' => $isian_ke], 1)->row();
		}
		
		return $isian_proposal;
	}
	
	function update_isian_proposal($proposal_id, $isian_ke, $isian)
	{
		$isian = strlen(trim($isian)) == 0 ? NULL : $isian;
		
		return $this->db->update('isian_proposal', [
			'isian' => $isian,
			'updated_at' => date('Y-m-d H:i:s')
		], ['proposal_id' => $proposal_id, 'isian_ke' => $isian_ke]);
	}
	
	/**
	 * Mengambil status kelengkapan isian proposal
	 * @param int $proposal_id
	 * @return mixed Array - Jika terdapat kelengkapan yang belum lengkap. TRUE - Jika sudah lengkap
	 */
	function get_kelengkapan_proposal($proposal_id)
	{
		$hasil = array();

		// Cek Isian Mulai Berjalan
		$mulai_berjalan = $this->db->select('mulai_berjalan')
			->get_where('proposal', ['id' => $proposal_id])
			->row()->mulai_berjalan;

		if ($mulai_berjalan == null)
		{
			array_push($hasil, 'Isian Identitas Proposal belum lengkap.');
		}

		// Ambil jumlah anggota kelompok
		$jumlah_anggota = $this->db->where('proposal_id', $proposal_id)->count_all_results('anggota_proposal');
		
		// Cek jumlah anggota 3-5
		if ($jumlah_anggota < 3 || $jumlah_anggota > 5)
		{
			array_push($hasil, 'Jumlah anggota kelompok harus 3-5 orang.');
		}
		
		// Ambil jumlah isian
		$jumlah_isian_proposal = $this->db->where([
			'isian_ke > ' => 0,
			'isian IS NOT NULL' => NULL
		])->count_all_results('isian_proposal');
		
		// Cek isian form harus 31
		if ($jumlah_isian_proposal < 31)
		{
			array_push($hasil, 'Isian proposal masih kurang lengkap');
		}
		
		// Ambil syarat beserta hasil upload
		$file_lengkap = $this->db
			->select('count(syarat.id) = count(file_proposal.id) as file_lengkap', FALSE)
			->from('proposal')
			->join('kegiatan', 'kegiatan.id = proposal.kegiatan_id')
			->join('syarat', 'syarat.kegiatan_id = kegiatan.id AND syarat.is_wajib = 1')
			->join('file_proposal', 'file_proposal.proposal_id = proposal.id AND file_proposal.syarat_id = syarat.id', 'LEFT')
			->where('proposal.id', $proposal_id)
			->get()->row()->file_lengkap;
		
		// Cek kelengkapan file syarat
		if ($file_lengkap == 0)
		{
			array_push($hasil, 'Berkas proposal masih ada yang belum diunggah');
		}
		
		return (count($hasil) == 0) ? TRUE : $hasil;
	}
	
	/**
	 * Mendapatkan jumlah anggota
	 * @param int $id
	 * @return int Jumlah anggota
	 */
	function get_jumlah_anggota($id)
	{
		return $this->db->where('proposal_id', $id)->from('anggota_proposal')->count_all_results();
	}
	
	/**
	 * Mendapatkan list usulan startup
	 * @param int $kegiatan_id
	 * @return array
	 */
	public function list_all_startup($kegiatan_id)
	{
		$select_file_pitchdeck = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Pitchdeck\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		$select_link_presentasi = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Presentasi\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		$select_link_produk = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Produk\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();

		$select_file_pitchdeck_2 = $this->db
			->select('fp.nama_file')->from('file_proposal fp')
			->join('syarat s', 's.id = fp.syarat_id and s.syarat = \'Pitchdeck Tahap 2\'')
			->where('fp.proposal_id = p.id')->get_compiled_select();
		
		return $this->db
			->select('p.id, p.judul, pt.nama_pt, ap.mahasiswa_id, m.nim, m.nama, ps.nama as nama_program_studi, d.nama as nama_dosen, p.is_submited, p.is_reviewed')
			->select("({$select_file_pitchdeck}) as file_pitchdeck", FALSE)
			->select("({$select_link_presentasi}) as link_presentasi", FALSE)
			->select("({$select_link_produk}) as link_produk", FALSE)
			->select("({$select_file_pitchdeck_2}) as file_pitchdeck_2", FALSE)
			->from('proposal p')
			->join('perguruan_tinggi pt', 'pt.id = p.perguruan_tinggi_id')
			->join('anggota_proposal ap', 'ap.proposal_id = p.id AND ap.no_urut = 1') // Ketua di No Urut 1
			->join('mahasiswa m', 'm.id = ap.mahasiswa_id')
			->join('program_studi ps', 'ps.id = m.program_studi_id')
			->join('dosen d', 'd.id = p.dosen_id', 'LEFT')
			->where('kegiatan_id', $kegiatan_id)
			->get()->result();
	}

	function is_lolos_tahapan($proposal_id, $tahapan_id)
	{
		$count = $this->db
			->from('tahapan_proposal')
			->where(['proposal_id' => $proposal_id, 'tahapan_id' => $tahapan_id])
			->count_all_results();
		return ($count > 0);
	}
}
