<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Alter_table_peserta_meeting extends CI_Migration
{
	function up()
	{
		echo "  > alter table peserta_meeting ... ";
		$this->db->query('alter table peserta_meeting change kehadiran_1 kehadiran int(1) not null default 0');
		$this->dbforge->drop_column('peserta_meeting', 'kehadiran_2');
		$this->db->query('alter table peserta_meeting add column is_terpilih_meeting int(1) null after kehadiran');
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table peserta_meeting ... ";
		$this->db->query('alter table peserta_meeting change kehadiran kehadiran_1 int(1) not null default 0');
		$this->db->query('alter table peserta_meeting add column kehadiran_2 int(1) not null default 0 after kehadiran_1');
		$this->dbforge->drop_column('peserta_meeting', 'is_terpilih_meeting');
		echo "OK\n";
	}
}
