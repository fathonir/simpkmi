<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Fathoni <m.fathoni@mail.com>
 * 
 * @property CI_Session|Backend_Session_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_Upload $upload
 * @property Smarty_wrapper $smarty
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property Mpdf_wrapper $mpdf
 */
class Admin_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->check_credentials();
	}
	
	public function check_credentials()
	{
		if ($this->session->userdata('user') == NULL)
		{
			redirect($this->config->item('global_base_url'));
		}
	}
}
