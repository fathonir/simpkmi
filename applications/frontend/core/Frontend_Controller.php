<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Fathoni <m.fathoni@mail.com>
 * 
 * @property Frontend_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_Upload $upload
 * @property Smarty_wrapper $smarty
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_Migration $migration
 * @property CI_Form_validation $form_validation
 */
class Frontend_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function check_credentials()
	{
		if ($this->session->userdata('user') == NULL)
		{
			redirect($this->config->item('base_url'));
			exit();
		}
		
		// Memastikan yang login disini adalah admin PT
		if ($this->session->user->tipe_user != TIPE_USER_NORMAL)
		{
			redirect('auth/logout');
			exit();
		}
	}
}
