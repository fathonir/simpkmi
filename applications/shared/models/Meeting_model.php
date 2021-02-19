<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db
 * @property int $id
 * @property int $kegiatan_id
 * @property string $topik
 * @property string $pemateri
 * @property string $meeting_url
 * @property string $meeting_password
 * @property string $youtube_url
 * @property string $waktu_mulai
 * @property int $kapasitas
 * @property string $kode_kehadiran
 * @property string $batas_presensi
 * @property string $tgl_awal_registrasi
 * @property string $tgl_akhir_registrasi
 * @property string $kuesioner_url
 * @property string $file_sertifikat
 * @property string $created_at
 * @property string $updated_at
 */
class Meeting_model extends CI_Model
{

	public function list_all($kegiatan_id)
	{
		return $this->db
			->order_by('waktu_mulai ASC')
			->get_where('meeting', ['kegiatan_id' => $kegiatan_id])->result();
	}

	/**
	 * Mendapatkan list meeting / online workshop berdasarkan mahasiswa
	 * @param int $kegiatan_id
	 * @param int $mahasiswa_id
	 */
	public function list_with_mahasiswa($kegiatan_id, $mahasiswa_id = null)
	{
		return $this->db
			->select('meeting.*')
			->select('pm.mahasiswa_id')
			->from('meeting')
			->join('peserta_meeting pm', 'pm.meeting_id = meeting.id and pm.mahasiswa_id = '.$mahasiswa_id, 'left')
			->where('meeting.kegiatan_id', $kegiatan_id)
			->order_by('waktu_mulai ASC')
			->get()->result();
	}
	
	public function list_by_mahasiswa($mahasiswa_id)
	{
		return $this->db
			->select('meeting.*, pm.kehadiran, pm.is_terpilih_meeting')
			->from('meeting')
			->join('peserta_meeting pm', 'pm.meeting_id = meeting.id')
			->where('pm.mahasiswa_id', $mahasiswa_id)
			->get()->result();
	}

	public function add()
	{
		$post = $this->input->post();

		$meeting = new stdClass();
		$meeting->kegiatan_id = $post['kegiatan_id'];
		$meeting->topik = $post['topik'];
		$meeting->pemateri = $post['pemateri'];
		$meeting->waktu_mulai = "{$post['waktu_mulai_Year']}-{$post['waktu_mulai_Month']}-{$post['waktu_mulai_Day']} {$post['waktu_mulai_time']}";
		$meeting->meeting_url = $post['meeting_url'];
		$meeting->meeting_password = $post['meeting_password'];
		$meeting->tgl_awal_registrasi = "{$post['awal_registrasi_Year']}-{$post['awal_registrasi_Month']}-{$post['awal_registrasi_Day']} {$post['awal_registrasi_time']}";
		$meeting->tgl_akhir_registrasi = "{$post['akhir_registrasi_Year']}-{$post['akhir_registrasi_Month']}-{$post['akhir_registrasi_Day']} {$post['akhir_registrasi_time']}";
		$meeting->kode_kehadiran = $post['kode_kehadiran'];
		$meeting->kapasitas = $post['kapasitas'];
		
		return $this->db->insert('meeting', $meeting);
	}

	/**
	 * @param int $id
	 * @return Meeting_model
	 */
	public function get_single($id)
	{
		return $this->db->get_where('meeting', ['id' => $id], 1)->row();
	}

	public function update($id)
	{
		$post = $this->input->post();

		$meeting = $this->get_single($id);
		$meeting->topik = $post['topik'];
		$meeting->pemateri = $post['pemateri'];
		$meeting->waktu_mulai = "{$post['waktu_mulai_Year']}-{$post['waktu_mulai_Month']}-{$post['waktu_mulai_Day']} {$post['waktu_mulai_time']}";
		$meeting->meeting_url = $post['meeting_url'];
		$meeting->meeting_password = $post['meeting_password'];
		$meeting->youtube_url = $post['youtube_url'];
		$meeting->tgl_awal_registrasi = "{$post['awal_registrasi_Year']}-{$post['awal_registrasi_Month']}-{$post['awal_registrasi_Day']} {$post['awal_registrasi_time']}";
		$meeting->tgl_akhir_registrasi = "{$post['akhir_registrasi_Year']}-{$post['akhir_registrasi_Month']}-{$post['akhir_registrasi_Day']} {$post['akhir_registrasi_time']}";
		$meeting->kode_kehadiran = $post['kode_kehadiran'];
		if (checkdate($post['batas_presensi_Month'], $post['batas_presensi_Day'], $post['batas_presensi_Year']))
			$meeting->batas_presensi = "{$post['batas_presensi_Year']}-{$post['batas_presensi_Month']}-{$post['batas_presensi_Day']} {$post['batas_presensi_time']}";
		$meeting->kapasitas = $post['kapasitas'];
		$meeting->kuesioner_url = $post['kuesioner_url'];

		return $this->db->update('meeting', $meeting, ['id' => $id]);
	}

	public function add_peserta($meeting_id, $mahasiswa_id)
	{
		return $this->db->insert('peserta_meeting', [
			'meeting_id' => $meeting_id,
			'mahasiswa_id' => $mahasiswa_id
		]);
	}

	function is_peserta_exist($meeting_id, $mahasiswa_id)
	{
		return ($this->db
			->where('meeting_id', $meeting_id)
			->where('mahasiswa_id', $mahasiswa_id)
			->get('peserta_meeting')
			->row() != null);
	}

}
