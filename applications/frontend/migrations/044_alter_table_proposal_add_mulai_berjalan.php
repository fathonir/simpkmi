<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_proposal_add_mulai_berjalan extends CI_Migration
{
	function up()
	{
		echo "  > alter table proposal ... ";
		$this->dbforge->add_column('proposal', [
			'mulai_berjalan' => ['type' => 'varchar(100)', 'after' => 'lama_kegiatan_bln']
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table proposal ... ";
		$this->dbforge->drop_column('proposal', 'mulai_berjalan');
		echo "OK\n";
	}
}
