<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_proposal_modify_ketua extends CI_Migration
{
	function up()
	{
		echo "  > alter table proposal ... ";
		$this->dbforge->modify_column('proposal', [
			'nim_ketua VARCHAR(20) NULL',
			'nama_ketua VARCHAR(100) NULL'
		]);
		echo "OK\n";

		echo "  > update data proposal yang nim + nama ketua kosong ... ";
		$this->db->update('proposal',
			['nim_ketua' => null, 'nama_ketua' => null],
			['nim_ketua' => '', 'nama_ketua' => '']);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table proposal ... ";
		$this->dbforge->modify_column('proposal', [
			'nim_ketua VARCHAR(20) NULL',
			'nama_ketua VARCHAR(100) NULL'
		]);
		echo "OK\n";
	}
}
