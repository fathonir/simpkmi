<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property Program_studi_model $program_studi_model
 * @property Mahasiswa_model $mahasiswa_model
 * @property Dosen_model $dosen_model
 * @property Kegiatan_model $kegiatan_model
 * @property Proposal_model $proposal_model
 * @property Anggota_proposal_model $anggota_model
 * @property Syarat_model $syarat_model
 * @property Isian_model $isian_model
 * @property Kategori_model $kategori_model
 */
class Kbmi extends Mahasiswa_Controller
{
	const CAPTCHA_TIMEOUT = 120;
	const CAPTCHA_LENGTH = 8;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->check_credentials();
		
		$this->load->model(MODEL_PROGRAM_STUDI, 'program_studi_model');
		$this->load->model(MODEL_MAHASISWA, 'mahasiswa_model');
		$this->load->model(MODEL_DOSEN, 'dosen_model');
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_PROPOSAL, 'proposal_model');
		$this->load->model(MODEL_ANGGOTA_PROPOSAL, 'anggota_model');
		$this->load->model(MODEL_SYARAT, 'syarat_model');
		$this->load->model(MODEL_ISIAN, 'isian_model');
		$this->load->model(MODEL_KATEGORI, 'kategori_model');
	}
	
	public function identitas()
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);
		
		// If expired, redirect ke home
		if (time() < strtotime($kegiatan->tgl_awal_upload) || strtotime($kegiatan->tgl_akhir_upload) < time())
		{
			redirect('home'); exit();
		}
		
		$proposal = $this->proposal_model->get_by_ketua($kegiatan->id, $this->session->user->mahasiswa_id);
		
		// Tombol Berikutnya di klik
		if ($this->input->method() == 'post')
		{
			// Update jika belum di submit
			if (!$proposal->is_submited)
			{
				$proposal->judul = trim($this->input->post('judul'));
				$proposal->mulai_berjalan = trim($this->input->post('mulai_berjalan'));
				$proposal->kategori_id = $this->input->post('kategori_id');
				$proposal->updated_at = date('Y-m-d H:i:s');
				$this->proposal_model->update($proposal->id, $proposal);
			}
			
			redirect('kbmi/step/0');
			exit();
		}
		
		$proposal->anggota_proposal_set = $this->anggota_model->list_by_proposal($proposal->id);
		if ($proposal->dosen_id != '') $proposal->dosen = $this->dosen_model->get($proposal->dosen_id);
		$this->smarty->assign('proposal', $proposal);
		
		$mahasiswa = $this->session->user->mahasiswa;
		$this->smarty->assign('mahasiswa', $mahasiswa);
		
		$program_studi_set = $this->program_studi_model->list_by_pt_sarjana_only($this->session->perguruan_tinggi->npsn);
		$this->smarty->assignForCombo('program_studi_set', $program_studi_set, 'id', 'nama');

		$kategori_set = $this->kategori_model->list_kategori_by_program(PROGRAM_KBMI);
		$this->smarty->assignForCombo('kategori_set', $kategori_set, 'id', 'nama_kategori');
		
		$this->smarty->display();
	}
	
	public function cari_mahasiswa()
	{
		try
		{
			$mahasiswa = $this->mahasiswa_model->get_by_nim(
				$this->session->perguruan_tinggi->npsn,
				$this->input->post('program_studi_id'),
				$this->input->post('nim'));
			
			if ($mahasiswa != NULL)
			{
				
				$mahasiswa->program_studi = $this->program_studi_model->get($mahasiswa->program_studi_id);
				echo json_encode(['result' => true, 'mahasiswa' => $mahasiswa]);
				exit();
			}
		}
		catch (Exception $exc)
		{
			echo json_encode(['result' => false, 'message' => $exc->getMessage()]);
			exit();
		}
		
		echo json_encode(['result' => false, 'message' => 'Mahasiswa tidak ditemukan']);
	}
	
	public function tambah_anggota()
	{
		if ($this->input->method() == 'post')
		{
			$anggota = new stdClass();
			$anggota->proposal_id	= $this->input->post('proposal_id');
			$anggota->no_urut		= $this->input->post('no_urut');
			$anggota->mahasiswa_id	= $this->input->post('mahasiswa_id');
			$anggota->created_at	= date('Y-m-d H:i:s');

			if ($this->anggota_model->add($anggota))
			{
				echo json_encode(['result' => true, 'anggota_id' => $anggota->id]);
			}
			else
			{
				echo json_encode(['result' => false]);
			}	
		}
	}
	
	public function update_anggota()
	{
		if ($this->input->method() == 'post')
		{
			$anggota = $this->anggota_model->get($this->input->post('anggota_id'));
			$anggota->mahasiswa_id = $this->input->post('mahasiswa_id');
			$anggota->updated_at = date('Y-m-d H:i:s');
			
			$update_result = $this->anggota_model->update($anggota);
			
			echo json_encode(['result' => $update_result]);
		}
	}
	
	public function delete_anggota()
	{
		if ($this->input->method() == 'post')
		{			
			$update_result = $this->anggota_model->delete($this->input->post('anggota_id'));
			
			echo json_encode(['result' => $update_result]);
		}
	}

	public function cari_dosen()
	{
		try
		{
			$dosen = $this->dosen_model->get_by_nidn(
				$this->session->perguruan_tinggi->npsn,
				$this->input->post('program_studi_id'),
				$this->input->post('nidn'));

			if ($dosen != NULL)
			{
				echo json_encode(['result' => true, 'dosen' => $dosen]);
				exit();
			}
		}
		catch (Exception $exc)
		{
			echo json_encode(['result' => false, 'message' => $exc->getMessage()]);
			exit();
		}

		echo json_encode(['result' => false, 'message' => 'Dosen tidak ditemukan']);
	}
	
	public function update_dosen()
	{
		if ($this->input->method() == 'post')
		{
			$update_result = $this->proposal_model->update_dosen(
				$this->session->perguruan_tinggi->id,
				$this->input->post('proposal_id'),
				$this->input->post('dosen_id'));
			
			echo json_encode(['result' => $update_result]);
		}
	}

	public function step($step)
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);
		$isian = $this->isian_model->get_single($kegiatan->id, $step);
		$kelompok_isian = $this->isian_model->get_kelompok_isian($isian->kelompok_isian_id);

		// If expired, redirect ke home
		if (time() < strtotime($kegiatan->tgl_awal_upload) || strtotime($kegiatan->tgl_akhir_upload) < time())
		{
			redirect('home'); exit();
		}

		$count_isian = $this->isian_model->get_count_isian($kegiatan->id);
		
		// Prevent URL Hack
		if ($step < 0 || $step > $count_isian || !is_numeric($step))
		{
			show_error('Halaman tidak ditemukan.');
		}
		
		$proposal = $this->proposal_model->get_by_ketua($kegiatan->id, $this->session->user->mahasiswa_id);
		
		if ($this->input->method() == 'post')
			$this->_step($step, $proposal, $count_isian);
		
		$this->smarty->assign('heading', $kelompok_isian->kelompok_isian);
		$this->smarty->assign('isian', $isian);
		$this->smarty->assign('isian_proposal', $this->proposal_model->get_isian_proposal($proposal->id, $step));

		$this->smarty->assign('step', $step);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->display();
	}
	
	/**
	 * @param int $step
	 * @param Proposal_model $proposal
	 * @param int $count_isian
	 */
	private function _step($step, $proposal, $count_isian)
	{
		if ($step == 0)
		{
			if ($this->input->post('tombol') == 'Sebelumnya')
			{
				redirect('kbmi/identitas'); exit();
			}
			
			if ($this->input->post('tombol') == 'Berikutnya')
			{
				$step++;
				redirect("kbmi/step/{$step}"); exit();
			}
		}
		
		// Bab Noble Purpose, Sasaran Pelanggan
		if ($step >= 1 && $step <= $count_isian)
		{
			// Data proposal diupdate jika belum disubmit
			if (!$proposal->is_submited)
			{
				$this->proposal_model->update_isian_proposal($proposal->id, $step, $this->input->post('isian'));
			}
			
			if ($this->input->post('tombol') == 'Sebelumnya')
			{
				$step--;
				redirect("kbmi/step/{$step}"); exit();
			}

			if ($this->input->post('tombol') == 'Berikutnya')
			{
				// Last Step
				if ($step == $count_isian)
				{
					redirect("kbmi/upload"); exit();
				}
			
				$step++;
				redirect("kbmi/step/{$step}"); exit();
			}
		}
	}
	
	public function upload()
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);
		$count_isian = $this->isian_model->get_count_isian($kegiatan->id);

		if ($this->input->post('tombol') == 'Sebelumnya')
		{
			redirect("kbmi/step/{$count_isian}"); exit();
		}
		
		if ($this->input->post('tombol') == 'Berikutnya')
		{
			redirect("kbmi/confirm"); exit();
		}
		
		// If expired, redirect ke home
		if (time() < strtotime($kegiatan->tgl_awal_upload) || strtotime($kegiatan->tgl_akhir_upload) < time())
		{
			redirect('home'); exit();
		}
		
		$proposal = $this->proposal_model->get_by_ketua($kegiatan->id, $this->session->user->mahasiswa_id);
		$syarat_set = $this->syarat_model->list_by_kegiatan($kegiatan->id, $proposal->id);
		
		if ($this->input->post('tombol') == 'Unggah')
		{
			$this->load->library('upload');
			
			foreach ($syarat_set as &$syarat)
			{
				$this->upload->initialize([
					'encrypt_name'	=> TRUE,
					'upload_path'	=> FCPATH.'upload/lampiran/',
					'allowed_types'	=> explode(',', $syarat->allowed_types),
					'max_size'		=> (int)$syarat->max_size * 1024
				]);
				
				if ($this->upload->do_upload('file_syarat_' . $syarat->id))
				{
					$data = $this->upload->data();
					
					$file_row_exist = $this->db->where(array(
						'proposal_id' => $proposal->id,
						'syarat_id' => $syarat->id
					))->count_all_results('file_proposal') > 0;

					// if file record exist : update
					if ($file_row_exist)
					{
						$this->db->update('file_proposal', array(
							'nama_asli' => $data['orig_name'],
							'nama_file' => $data['file_name']
						), array('proposal_id' => $proposal->id, 'syarat_id' => $syarat->id));
					}
					else // insert
					{
						$this->db->insert('file_proposal', array(
							'proposal_id' => $proposal->id,
							'nama_asli' => $data['orig_name'],
							'nama_file' => $data['file_name'],
							'syarat_id' => $syarat->id
						));
					}
				}
				else
				{
					if ($this->upload->display_errors('', '') == 'You did not select a file to upload.' && $syarat->is_wajib == 0)
					{
						// Jika tidak wajib, maka tidak diperlukan file untuk upload
					}
					else
					{
						$syarat->upload_error_msg = $this->upload->display_errors('', '');
					}
				}
			}
			
			redirect('kbmi/upload');
			exit();
		}

		$this->smarty->assign('syarat_set', $syarat_set);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->display();
	}
	
	public function confirm()
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);

		// If expired, redirect ke home
		if (time() < strtotime($kegiatan->tgl_awal_upload) || strtotime($kegiatan->tgl_akhir_upload) < time())
		{
			redirect('home'); exit();
		}

		$proposal = $this->proposal_model->get_by_ketua($kegiatan->id, $this->session->user->mahasiswa_id);
		$kelengkapan = $this->proposal_model->get_kelengkapan_proposal($proposal->id);
		
		// Jika sudah submit, redirect ke halaman submit
		if ($proposal->is_submited)
		{
			redirect('kbmi/submited'); exit();
		}
		
		// Tombol Sebelumnya ke halaman upload
		if ($this->input->post('tombol') == 'Sebelumnya')
		{
			redirect("kbmi/upload"); exit();
		}
		
		// Tombol Submit Proposal
		if ($this->input->post('tombol') == 'Submit Proposal')
		{
			// Memastikan waktu submit sesuai jadwal
			if (time() < strtotime($kegiatan->tgl_akhir_upload))
			{
				$expiration = time() - $this::CAPTCHA_TIMEOUT;

				// Hapus file captcha lama yang expired
				$captcha_set = $this->db
					->where('length(word)', $this::CAPTCHA_LENGTH)
					->where('captcha_time < ', $expiration)
					->get('captcha')->result();
				foreach ($captcha_set as $captcha_row)
					@unlink('./assets/captcha/'.$captcha_row->filename);
				// Hapus record db
				$this->db
					->where('length(word)', $this::CAPTCHA_LENGTH)
					->where('captcha_time < ', $expiration)
					->delete('captcha');

				// ambil data captcha
				$captcha_count = $this->db->query(
					"SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?",
					[$this->input->post('captcha'), $this->input->ip_address(), $expiration])
					->row()->count;

				// Jika captcha match
				if ($captcha_count > 0)
				{				
					// Proses submit proposal
					$this->proposal_model->submit($proposal->id);

					redirect('kbmi/submited');
					exit();
				}
				else
				{
					$this->smarty->assign('error_message', 'Kode keamanan tidak sesuai. Silahkan ulangi.');
				}
			}
			else
			{
				$error_message = 'Batas waktu submit sudah selesai. Batas Akhir : ' . strftime('%d/%m/%Y %H:%M:%S', strtotime($kegiatan->tgl_akhir_upload)) . ' . Waktu sistem : ' . strftime('%d/%m/%Y %H:%M:%S');
				$this->smarty->assign('error_message', $error_message);
			}
		}
		
		$this->smarty->assign('kelengkapan', $kelengkapan);
		$this->smarty->assign('img_captcha', $this->get_captcha());
		$this->smarty->display();
	}
	
	public function get_captcha()
	{		
		$this->load->helper('captcha');
			
		// Captcha Parameter
		$captcha_params = [
			'img_path'		=> FCPATH . 'assets/captcha/',
			'img_url'		=> base_url('../assets/captcha/'),
			'font_path'		=> FCPATH . 'assets/fonts/OpenSans-Semibold.ttf',
			'img_width'     => 300,
			'img_height'    => 60,
			'expiration'    => $this::CAPTCHA_TIMEOUT,
			'word_length'   => $this::CAPTCHA_LENGTH,
			'font_size'     => 28,
			'pool'          => '0123456789abcdefghijklmnopqrstuvwxyz',
			'img_id'		=> time(),

			// White background and border, black text and red grid
			'colors'        => [
				'background'	=> [255, 255, 255],
				'border'		=> [0, 0, 0],
				'text'			=> [0, 0, 0],
				'grid'			=> [rand(0, 255), rand(0, 255), rand(0, 255)]
			]
		];
		
		$captcha = create_captcha($captcha_params);
		
		if ($captcha)
		{
			$data = [
				'captcha_time'  => $captcha['time'],
				'ip_address'    => $this->input->ip_address(),
				'word'          => $captcha['word'],
				'filename'		=> $captcha['filename']
			];

			$this->db->insert('captcha', $data);

			return $captcha['image'];
		}
		else
		{
			return 'Captcha Error: GD Extension / Image Path Not Writeable';
		}
	}
	
	public function submited()
	{
		$this->smarty->display();
	}

	public function upload_kemajuan()
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);

		// If expired, redirect ke home
		if (time() < strtotime($kegiatan->tgl_awal_upload_kemajuan) || strtotime($kegiatan->tgl_akhir_upload_kemajuan) < time())
		{
			redirect('home'); exit();
		}

		$proposal = $this->proposal_model->get_by_ketua($kegiatan->id, $this->session->user->mahasiswa_id);
		$syarat_set = $this->syarat_model->list_by_kegiatan($kegiatan->id, $proposal->id, [
			'Kemajuan', 'Bukti Pembelanjaan', 'Bukti Kegiatan'
		]);

		if ($this->input->post('tombol') == 'Simpan')
		{
			$proposal->dana_dipakai_t1 = (int)str_replace('.', '', $this->input->post('dana_dipakai_t1'));
			$proposal->updated_at = date('Y-m-d H:i:s');
			$this->proposal_model->update($proposal->id, $proposal);

			$this->session->set_flashdata('simpan_success', true);

			redirect('kbmi/upload-kemajuan');
			exit();
		}

		if ($this->input->post('tombol') == 'Unggah')
		{
			$this->load->library('upload');

			foreach ($syarat_set as &$syarat)
			{
				$this->upload->initialize([
					'encrypt_name'	=> TRUE,
					'upload_path'	=> FCPATH.'upload/lampiran/',
					'allowed_types'	=> explode(',', $syarat->allowed_types),
					'max_size'		=> (int)$syarat->max_size * 1024
				]);

				if ($this->upload->do_upload('file_syarat_' . $syarat->id))
				{
					$data = $this->upload->data();

					$file_row_exist = $this->db->where(array(
							'proposal_id' => $proposal->id,
							'syarat_id' => $syarat->id
						))->count_all_results('file_proposal') > 0;

					// if file record exist : update
					if ($file_row_exist)
					{
						$this->db->update('file_proposal', array(
							'nama_asli' => $data['orig_name'],
							'nama_file' => $data['file_name']
						), array('proposal_id' => $proposal->id, 'syarat_id' => $syarat->id));
					}
					else // insert
					{
						$this->db->insert('file_proposal', array(
							'proposal_id' => $proposal->id,
							'nama_asli' => $data['orig_name'],
							'nama_file' => $data['file_name'],
							'syarat_id' => $syarat->id
						));
					}
				}
				else
				{
					if ($this->upload->display_errors('', '') == 'You did not select a file to upload.' && $syarat->is_wajib == 0)
					{
						// Jika tidak wajib, maka tidak diperlukan file untuk upload
					}
					else
					{
						$syarat->upload_error_msg = $this->upload->display_errors('', '');
					}
				}
			}

			redirect('kbmi/upload-kemajuan');
			exit();
		}

		$this->smarty->assign('syarat_set', $syarat_set);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->display();
	}
}
