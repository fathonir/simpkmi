<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Alter_table_skor extends CI_Migration
{
	function up()
	{
		echo "  > alter table skor ... ";
		$this->db->query('alter table skor add column is_aktif bool not null default \'1\' after keterangan');
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table skor ... ";
		$this->dbforge->drop_column('skor', 'is_aktif');
		echo "OK\n";
	}
}
