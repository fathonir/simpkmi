<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_proposal_add_kehadiran_rekening extends CI_Migration
{
	function up()
	{
		echo "  > alter table proposal ... ";
		$this->dbforge->add_column('proposal', [
			'is_hadir_offline INT(1) NULL AFTER kelas_presentasi_id',
			'rekening VARCHAR(20) NULL AFTER is_hadir_offline'
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table proposal ... ";
		$this->dbforge->drop_column('proposal', 'is_hadir_offline');
		$this->dbforge->drop_column('proposal', 'rekening');
		echo "OK\n";
	}
}
