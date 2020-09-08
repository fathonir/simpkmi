<?php

/**
 * Class Pwmi
 * @property Kegiatan_model $kegiatan_model
 * @property Proposal_model $proposal_model
 * @property Anggota_proposal_model $anggota_model
 * @property Mahasiswa_model $mahasiswa_model
 * @property LaporanPendampingan_model $lap_pendampingan_model
 * @property Dosen_Pendamping_model $dosen_pendamping_model
 */
class Pwmi extends Dosen_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->check_credentials();
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_PROPOSAL, 'proposal_model');
		$this->load->model(MODEL_ANGGOTA_PROPOSAL, 'anggota_model');
		$this->load->model(MODEL_MAHASISWA, 'mahasiswa_model');
		$this->load->model(MODEL_LAPORAN_PENDAMPINGAN, 'lap_pendampingan_model');
		$this->load->model(MODEL_DOSEN_PENDAMPING, 'dosen_pendamping_model');
	}

	public function view($proposal_id)
	{
		$dosen = $this->session->user->dosen;
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);
		$proposal = $this->proposal_model->get_single($proposal_id);
		$ketua = $this->mahasiswa_model->get($this->anggota_model->get_ketua($proposal->id)->mahasiswa_id);
		$lap_pendampingan_set = $this->lap_pendampingan_model->list_by_proposal($proposal_id, $kegiatan->id, $dosen->id);

		$this->smarty->assign('kegiatan', $kegiatan);
		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('ketua', $ketua);
		$this->smarty->assign('lap_pendampingan_set', $lap_pendampingan_set);
		$this->smarty->assign('now', time());
		$this->smarty->display();
	}

	public function update($proposal_id, $tahapan_pendampingan_id)
	{
		$dosen = $this->session->user->dosen;
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_KBMI);
		$dosen_pendamping = $this->dosen_pendamping_model->get_from_dosen($dosen->id, $kegiatan->id);
		$proposal = $this->proposal_model->get_single($proposal_id);
		$lap_pendampingan = $this->lap_pendampingan_model->get_single($dosen->id, $proposal_id, $tahapan_pendampingan_id);

		if ($this->input->method() == 'post')
		{
			$folder_target = FCPATH.'upload/laporan-pendampingan/';
			if ( ! file_exists($folder_target))
			{
				mkdir($folder_target);
			}

			$this->load->library('upload');
			$this->upload->initialize([
				'encrypt_name'	=> TRUE,
				'upload_path'	=> $folder_target,
				'allowed_types'	=> 'pdf',
				'max_size'		=> 5 * 1024
			]);

			if ($this->upload->do_upload('file'))
			{
				$data = $this->upload->data();
				$upload_berhasil = true;
				$attachment_nama_asli = $data['orig_name'];
				$attachment_nama_file = $data['file_name'];
			}
			else
			{
				$upload_berhasil = false;
				$attachment_nama_file = null;
				$attachment_nama_asli = null;

				$upload_error_msg = $this->upload->display_errors('', '');

				if ($upload_error_msg != 'You did not select a file to upload.')
				{
					if ($upload_error_msg == 'The filetype you are attempting to upload is not allowed.')
					{
						$this->session->set_flashdata('upload_error_msg', 'File yang boleh diupload hanya PDF');
					}
					else
					{
						$this->session->set_flashdata('upload_error_msg', $this->upload->display_errors());
					}

				}
			}

			if ($lap_pendampingan != null)
			{
				$lap_pendampingan->dosen_pendamping_id = $dosen_pendamping->id;
				$lap_pendampingan->tahapan_pendampingan_id = $tahapan_pendampingan_id;
				$lap_pendampingan->proposal_id = $proposal_id;
				$lap_pendampingan->laporan = $this->input->post('laporan');
				if ($upload_berhasil)
				{
					$lap_pendampingan->attachment_nama_file = $attachment_nama_file;
					$lap_pendampingan->attachment_nama_asli = $attachment_nama_asli;
				}
				$lap_pendampingan->updated_at = date('Y-m-d H:i:s');
				$result = $this->lap_pendampingan_model->update($lap_pendampingan);
			}
			else
			{
				$lap_pendampingan = new stdClass();
				$lap_pendampingan->dosen_pendamping_id = $dosen_pendamping->id;
				$lap_pendampingan->tahapan_pendampingan_id = $tahapan_pendampingan_id;
				$lap_pendampingan->proposal_id = $proposal_id;
				$lap_pendampingan->laporan = $this->input->post('laporan');
				if ($upload_berhasil)
				{
					$lap_pendampingan->attachment_nama_file = $attachment_nama_file;
					$lap_pendampingan->attachment_nama_asli = $attachment_nama_asli;
				}
				$lap_pendampingan->created_at = date('Y-m-d H:i:s');
				$result = $this->lap_pendampingan_model->add($lap_pendampingan);
			}

			$this->session->set_flashdata('success', $result);
			redirect('pwmi/update/' . $proposal_id . '/' . $tahapan_pendampingan_id);
			exit();
		}

		$this->smarty->assign('proposal', $proposal);
		$this->smarty->assign('lap_pendampingan', $lap_pendampingan);
		$this->smarty->display();
	}

	public function hapus_attachment($proposal_id, $tahapan_pendampingan_id)
	{
		$dosen = $this->session->user->dosen;
		$lap_pendampingan = $this->lap_pendampingan_model->get_single($dosen->id, $proposal_id, $tahapan_pendampingan_id);

		$lap_pendampingan->attachment_nama_file = null;
		$lap_pendampingan->attachment_nama_asli = null;
		$lap_pendampingan->updated_at = date('Y-m-d H:i:s');
		$result = $this->lap_pendampingan_model->update($lap_pendampingan);

		$this->session->set_flashdata('hapus_attachment_success', $result);
		redirect('pwmi/update/' . $proposal_id . '/' . $tahapan_pendampingan_id);
		exit();
	}
}
