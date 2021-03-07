<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property Kegiatan_model $kegiatan_model 
 * @property Meeting_model $meeting_model
 * @property PesertaMeeting_model $pmeeting_model
 */
class Online_workshop extends Mahasiswa_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->check_credentials();
		$this->load->model(MODEL_KEGIATAN, 'kegiatan_model');
		$this->load->model(MODEL_MEETING, 'meeting_model');
		$this->load->model(MODEL_PESERTA_MEETING, 'pmeeting_model');
	}
	
	public function index()
	{
		$kegiatan = $this->kegiatan_model->get_aktif(PROGRAM_ONLINE_WORKSHOP);
		$mahasiswa = $this->session->user->mahasiswa;
		$meeting_set = $this->meeting_model->list_with_mahasiswa($kegiatan->id, $mahasiswa->id);

		$this->smarty->assign('kegiatan', $kegiatan);
		$this->smarty->assign('meeting_set', $meeting_set);
		$this->smarty->display();
	}
	
	public function register($meeting_id)
	{
		$meeting = $this->meeting_model->get_single($meeting_id);
		$mahasiswa = $this->session->user->mahasiswa;
		
		if (!$this->meeting_model->is_peserta_exist($meeting->id, $mahasiswa->id))
		{
			$this->meeting_model->add_peserta($meeting->id, $mahasiswa->id);
		}
		
		$kegiatan = $this->kegiatan_model->get_single($meeting->kegiatan_id);
		
		$this->session->set_flashdata('result', [
			'page_title' => "Program Online Workshop <small>Tahun {$kegiatan->tahun}</small>",
			'message' => 'Pendaftaran workshop berhasil. Pastikan mengingat jadwal.',
			'link_1' => anchor('home', 'Kembali ke Beranda'),
			'link_2' => anchor('online-workshop', 'Kembali ke Daftar Online Workshop')
		]);
		
		redirect('alert/success');
	}
	
	public function presensi()
	{
		$meeting = $this->meeting_model->get_single($this->input->post('meeting_id'));
		$mahasiswa = $this->session->user->mahasiswa;
		
		if (date('Y-m-d H:i:s') < $meeting->batas_presensi)
		{
			if (trim($this->input->post('kode_kehadiran')) == $meeting->kode_kehadiran)
			{
				$peserta_meeting = $this->pmeeting_model->get_single($meeting->id, $mahasiswa->id);
				$peserta_meeting->kehadiran = 1;
				$this->pmeeting_model->update($peserta_meeting);
				
				$link_kuesioner = anchor(
					$meeting->kuesioner_url, 
					'Link Google Form Ini <span class="glyphicon glyphicon-new-window"></span>', 
					['target' => '_blank']);
				
				$this->session->set_flashdata('result', [
					'page_title' => "Online Workshop Peningkatan dan Pengembangan Kewirausahaan",
					'message' => 'Selamat Anda berhasil mengisi absensi kehadiran.<br/>'
						. "Mohon isi kuesioner di {$link_kuesioner}",
					'link_1' => anchor('home', 'Kembali ke Beranda'),
				]);

				redirect('alert/success');
			}
			else
			{
				$this->session->set_flashdata('result', [
					'page_title' => "Online Workshop Peningkatan dan Pengembangan Kewirausahaan",
					'message' => 'Mohon maaf, kode presensi tidak sesuai. Silahkan ulangi',
					'link_1' => anchor('home', 'Kembali ke Beranda'),
				]);

				redirect('alert/error');
			}
		}
		else
		{
			$this->session->set_flashdata('result', [
				'page_title' => "Online Workshop Peningkatan dan Pengembangan Kewirausahaan",
				'message' => 'Mohon maaf, waktu presensi sudah selesai',
				'link_1' => anchor('home', 'Kembali ke Beranda'),
			]);

			redirect('alert/error');
		}
	}
	
	public function cetak_sertifikat()
	{
		$meeting_id = $this->input->get('meeting_id');
		$meeting = $this->meeting_model->get_single($meeting_id);
		$kegiatan = $this->kegiatan_model->get_single($meeting->kegiatan_id);
		
		$background_path = FCPATH . 'upload' . 
			DIRECTORY_SEPARATOR . 'sertifikat' . 
			DIRECTORY_SEPARATOR . $meeting->file_sertifikat;		
		
		$this->mpdf->AddPage('L');
		$this->mpdf->Image($background_path, 0, 0, 297, 210, '', '', true, false);

		if ($kegiatan->tahun == 2020)
		{
			$this->mpdf->SetFont('FreeSerif');
			$this->mpdf->SetFontSize(22);
			$this->mpdf->SetY(90);
			$this->mpdf->WriteCell(267, 15, ucwords(strtolower($this->session->user->mahasiswa->nama)), 0, 0, 'C');
		}
		elseif ($kegiatan->tahun == 2021)
		{
			$this->mpdf->SetFont('FreeSerif');
			$this->mpdf->SetFontSize(22);
			$this->mpdf->SetY(75);
			$this->mpdf->WriteCell(267, 15, ucwords(strtolower($this->session->user->mahasiswa->nama)), 0, 0, 'C');
		}

		$this->mpdf->Output('Sertifikat ', 'I');
	}
}
