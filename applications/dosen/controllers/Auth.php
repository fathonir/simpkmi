<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 */
class Auth extends Dosen_Controller
{	
	public function logout()
	{
		$this->session->unset_userdata('user');

		// redirect to home
		redirect(base_url());
	}
}
