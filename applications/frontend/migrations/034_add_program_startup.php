<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Add_program_startup extends CI_Migration
{
	function up()
	{
		echo "  > insert program Akselerator Startup ... ";
		$this->db->insert('program', [
			'id' => PROGRAM_STARTUP, 
			'nama_program' => 'Akselerasi Startup Mahasiswa Indonesia', 
			'nama_program_singkat' => 'Startup'
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > remove program Akselerator Startup ... ";
		// Transaksi Anggota Proposal
		$this->db->where_in('proposal_id', 'select id from proposal where kegiatan_id in (select id from kegiatan where program_id = '.PROGRAM_STARTUP.')', FALSE);
		$this->db->delete('anggota_proposal');
		// Transaksi Proposal
		$this->db->where_in('kegiatan_id', 'select id from kegiatan where program_id = '.PROGRAM_STARTUP, FALSE);
		$this->db->delete('proposal');
		// Master Syarat
		$this->db->where_in('kegiatan_id', 'select id from kegiatan where program_id = '.PROGRAM_STARTUP, FALSE);
		$this->db->delete('syarat');
		// Transaksi User dan user request
		$this->db->delete('request_user', ['program_id' => PROGRAM_STARTUP]);
		$this->db->delete('user', ['program_id' => PROGRAM_STARTUP]);
		// Transaksi Kegiatan
		$this->db->delete('kegiatan', ['program_id' => PROGRAM_STARTUP]);
		// Program
		$this->db->delete('program', ['id' => PROGRAM_STARTUP]);
		echo "OK\n";
	}
}
