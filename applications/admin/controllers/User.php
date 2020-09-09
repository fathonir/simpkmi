<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property RequestUser_model $request_user_model
 * @property User_model $user_model
 * @property PerguruanTinggi_model $pt_model 
 * @property Program_model $program_model
 * @property RejectMessage_model $reject_message_model
 */
class User extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->check_credentials();
		
		$this->load->model(MODEL_REQUEST_USER, 'request_user_model');
		$this->load->model(MODEL_USER, 'user_model');
		$this->load->model(MODEL_PERGURUAN_TINGGI, 'pt_model');
		$this->load->model(MODEL_PROGRAM, 'program_model');
		$this->load->model(MODEL_REJECT_MESSAGE, 'reject_message_model');
		
		$this->load->library('email');
		
		$this->load->helper('string_helper');
		$this->load->helper('time_elapsed');
	}
	
	public function index()
	{
		$data_set = $this->user_model->list_user();
			
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->display();
	}
	
	public function data_index()
	{
		$data_set = $this->user_model->list_user();
		echo json_encode(array('data' => $data_set));
	}
	
	public function update($id)
	{	
		$user = $this->user_model->get_single($id);
		
		if ($this->input->method() == 'post')
		{
			$user->email = $this->input->post('email');
			$result = $this->user_model->change_email($user);
			$this->smarty->assign('result', $result);
		}
		
		$user->program = $this->program_model->get_single($user->program_id);
		$user->perguruan_tinggi = $this->pt_model->get_single($user->perguruan_tinggi_id);
		
		$this->smarty->assign('user', $user);
		$this->smarty->display();
	}
	
	public function request()
	{
		$data_set = $this->request_user_model->list_request();
		
		foreach ($data_set as &$data)
		{
			// get elapsed time
			$data->waktu = time_elapsed_string($data->created_at);
		}
		
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->display();
	}
	
	public function request_rejected()
	{
		$data_set = $this->request_user_model->list_request_rejected();
		
		foreach ($data_set as &$data)
		{
			// get elapsed time
			$data->waktu = time_elapsed_string($data->created_at);
		}
		
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->display();
	}
	
	public function request_approved()
	{
		$data_set = $this->request_user_model->list_request_approved();
		
		foreach ($data_set as &$data)
		{
			// get elapsed time
			$data->waktu = time_elapsed_string($data->created_at);
		}
		
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->display();
	}
	
	public function request_unreject()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$id = (int)$this->input->get('id');
			
			return $this->db->update('request_user', ['rejected_at' => NULL, 'reject_message' => NULL], ['id' => $id], 1);
		}
	}
	
	public function request_approve()
	{
		$id = (int)$this->input->get('id');
		
		$data = $this->request_user_model->get_single($id);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// start transaction
			$this->db->trans_start();
			
			// get variabel
			$pt			= $this->pt_model->get_single($this->input->post('perguruan_tinggi_id'));
			$program	= $this->program_model->get_single($data->program_id);
			
			$user = new User_model();
			$user->tipe_user			= TIPE_USER_NORMAL;
			$user->program_id			= $data->program_id;
			$user->perguruan_tinggi_id	= $pt->id;
			$user->email				= $data->email;
			
			// Buat username dengan format : [Kode PT][Kode Program]
			// Contoh : 07108601
			// 01 - PBBT, 02 - KBMI, 03 - Expo, 04 - Workshop
			$user->username = trim($pt->npsn) . str_pad($data->program_id, 2, '0', STR_PAD_LEFT);
			
			// Cek Exist User
			if ($this->user_model->is_exist($user->username, $user->program_id, $user->tipe_user))
			{
				$this->session->set_flashdata('failed', TRUE);
			}
			else
			{
				// generate password
				$user->password = random_string('numeric');
				// hash password
				$user->password_hash = sha1($user->password);

				$user->created_at = date('Y-m-d H:i:s');

				$create_result = $this->user_model->create_user($user);

				// approve
				if ($create_result) $this->request_user_model->approve($id);

				// commit if success
				$this->db->trans_commit();

				// Assign variable
				$this->smarty->assign('nama', $data->nama_pengusul);
				$this->smarty->assign('nama_program', $program->nama_program);
				$this->smarty->assign('login_link', 'http://sim-pkmi.ristekdikti.go.id');
				$this->smarty->assign('username', $user->username);
				$this->smarty->assign('password', $user->password);
				$body = $this->smarty->fetch("email/request_user_approve.tpl");

				// Kirim Email
				$this->config->load('email');
				$this->email->from($this->config->item('email_from'), $this->config->item('email_from_name'));
				$this->email->to($data->email);
				$this->email->subject('Informasi Akun SIM-PKMI');
				$this->email->message($body);
				$this->email->set_mailtype("html");
				$send_result = $this->email->send(FALSE);

				if ($send_result)
				{
					$this->session->set_flashdata('result', array(
						'page_title' => 'Daftar User Request',
						'message' => 'Berhasil',
						'link_1' => '<a href="'.site_url('user/request').'">Kembali ke daftar user request</a>'
					));
				}
				else
				{
					$this->session->set_flashdata('result', array(
						'page_title' => 'Daftar User Request',
						'message' => 'Gagal. Error Message: ' .
							$this->email->print_debugger(['headers', 'subject', 'body']),
						'link_1' => '<a href="'.site_url('user/request').'">Kembali ke daftar user request</a>'
					));
				}

				redirect(site_url('alert/success'));
			}
		}
		
		$this->smarty->assign('pt_set', $this->pt_model->list_by_name($data->perguruan_tinggi));
		$this->smarty->assign('program', $this->program_model->get_single($data->program_id));
		
		$this->smarty->assign('data', $data);
		$this->smarty->display();
	}
	
	public function request_reject()
	{
		$id = (int)$this->input->get('id');
		
		$data = $this->request_user_model->get_single($id);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$reject_message_id	= $this->input->post('reject_message_id');
			$reject_message		= $this->input->post('reject_message');
			
			if ($reject_message_id != '-1')
				$reject_message = $this->reject_message_model->get_single($reject_message_id)->message;
			
			$this->request_user_model->reject($id, $reject_message);

			// Kirim email
			$this->config->load('email');
			$this->email->from($this->config->item('email_from'), $this->config->item('email_from_name'));
			$this->email->to($data->email);
			$this->email->subject('Registrasi User SIM-PKMI Tidak Disetujui '. date('H:i:s d/m/Y'));
			$this->smarty->assign('message', $reject_message);
			$body = $this->smarty->fetch("email/request_user_reject.tpl");
			$this->email->message($body);
			$this->email->set_mailtype("html");
			$send_result = $this->email->send(FALSE);

			if ($send_result)
			{
				$this->session->set_flashdata('result', array(
					'page_title' => 'Daftar User Request',
					'message' => 'Berhasil',
					'link_1' => '<a href="'.site_url('user/request').'">Kembali ke daftar user request</a>'
				));
			}
			else
			{
				$this->session->set_flashdata('result', array(
					'page_title' => 'Daftar User Request',
					'message' => 'Gagal. Error: ' . $this->email->print_debugger(['headers', 'subject', 'body']),
					'link_1' => '<a href="'.site_url('user/request').'">Kembali ke daftar user request</a>'
				));
			}
			
			redirect(site_url('alert/success'));
		}
		
		$this->smarty->assign('reject_message_set', $this->reject_message_model->list_all());
		
		$this->smarty->assign('data', $data);
		$this->smarty->display();
	}
	
	public function reset()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$id = (int)$this->input->get('id');
			$user = $this->user_model->get_single($id);
			$new_password = random_string('numeric');
			
			$change_result = $this->user_model->change_password($id, $new_password);
			
			$program = $this->program_model->get_single($user->program_id);
			
			// Assign variable
			$this->smarty->assign('nama_program', $program->nama_program);
			$this->smarty->assign('login_link', 'http://sim-pkmi.ristekdikti.go.id');
			$this->smarty->assign('username', $user->username);
			$this->smarty->assign('password', $new_password);
			$body = $this->smarty->fetch("email/user_reset_password.tpl");

			// Kirim Email
			$this->config->load('email');
			$this->email->from($this->config->item('email_from'), $this->config->item('email_from_name'));
			$this->email->to($user->email);
			$this->email->subject('Reset Password Berhasil - SIM-PKMI');
			$this->email->message($body);
			$this->email->set_mailtype("html");
			$mail_result = $this->email->send(FALSE);
			
			echo json_encode(array(
				'change_result' => $change_result,
				'mail_result' => $mail_result
			));
		}
	}
	
	public function resend()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$id = (int)$this->input->get('id');
			$user = $this->user_model->get_single($id);
			$program = $this->program_model->get_single($user->program_id);
			
			// Assign variable
			$this->smarty->assign('nama_program', $program->nama_program);
			$this->smarty->assign('login_link', 'http://sim-pkmi.ristekdikti.go.id');
			$this->smarty->assign('username', $user->username);
			$this->smarty->assign('password', $user->password);
			$body = $this->smarty->fetch("email/user_resend_login.tpl");

			// Kirim Email
			$this->config->load('email');
			$this->email->from($this->config->item('email_from'), $this->config->item('email_from_name'));
			$this->email->to($user->email);
			$this->email->subject('Informasi Login - SIM-PKMI');
			$this->email->message($body);
			$this->email->set_mailtype("html");
			$mail_result = $this->email->send(FALSE);

			echo json_encode([
				'result' => $mail_result,
				'debug' => $mail_result ? null : $this->email->print_debugger(['headers', 'subject', 'body'])
			]);
		}
	}
	
	public function find_pt_for_select2()
	{
		$q = $this->input->get('q');
		
		$pt_set = $this->db
			->select("id, concat('[', npsn, ,'] ', nama_pt) as nama_pt")
			->where("nama_pt like '%{$q}%'", NULL, FALSE)
			->from('perguruan_tinggi')
			->get()->result();
			
		$result = array(
			'total_count' => count($pt_set),
			'incomplete_results' => false,
			'items' => $pt_set
		);
		
		echo json_encode($result);
	}
}
