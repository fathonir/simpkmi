<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder $db 
 * @property CI_Input $input
 * @property Program_model $program
 * @property int $id 
 * @property int $program_id
 * @property int $tahun
 * @property int $proposal_per_pt
 * @property int $peserta_per_pt
 * @property int $is_aktif
 * @property string $tgl_awal_upload
 * @property string $tgl_akhir_upload
 * @property string $tgl_awal_upload_kemajuan
 * @property string $tgl_akhir_upload_kemajuan
 */
class Kegiatan_model extends CI_Model
{	
	public function add()
	{
		$post = $this->input->post();
		
		$this->db->trans_start();
		
		// Create object kegiatan
		$kegiatan = new stdClass();
		$kegiatan->program_id		= $this->input->post('program_id');
		$kegiatan->tahun			= $this->input->post('tahun');
		$kegiatan->proposal_per_pt	= $this->input->post('proposal_per_pt');
		$kegiatan->peserta_per_pt	= $this->input->post('peserta_per_pt');
		$kegiatan->tgl_awal_upload	= "{$post['awal_upload_Year']}-{$post['awal_upload_Month']}-{$post['awal_upload_Day']} {$post['awal_upload_HMS']}";
		$kegiatan->tgl_akhir_upload	= "{$post['akhir_upload_Year']}-{$post['akhir_upload_Month']}-{$post['akhir_upload_Day']} {$post['akhir_upload_HMS']}";
		$kegiatan->tgl_awal_review	= "{$post['awal_review_Year']}-{$post['awal_review_Month']}-{$post['awal_review_Day']} {$post['awal_review_HMS']}";
		$kegiatan->tgl_akhir_review	= "{$post['akhir_review_Year']}-{$post['akhir_review_Month']}-{$post['akhir_review_Day']} {$post['akhir_review_HMS']}";
		$kegiatan->tgl_pengumuman	= "{$post['pengumuman_Year']}-{$post['pengumuman_Month']}-{$post['pengumuman_Day']} {$post['pengumuman_HMS']}";
		$kegiatan->tgl_awal_upload_kemajuan =
			"{$post['awal_upload_kemajuan_Year']}-{$post['awal_upload_kemajuan_Month']}-" .
			"{$post['awal_upload_kemajuan_Day']} {$post['awal_upload_kemajuan_HMS']}";
		$kegiatan->tgl_akhir_upload_kemajuan =
			"{$post['akhir_upload_kemajuan_Year']}-{$post['akhir_upload_kemajuan_Month']}-" .
			"{$post['akhir_upload_kemajuan_Day']} {$post['akhir_upload_kemajuan_HMS']}";

		// insert ke database
		$this->db->insert('kegiatan', $kegiatan);
		
		return $this->db->trans_complete();
	}
	
	public function update($id)
	{
		$post = $this->input->post();
		
		$this->db->trans_start();
		
		// Create object kegiatan untuk update
		$kegiatan = new stdClass();
		$kegiatan->proposal_per_pt	= $this->input->post('proposal_per_pt');
		$kegiatan->peserta_per_pt	= $this->input->post('peserta_per_pt');
		$kegiatan->tgl_awal_upload	= "{$post['awal_upload_Year']}-{$post['awal_upload_Month']}-{$post['awal_upload_Day']} {$post['awal_upload_HMS']}";
		$kegiatan->tgl_akhir_upload	= "{$post['akhir_upload_Year']}-{$post['akhir_upload_Month']}-{$post['akhir_upload_Day']} {$post['akhir_upload_HMS']}";
		$kegiatan->tgl_awal_review	= "{$post['awal_review_Year']}-{$post['awal_review_Month']}-{$post['awal_review_Day']} {$post['awal_review_HMS']}";
		$kegiatan->tgl_akhir_review	= "{$post['akhir_review_Year']}-{$post['akhir_review_Month']}-{$post['akhir_review_Day']} {$post['akhir_review_HMS']}";
		$kegiatan->tgl_pengumuman	= "{$post['pengumuman_Year']}-{$post['pengumuman_Month']}-{$post['pengumuman_Day']} {$post['pengumuman_HMS']}";
		$kegiatan->tgl_awal_upload_kemajuan =
			"{$post['awal_upload_kemajuan_Year']}-{$post['awal_upload_kemajuan_Month']}-" .
			"{$post['awal_upload_kemajuan_Day']} {$post['awal_upload_kemajuan_HMS']}";
		$kegiatan->tgl_akhir_upload_kemajuan =
			"{$post['akhir_upload_kemajuan_Year']}-{$post['akhir_upload_kemajuan_Month']}-" .
			"{$post['akhir_upload_kemajuan_Day']} {$post['akhir_upload_kemajuan_HMS']}";
		$kegiatan->is_aktif			= $this->input->post('is_aktif');
		
		$this->db->update('kegiatan', $kegiatan, ['id' => $id]);
		
		// jika sedang mengaktifkan kegiatan, kegiatan yg lain utk program yg sama perlu dimatikan juga
		if ($kegiatan->is_aktif == '1')
		{
			// ambil id program
			$old_kegiatan = $this->db->select('program_id')->get_where('kegiatan', ['id' => $id], 1)->row();
			
			// nonaktifkan program yg sama selain yg akan di update
			$this->db->update('kegiatan', ['is_aktif' => 0], ['id <>' => $id, 'program_id' => $old_kegiatan->program_id]);
		}
		
		return $this->db->trans_complete();
	}
	
