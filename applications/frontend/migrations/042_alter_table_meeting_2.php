<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Alter_table_meeting_2 extends CI_Migration
{
	function up()
	{
		echo "  > alter table meeting ... ";
		$this->db->query('alter table meeting add column kuesioner_url varchar(500) null after tgl_akhir_registrasi');
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table meeting ... ";
		$this->dbforge->drop_column('meeting', 'kuesioner_url');
		echo "OK\n";
	}
}
