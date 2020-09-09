<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Add_tahapan_startup_summit extends CI_Migration
{
	function up()
	{
		echo "  > insert tahapan Startup Summit ... ";
		$this->db->insert('tahapan', [
			'id' => TAHAPAN_STARTUP_SUMMIT, 
			'tahapan' => 'Startup Summit',
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > remove tahapan Startup Summit ... ";
		// Tahapan
		$this->db->delete('tahapan', ['id' => TAHAPAN_STARTUP_SUMMIT]);
		echo "OK\n";
	}
}
