<?php

/**
 * Class Pwmi
 * @property Usulan_pendamping_model $usulan_pendamping_model
 * @property Dosen_model $dosen_model
 * @property Program_studi_model $program_studi_model
 * @property Syarat_model $syarat_model
 */
class Pwmi extends Frontend_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->check_credentials();
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_USULAN_PENDAMPING, 'usulan_pendamping_model');
		$this->load->model(MODEL_DOSEN, 'dosen_model');
		$this->load->model(MODEL_PROGRAM_STUDI, 'program_studi_model');
		$this->load->model(MODEL_SYARAT, 'syarat_model');
	}

	/**
	 * @param Kegiatan_model $kegiatan
	 * @return bool
	 */
	private function is_in_jadwal($kegiatan = null)
	{
		if ($kegiatan == null)
		{
			return false;
		}

		$now = date('Y-m-d H:i:s');
		return $kegiatan->tgl_awal_upload < $now && $now < $kegiatan->tgl_akhir_upload;
	}

	public function index()
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_PWMI);
		$this->smarty->assign('kegiatan', $kegiatan);
		$this->smarty->assign('is_in_jadwal', $this->is_in_jadwal($kegiatan));

		$data_set = $this->usulan_pendamping_model->list_all($this->session->user->perguruan_tinggi_id);
		$this->smarty->assign('data_set', $data_set);

		$this->smarty->display();
	}

	public function add()
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_PWMI);

		// Cek Jadwal Exist
		if ($kegiatan == null)
		{
			$this->smarty->assign('message', "Tidak ada jadwal pengusulan PWMI yang aktif");
			$this->smarty->display('pwmi/alert.tpl');
			return;
		}

		// Cek Rentang Jadwal
		$now = date('Y-m-d H:i:s');
		if ($now < $kegiatan->tgl_awal_upload || $kegiatan->tgl_akhir_upload < $now)
		{
			$this->smarty->assign('message', "Diluar jadwal pengusulan PWMI");
			$this->smarty->display('pwmi/alert.tpl');
			return;
		}

		// Cek Kuota
		$count = $this->usulan_pendamping_model->count_usulan($kegiatan->tahun, $this->session->perguruan_tinggi->id);
		if ($count == $kegiatan->peserta_per_pt)
		{
			$this->smarty->assign('message', "Tidak bisa menambah usulan dosen PWMI lagi");
			$this->smarty->display('pwmi/alert.tpl');
			return;
		}

		if ($this->input->method() == 'post')
		{
			$usulan_pendamping = new Usulan_pendamping_model();
			$usulan_pendamping->kegiatan_id = $kegiatan->id;
			$usulan_pendamping->perguruan_tinggi_id = $this->session->user->perguruan_tinggi_id;
			$usulan_pendamping->dosen_id = $this->input->post('dosen_id');
			$result = $this->usulan_pendamping_model->add($usulan_pendamping);

			if ($result)
			{
				$this->session->set_flashdata('result', [
					'page_title' => 'Daftar Dosen PWMI',
					'message' => 'Usulan dosen pendamping berhasil ditambahkan',
					'link_1' => anchor(site_url('pwmi'), 'Kembali')
				]);
				redirect('alert/success');
				exit();
			}
			else
			{
				$this->session->set_flashdata('result', [
					'page_title' => 'Daftar Dosen PWMI',
					'message' => 'Usulan dosen pendamping gagal ditambahkan',
					'link_1' => anchor(site_url('pwmi'), 'Kembali')
				]);
				redirect('alert/error');
				exit();
			}
		}

		$program_studi_set = $this->program_studi_model->list_by_pt($this->session->perguruan_tinggi->npsn);
		$this->smarty->assign('program_studi_set', $program_studi_set);

		$this->smarty->display();
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
		catch (Exception $e)
		{
			echo json_encode(['result' => false, 'message' => $e->getMessage()]);
			exit();
		}

		echo json_encode(['result' => false, 'message' => 'Dosen tidak ditemukan']);
	}

	/**
	 * @param int $id Usulan Pendamping
	 */
	public function syarat($id)
	{
		$usulan_pendamping = $this->usulan_pendamping_model->get_single($id, $this->session->user->perguruan_tinggi_id);
		$kegiatan = $this->kegiatan_model->get_single($usulan_pendamping->kegiatan_id);
		$this->smarty->assign('is_in_jadwal', $this->is_in_jadwal($kegiatan));

		$syarat_set = $this->syarat_model->list_by_kegiatan_pwmi($kegiatan->id, $usulan_pendamping->id);

		if ($this->input->method() == 'post')
		{
			$this->load->library('upload');

			// create folder upload
			if ( ! file_exists(FCPATH.'upload/lampiran-usulan-pendamping/'))
			{
				mkdir(FCPATH.'upload/lampiran-usulan-pendamping/');
			}

			$uploaded_count = 0;

			foreach ($syarat_set as $syarat)
			{
				$this->upload->initialize([
					'encrypt_name'	=> TRUE,
					'upload_path'	=> FCPATH.'upload/lampiran-usulan-pendamping/',
					'allowed_types'	=> explode(',', $syarat->allowed_types),
					'max_size'		=> (int)$syarat->max_size * 1024
				]);

				if ($this->upload->do_upload('file_syarat_' . $syarat->id))
				{
					$data = $this->upload->data();

					$file_row_count = $this->db->where([
						'usulan_pendamping_id' => $usulan_pendamping->id,
						'syarat_id' => $syarat->id
					])->count_all_results('file_usulan_pendamping');

					// if file record exist : update
					if ($file_row_count > 0)
					{
						$this->db->update('file_usulan_pendamping', [
							'nama_asli' => $data['orig_name'],
							'nama_file' => $data['file_name']
						], [
							'usulan_pendamping_id' => $usulan_pendamping->id,
							'syarat_id' => $syarat->id
						]);

						$syarat->nama_file = $data['file_name'];
					}
					else // insert
					{
						$this->db->insert('file_usulan_pendamping', [
							'usulan_pendamping_id' => $usulan_pendamping->id,
							'nama_asli' => $data['orig_name'],
							'nama_file' => $data['file_name'],
							'syarat_id' => $syarat->id
						]);

						// Get insert id
						$syarat->file_usulan_pendamping_id = $this->db->insert_id();
						$syarat->nama_file = $data['file_name'];
					}

					$syarat->upload_success = true;
				}
				else
				{
					if ($syarat->file_usulan_pendamping_id == null)
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
			}
		}

		$this->smarty->assign('syarat_set', $syarat_set);
		$this->smarty->display();
	}

	public function delete($id)
	{
		$usulan_pendamping = $this->usulan_pendamping_model->get_single($id, $this->session->user->perguruan_tinggi_id);
		$dosen = $this->dosen_model->get($usulan_pendamping->dosen_id);
		$this->smarty->assign('dosen', $dosen);

		$prodi = $this->program_studi_model->get($dosen->program_studi_id);
		$this->smarty->assign('prodi', $prodi);

		if ($this->input->method() == 'post')
		{
			$this->usulan_pendamping_model->delete($usulan_pendamping);

			$this->session->set_flashdata('result', [
				'page_title' => 'Daftar Dosen PWMI',
				'message' => 'Usulan dosen pendamping berhasil dihapus',
				'link_1' => anchor(site_url('pwmi'), 'Kembali')
			]);
			redirect('alert/success');
			exit();
		}

		$this->smarty->display();
	}
}
