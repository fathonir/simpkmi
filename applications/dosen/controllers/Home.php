<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property Kegiatan_model $kegiatan_model 
 * @property Proposal_model $proposal_model
 * @property Meeting_model $meeting_model
 * @property TahapanPendampingan_model $tpendampingan_model
 */
class Home extends Dosen_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->check_credentials();
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_PROPOSAL, 'proposal_model');
		$this->load->model(MODEL_MEETING, 'meeting_model');
		$this->load->model(MODEL_TAHAPAN_PENDAMPINGAN, 'tpendampingan_model');
	}

	public function index()
	{
		$dosen = $this->session->user->dosen;
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);
		$tahapan_pendampingan = $this->tpendampingan_model->get_aktif($kegiatan->id);

		if ($tahapan_pendampingan != null)
		{
			$proposal_set = $this->proposal_model->list_by_dosen_pendamping($kegiatan->id, $dosen->id, $tahapan_pendampingan->id);
		}
		else
		{
			$proposal_set = $this->proposal_model->list_by_dosen_pendamping($kegiatan->id, $dosen->id);
		}

		$this->smarty->assign('kegiatan', $kegiatan);
		$this->smarty->assign('tahapan_pendampingan', $tahapan_pendampingan);
		$this->smarty->assign('proposal_set', $proposal_set);
		$this->smarty->display();
	}
}
