<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Alter_table_meeting extends CI_Migration
{
	function up()
	{
		echo "  > alter table meeting ... ";
		$this->db->query('alter table meeting change kode_kehadiran_1 kode_kehadiran varchar(5) null');
		$this->db->query('alter table meeting add column batas_presensi datetime null after kode_kehadiran');
		$this->dbforge->drop_column('meeting', 'kode_kehadiran_2');
		$this->db->query('alter table meeting add column youtube_url varchar(250) null after meeting_password');
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table meeting ... ";
		$this->dbforge->drop_column('meeting', 'youtube_url');
		$this->db->query('alter table meeting add column kode_kehadiran_2 varchar(5) null after kode_kehadiran');
		$this->dbforge->drop_column('meeting', 'batas_presensi');
		$this->db->query('alter table meeting change kode_kehadiran kode_kehadiran_1 varchar(5) null');
		echo "OK\n";
	}
}
