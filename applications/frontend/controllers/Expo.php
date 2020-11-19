<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property Proposal_model $proposal_model
 * @property File_proposal_model $file_proposal_model
 * @property File_expo_model $file_expo_model
 * @property Kegiatan_model $kegiatan_model
 * @property Program_model $program_model
 * @property Syarat_model $syarat_model
 * @property Anggota_proposal_model $anggota_proposal_model
 * @property Program_studi_model $program_studi_model
 * @property Mahasiswa_model $mahasiswa_model
 */
class Expo extends Frontend_Controller
{
	const MAX_FILE_SIZE = 5242880;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->check_credentials();
		
		$this->load->model(MODEL_PROPOSAL, 'proposal_model');
		$this->load->model(MODEL_FILE_PROPOSAL, 'file_proposal_model');
		$this->load->model(MODEL_FILE_EXPO, 'file_expo_model');
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_PROGRAM, 'program_model');
		$this->load->model(MODEL_SYARAT, 'syarat_model');
		$this->load->model(MODEL_ANGGOTA_PROPOSAL, 'anggota_proposal_model');
		$this->load->model(MODEL_PROGRAM_STUDI, 'program_studi_model');
		$this->load->model(MODEL_MAHASISWA, 'mahasiswa_model');
	}
	
	/**
	 * List daftar usaha yang ikut expo
	 */
	public function index()
	{
		$tahun_set = ['2020' => '2020', '2019' => '2019', '2018' => '2018', '2017' => '2017'];
		$this->smarty->assign('tahun_set', $tahun_set);

		$tahun_selected = ($this->input->get('tahun') == '') ? date('Y') : $this->input->get('tahun');
		$this->smarty->assign('tahun_selected', $tahun_selected);

		$kegiatan = $this->kegiatan_model->get_by_tahun(PROGRAM_EXPO, $tahun_selected);
		$kegiatan->program = $this->program_model->get_single($kegiatan->program_id);
		$kegiatan->is_masa_upload =
			(strtotime($kegiatan->tgl_awal_upload) < time()) &&
			(time() < strtotime($kegiatan->tgl_akhir_upload));
		$kegiatan->tgl_awal_upload_dmy = strftime('%d %B %Y %H:%M:%S', strtotime($kegiatan->tgl_awal_upload));
		$kegiatan->tgl_akhir_upload_dmy = strftime('%d %B %Y %H:%M:%S', strtotime($kegiatan->tgl_akhir_upload));
		$this->smarty->assign('kegiatan', $kegiatan);
		
		$file_expo = $this->file_expo_model->get_single($kegiatan->id, $this->session->perguruan_tinggi->id);
		$this->smarty->assign('file_expo', $file_expo);

		// Hitung kategori Expo KMI Umum (Khusus Tahun 2020)
		$jumlah_proposal_umum = $this->proposal_model->count_proposal_kmi_award_umum($kegiatan->id,
			$this->session->perguruan_tinggi->id);
		$this->smarty->assign('jumlah_proposal_umum', $jumlah_proposal_umum);
		
		if ($this->input->method() == 'post')
		{
			// Inisialisasi file upload
			$this->load->library('upload', array(
				'allowed_types' => 'pdf',
				'max_size' => 5 * 1024, // 5 MB,
				'encrypt_name' => TRUE,
				'upload_path' => FCPATH.'upload/usulan-expo/'
			));
			
			// Coba upload dahulu, kemudian proses datanya
			if ($this->upload->do_upload('file1'))
			{
				$data_file = $this->upload->data();
				
				if ($file_expo == NULL)
				{
					$file_expo = new stdClass();
					$file_expo->created_at = date('Y-m-d H:i:s');
				}
				else
				{
					$file_expo->updated_at = date('Y-m-d H:i:s');
				}
				
				$file_expo->kegiatan_id = $kegiatan->id;
				$file_expo->perguruan_tinggi_id = $this->session->perguruan_tinggi->id;
				$file_expo->nama_file = $data_file['file_name'];
				$file_expo->nama_asli = $data_file['orig_name'];
				
				if ( ! isset($file_expo->id))
				{
					$this->file_expo_model->insert($file_expo);
				}
				else
				{
					$this->file_expo_model->update($file_expo->id, $file_expo);
				}
				
				// set message
				$this->session->set_flashdata('result', array(
					'page_title' => 'Daftar Delegasi Expo KMI',
					'message' => 'Upload file berhasil',
					'link_1' => '<a href="'.site_url('expo').'" class="alert-link">Kembali</a>'
				));

				redirect(site_url('alert/success'));
			}
			else
			{
				// set message
				$this->session->set_flashdata('result', array(
					'page_title' => 'Daftar Delegasi Expo KMI',
					'message' => 'Gagal upload file : ' . $this->upload->display_errors('' ,''),
					'link_1' => '<a href="'.site_url('expo').'" class="alert-link">Kembali</a>'
				));

				redirect(site_url('alert/error'));
			}
			
			exit();
		}
		
		$data_set = $this->proposal_model->list_proposal_expo_by_tahun(
			$this->session->perguruan_tinggi->id, $kegiatan->id);
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->display();
	}
	
	public function submit($id)
	{
		// get proposal by perguruan tinggi
		$proposal = $this->proposal_model->get_single($id, $this->session->perguruan_tinggi->id);
		$anggota_proposal_set = $this->anggota_proposal_model->list_by_proposal($proposal->id);
		$syarat_set = $this->syarat_model->list_by_kegiatan($proposal->kegiatan_id, $proposal->id);
		$kegiatan_asal = $this->kegiatan_model->get_single($proposal->kegiatan_id_asal);

		// Jika dari kegiatan sebelumnya, maka kelengkapan proposal harus di cek
		if ($kegiatan_asal != NULL)
		{
			$lengkap = TRUE;
			$properties_check = ['kategori_id', 'email', 'headline', 'deskripsi'];

			foreach ($proposal as $key => $value)
			{
				foreach ($properties_check as $property)
				{
					if (empty($proposal->{$property}))
					{
						$lengkap = FALSE;
						break;
					}

					// Jika ditemukan 1 tidak lengkap langsung break
					if ( ! $lengkap)
						break;
				}
			}

			// Cek Jumlah Anggota
			if (count($anggota_proposal_set) < 3)
			{
				$lengkap = FALSE;
			}

			// Cek syarat upload
			foreach ($syarat_set as $syarat)
			{
				// Jika logo / banner belum di upload
				if ($syarat->syarat == 'Logo' || $syarat->syarat == 'Banner')
				{
					if (empty($syarat->file_proposal_id))
					{
						$lengkap = FALSE;
						break;
					}
				}
			}

			if ( ! $lengkap)
			{
				$this->session->set_flashdata('result', array(
					'page_title' => 'Daftar Delegasi Expo KMI',
					'message' => 'Submit gagal. Silahkan masuk menu Edit untuk mengecek kelengkapan isian.',
					'link_1' => '<a href="'.site_url('expo').'" class="alert-link">Kembali</a>'
				));

				redirect(site_url('alert/error'));

				exit();
			}
		}

		// Untuk KMI Award UMUM / KMI Award KBMI, maks 1 sub kategori = 1 proposal
		if ($proposal->is_kmi_award && ($proposal->kegiatan_id_asal == '' || $kegiatan_asal->program_id == PROGRAM_KBMI))
		{
			if ($this->proposal_model->has_kmi_award($proposal->kegiatan_id, $proposal->perguruan_tinggi_id,
				$proposal->kategori_id, $proposal->kegiatan_id_asal))
			{
				$this->session->set_flashdata('result', array(
					'page_title' => 'Daftar Delegasi Expo KMI',
					'message' => 'Submit gagal. Pastikan 1 sub-kategori untuk 1 proposal saja untuk bisa di daftarkan
						di KMI Award. Jika ingin didaftarkan ke Expo KMI saja tanpa KMI Award, cukup Edit usulan 
						dengan tidak memilih KMI Award.',
					'link_1' => '<a href="'.site_url('expo').'" class="alert-link">Kembali</a>'
				));

				redirect(site_url('alert/error'));

				exit();
			}
		}
		
		if ($proposal != NULL)
		{
			$this->proposal_model->submit($proposal->id);
			
			// set message
			$this->session->set_flashdata('result', array(
				'page_title' => 'Daftar Delegasi Expo KMI',
				'message' => 'Submit usulan berhasil',
				'link_1' => '<a href="'.site_url('expo').'" class="alert-link">Kembali</a>'
			));

			redirect(site_url('alert/success'));
			
			exit();
		}
	}

	public function unsubmit($id)
	{
		// get proposal by perguruan tinggi
		$proposal = $this->proposal_model->get_single($id, $this->session->perguruan_tinggi->id);

		if ($proposal != NULL)
		{
			$this->proposal_model->unsubmit($proposal->id);

			// set message
			$this->session->set_flashdata('result', array(
				'page_title' => 'Daftar Delegasi Expo KMI',
				'message' => 'Pembatalan Submit usulan berhasil',
				'link_1' => '<a href="'.site_url('expo').'" class="alert-link">Kembali</a>'
			));

			redirect(site_url('alert/success'));

			exit();
		}
	}
	
	public function pilih_proposal()
	{
		$data_set = $this->db->query(
			"select proposal.id, judul, nama_kategori, nim_ketua, nama_ketua
			from proposal
			join kegiatan on kegiatan.id = proposal.kegiatan_id
			join program on program.id = kegiatan.program_id
			join kategori on kategori.id = proposal.kategori_id
			where
				proposal.is_didanai = 1 and
				proposal.kegiatan_id = ? and
				proposal.perguruan_tinggi_id = ? and
				proposal.id not in (select proposal_id from usaha_expo where proposal_id is not null)", 
			array(
				$this->session->kegiatan->id,
				$this->session->perguruan_tinggi->id
			))->result();
		
		$this->smarty->assign('data_set', $data_set);
		
		$this->smarty->display();
	}

	private function set_form_validation()
	{
		// Validasi Isian
		$this->form_validation->set_rules('judul', 'Nama Usaha', 'required');
		$this->form_validation->set_rules('email', 'Email Usaha', 'required|valid_email');
		$this->form_validation->set_rules('headline', 'Headline', 'required');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required');
		$this->form_validation->set_rules('link_web', 'Web', 'callback_required_one|valid_url');
		$this->form_validation->set_rules('link_instagram', 'Instagram', 'callback_required_one|valid_url');
		$this->form_validation->set_rules('link_twitter', 'Twitter', 'callback_required_one|valid_url');
		$this->form_validation->set_rules('link_youtube', 'Youtube', 'required|valid_url');
		// Anggota min 3
		for ($i = 1; $i <= 3; $i++)
		{
			$this->form_validation->set_rules("nim_anggota_{$i}", "NIM Anggota {$i}", 'required');
			$this->form_validation->set_rules("nama_anggota_{$i}", "Nama Anggota {$i}", 'required');
			$this->form_validation->set_rules("hp_anggota_{$i}", "HP Anggota {$i}", 'required');
		}
	}

	public function required_one()
	{
		// Jika salah satu saja sudah terisi, maka oke
		if ($this->input->post('link_web') != '' || $this->input->post('link_instagram') != '' ||
			$this->input->post('link_twitter') != '')
		{
			return TRUE;
		}

		$this->form_validation->set_message('required_one', 'Isi salah satu dari Web / Instagram / Twitter');
		return FALSE;
	}
	
	public function add()
	{
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->helper('assigner');

		$kategori_set = $this->db->get_where('kategori', ['program_id' => $this->session->program_id])->result();
		$this->smarty->assignForCombo('kategori_set', $kategori_set, 'id', 'nama_kategori');

		// get kegiatan aktif
		$kegiatan = $this->kegiatan_model->get_single($this->input->get('kegiatan_id'));
		$syarat_set = $this->syarat_model->list_by_kegiatan($kegiatan->id);

		// get program studi
		$program_studi_set = $this->program_studi_model->list_by_pt($this->session->perguruan_tinggi->npsn);
		$this->smarty->assignForCombo('program_studi_set', $program_studi_set, 'id', 'nama');

		if ($this->input->method() == 'post')
		{
			$now = date('Y-m-d H:i:s');

			$this->set_form_validation();

			// Validasi syarat
			$syarat_has_error = FALSE;
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

					// Simpan informasi terupload, untuk pemrosesan setelah form_validation->run()
					$file_syarat = new stdClass();
					$file_syarat->syarat_id = $syarat->id;
					$file_syarat->nama_file = $data['file_name'];
					$file_syarat->nama_asli = $data['orig_name'];
					$file_syarat->created_at = $now;
					$file_syarat_set[] = $file_syarat;
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
						$syarat_has_error = TRUE;
					}
				}
			}

			if ($this->form_validation->run() && $syarat_has_error == FALSE)
			{
				$this->db->trans_begin();

				$proposal = new stdClass();
				$proposal->perguruan_tinggi_id = $this->session->perguruan_tinggi->id;
				$proposal->kegiatan_id = $kegiatan->id;
				$proposal->is_kmi_award = $this->input->post('is_kmi_award');
				$proposal->judul = $this->input->post('judul');
				$proposal->kategori_id = $this->input->post('kategori_id');
				$proposal->email = $this->input->post('email');
				$proposal->headline = $this->input->post('headline');
				$proposal->deskripsi = $this->input->post('deskripsi');
				$proposal->link_instagram = $this->input->post('link_instagram');
				$proposal->link_web = $this->input->post('link_web');
				$proposal->link_twitter = $this->input->post('link_twitter');
				$proposal->link_youtube = $this->input->post('link_youtube');
				$proposal->created_at = $now;
				$this->db->insert('proposal', $proposal);

				// get last insert id
				$proposal->id = $this->db->insert_id();

				// Proses anggota
				for ($i = 1; $i <= 5; $i++)
				{
					// Jika tidak penuh pengisiannya
					if ($this->input->post("nim_anggota_$i") == '' || $this->input->post("nama_anggota_$i") == '')
					{
						// skip proses
						continue;
					}

					$anggota = new stdClass();
					$anggota->proposal_id = $proposal->id;
					$anggota->no_urut = $i;
					$anggota->nim = trim($this->input->post("nim_anggota_$i"));
					$anggota->nama = trim($this->input->post("nama_anggota_$i"));
					$anggota->no_hp = trim($this->input->post("hp_anggota_$i"));
					$anggota->created_at = $now;
					$this->db->insert('anggota_proposal', $anggota);
				}

				// Proses file syarat
				foreach ($file_syarat_set as $file_syarat)
				{
					$file_syarat->proposal_id = $proposal->id;
					$this->db->insert('file_proposal', $file_syarat);
				}

				// terjadi kegagalan insert data
				if ($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();

					$this->session->set_flashdata('result', array(
						'page_title' => 'Tambah usulan untuk ikut Expo KMI',
						'message' => 'Penambahan sudah berhasil !',
						'link_1' => anchor('expo', 'Kembali ke daftar expo', ['class' => 'alert-link'])
					));

					redirect(site_url('alert/success'));
					exit();
				}

				$this->db->trans_rollback();
			}

			for ($i = 1; $i <= 3; $i++)
			{
				if (form_error("nim_anggota_$i") || form_error("nama_anggota_$i") || form_error("hp_anggota_$i"))
				{
					$this->smarty->assign("error_anggota_$i", true);
				}
			}
		}

		$this->smarty->assign('syarat_set', $syarat_set);
		
		$this->smarty->display();
	}
	
	public function edit($id)
	{
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->helper('assigner');

		// memastikan id sesuai dengan pt (menghindari hack)
		$proposal = $this->proposal_model->get_single($id, $this->session->perguruan_tinggi->id);
		$kegiatan_asal = $this->kegiatan_model->get_single($proposal->kegiatan_id_asal);
		$proposal->anggota_proposal_set = $this->anggota_proposal_model->list_by_proposal($proposal->id);
		$proposal->file_proposal_set = $this->file_proposal_model->list_by_proposal($proposal->id);

		$syarat_set = $this->syarat_model->list_by_kegiatan($proposal->kegiatan_id, $proposal->id);
		if ($kegiatan_asal != NULL)
		{
			// Untuk tahun 2020 : Proposal dari KBMI / ASMI tanpa proposal
			if ($kegiatan_asal->program_id == PROGRAM_KBMI || $kegiatan_asal->program_id == PROGRAM_STARTUP)
			{
				// Hapus Syarat Proposal
				for ($s = 0; $s < count($syarat_set); $s++)
				{
					if ($syarat_set[$s]->syarat == 'Proposal')
					{
						unset($syarat_set[$s]);
						break;
					}
				}
			}
		}
		$this->smarty->assign('syarat_set', $syarat_set);
		
		if ($this->input->method() == 'post')
		{
			$now = date('Y-m-d H:i:s');

			$this->set_form_validation();

			// Validasi syarat
			$syarat_has_error = FALSE;
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

					// Simpan informasi terupload, untuk pemrosesan setelah form_validation->run()
					$file_syarat = new stdClass();
					$file_syarat->syarat_id = $syarat->id;
					$file_syarat->nama_file = $data['file_name'];
					$file_syarat->nama_asli = $data['orig_name'];
					if ($syarat->file_proposal_id == null)
					{
						$file_syarat->created_at = $now;
					}
					else
					{
						$file_syarat->id = $syarat->file_proposal_id;
						$file_syarat->updated_at = $now;
					}
					$file_syarat_set[] = $file_syarat;
				}
				else
				{
					// Jika belum di pilih file nya
					if ($this->upload->display_errors('', '') == 'You did not select a file to upload.')
					{
						// Jika tidak wajib, OK
						if ( ! $syarat->is_wajib)
						{

						}
						// atau jika sudah upload, OK
						elseif ($syarat->file_proposal_id != NULL)
						{

						}
						else // jika belum, munculkan errornya
						{
							$syarat->upload_error_msg = $this->upload->display_errors('', '');
							$syarat_has_error = TRUE;
						}
					}
					else // untuk error yang bukan masalah belum dipilih file
					{
						$syarat->upload_error_msg = $this->upload->display_errors('', '');
						$syarat_has_error = TRUE;
					}
				}
			}

			if ($this->form_validation->run() && $syarat_has_error == FALSE)
			{
				$this->db->trans_begin();

				assign_to($proposal, $this->input->post(NULL, TRUE));
				$proposal->updated_at = $now;
				$this->proposal_model->update($proposal->id, $proposal);

				// Proses Anggota
				for ($i = 1; $i <= 5; $i++)
				{
					// Jika isian tidak kosong, insert / update
					if (trim($this->input->post('nim_anggota_'.$i)) != '' && trim($this->input->post('nama_anggota_'.$i)) != '')
					{
						// Jika belum ada
						if ( ! isset($proposal->anggota_proposal_set[$i - 1]))
						{
							$anggota = new stdClass();
							$anggota->proposal_id = $proposal->id;
							$anggota->no_urut = $i;
							$anggota->nim = trim($this->input->post('nim_anggota_'.$i));
							$anggota->nama = trim($this->input->post('nama_anggota_'.$i));
							$anggota->no_hp = trim($this->input->post('hp_anggota_'.$i));
							$anggota->created_at = $now;
							$this->db->insert('anggota_proposal', $anggota);

							$proposal->anggota_proposal_set[$i - 1] = $anggota;
						}
						else
						{
							$anggota = $proposal->anggota_proposal_set[$i - 1];
							$anggota->nim = trim($this->input->post('nim_anggota_'.$i));
							$anggota->nama = trim($this->input->post('nama_anggota_'.$i));
							$anggota->no_hp = trim($this->input->post('hp_anggota_'.$i));
							$anggota->updated_at = $now;

							// Tidak menggunakan relasi mahasiswa, mahasiswa_id dihapus
							unset($anggota->program_studi_id);
							unset($anggota->nama_program_studi);
							$this->db->update('anggota_proposal', $anggota, ['proposal_id' => $proposal->id, 'no_urut' => $i]);
						}
					}
				}

				// Proses file syarat
				foreach ($file_syarat_set as $file_syarat)
				{
					$file_syarat->proposal_id = $proposal->id;

					if ($file_syarat->id == NULL)
					{
						$this->db->insert('file_proposal', $file_syarat);
					}
					else
					{
						$this->db->update('file_proposal', $file_syarat, ['id' => $file_syarat->id], 1);
					}
				}

				// terjadi kegagalan insert data
				if ($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();

					$this->session->set_flashdata('result', array(
						'page_title' => 'Edit usulan untuk ikut Expo KMI',
						'message' => 'Edit berhasil !',
						'link_1' => anchor('expo', 'Kembali ke daftar expo', ['class' => 'alert-link'])
					));

					redirect(site_url('alert/success'));
					exit();
				}

				$this->db->trans_rollback();
			}

			for ($i = 1; $i <= 3; $i++)
			{
				if (form_error("nim_anggota_$i") || form_error("nama_anggota_$i") || form_error("hp_anggota_$i"))
				{
					$this->smarty->assign("error_anggota_$i", true);
				}
			}
		}
		
		// Iterasi tiap isian anggota
		for ($i = 1; $i <= 5; $i++)
		{
			// Jika kosong set null
			if ( ! isset($proposal->anggota_proposal_set[$i - 1]))
			{
				$proposal->anggota_proposal_set[$i - 1] = new stdClass();
				$proposal->anggota_proposal_set[$i - 1]->nim = NULL;
				$proposal->anggota_proposal_set[$i - 1]->nama = NULL;
				$proposal->anggota_proposal_set[$i - 1]->no_hp = NULL;
			}
		}
		
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('kegiatan_asal', $kegiatan_asal);
		
		$kategori_set = $this->db->get_where('kategori', ['program_id' => $this->session->program_id])->result();
		$this->smarty->assignForCombo('kategori_set', $kategori_set, 'id', 'nama_kategori');
		
		$this->smarty->display();
	}
	
	public function hapus($id)
	{
		// memastikan id sesuai dengan pt (menghindari hack)
		$proposal = $this->proposal_model->get_single($id, $this->session->perguruan_tinggi->id);
		
		if ($proposal != NULL)
		{
			$this->db->trans_begin();
			
			// delete file
			$this->file_proposal_model->delete_by_proposal($proposal->id);
			// delete anggota
			$this->anggota_proposal_model->delete_by_proposal($proposal->id);
			// delete proposal
			$this->proposal_model->delete($proposal->id, $this->session->perguruan_tinggi->id);
			
			$this->db->trans_commit();
			
			$this->session->set_flashdata('result', array(
				'page_title' => 'Daftar Usaha Expo KMI',
				'message' => 'Penghapusan data sudah berhasil !',
				'link_1' => '<a href="'.site_url('expo').'" class="alert-link">Kembali ke daftar Expo</a>'
			));

			redirect(site_url('alert/success'));
			
			exit();
		}
		
		redirect(site_url('expo'));
	}
}
