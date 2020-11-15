<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_anggota_add_kontak extends CI_Migration
{
	function up()
	{
		echo "  > alter table anggota_proposal ... ";
		$this->dbforge->add_column('anggota_proposal', [
			'no_hp VARCHAR(30) NULL after nama'
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table anggota_proposal ... ";
		$this->dbforge->drop_column('anggota_proposal', 'no_hp');
		echo "OK\n";
	}
}
