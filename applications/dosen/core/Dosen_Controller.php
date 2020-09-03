<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Fathoni <m.fathoni@mail.com>
 * 
 * @property Dosen_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_Upload $upload
 * @property Smarty_wrapper $smarty
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_Migration $migration
 */
class Dosen_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function check_credentials()
	{
		if ($this->session->userdata('user') == NULL)
		{
			redirect($this->config->item('global_base_url'));
		}
	}
}
