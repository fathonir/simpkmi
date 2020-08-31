<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property RequestUser_model $requestuser_model
 * @property LembagaPengusul_model $lembaga_model
 * @property PerguruanTinggi_model $pt_model 
 * @property User_model $user_model
 * @property Kegiatan_model $kegiatan_model
 * @property Reviewer_model $reviewer_model 
 * @property Mahasiswa_model $mahasiswa_model
 * @property Dosen_model $dosen_model
 * @property Program_studi_model $program_studi_model
 * @property CI_Email $email
 */
class Auth extends Frontend_Controller
{
	const CAPTCHA_TIMEOUT = 7200;

	public function __construct()
	{
		parent::__construct();
		
		// Load default model
		$this->load->model(MODEL_REQUEST_USER, 'requestuser_model');
		$this->load->model(MODEL_PERGURUAN_TINGGI, 'pt_model');
		$this->load->model(MODEL_LEMBAGA_PENGUSUL, 'lembaga_model');
		$this->load->model(MODEL_USER, 'user_model');
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_REVIEWER, 'reviewer_model');
		$this->load->model(MODEL_DOSEN, 'dosen_model');
		$this->load->model(MODEL_MAHASISWA, 'mahasiswa_model');
		$this->load->model(MODEL_PROGRAM_STUDI, 'program_studi_model');
	}
	
	public function registrasi_pt()
	{
		if ($this->input->method() == 'post')
		{
			// Inisialisasi file upload
			$this->load->library('upload', array(
				'allowed_types' => 'pdf',
				'max_size' => 5 * 1024, // 5 MB,
				'encrypt_name' => TRUE,
				'upload_path' => FCPATH.'upload/request-user/'
			));
					
			// Coba upload dahulu, kemudian proses datanya
			if ($this->upload->do_upload('file1'))
			{
				$data = $this->upload->data();
				
				$this->requestuser_model->nama_file = $data['file_name'];
				$this->requestuser_model->insert();
				
				$this->session->set_flashdata('result', array(
					'page_title'	=> 'Registrasi Akun SIM-PKMI',
					'message'		=> 'Request user telah berhasil. '
					. 'Dokumen yang diupload akan diverifikasi oleh tim admin maksimal 1x24 jam. '
					. 'User login akan dikirimkan ke email : '.$this->input->post('email')
				));
				
				redirect(site_url('alert/success'));
			}
			else
			{
				$this->smarty->assign('error', array(
					'message' => 'Gagal upload file. ' . $this->upload->display_errors('' ,'')
				));
			}
		}
		
		$this->smarty->assign('lembaga_set', $this->lembaga_model->list_all());
		
		$this->smarty->display();
	}
	
	public function registrasi_mahasiswa()
	{
		if ($this->input->method() == 'post')
		{
			// Load Email Library
			$this->load->library('email');
			$this->config->load('email');
			
			if ($this->input->post('submit') == 'daftar')
			{
				$pt = $this->pt_model->get_single($this->input->post('perguruan_tinggi_id'));				
				$mahasiswa = $this->mahasiswa_model->get_by_nim(
					$pt->npsn, 
					$this->input->post('program_studi_id'), 
					$this->input->post('nim'));
				$user = $this->user_model->get_single_by_mahasiswa($mahasiswa->id);
				
				$email = $this->input->post('email');
				
				// Pengecekan email yang sudah pernah dipakai
				if ($this->user_model->is_email_exist($email))
				{
					$this->session->set_flashdata('result', array(
						'page_title' => 'Registrasi Akun SIM-PKMI untuk Mahasiswa',
						'message' => "Registrasi tidak berhasil. <strong>{$email}</strong> sudah terdaftar.",
						'link_1' => anchor(site_url('auth/registrasi-mahasiswa'), 'Kembali ke halaman registrasi')
					));

					redirect(site_url('alert/error'));
					
					exit();
				}
				
				// Buat user baru jika belum ada
				if ($user == null)
				{
					$mahasiswa->email = $email;
					$this->mahasiswa_model->update($mahasiswa);
					
					$user = $this->user_model->create_user_mahasiswa($mahasiswa, $pt->id, $pt->npsn);
					$user->email = $email;
					$this->user_model->add($user);
				}
				else // update email saja
				{
					$user->email = $email;
					$this->user_model->change_email($user);
				}
				
				// Prepare Send Email
				$this->smarty->assign('nama', $mahasiswa->nama);
				$this->smarty->assign('username', $user->username);
				$this->smarty->assign('password', $user->password);
				$body = $this->smarty->fetch('email/login_mahasiswa.tpl');
				
				// Kirim Email
				$this->email->from($this->config->item('smtp_user'), 'SIM-PKMI');
				$this->email->to($email);
				$this->email->subject('Informasi Akun SIM-PKMI');
				$this->email->message($body);
				$this->email->send();
				
				// Untuk menampilkan login langsung
				$message_2 = "<p style='margin-bottom: 15px'>Berikut adalah login Anda harap disimpan.</p>"
					. "<form class='form-horizontal'>"
					. "    <div class='form-group' style>"
					. "        <label for='username' class='col-md-2 control-label'>Username</label>"
					. "        <div class='col-md-10'>"
					. "            <p class='form-control-static'>{$user->username}</p>"
					. "        </div>"
					. "    </div>"
					. "    <div class='form-group'>"
					. "        <label for='password' class='col-md-2 control-label'>Password</label>"
					. "        <div class='col-md-10'>"
					. "            <p class='form-control-static'>{$user->password}</p>"
					. "        </div>"
					. "    </div>"
					. "</form>";

				$this->session->set_flashdata('result', array(
					'page_title' => 'Registrasi Akun SIM-PKMI untuk Mahasiswa',
					'message' => "Registrasi berhasil. User login akan dikirimkan ke {$email}",
					'message_2' => $message_2,
					'link_1' => anchor(site_url('auth/login'), 'Kembali ke Login')
				));
				
				redirect(site_url('alert/success'));
				
			}
			else if ($this->input->post('submit') == 'reset-password')
			{
				$pt = $this->pt_model->get_single($this->input->post('perguruan_tinggi_id'));				
				$mahasiswa = $this->mahasiswa_model->get_by_nim(
					$pt->npsn, 
					$this->input->post('program_studi_id'), 
					$this->input->post('nim'));
				$user = $this->user_model->get_single_by_mahasiswa($mahasiswa->id);
				
				$email = $user->email;
				
				// Prepare Send Email
				$this->smarty->assign('nama', $mahasiswa->nama);
				$this->smarty->assign('username', $user->username);
				$this->smarty->assign('password', $user->password);
				$body = $this->smarty->fetch('email/login_mahasiswa.tpl');
				
				// Kirim Email
				$this->email->from($this->config->item('smtp_user'), 'SIM-PKMI');
				$this->email->to($email);
				$this->email->subject('Reset Password Akun SIM-PKMI');
				$this->email->message($body);
				$this->email->send();
				
				// Untuk menampilkan login langsung
				$message_2 = "<p style='margin-bottom: 15px'>Berikut adalah login Anda harap disimpan.</p>"
					. "<form class='form-horizontal'>"
					. "    <div class='form-group' style>"
					. "        <label for='username' class='col-md-2 control-label'>Username</label>"
					. "        <div class='col-md-10'>"
					. "            <p class='form-control-static'>{$user->username}</p>"
					. "        </div>"
					. "    </div>"
					. "    <div class='form-group'>"
					. "        <label for='password' class='col-md-2 control-label'>Password</label>"
					. "        <div class='col-md-10'>"
					. "            <p class='form-control-static'>{$user->password}</p>"
					. "        </div>"
					. "    </div>"
					. "</form>";

				$this->session->set_flashdata('result', array(
					'page_title' => 'Registrasi Akun SIM-PKMI untuk Mahasiswa',
					'message' => "Reset password berhasil. User login akan dikirimkan ke {$email}",
					'message_2' => $message_2,
					'link_1' => anchor(site_url('auth/login'), 'Kembali ke Login')
				));
				
				redirect(site_url('alert/success'));
			}
			
			exit();
		}
		
		$this->smarty->display();
	}
	
	public function login()
	{
		if ($this->input->method() == 'post')
		{
			$username	= $this->input->post('username');
			$password	= $this->input->post('password');
			$captcha	= $this->input->post('captcha');
			
			// Ambil data user by username
			$user = $this->db->get_where('user', ['username' => $username], 1)->row();
			
			$expiration = time() - $this::CAPTCHA_TIMEOUT;
			
			// Hapus file captcha lama yang expired
			$captcha_set = $this->db->where('captcha_time < ', $expiration)->get('captcha')->result();
			foreach ($captcha_set as $captcha_row)
				@unlink('./assets/captcha/'.$captcha_row->filename);
			// Hapus record db
			$this->db->where('captcha_time < ', $expiration)->delete('captcha');
			
			// ambil data captcha
			$captcha_count = $this->db->query(
				"SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?",
				array($captcha, $this->input->ip_address(), $expiration))->row()->count;
			
			// Jika row ada
			if ($user != null)
			{
				// Bandingkan password
				if ($user->password_hash == sha1($password))
				{
					// jika captcha OK
					if ($captcha_count > 0)
					{
						// Ambil data perguruan tinggi
						$perguruan_tinggi = $this->pt_model->get_single($user->perguruan_tinggi_id);
						
						// Ambil kegiatan yang aktif
						$kegiatan = $this->kegiatan_model->get_aktif($user->program_id);

						if ($user->tipe_user == TIPE_USER_NORMAL)
						{
							$redirect_to = site_url('home');
						}
						else if ($user->tipe_user == TIPE_USER_REVIEWER)
						{
							// tambahkan session reviewer
							$user->reviewer = $this->reviewer_model->get_single($user->reviewer_id);
							$redirect_to = base_url() . 'reviewer';
						}
						else if ($user->tipe_user == TIPE_USER_MAHASISWA)
						{
							// tambahkan session mahasiswa
							$user->mahasiswa = $this->mahasiswa_model->get($user->mahasiswa_id);
							$user->mahasiswa->program_studi = $this->program_studi_model->get($user->mahasiswa->program_studi_id);
							$redirect_to = base_url() . 'mahasiswa';
						}
						else if ($user->tipe_user == TIPE_USER_DOSEN)
						{
							$user->dosen = $this->dosen_model->get($user->dosen_id);
							$redirect_to = base_url() . 'dosen';
						}
						else if ($user->tipe_user == TIPE_USER_ADMIN)
						{
							$redirect_to = base_url() . 'admin';
						}
						
						// Assign data login ke session
						$this->session->set_userdata('user', $user);
						$this->session->set_userdata('perguruan_tinggi', $perguruan_tinggi);
						$this->session->set_userdata('program_id', $user->program_id);
						$this->session->set_userdata('kegiatan', $kegiatan);
						
						// update latest login
						$this->db->update('user', array('latest_login' => date('Y-m-d H:i:s')), array('id' => $user->id));
						
						// end output after redirect
						redirect($redirect_to);
						exit();
					}
					else
					{	
						$this->user_model->login_failed($username, $password, $this->input->ip_address(), 'CAPTCHA_FAIL');
						$this->session->set_flashdata('failed_login', 'Isian captcha tidak sesuai. Silahkan ulangi login');
					}
				}
				else
				{
					$this->user_model->login_failed($username, $password, $this->input->ip_address(), 'WRONG_PASSWORD');
					$this->session->set_flashdata('failed_login', 'Password tidak sesuai. Silahkan ulangi login.');
				}
			}
			else
			{
				$this->user_model->login_failed($username, $password, $this->input->ip_address(), 'USER_NOT_FOUND');
				$this->session->set_flashdata('failed_login', 'Username tidak ditemukan. Silahkan ulangi login.');
			}
		}
		
		$this->smarty->assign('img_captcha', $this->get_captcha());
		
		$this->smarty->display();
	}
	
	/**
	 * POST /site/logout
	 */
	public function logout()
	{
		$this->session->unset_userdata('user');
		$this->session->unset_userdata('perguruan_tinggi');
		$this->session->unset_userdata('program_id');
		$this->session->unset_userdata('kegiatan');

		// redirect to home
		redirect(base_url());
	}
	
	public function search_pt()
	{
		$term = $this->input->get('term');
		
		$result_set = $this->pt_model->list_by_fts($term);
		
		echo json_encode($result_set);
	}
	
	/**
	 * AJAX-REQ /auth/list-prodi
	 * @param int $perguruan_tinggi_id
	 */
	public function list_prodi($perguruan_tinggi_id)
	{
		$pt = $this->pt_model->get_single($perguruan_tinggi_id);
		$program_studi_set = $this->program_studi_model->list_by_pt($pt->npsn);
		
		header('Content-type: application/json');
		echo json_encode($program_studi_set);
	}
	
	/**
	 * AJAX-REQ /auth/cari-mahasiswa
	 */
	public function cari_mahasiswa()
	{
		$perguruan_tinggi_id = $this->input->get('perguruan_tinggi_id');
		$program_studi_id = $this->input->get('program_studi_id');
		$nim = $this->input->get('nim');
		
		$pt = $this->pt_model->get_single($perguruan_tinggi_id);
		
		header('Content-type: application/json');
		
		if ($pt != NULL)
		{
			try
			{
				$mahasiswa = $this->mahasiswa_model->get_by_nim($pt->npsn, $program_studi_id, $nim);
				$user = $this->user_model->get_single_by_mahasiswa($mahasiswa->id);
				$mahasiswa->has_login = false;
					
				if ($user != null)
				{
					if ($user->email != null)
					{
						$mahasiswa->has_login = true;
						$mahasiswa->email = $user->email;
					}
				}
				
				unset($mahasiswa->id_pdpt);
				
				echo json_encode($mahasiswa);
			}
			catch (Exception $exc)
			{
				echo json_encode(null);
			}
		}
		else
		{
			echo json_encode(null);
		}
	}
	
	public function get_captcha()
	{		
		$this->load->helper('captcha');
			
		// Captcha Parameter
		$captcha_params = array(
			'img_path'		=> FCPATH . 'assets/captcha/',
			'img_url'		=> base_url('assets/captcha/'),
			'font_path'		=> FCPATH . 'assets/fonts/OpenSans-Semibold.ttf',
			'img_width'     => 180,
			'img_height'    => 60,
			'expiration'    => $this::CAPTCHA_TIMEOUT,
			'word_length'   => 4,
			'font_size'     => 28,
			'pool'          => '0123456789',
			'img_id'		=> time(),

			// White background and border, black text and red grid
			'colors'        => array(
					'background' => array(255, 255, 255),
					'border' => array(0, 0, 0),
					'text' => array(0, 0, 0),
					'grid' => array(rand(0, 255), rand(0, 255), rand(0, 255))
			)
		);
		
		$captcha = create_captcha($captcha_params);
		
		if ($captcha)
		{
			$data = array(
				'captcha_time'  => $captcha['time'],
				'ip_address'    => $this->input->ip_address(),
				'word'          => $captcha['word'],
				'filename'		=> $captcha['filename']
			);

			$this->db->insert('captcha', $data);

			return $captcha['image'];
		}
		else
		{
			return 'Captcha Error: GD Extension / Image Path Not Writeable';
		}
	}
}
