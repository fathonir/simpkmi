<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_proposal_add_pendanaan extends CI_Migration
{
	function up()
	{
		echo "  > alter table proposal ... ";
		$this->dbforge->add_column('proposal', [
			'dana_disetujui INT AFTER is_didanai',
			'dana_dipakai_t1 INT AFTER dana_disetujui',
			'dana_dipakai_t2 INT AFTER dana_dipakai_t1',
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table proposal ... ";
		$this->dbforge->drop_column('proposal', 'dana_disetujui');
		$this->dbforge->drop_column('proposal', 'dana_dipakai_t1');
		$this->dbforge->drop_column('proposal', 'dana_dipakai_t2');
		echo "OK\n";
	}
}