	public function list_all($program_id = 'all')
	{
		$this->db
			->select('kegiatan.*, program.nama_program')
			->from('kegiatan')
			->join('program', 'program.id = kegiatan.program_id')
			->order_by('tahun DESC, tgl_awal_upload DESC');
		
		if ($program_id !== 'all')
		{
			$this->db->where(['program.id' => $program_id]);
		}
		
		return $this->db->get()->result();
	}
	
	public function list_workshop()
	{
		return $this->db
			->select('kegiatan.id, program.nama_program, kegiatan.tahun')
			->from('kegiatan')
			->join('program', 'program.id = kegiatan.program_id')
			->where(['program_id' => PROGRAM_WORKSHOP])
			->or_where(['program_id' => PROGRAM_STARTUP_MEETUP])
			->get()->result();
	}
	
	public function list_workshop_aktif()
	{
		return $this->db
			->select('kegiatan.id, program.nama_program, kegiatan.tahun')
			->from('kegiatan')
			->join('program', 'program.id = kegiatan.program_id')
			->where(['program_id' => PROGRAM_WORKSHOP, 'is_aktif' => 1])
			->get()->result();
	}
	
	public function list_online_workshop()
	{
		return $this->db
			->select('kegiatan.id, program.nama_program, kegiatan.tahun')
			->from('kegiatan')
			->join('program', 'program.id = kegiatan.program_id')
			->where(['program_id' => PROGRAM_ONLINE_WORKSHOP])
			->get()->result();
	}
	
	public function list_aktif_for_option($program_id = 0)
	{
		if ($program_id != 0)
		{
			$this->db->where('p.id', $program_id);
		}
		
		$kegiatan_set = $this->db
			->select('k.id, concat(p.nama_program, \' Tahun \', k.tahun) nama_program')
			->from('kegiatan k')->join('program p', 'p.id = k.program_id')
			->where('k.is_aktif', 1)->get()->result_array();
		return array_column($kegiatan_set, 'nama_program', 'id');
	}
	
	/**
	 * @param int $id
	 * @return Kegiatan_model
	 */
	public function get_single($id)
	{
		return $this->db
			->select('kegiatan.*, program.nama_program')
			->from('kegiatan')
			->join('program', 'program.id = kegiatan.program_id')
			->where('kegiatan.id', $id)
			->get()
			->row();
	}
	
	/**
	 * @param int $program_id
	 * @return Kegiatan_model
	 */
	public function get_aktif($program_id)
	{
		return $this->db->get_where('kegiatan', [
			'program_id' => $program_id, 
			'is_aktif' => 1
		], 1)->row();
	}
	
	/**
	 * @param int $program_id
	 * @param int $tahun
	 * @return Kegiatan_model
	 * @deprecated Gunakan get_by_tahun()
	 */
	public function get_by_program($program_id, $tahun)
	{
		return $this->get_by_tahun($program_id, $tahun);
	}

	/**
	 * @param int $program_id
	 * @param int $tahun
	 * @return Kegiatan_model
	 */
	public function get_by_tahun($program_id, $tahun)
	{
		return $this->db->get_where('kegiatan', [
			'program_id'	=> $program_id,
			'tahun'			=> $tahun
		], 1)->row();
	}
}
