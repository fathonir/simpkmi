<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_user_add_fk_dosen extends CI_Migration
{
	function up()
	{
		echo "  > alter table user ... ";
		$this->dbforge->add_column('user', [
			'dosen_id INT after mahasiswa_id',
			'FOREIGN KEY fk_user_dosen (dosen_id) REFERENCES dosen (id)'
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table user ... ";
		$this->db->query('ALTER TABLE `user` DROP FOREIGN KEY fk_user_dosen');
		echo "OK\n";
	}
}
