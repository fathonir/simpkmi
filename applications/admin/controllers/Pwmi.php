<?php

/**
 * Class Pwmi
 * @property Usulan_pendamping_model $usulan_pendamping_model
 */
class Pwmi extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_USULAN_PENDAMPING, 'usulan_pendamping_model');
	}

	public function usulan()
	{
		$kegiatan_id = $this->input->get('kegiatan_id');

		if ($kegiatan_id != null)
		{
			$data_set = $this->usulan_pendamping_model->list_by_kegiatan($kegiatan_id);
			$this->smarty->assign('data_set', $data_set);
		}

		$this->smarty->assign('kegiatan_option_set', $this->kegiatan_model->list_aktif_for_option(PROGRAM_PWMI));
		$this->smarty->assign('kegiatan_id', $kegiatan_id);
		$this->smarty->display();
	}

	public function index()
	{

	}

	public function penetapan()
	{

	}
}
