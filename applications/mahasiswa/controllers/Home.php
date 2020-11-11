<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property Kegiatan_model $kegiatan_model 
 * @property Proposal_model $proposal_model
 * @property Meeting_model $meeting_model
 */
class Home extends Mahasiswa_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->check_credentials();
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_PROPOSAL, 'proposal_model');
		$this->load->model(MODEL_MEETING, 'meeting_model');
	}
	
	public function index()
	{
		$kegiatan_kbmi = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);
		$kegiatan_expo = $this->kegiatan_model->get_aktif(PROGRAM_EXPO);
		$kegiatan_startup = $this->kegiatan_model->get_aktif(PROGRAM_STARTUP);
		$kegiatan_online_workshop = $this->kegiatan_model->get_aktif(PROGRAM_ONLINE_WORKSHOP);
		
		$proposal_kbmi_set = $this->proposal_model->list_by_mahasiswa($this->session->user->mahasiswa->id, PROGRAM_KBMI);
		$proposal_expo_set = $this->proposal_model->list_by_mahasiswa($this->session->user->mahasiswa->id, PROGRAM_EXPO);
		$proposal_startup_set = $this->proposal_model->list_by_mahasiswa($this->session->user->mahasiswa->id, PROGRAM_STARTUP);
		$meeting_set = $this->meeting_model->list_by_mahasiswa($this->session->user->mahasiswa->id);

		// Pengecekan Lolos Tahap 2
		foreach ($proposal_startup_set as $proposal_startup)
		{
			$proposal_startup->is_lolos_tahap_2 = $this->proposal_model->is_lolos_tahapan(
				$proposal_startup->id, TAHAPAN_EVALUASI_TAHAP_2);
		}
		
		$this->smarty->assign('kegiatan_kbmi', $kegiatan_kbmi);
		$this->smarty->assign('kegiatan_expo', $kegiatan_expo);
		$this->smarty->assign('kegiatan_startup', $kegiatan_startup);
		$this->smarty->assign('proposal_kbmi_set', $proposal_kbmi_set);
		$this->smarty->assign('proposal_expo_set', $proposal_expo_set);
		$this->smarty->assign('proposal_startup_set', $proposal_startup_set);
		$this->smarty->assign('meeting_set', $meeting_set);;
		$this->smarty->display();
	}
}
