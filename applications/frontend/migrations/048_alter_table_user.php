<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_user extends CI_Migration
{
	function up()
	{
		echo "  > alter table user ... ";
		$this->dbforge->add_column('user', [
			'is_sent BOOL NOT NULL DEFAULT 0 AFTER status'
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table user ... ";
		$this->dbforge->drop_column('user', 'is_sent');
		echo "OK\n";
	}
}
