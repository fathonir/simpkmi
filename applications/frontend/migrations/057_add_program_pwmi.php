<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Add_program_pwmi extends CI_Migration
{
	function up()
	{
		echo "  > insert program PWMI ... ";
		$this->db->insert('program', [
			'id' => PROGRAM_PWMI,
			'nama_program' => 'Pendampingan Wirausaha Mahasiswa Indonesia',
			'nama_program_singkat' => 'PWMI'
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > remove program PWMI ... ";
		$this->db->delete('program', ['id' => PROGRAM_PWMI]);
		echo "OK\n";
	}
}
