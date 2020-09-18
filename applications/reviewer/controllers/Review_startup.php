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
class Review_startup extends Reviewer_Controller
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
		
		$this->load->library('form_validation');
	}
	
	public function index()
	{
		$tahapan_id = $this->input->get('tahapan_id');
		
		$this->smarty->assign('tahapan', $this->tahapan_model->get_single($tahapan_id));
		
		$this->smarty->assign('kegiatan_option_set', $this->kegiatan_model->list_aktif_for_option(PROGRAM_STARTUP));
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
		
		$this->smarty->assign('penilaian_set', $penilaian_set);
		
		$skor_set = $this->db->select("skor, concat(skor,' - ',keterangan) as keterangan", FALSE)->where('id between 1 and 6', null, false)->get('skor')->result_array();
		$this->smarty->assign('skor_option_set', array_column($skor_set, 'keterangan', 'skor'));
		
		$this->smarty->display();
	}
}
