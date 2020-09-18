<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property Kegiatan_model $kegiatan_model 
 * @property Tahapan_model $tahapan_model 
 * @property PerguruanTinggi_model $pt_model
 * @property Kelas_presentasi_model $kelas_model
 */
class Penilaian extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_TAHAPAN, 'tahapan_model');
		$this->load->model(MODEL_PERGURUAN_TINGGI, 'pt_model');
		$this->load->model(MODEL_KELAS_PRESENTASI, 'kelas_model');
	}
	
	public function index()
	{
		$kegiatan_id	= $this->input->get('kegiatan_id');
		$tahapan_id		= $this->input->get('tahapan_id');
		
		$data_set = $this->db
			->select('tp.id as tp_id')
			->select('p.judul, pt.nama_pt, r1.nama as reviewer_1, r2.nama as reviewer_2, r3.nama as reviewer_3')
			->select('pr1.biaya_rekomendasi as biaya_rekomendasi_1, pr2.biaya_rekomendasi as biaya_rekomendasi_2, pr3.biaya_rekomendasi as biaya_rekomendasi_3')
			->select('pr1.nilai_reviewer as nilai_reviewer_1, pr2.nilai_reviewer as nilai_reviewer_2, pr3.nilai_reviewer as nilai_reviewer_3, ABS(pr1.nilai_reviewer - pr2.nilai_reviewer) as nilai_selisih')
			->select('AVG(pr.nilai_reviewer) as nilai_rata, SUM(pr.nilai_reviewer) as nilai_total')
			->from('tahapan_proposal tp')
			->join('proposal p', 'p.id = tp.proposal_id')
			->join('perguruan_tinggi pt', 'pt.id = p.perguruan_tinggi_id')
			->join('plot_reviewer pr1', 'pr1.tahapan_proposal_id = tp.id AND pr1.no_urut = 1', 'LEFT')
			->join('reviewer r1', 'r1.id = pr1.reviewer_id', 'LEFT')
			->join('plot_reviewer pr2', 'pr2.tahapan_proposal_id = tp.id AND pr2.no_urut = 2', 'LEFT')
			->join('reviewer r2', 'r2.id = pr2.reviewer_id', 'LEFT')
			->join('plot_reviewer pr3', 'pr3.tahapan_proposal_id = tp.id AND pr3.no_urut = 3', 'LEFT')
			->join('reviewer r3', 'r3.id = pr3.reviewer_id', 'LEFT')
			->join('plot_reviewer pr', 'pr.tahapan_proposal_id = tp.id', 'LEFT')
			->where(['tp.kegiatan_id' => $kegiatan_id, 'tp.tahapan_id' => $tahapan_id])
			->group_by('p.judul, pt.nama_pt, r1.nama, r2.nama, r3.nama, pr1.nilai_reviewer, pr2.nilai_reviewer, pr3.nilai_reviewer, pr1.biaya_rekomendasi, pr2.biaya_rekomendasi, pr3.biaya_rekomendasi')
			->order_by('14 DESC, 15 DESC', NULL, FALSE) // AVG & SUM
			->get()->result();
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->assign('kegiatan_option_set', $this->kegiatan_model->list_aktif_for_option());
		$this->smarty->assign('tahapan_option_set', $this->tahapan_model->list_all_for_option());
		$this->smarty->display();
	}
	
	/**
	 * Penilaian tahap 2 dengan reviewer ke 3
	 */
	public function tahap2()
	{
		$kegiatan_id	= $this->input->get('kegiatan_id');
		$tahapan_id		= $this->input->get('tahapan_id');
		$pt_id			= $this->input->get('pt_id');
		$tampilan		= $this->input->get('tampilan');
		
		$this->smarty->assign('kegiatan_option_set', $this->kegiatan_model->list_aktif_for_option());
		$this->smarty->assign('tahapan_option_set', $this->tahapan_model->list_all_for_option());
		
		if ($kegiatan_id !='' && $tahapan_id != '')
			$this->smarty->assign('pt_set', $this->pt_model->list_by_tahapan_kegiatan($kegiatan_id, $tahapan_id));
		
		$rumus_nilai_total = 
			"CASE "
			. "WHEN pr3.id IS NULL THEN (pr1.nilai_reviewer + pr2.nilai_reviewer) "
			. "ELSE "
			. "  IF( ABS(pr1.nilai_reviewer - pr3.nilai_reviewer) < ABS(pr2.nilai_reviewer - pr3.nilai_reviewer), pr1.nilai_reviewer + pr3.nilai_reviewer, pr2.nilai_reviewer + pr3.nilai_reviewer ) "
			. "END";
		
		$rumus_rata_rekom =
			"CASE "
			. "WHEN pr3.id IS NULL THEN (pr1.biaya_rekomendasi + pr2.biaya_rekomendasi) / 2 "
			. "ELSE "
			. "  IF( ABS(pr1.nilai_reviewer - pr3.nilai_reviewer) < ABS(pr2.nilai_reviewer - pr3.nilai_reviewer), ((pr1.biaya_rekomendasi + pr3.biaya_rekomendasi) / 2), ((pr2.biaya_rekomendasi + pr3.biaya_rekomendasi) / 2) ) "
			. "END";
		
		$rumus_min_rekom =
			"CASE "
			. "WHEN pr3.id IS NULL THEN IF(pr1.biaya_rekomendasi < pr2.biaya_rekomendasi, pr1.biaya_rekomendasi, pr2.biaya_rekomendasi) "
			. "ELSE "
			. "  IF( ABS(pr1.nilai_reviewer - pr3.nilai_reviewer) < ABS(pr2.nilai_reviewer - pr3.nilai_reviewer), "
			. "    IF(pr1.biaya_rekomendasi < pr3.biaya_rekomendasi, pr1.biaya_rekomendasi, pr3.biaya_rekomendasi), "
			. "    IF(pr2.biaya_rekomendasi < pr3.biaya_rekomendasi, pr2.biaya_rekomendasi, pr3.biaya_rekomendasi) "
			. "  ) "
			. "END";
		
		// Tambahan untuk filter perguruan tinggi
		if ($pt_id != 'all')
			$this->db->where('p.perguruan_tinggi_id', $pt_id);
		
		if ($kegiatan_id != '' && $tahapan_id != '' && $pt_id != '')
		{
			$data_set = $this->db
				->select('p.judul, k.nama_kategori, pt.nama_pt, r1.nama as reviewer_1, r2.nama as reviewer_2, r3.nama as reviewer_3') // 6 col
				->select('pr1.biaya_rekomendasi as biaya_rekomendasi_1, pr2.biaya_rekomendasi as biaya_rekomendasi_2, pr3.biaya_rekomendasi as biaya_rekomendasi_3') // 3 col
				->select('pr1.nilai_reviewer as nilai_reviewer_1, pr2.nilai_reviewer as nilai_reviewer_2, pr3.nilai_reviewer as nilai_reviewer_3, ABS(pr1.nilai_reviewer - pr2.nilai_reviewer) as nilai_selisih') // 4 col
				->select('pr1.komentar as komentar_1, pr2.komentar as komentar_2, pr3.komentar as komentar_3') // 3 col
				->select($rumus_nilai_total . ' as nilai_total', FALSE) // col 17
				->select($rumus_rata_rekom . ' as rata_rekomendasi', FALSE)
				->select($rumus_min_rekom . ' as min_rekomendasi', FALSE)
				->select('p.is_afirmasi')
				->from('tahapan_proposal tp')
				->join('proposal p', 'p.id = tp.proposal_id')
				->join('kategori k', 'k.id = p.kategori_id')
				->join('perguruan_tinggi pt', 'pt.id = p.perguruan_tinggi_id')
				->join('plot_reviewer pr1', 'pr1.tahapan_proposal_id = tp.id AND pr1.no_urut = 1', 'LEFT')
				->join('reviewer r1', 'r1.id = pr1.reviewer_id', 'LEFT')
				->join('plot_reviewer pr2', 'pr2.tahapan_proposal_id = tp.id AND pr2.no_urut = 2', 'LEFT')
				->join('reviewer r2', 'r2.id = pr2.reviewer_id', 'LEFT')
				->join('plot_reviewer pr3', 'pr3.tahapan_proposal_id = tp.id AND pr3.no_urut = 3', 'LEFT')
				->join('reviewer r3', 'r3.id = pr3.reviewer_id', 'LEFT')
				->where(['tp.kegiatan_id' => $kegiatan_id, 'tp.tahapan_id' => $tahapan_id])
				->order_by('p.is_didanai DESC, 17 DESC', NULL, FALSE) // didanai, nilai total
				->get()->result();
		
			$this->smarty->assign('data_set', $data_set);
		}
		else
		{
			$this->smarty->assign('data_set', array());		
		}
		
		
		if ($tampilan == 'komentar')
			$this->smarty->display('penilaian/komentar2.tpl');
		else
			$this->smarty->display();
	}
	
	/**
	 * Penilaian KMI Award
	 */
	public function kmi_award()
	{
		$kegiatan_id			= $this->input->get('kegiatan_id');
		$tahapan_id				= $this->input->get('tahapan_id');
		$kelas_presentasi_id	= $this->input->get('kelas_presentasi_id');
		
		$this->smarty->assign('kegiatan_option_set', $this->kegiatan_model->list_aktif_for_option());
		$this->smarty->assign('tahapan_option_set', $this->tahapan_model->list_all_for_option());
		$this->smarty->assign('kelas_option_set', $this->kelas_model->list_all_for_option($kegiatan_id));
		
		$rumus_nilai_total = 
			"CASE "
			. "WHEN pr3.id IS NULL THEN (pr1.nilai_reviewer + pr2.nilai_reviewer) "
			. "ELSE "
			. "  IF( ABS(pr1.nilai_reviewer - pr3.nilai_reviewer) < ABS(pr2.nilai_reviewer - pr3.nilai_reviewer), pr1.nilai_reviewer + pr3.nilai_reviewer, pr2.nilai_reviewer + pr3.nilai_reviewer ) "
			. "END";
		
		$rumus_rata_rekom =
			"CASE "
			. "WHEN pr3.id IS NULL THEN (pr1.biaya_rekomendasi + pr2.biaya_rekomendasi) / 2 "
			. "ELSE "
			. "  IF( ABS(pr1.nilai_reviewer - pr3.nilai_reviewer) < ABS(pr2.nilai_reviewer - pr3.nilai_reviewer), ((pr1.biaya_rekomendasi + pr3.biaya_rekomendasi) / 2), ((pr2.biaya_rekomendasi + pr3.biaya_rekomendasi) / 2) ) "
			. "END";
		
		$rumus_nilai_total_2019 = "pr1.nilai_reviewer + pr2.nilai_reviewer + pr3.nilai_reviewer";
		
		// Tambahan untuk filter perguruan tinggi
		if ($kelas_presentasi_id != 'all')
			$this->db->where('p.kelas_presentasi_id', $kelas_presentasi_id);
		
		if ($kegiatan_id != '' && $tahapan_id != '' && $kelas_presentasi_id != '')
		{
			$data_set = $this->db
				->select('p.judul, k.nama_kategori, pt.nama_pt, r1.nama as reviewer_1, r2.nama as reviewer_2, r3.nama as reviewer_3') // 6 col
				->select('pr1.biaya_rekomendasi as biaya_rekomendasi_1, pr2.biaya_rekomendasi as biaya_rekomendasi_2, pr3.biaya_rekomendasi as biaya_rekomendasi_3') // 3 col
				->select('pr1.nilai_reviewer as nilai_reviewer_1, pr2.nilai_reviewer as nilai_reviewer_2, pr3.nilai_reviewer as nilai_reviewer_3, ABS(pr1.nilai_reviewer - pr2.nilai_reviewer) as nilai_selisih') // 4 col
				->select('pr1.komentar as komentar_1, pr2.komentar as komentar_2, pr3.komentar as komentar_3') // 3 col
				->select($rumus_nilai_total_2019 . ' as nilai_total', FALSE) // col 17
				->select($rumus_rata_rekom . ' as rata_rekomendasi', FALSE)
				->select('p.is_afirmasi')
				->from('tahapan_proposal tp')
				->join('proposal p', 'p.id = tp.proposal_id')
				->join('kategori k', 'k.id = p.kategori_id', 'LEFT')
				->join('perguruan_tinggi pt', 'pt.id = p.perguruan_tinggi_id')
				->join('plot_reviewer pr1', 'pr1.tahapan_proposal_id = tp.id AND pr1.no_urut = 1', 'LEFT')
				->join('reviewer r1', 'r1.id = pr1.reviewer_id', 'LEFT')
				->join('plot_reviewer pr2', 'pr2.tahapan_proposal_id = tp.id AND pr2.no_urut = 2', 'LEFT')
				->join('reviewer r2', 'r2.id = pr2.reviewer_id', 'LEFT')
				->join('plot_reviewer pr3', 'pr3.tahapan_proposal_id = tp.id AND pr3.no_urut = 3', 'LEFT')
				->join('reviewer r3', 'r3.id = pr3.reviewer_id', 'LEFT')
				->where(['tp.kegiatan_id' => $kegiatan_id, 'tp.tahapan_id' => $tahapan_id])
				->order_by('p.is_didanai DESC, 17 DESC', NULL, FALSE) // didanai, nilai total
				->get()->result();
			
			$this->smarty->assign('data_set', $data_set);
		}
		else
		{
			$this->smarty->assign('data_set', array());		
		}
		
		$this->smarty->display();
	}
	
	public function data_kelas($kegiatan_id)
	{
		echo json_encode($this->kelas_model->list_all_for_option($kegiatan_id));
	}
	
	public function didanai_pt()
	{
		$data_set = $this->db->query(
			"SELECT pt.id, pt.nama_pt, count(p.id) as jumlah_usul, sum(p.is_didanai) as jumlah_didanai
			from tahapan_proposal tp
			join proposal p on p.id = tp.proposal_id
			join perguruan_tinggi pt on pt.id = p.perguruan_tinggi_id
			where p.kegiatan_id = '2' and tp.tahapan_id = '1'
			group by pt.id, pt.nama_pt
			order by 4 desc, 3 desc")->result();
		
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->display();
	}
	
	public function didanai_kategori()
	{
		$data_set = $this->db->query(
			"SELECT k.id, k.nama_kategori, count(p.id) as jumlah_usul, sum(p.is_didanai) as jumlah_didanai
			from tahapan_proposal tp
			join proposal p on p.id = tp.proposal_id
			join kategori k on k.id = p.kategori_id
			where p.kegiatan_id = '2' and tp.tahapan_id = '1'
			group by k.id, k.nama_kategori
			order by 4 desc, 3 desc")->result();
		
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->display();
	}
}
