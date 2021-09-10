<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property PerguruanTinggi_model $pt_model
 * @property Kegiatan_model $kegiatan_model
 * @property Tahapan_model $tahapan_model
 * @property Proposal_model $proposal_model
 * @property TahapanProposal_model $tproposal_model
 * @property File_proposal_model $file_proposal_model
 * @property Reviewer_model $reviewer_model
 * @property PlotReviewer_model $plotr_model
 * @property CI_Config $config
 * @property CI_Form_validation $form_validation
 */
class Review extends Reviewer_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model(MODEL_PERGURUAN_TINGGI, 'pt_model');
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_TAHAPAN, 'tahapan_model');
		$this->load->model(MODEL_PROPOSAL, 'proposal_model');
		$this->load->model(MODEL_TAHAPAN_PROPOSAL, 'tproposal_model');
		$this->load->model(MODEL_FILE_PROPOSAL, 'file_proposal_model');
		$this->load->model(MODEL_REVIEWER, 'reviewer_model');
		$this->load->model(MODEL_PLOT_REVIEWER, 'plotr_model');
		$this->load->model(MODEL_FILE_EXPO, 'file_expo_model');
		
		$this->load->library('form_validation');
	}
	
	public function index()
	{
		$tahapan_id = $this->input->get('tahapan_id');
		$kegiatan_id = $this->input->get('kegiatan_id');

		if ($kegiatan_id)
		{
			$this->smarty->assign('kegiatan', $this->kegiatan_model->get_single($kegiatan_id));
		}
		
		$this->smarty->assign('tahapan', $this->tahapan_model->get_single($tahapan_id));
		$this->smarty->assign('kegiatan_option_set', $this->kegiatan_model->list_aktif_for_option(PROGRAM_KBMI));
		$this->smarty->assign('tahapan_option_set', $this->tahapan_model->list_all_for_option());
		$this->smarty->assign('reviewer_id', $this->session->userdata('user')->reviewer_id);
		
		$this->smarty->display();
	}
	
	public function index_data()
	{
		$kegiatan_id = $this->input->get('kegiatan_id');
		$tahapan_id = $this->input->get('tahapan_id');
		$order_by = $this->input->get('order_by');
		
		$data_set = $this->proposal_model->list_proposal_per_reviewer($kegiatan_id, $tahapan_id, $this->session->userdata('user')->reviewer_id, $order_by);
		
		// reformat data
		foreach ($data_set as &$data)
		{
			$data->judul_display = new stdClass();
			$data->judul_display->judul = $data->judul;
			$data->judul_display->nama_pt = $data->nama_pt;
			$data->judul_display->nama_ketua = $data->nama_ketua;
			
			unset($data->judul);
			unset($data->nama_pt);
			unset($data->nama_ketua);
		}
		
		echo json_encode(array('data' => $data_set));
	}
	
	public function penilaian($plot_reviewer_id)
	{
		$plot_reviewer		= $this->plotr_model->get_single($plot_reviewer_id);
		$tahapan_proposal	= $this->tproposal_model->get_single($plot_reviewer->tahapan_proposal_id);
		$tahapan			= $this->tahapan_model->get_single($tahapan_proposal->tahapan_id);
		$proposal			= $this->proposal_model->get_single($tahapan_proposal->proposal_id);
		$file_proposal_set	= $this->file_proposal_model->list_by_proposal($tahapan_proposal->proposal_id);
		$kegiatan			= $this->kegiatan_model->get_single($proposal->kegiatan_id);
		$pt					= $this->pt_model->get_single($proposal->perguruan_tinggi_id);
		
		$reviewer_id = $this->session->userdata('user')->reviewer_id;
		
		// Ambil komponen penilaian di left join dengan hasil penilaian per reviewer
		// dimulai dari tabel plot reviewer
		$penilaian_set = $this->db
			->select('kp.id as komponen_penilaian_id, kp.urutan, kp.kriteria, kp.bobot, hp.id as hp_id, hp.skor, hp.nilai')
			->from('plot_reviewer pr')
			->join('tahapan_proposal tp', 'tp.id = pr.tahapan_proposal_id')
			->join('kegiatan k', 'k.id = tp.kegiatan_id')
			->join('tahapan t', 't.id = tp.tahapan_id')
			->join('komponen_penilaian kp', 'kp.kegiatan_id = k.id AND kp.tahapan_id = t.id')
			->join('hasil_penilaian hp', 'hp.plot_reviewer_id = pr.id AND hp.komponen_penilaian_id = kp.id', 'LEFT')
			->where(['pr.id' => $plot_reviewer_id])
			->order_by('kp.urutan')
			->get()->result();
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// replace input
			$_POST['biaya_rekomendasi'] = str_replace('.', '', $_POST['biaya_rekomendasi']);
			
			// Isian biaya_rekomendasi wajib ada, minimal 1
			$this->form_validation->set_rules('biaya_rekomendasi', 'Biaya Rekomendasi', 'greater_than[0]', 
				array('greater_than' => "Biaya rekomendasi Wajib di isi !"));
			
			// Isian komponen nilai
			foreach ($this->input->post('skor') as $komponen_penilaian_id => $skor)
			{
				$this->form_validation->set_rules("skor[{$komponen_penilaian_id}]", "Skor", 'required');
			}
			
			// Pre-updated object
			$proposal->lama_kegiatan_thn		= $this->input->post('lama_kegiatan_thn');
			$proposal->lama_kegiatan_bln		= $this->input->post('lama_kegiatan_bln');
			// Nilai diupdate dari isian reviewer, tp tidak ditampilkan
			$proposal->biaya_diusulkan			= str_replace('.', '', $this->input->post('biaya_diusulkan'));
			$proposal->biaya_kontribusi_pt		= str_replace('.', '', $this->input->post('biaya_kontribusi_pt'));
			$proposal->biaya_kontribusi_umkm	= str_replace('.', '', $this->input->post('biaya_kontribusi_umkm'));
			$proposal->updated_at				= date('Y-m-d H:i:s');
			
			$plot_reviewer->nilai_reviewer			= 0; // di 0 kan dulu sebelum di counting ulang
			$plot_reviewer->biaya_diusulkan			= str_replace('.', '', $this->input->post('biaya_diusulkan'));
			$plot_reviewer->biaya_rekomendasi		= str_replace('.', '', $this->input->post('biaya_rekomendasi'));
			$plot_reviewer->biaya_kontribusi_pt		= str_replace('.', '', $this->input->post('biaya_kontribusi_pt'));
			$plot_reviewer->biaya_kontribusi_umkm	= str_replace('.', '', $this->input->post('biaya_kontribusi_umkm'));
			$plot_reviewer->komentar				= $this->input->post('komentar');
			$plot_reviewer->updated_at				= date('Y-m-d H:i:s');
			
			// Pre-updated object
			foreach ($penilaian_set as &$penilaian)
			{
				$penilaian->skor = (int)$this->input->post("skor[{$penilaian->komponen_penilaian_id}]");
				
				// update nilai jika ada isian skor
				if ($skor != '')
				{
					$penilaian->nilai = $penilaian->bobot * $penilaian->skor;
				}

				$plot_reviewer->nilai_reviewer += $penilaian->nilai;
			}
			
			// clear references
			unset($penilaian);
			
			if ($this->form_validation->run())
			{
				$this->db->trans_begin();
				
				$this->db->update('proposal', $proposal, ['id' => $proposal->id]);
				$this->db->update('plot_reviewer', $plot_reviewer, ['id' => $plot_reviewer->id]);
				
				foreach ($penilaian_set as $penilaian)
				{
					if ($penilaian->hp_id == '')  // hasil_penilaian_id kosong artinya belum ada record, perlu INSERT
					{
						$this->db->insert('hasil_penilaian', array(
							'plot_reviewer_id'		=> $plot_reviewer->id,
							'komponen_penilaian_id'	=> $penilaian->komponen_penilaian_id,
							'skor'					=> $penilaian->skor,	// nilai sudah di dapat : lihat line 107
							'nilai'					=> $penilaian->nilai	// nilai sudah di dapat : lihat line 110
						));
					}
					else
					{
						$this->db->update('hasil_penilaian', array(
							'skor'					=> $penilaian->skor,	// nilai sudah di dapat : lihat line 107
							'nilai'					=> $penilaian->nilai,	// nilai sudah di dapat : lihat line 110
							'updated_at'			=> date('Y-m-d H:i:s')
						), ['id' => $penilaian->hp_id]);	// hasil_penilaian_id
					}
				}
				
				
				if ($this->db->trans_status() !== FALSE)
				{
					$this->db->trans_commit();
					
					$this->smarty->assign('updated', TRUE);
				}
				else
				{
					show_error("Terdapat kesalahan");
				}
			}
			else
			{
				$this->smarty->assign('updated', FALSE);
			}
		}
		
		$this->smarty->assign('plot_reviewer', $plot_reviewer);
		$this->smarty->assign('tahapan_proposal', $tahapan_proposal);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('tahapan', $tahapan);
		$this->smarty->assign('file_proposal_set', $file_proposal_set);
		$this->smarty->assign('pt', $pt);
		
		$download_url = $this->config->item('global_base_url') . '/index.php/download/';
		$this->smarty->assign('download_url', $download_url);
		
		if ($kegiatan->program_id == PROGRAM_PBBT) { $program_folder = 'pbbt'; $username = $pt->npsn . '01'; }
		if ($kegiatan->program_id == PROGRAM_KBMI) { $program_folder = 'kbmi'; $username = $pt->npsn . '02'; }
		$preview_url = $this->config->item('global_base_url') . '/upload/file-proposal/'.$program_folder.'/'.$username.'/'.$proposal->id.'/';
		$this->smarty->assign('preview_url', $preview_url);
		
		$this->smarty->assign('penilaian_set', $penilaian_set);
		
		$skor_set = $this->db
			->select("skor, concat(skor,' - ',keterangan) as keterangan", FALSE)
			->where('is_aktif', 1)
			->get('skor')->result_array();
		$this->smarty->assign('skor_option_set', array_column($skor_set, 'keterangan', 'skor'));
		
		$this->smarty->display();
	}
	
	/**
	 * Form penilaian dengan format tahun 2019
	 * @param int $plot_reviewer_id
	 */
	public function penilaian_2019($plot_reviewer_id)
	{
		$plot_reviewer		= $this->plotr_model->get_single($plot_reviewer_id);
		$tahapan_proposal	= $this->tproposal_model->get_single($plot_reviewer->tahapan_proposal_id);
		$tahapan			= $this->tahapan_model->get_single($tahapan_proposal->tahapan_id);
		$proposal			= $this->proposal_model->get_single($tahapan_proposal->proposal_id);
		$file_proposal_set	= $this->file_proposal_model->list_by_proposal($tahapan_proposal->proposal_id);
		$kegiatan			= $this->kegiatan_model->get_single($proposal->kegiatan_id);
		$pt					= $this->pt_model->get_single($proposal->perguruan_tinggi_id);
		
		$reviewer_id = $this->session->userdata('user')->reviewer_id;
		
		// Ambil komponen penilaian di left join dengan hasil penilaian per reviewer
		// dimulai dari tabel plot reviewer
		$penilaian_set = $this->db
			->select('kp.id as komponen_penilaian_id, kp.urutan, kp.kriteria, kp.bobot, hp.id as hp_id, hp.skor, hp.nilai')
			->from('plot_reviewer pr')
			->join('tahapan_proposal tp', 'tp.id = pr.tahapan_proposal_id')
			->join('kegiatan k', 'k.id = tp.kegiatan_id')
			->join('tahapan t', 't.id = tp.tahapan_id')
			->join('komponen_penilaian kp', 'kp.kegiatan_id = k.id AND kp.tahapan_id = t.id')
			->join('hasil_penilaian hp', 'hp.plot_reviewer_id = pr.id AND hp.komponen_penilaian_id = kp.id', 'LEFT')
			->where(['pr.id' => $plot_reviewer_id])
			->order_by('kp.urutan')
			->get()->result();
		
		foreach ($penilaian_set as &$penilaian)
		{
			$penilaian->isian_set = $isian_set = $this->db
				->select('kpi.pertanyaan, ip.isian_ke, ip.isian')
				->from('isian_proposal ip')
				->join('proposal p', 'p.id = ip.proposal_id')
				->join('tahapan_proposal tp', 'tp.proposal_id = p.id')
				->join('plot_reviewer pr', 'pr.tahapan_proposal_id = tp.id')
				->join('komponen_penilaian_isian kpi', 'kpi.isian_ke = ip.isian_ke')
				->where('pr.id', $plot_reviewer_id)
				->where('kpi.komponen_penilaian_id', $penilaian->komponen_penilaian_id)
				->order_by('kpi.urutan')
				->get()->result();
		}
		
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// replace input
			$_POST['biaya_rekomendasi'] = str_replace('.', '', $_POST['biaya_rekomendasi']);
			
			// Isian biaya_rekomendasi wajib ada, minimal 1
			$this->form_validation->set_rules('biaya_rekomendasi', 'Biaya Rekomendasi', 'greater_than[0]', 
				array('greater_than' => "Biaya rekomendasi Wajib di isi !"));
			
			// Isian komponen nilai
			foreach ($this->input->post('skor') as $komponen_penilaian_id => $skor)
			{
				$this->form_validation->set_rules("skor[{$komponen_penilaian_id}]", "Skor", 'required');
			}
			
			// Pre-updated object
			$proposal->lama_kegiatan_thn		= $this->input->post('lama_kegiatan_thn');
			$proposal->lama_kegiatan_bln		= $this->input->post('lama_kegiatan_bln');
			// Nilai diupdate dari isian reviewer, tp tidak ditampilkan
			$proposal->biaya_diusulkan			= str_replace('.', '', $this->input->post('biaya_diusulkan'));
			$proposal->biaya_kontribusi_pt		= str_replace('.', '', $this->input->post('biaya_kontribusi_pt'));
			$proposal->biaya_kontribusi_umkm	= str_replace('.', '', $this->input->post('biaya_kontribusi_umkm'));
			$proposal->updated_at				= date('Y-m-d H:i:s');
			
			$plot_reviewer->nilai_reviewer			= 0; // di 0 kan dulu sebelum di counting ulang
			$plot_reviewer->biaya_diusulkan			= str_replace('.', '', $this->input->post('biaya_diusulkan'));
			$plot_reviewer->biaya_rekomendasi		= str_replace('.', '', $this->input->post('biaya_rekomendasi'));
			$plot_reviewer->biaya_kontribusi_pt		= str_replace('.', '', $this->input->post('biaya_kontribusi_pt'));
			$plot_reviewer->biaya_kontribusi_umkm	= str_replace('.', '', $this->input->post('biaya_kontribusi_umkm'));
			$plot_reviewer->komentar				= $this->input->post('komentar');
			$plot_reviewer->updated_at				= date('Y-m-d H:i:s');
			
			// Pre-updated object
			foreach ($penilaian_set as &$penilaian)
			{
				$penilaian->skor = (int)$this->input->post("skor[{$penilaian->komponen_penilaian_id}]");
				
				// update nilai jika ada isian skor
				if ($skor != '')
				{
					$penilaian->nilai = $penilaian->bobot * $penilaian->skor;
				}

				$plot_reviewer->nilai_reviewer += $penilaian->nilai;
			}
			
			// clear references
			unset($penilaian);
			
			if ($this->form_validation->run())
			{
				$this->db->trans_begin();
				
				$this->db->update('proposal', $proposal, ['id' => $proposal->id]);
				$this->db->update('plot_reviewer', $plot_reviewer, ['id' => $plot_reviewer->id]);
				
				foreach ($penilaian_set as $penilaian)
				{
					if ($penilaian->hp_id == '')  // hasil_penilaian_id kosong artinya belum ada record, perlu INSERT
					{
						$this->db->insert('hasil_penilaian', array(
							'plot_reviewer_id'		=> $plot_reviewer->id,
							'komponen_penilaian_id'	=> $penilaian->komponen_penilaian_id,
							'skor'					=> $penilaian->skor,	// nilai sudah di dapat : lihat line 107
							'nilai'					=> $penilaian->nilai	// nilai sudah di dapat : lihat line 110
						));
					}
					else
					{
						$this->db->update('hasil_penilaian', array(
							'skor'					=> $penilaian->skor,	// nilai sudah di dapat : lihat line 107
							'nilai'					=> $penilaian->nilai,	// nilai sudah di dapat : lihat line 110
							'updated_at'			=> date('Y-m-d H:i:s')
						), ['id' => $penilaian->hp_id]);	// hasil_penilaian_id
					}
				}
				
				
				if ($this->db->trans_status() !== FALSE)
				{
					$this->db->trans_commit();
					
					$this->smarty->assign('updated', TRUE);
				}
				else
				{
					show_error("Terdapat kesalahan");
				}
			}
			else
			{
				$this->smarty->assign('updated', FALSE);
			}
		}
		
		// Additional data
		$proposal->jumlah_anggota = $this->proposal_model->get_jumlah_anggota($tahapan_proposal->proposal_id);
		
		$this->smarty->assign('plot_reviewer', $plot_reviewer);
		$this->smarty->assign('tahapan_proposal', $tahapan_proposal);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('tahapan', $tahapan);
		$this->smarty->assign('file_proposal_set', $file_proposal_set);
		$this->smarty->assign('pt', $pt);
		
		$download_url = $this->config->item('global_base_url') . '/index.php/download/';
		$this->smarty->assign('download_url', $download_url);
		
		if ($kegiatan->program_id == PROGRAM_PBBT) { $program_folder = 'pbbt'; $username = $pt->npsn . '01'; }
		if ($kegiatan->program_id == PROGRAM_KBMI) { $program_folder = 'kbmi'; $username = $pt->npsn . '02'; }
		$preview_url = $this->config->item('global_base_url') . '/upload/lampiran/';
		$this->smarty->assign('preview_url', $preview_url);
		
		$this->smarty->assign('penilaian_set', $penilaian_set);
		
		$skor_set = $this->db
			->select("skor, concat(skor,' - ',keterangan) as keterangan", FALSE)
			->where('is_aktif', 1)
			->get('skor')->result_array();
		$this->smarty->assign('skor_option_set', array_column($skor_set, 'keterangan', 'skor'));
		
		$this->smarty->display();
	}
	
	/**
	 * Fungsi penilaian Monev
	 * @param int $plot_reviewer_id
	 */
	public function monev($plot_reviewer_id)
	{
		$plot_reviewer		= $this->plotr_model->get_single($plot_reviewer_id);
		$tahapan_proposal	= $this->tproposal_model->get_single($plot_reviewer->tahapan_proposal_id);
		$tahapan			= $this->tahapan_model->get_single($tahapan_proposal->tahapan_id);
		$proposal			= $this->proposal_model->get_single($tahapan_proposal->proposal_id);
		$file_proposal_set	= $this->file_proposal_model->list_by_proposal($tahapan_proposal->proposal_id, [
			'Kemajuan', 'Bukti Pembelanjaan', 'Bukti Kegiatan'
		]);
		$kegiatan			= $this->kegiatan_model->get_single($proposal->kegiatan_id);
		$pt					= $this->pt_model->get_single($proposal->perguruan_tinggi_id);
		
		// Ambil komponen penilaian di left join dengan hasil penilaian per reviewer
		// dimulai dari tabel plot reviewer
		$penilaian_set = $this->db
			->select('kp.id as komponen_penilaian_id, kp.urutan, kp.kriteria, kp.bobot, hp.id as hp_id, hp.skor, hp.nilai, hp.komentar')
			->from('plot_reviewer pr')
			->join('tahapan_proposal tp', 'tp.id = pr.tahapan_proposal_id')
			->join('kegiatan k', 'k.id = tp.kegiatan_id')
			->join('tahapan t', 't.id = tp.tahapan_id')
			->join('komponen_penilaian kp', 'kp.kegiatan_id = k.id AND kp.tahapan_id = t.id')
			->join('hasil_penilaian hp', 'hp.plot_reviewer_id = pr.id AND hp.komponen_penilaian_id = kp.id', 'LEFT')
			->where(['pr.id' => $plot_reviewer_id])
			->order_by('kp.urutan')
			->get()->result();
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// Isian komponen nilai
			foreach ($this->input->post('skor') as $komponen_penilaian_id => $skor)
			{
				$this->form_validation->set_rules("skor[{$komponen_penilaian_id}]", "Skor", 'required');
				$this->form_validation->set_rules("komentar_penilaian[{$komponen_penilaian_id}]", "Skor", 'required');
			}
			
			$plot_reviewer->nilai_reviewer			= 0; // di 0 kan dulu sebelum di counting ulang
			$plot_reviewer->komentar				= $this->input->post('komentar');
			$plot_reviewer->updated_at				= date('Y-m-d H:i:s');
			
			// Pre-updated object
			foreach ($penilaian_set as &$penilaian)
			{
				$penilaian->skor = (int)$this->input->post("skor[{$penilaian->komponen_penilaian_id}]");
				$penilaian->komentar = $this->input->post("komentar_penilaian[{$penilaian->komponen_penilaian_id}]");
				
				// update nilai jika ada isian skor
				if ($skor != '')
				{
					$penilaian->nilai = $penilaian->bobot * $penilaian->skor;
				}

				$plot_reviewer->nilai_reviewer += $penilaian->nilai;
			}
			
			// clear references
			unset($penilaian);
			
			if ($this->form_validation->run())
			{
				$this->db->trans_begin();
				
				// sudah tidak mengupdate proposal lagi
				// $this->db->update('proposal', $proposal, ['id' => $proposal->id]);
				$this->db->update('plot_reviewer', $plot_reviewer, ['id' => $plot_reviewer->id]);
				
				foreach ($penilaian_set as $penilaian)
				{
					if ($penilaian->hp_id == '')  // hasil_penilaian_id kosong artinya belum ada record, perlu INSERT
					{
						$this->db->insert('hasil_penilaian', array(
							'plot_reviewer_id'		=> $plot_reviewer->id,
							'komponen_penilaian_id'	=> $penilaian->komponen_penilaian_id,
							'skor'					=> $penilaian->skor,	// nilai sudah di dapat : lihat line 107
							'nilai'					=> $penilaian->nilai,	// nilai sudah di dapat : lihat line 110
							'komentar'				=> $penilaian->komentar,
							'created_at'			=> date('Y-m-d H:i:s')
						));
					}
					else
					{
						$this->db->update('hasil_penilaian', array(
							'skor'					=> $penilaian->skor,	// nilai sudah di dapat : lihat line 107
							'nilai'					=> $penilaian->nilai,	// nilai sudah di dapat : lihat line 110
							'komentar'				=> $penilaian->komentar,
							'updated_at'			=> date('Y-m-d H:i:s')
						), ['id' => $penilaian->hp_id]);	// hasil_penilaian_id
					}
				}
				
				
				if ($this->db->trans_status() !== FALSE)
				{
					$this->db->trans_commit();
					
					$this->smarty->assign('updated', TRUE);
				}
				else
				{
					show_error("Terdapat kesalahan");
				}
			}
			else
			{
				$this->smarty->assign('updated', FALSE);
			}
		}
		
		$this->smarty->assign('plot_reviewer', $plot_reviewer);
		$this->smarty->assign('tahapan_proposal', $tahapan_proposal);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('tahapan', $tahapan);
		$this->smarty->assign('file_proposal_set', $file_proposal_set);
		$this->smarty->assign('pt', $pt);
		
		$download_url = $this->config->item('global_base_url') . '/index.php/download/';
		$this->smarty->assign('download_url', $download_url);
		
		if ($kegiatan->program_id == 1) { $program_folder = 'pbbt'; $username = $pt->npsn . '01'; }
		if ($kegiatan->program_id == 2) { $program_folder = 'kbmi'; $username = $pt->npsn . '02'; }
		$preview_url = $this->config->item('global_base_url') . '/upload/file-proposal/'.$program_folder.'/'.$username.'/'.$proposal->id.'/';
		$this->smarty->assign('preview_url', $preview_url);
		
		$this->smarty->assign('penilaian_set', $penilaian_set);

		// Monev KBMI 2020 pakai format 1-3
		if ($proposal->kegiatan_id == 14)
		{
			$skor_set = $this->db
				->select("skor, concat(skor,' - ',keterangan) as keterangan", FALSE)
				->where('skala_skor_id', SKALA_SKOR_1_3)
				->get('skor')->result_array();
		}
		// Monev KBMI 2021 pakai format Perubahan 1-3
		else if ($proposal->kegiatan_id == 18)
		{
			$skor_set = $this->db
				->select("skor, keterangan", FALSE)
				->where('skala_skor_id', SKALA_SKOR_PERUBAHAN_1_3)
				->get('skor')->result_array();
		}
		else
		{
			$skor_set = $this->db
				->select("skor, concat(skor,' - ',keterangan) as keterangan", FALSE)
				->where('skala_skor_id', SKALA_SKOR_1_7)
				->get('skor')->result_array();
		}

		$this->smarty->assign('skor_option_set', array_column($skor_set, 'keterangan', 'skor'));
		
		$this->smarty->display();
	}
	
	public function kmi_award($plot_reviewer_id)
	{
		$plot_reviewer		= $this->plotr_model->get_single($plot_reviewer_id);
		$tahapan_proposal	= $this->tproposal_model->get_single($plot_reviewer->tahapan_proposal_id);
		$tahapan			= $this->tahapan_model->get_single($tahapan_proposal->tahapan_id);
		$proposal			= $this->proposal_model->get_single($tahapan_proposal->proposal_id);
		$file_proposal_set	= $this->file_proposal_model->list_by_proposal($tahapan_proposal->proposal_id);
		$kegiatan			= $this->kegiatan_model->get_single($proposal->kegiatan_id);
		$pt					= $this->pt_model->get_single($proposal->perguruan_tinggi_id);
		
		// Ambil komponen penilaian di left join dengan hasil penilaian per reviewer
		// dimulai dari tabel plot reviewer
		$penilaian_set = $this->db
			->select('kp.id as komponen_penilaian_id, kp.urutan, kp.kriteria, kp.bobot, hp.id as hp_id, hp.skor, hp.nilai, hp.komentar')
			->from('plot_reviewer pr')
			->join('tahapan_proposal tp', 'tp.id = pr.tahapan_proposal_id')
			->join('kegiatan k', 'k.id = tp.kegiatan_id')
			->join('tahapan t', 't.id = tp.tahapan_id')
			->join('komponen_penilaian kp', 'kp.kegiatan_id = k.id AND kp.tahapan_id = t.id')
			->join('hasil_penilaian hp', 'hp.plot_reviewer_id = pr.id AND hp.komponen_penilaian_id = kp.id', 'LEFT')
			->where(['pr.id' => $plot_reviewer_id])
			->order_by('kp.urutan')
			->get()->result();
		
		if ($this->input->method() == 'post')
		{
			// Isian komponen nilai
			foreach ($this->input->post('skor') as $komponen_penilaian_id => $skor)
			{
				$this->form_validation->set_rules("skor[{$komponen_penilaian_id}]", "Skor", 'required');
			}
			
			$plot_reviewer->nilai_reviewer			= 0; // di 0 kan dulu sebelum di counting ulang
			$plot_reviewer->komentar				= $this->input->post('komentar');
			$plot_reviewer->updated_at				= date('Y-m-d H:i:s');
			
			// Pre-updated object
			foreach ($penilaian_set as &$penilaian)
			{
				$penilaian->skor = (int)$this->input->post("skor[{$penilaian->komponen_penilaian_id}]");
				
				// update nilai jika ada isian skor
				if ($skor != '')
				{
					$penilaian->nilai = $penilaian->bobot * $penilaian->skor;
				}

				$plot_reviewer->nilai_reviewer += $penilaian->nilai;
			}
			
			// clear references
			unset($penilaian);
			
			if ($this->form_validation->run())
			{
				$this->db->trans_begin();
				
				$this->db->update('plot_reviewer', $plot_reviewer, ['id' => $plot_reviewer->id]);
				
				foreach ($penilaian_set as $penilaian)
				{
					if ($penilaian->hp_id == '') 
					{
						$this->db->insert('hasil_penilaian', array(
							'plot_reviewer_id'		=> $plot_reviewer->id,
							'komponen_penilaian_id'	=> $penilaian->komponen_penilaian_id,
							'skor'					=> $penilaian->skor,	
							'nilai'					=> $penilaian->nilai,	
							'komentar'				=> $penilaian->komentar,
							'created_at'			=> date('Y-m-d H:i:s')
						));
					}
					else
					{
						$this->db->update('hasil_penilaian', array(
							'skor'					=> $penilaian->skor,	
							'nilai'					=> $penilaian->nilai,	
							'komentar'				=> $penilaian->komentar,
							'updated_at'			=> date('Y-m-d H:i:s')
						), ['id' => $penilaian->hp_id]);
					}
				}
				
				
				if ($this->db->trans_status() !== FALSE)
				{
					$this->db->trans_commit();
					
					$this->smarty->assign('updated', TRUE);
				}
				else
				{
					show_error("Terdapat kesalahan");
				}
			}
			else
			{
				$this->smarty->assign('updated', FALSE);
			}
		}
		
		$this->smarty->assign('plot_reviewer', $plot_reviewer);
		$this->smarty->assign('tahapan_proposal', $tahapan_proposal);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('tahapan', $tahapan);
		$this->smarty->assign('file_proposal_set', $file_proposal_set);
		$this->smarty->assign('pt', $pt);
		$this->smarty->assign('kegiatan', $kegiatan);
		$this->smarty->assign('penilaian_set', $penilaian_set);
		
		if ($kegiatan->program_id == PROGRAM_EXPO)
		{
			$file_expo = $this->file_expo_model->get_single($kegiatan->id, $proposal->perguruan_tinggi_id);
			$this->smarty->assign('file_expo', $file_expo);
		}
		
		// Skor Penilaian
		$skor_set = $this->db
			->select("skor, concat(skor,' - ',keterangan) as keterangan", FALSE)
			->where('is_aktif', 1)
			->get('skor')->result_array();
		$this->smarty->assign('skor_option_set', array_column($skor_set, 'keterangan', 'skor'));
		
		$this->smarty->display();
	}
}
