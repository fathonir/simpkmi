<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Add_program_startup_meetup extends CI_Migration
{
	function up()
	{
		echo "  > insert program Startup Meetup ... ";
		$this->db->insert('program', [
			'id' => PROGRAM_STARTUP_MEETUP, 
			'nama_program' => 'Startup Meetup', 
			'nama_program_singkat' => 'SU Meetup'
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > remove program Startup Meetup ... ";
		// Peserta Workshop
		$this->db->where_in('lokasi_workshop_id', 'select id from lokasi_workshop where kegiatan_id in (select id from kegiatan where program_id = '.PROGRAM_STARTUP_MEETUP.')', FALSE);
		$this->db->delete('peserta_workshop');
		// Lokasi Workshop
		$this->db->where_in('kegiatan_id', 'select id from kegiatan where program_id = '.PROGRAM_STARTUP_MEETUP, FALSE);
		$this->db->delete('lokasi_workshop');
		// Transaksi Kegiatan
		$this->db->delete('kegiatan', ['program_id' => PROGRAM_STARTUP_MEETUP]);
		// Program
		$this->db->delete('program', ['id' => PROGRAM_STARTUP_MEETUP]);
		echo "OK\n";
	}
}
