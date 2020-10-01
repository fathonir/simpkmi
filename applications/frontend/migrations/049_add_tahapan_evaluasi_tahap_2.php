<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Add_tahapan_evaluasi_tahap_2 extends CI_Migration
{
	function up()
	{
		echo "  > insert tahapan Evaluasi Tahap 2 ... ";
		$this->db->insert('tahapan', [
			'id' => TAHAPAN_EVALUASI_TAHAP_2,
			'tahapan' => 'Evaluasi Tahap 2',
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > remove tahapan Evaluasi Tahap 2 ... ";
		// Tahapan
		$this->db->delete('tahapan', ['id' => TAHAPAN_EVALUASI_TAHAP_2]);
		echo "OK\n";
	}
}
