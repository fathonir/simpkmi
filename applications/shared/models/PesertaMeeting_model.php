<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db 
 * @property CI_Input $input
 * @property int $id
 * @property int $meeting_id
 * @property int $mahasiswa_id
 * @property int $kehadiran
 * @property int $is_terpilih_meeting
 * 
 */
class PesertaMeeting_model extends CI_Model
{
	public function list_all_by_meeting($meeting_id)
	{
		return $this->db
			->select('pm.id, pt.nama_pt, m.nim, m.nama, pm.kehadiran, u.username, u.password')
			->from('peserta_meeting pm')
			->join('mahasiswa m', 'm.id = pm.mahasiswa_id')
			->join('perguruan_tinggi pt', 'pt.id = m.perguruan_tinggi_id')
			->join('user u', 'u.mahasiswa_id = m.id', 'LEFT')
			->where('pm.meeting_id', $meeting_id)
			->get()->result();
	}
	
	/**
	 * @param int $meeting_id
	 * @param int $mahasiswa_id
	 * @return PesertaMeeting_model
	 */
	public function get_single($meeting_id, $mahasiswa_id)
	{
		return $this->db->get_where('peserta_meeting', [
			'meeting_id' => $meeting_id,
			'mahasiswa_id' => $mahasiswa_id
		], 1)->row();
	}
	
	/**
	 * @param PesertaMeeting_model $model
	 */
	public function update($model)
	{
		return $this->db->update('peserta_meeting', $model, ['id' => $model->id]);
	}
}
