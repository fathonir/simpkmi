<?php
use GuzzleHttp\Client;

/**
 * @author Fathoni
 * @property Program_studi_model $program_studi_model
 * @property Mahasiswa_model $mahasiswa_model
 * @property Dosen_model $dosen_model
 * @property Kegiatan_model $kegiatan_model
 * @property Tahapan_model $tahapan_model
 * @property Proposal_model $proposal_model
 * @property Anggota_proposal_model $anggota_model
 * @property Syarat_model $syarat_model
 * @property GuzzleHttp\Client $client
 * @property CI_Form_validation $form_validation
 */
class Startup extends Mahasiswa_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->check_credentials();
		
		$this->load->model(MODEL_PROGRAM_STUDI, 'program_studi_model');
		$this->load->model(MODEL_MAHASISWA, 'mahasiswa_model');
		$this->load->model(MODEL_DOSEN, 'dosen_model');
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_TAHAPAN, 'tahapan_model');
		$this->load->model(MODEL_PROPOSAL, 'proposal_model');
		$this->load->model(MODEL_ANGGOTA_PROPOSAL, 'anggota_model');
		$this->load->model(MODEL_SYARAT, 'syarat_model');
	}

	/**
	 * redirect /startup -> /update/[proposal_id] secara otomatis
	 */
	function index()
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_STARTUP);
		$proposal = $this->proposal_model->get_by_ketua($kegiatan->id, $this->session->user->mahasiswa_id);
		redirect('startup/update/' . $proposal->id);
	}

	function trim_number($number)
	{
		return str_replace('.', '', $number);
	}

	/**
	 * @param $proposal_id
	 * @param int $tahapan_id
	 */
	function upload_syarat($proposal_id, $tahapan_id = TAHAPAN_EVALUASI)
	{
		$proposal	= $this->proposal_model->get_single($proposal_id, $this->session->user->perguruan_tinggi_id);
		$syarat_set = $this->syarat_model->list_by_kegiatan($proposal->kegiatan_id, $tahapan_id, $proposal->id);
		$kegiatan   = $this->kegiatan_model->get_single($proposal->kegiatan_id);
		
		if ($this->input->method() == 'post')
		{
			$this->load->library('upload');
			$this->load->library('form_validation');
			
			$error_count = 0;

			// Pada tahapan monev, ada input penggunaan anggaran
			if ($tahapan_id == TAHAPAN_MONEV)
			{
				$this->form_validation->set_rules('dana_dipakai_t1', 'Penggunaan Anggaran',
					'required|callback_trim_number|less_than_equal_to[25000000]');

				if ($this->form_validation->run() == FALSE)
				{
					$error_count++;
				}
				else
				{
					$proposal->dana_dipakai_t1 = str_replace('.', '', trim($this->input->post('dana_dipakai_t1')));
					$this->proposal_model->update_dana_dipakai($proposal);
				}
			}
			
			foreach ($syarat_set as &$syarat)
			{
				$file_row_exist = $this->db->where(array(
					'proposal_id' => $proposal->id,
					'syarat_id' => $syarat->id
				))->count_all_results('file_proposal') > 0;
				
				// Hanya di proses ketika tidak kosong
				if ($syarat->is_upload && isset($_FILES['file_syarat_' . $syarat->id]))
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
					
						if ($file_row_exist)
						{
							$this->db->update('file_proposal', array(
								'nama_asli' => $data['orig_name'],
								'nama_file' => $data['file_name'],
								'updated_at' => date('Y-m-d H:i:s')
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
							
							$syarat->file_proposal_id = $this->db->insert_id();
						}
					
						$syarat->nama_asli = $data['orig_name'];
						$syarat->nama_file = $data['file_name'];
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
							$error_count++;
						}
					}
				}
				else if ($this->input->post('file_syarat_' . $syarat->id) != '') // Link bukan upload & hanya diproses saat tidak kosong
				{
					$this->client = new \GuzzleHttp\Client();
					
					try
					{
						$response = $this->client->head($this->input->post('file_syarat_' . $syarat->id), [
							'verify' => FCPATH . 'cacert.pem'
						]);

						// Hanya disimpan saat link valid
						if ($response->getStatusCode() == 200)
						{
							if ($file_row_exist)
							{
								$this->db->update('file_proposal', array(
									'nama_file' => $this->input->post('file_syarat_' . $syarat->id),
									'nama_asli' => '',
									'updated_at' => date('Y-m-d H:i:s')
								), array('proposal_id' => $proposal->id, 'syarat_id' => $syarat->id));
							}
							else // Insert
							{
								$this->db->insert('file_proposal', array(
									'proposal_id' => $proposal->id,
									'nama_file' => $this->input->post('file_syarat_' . $syarat->id),
									'nama_asli' => '',
									'syarat_id' => $syarat->id
								));
								
								$syarat->file_proposal_id = $this->db->insert_id();
							}
							
							$syarat->nama_asli = '';
							$syarat->nama_file = $this->input->post('file_syarat_' . $syarat->id);
						}
						else
						{
							$syarat->upload_error_msg = "Link tidak valid";
							$error_count++;
						}
					}
					catch (Exception $ex)
					{
						$syarat->upload_error_msg = $ex->getMessage();
						$error_count++;
					}
				}
			}
			
			if ($error_count == 0)
			{
				redirect("startup/upload-syarat/{$proposal_id}/{$tahapan_id}");
				exit();
			}
		}

		$this->smarty->assign('tahapan_id', $tahapan_id);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('kegiatan', $kegiatan);
		$this->smarty->assign('syarat_set', $syarat_set);

		// Jadwal
		$now = date('Y-m-d H:i:s');
		$this->smarty->assign('is_jadwal_upload_usulan',
			$kegiatan->tgl_awal_upload < $now && $now < $kegiatan->tgl_akhir_upload);
		$this->smarty->assign('is_jadwal_upload_kemajuan',
			$kegiatan->tgl_awal_upload_kemajuan < $now && $now < $kegiatan->tgl_akhir_upload_kemajuan);
		
		$this->smarty->display();
	}
	
	function submit($proposal_id)
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_STARTUP);
		
		$proposal = $this->proposal_model->get_single($proposal_id, $this->session->user->perguruan_tinggi_id);
		$syarat_set = $this->syarat_model->list_by_kegiatan($kegiatan->id, $proposal->id);
		
		if ($this->input->method() == 'post')
		{
			// Check syarat terpenuhi semua
			$syarat_ok = TRUE;
			
			foreach ($syarat_set as $syarat)
			{
				if ($syarat->is_wajib && $syarat->file_proposal_id == '')
				{
					$syarat_ok = FALSE;
					break;
				}
			}
			
			if ( ! $syarat_ok)
			{
				$this->smarty->assign('error_msg', 'File yang diperlukan belum lengkap. Silahkan lengkapi terlebih dahulu.');
			}
			else
			{
				$this->proposal_model->submit($proposal->id);
				$proposal->is_submited = TRUE;
				$this->smarty->assign('success_msg', 'Submit berhasil.');
			}
		}
		
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('syarat_set', $syarat_set);
		
		$this->smarty->display();
	}

	function pitchdeck_2($proposal_id)
	{
		$proposal = $this->proposal_model->get_single($proposal_id, $this->session->user->perguruan_tinggi_id);
		$kegiatan = $this->kegiatan_model->get_single($proposal->kegiatan_id);

		// Pengecekan proposal lolos tahap 2
		if ( ! $this->proposal_model->is_lolos_tahapan($proposal->id, TAHAPAN_EVALUASI_TAHAP_2))
		{
			redirect('home');
			exit();
		}

		$nama_syarat_pitchdeck_2 = 'Pitchdeck Tahap 2';
		$syarat = $this->syarat_model->get_by_nama($kegiatan->id, $nama_syarat_pitchdeck_2, $proposal->id);

		// Saat Upload
		if ($this->input->method() == 'post')
		{
			$this->load->library('upload');

			$file_row_exist = $this->db->where(array(
					'proposal_id' => $proposal->id,
					'syarat_id' => $syarat->id
				))->count_all_results('file_proposal') > 0;

			// Hanya di proses ketika tidak kosong
			if (isset($_FILES['file_syarat_' . $syarat->id]))
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

					if ($file_row_exist)
					{
						$this->db->update('file_proposal', array(
							'nama_asli' => $data['orig_name'],
							'nama_file' => $data['file_name'],
							'updated_at' => date('Y-m-d H:i:s')
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

						$syarat->file_proposal_id = $this->db->insert_id();
					}

					$syarat->nama_asli = $data['orig_name'];
					$syarat->nama_file = $data['file_name'];
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
		}

		$this->smarty->assign('syarat', $syarat);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->display();
	}
}
