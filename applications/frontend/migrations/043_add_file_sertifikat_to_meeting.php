<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Add_file_sertifikat_to_meeting extends CI_Migration
{
	function up()
	{
		echo "  > alter table meeting ... ";
		$this->db->query('alter table meeting add column file_sertifikat varchar(100) null after kuesioner_url');
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table meeting ... ";
		$this->dbforge->drop_column('meeting', 'file_sertifikat');
		echo "OK\n";
	}
}
