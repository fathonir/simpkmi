<?php

/**
 * Class Migration_Create_table_kelompok_skor
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_skala_skor extends CI_Migration
{
	function up()
	{
		echo "  > create table skala_skor ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field([
			'skala_skor VARCHAR(20) NOT NULL',
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
		]);
		$this->dbforge->create_table('skala_skor');
		echo "OK\n";

		echo "  > insert skala_skor data ... ";
		$this->db->insert_batch('skala_skor',[
			['id' => SKALA_SKOR_1_7, 'skala_skor' => '1 s/d 7'],
			['id' => SKALA_SKOR_1_5, 'skala_skor' => '1 s/d 5'],
			['id' => SKALA_SKOR_1_3, 'skala_skor' => '1 s/d 3']
		]);
		echo "OK\n";

		echo "  > alter table skor ... ";
		$this->dbforge->add_column('skor', [
			'skala_skor_id INT AFTER id'
		]);
		$this->dbforge->modify_column('skor', ['keterangan VARCHAR(30) NULL']);
		echo "OK\n";

		echo "  > update skor + insert skor 1-3  ... ";
		$this->db->update('skor', ['skala_skor_id' => SKALA_SKOR_1_7], ['id >=' => 1, 'id <=' => 6]);
		$this->db->update('skor', ['skala_skor_id' => SKALA_SKOR_1_5], ['id >=' => 7, 'id <=' => 10]);
		$this->db->insert_batch('skor', [
			['id' => 11, 'skala_skor_id' => SKALA_SKOR_1_3, 'skor' => 1, 'keterangan' => 'Tidak ada progress',
				'is_aktif' => 0],
			['id' => 12, 'skala_skor_id' => SKALA_SKOR_1_3, 'skor' => 2, 'keterangan' => 'Ada progress',
				'is_aktif' => 0],
			['id' => 13, 'skala_skor_id' => SKALA_SKOR_1_3, 'skor' => 3, 'keterangan' => 'Progress melampaui target',
				'is_aktif' => 0],
		]);
		echo "OK\n";

		echo "  > alter table skor ... ";
		$this->dbforge->add_column('skor', [
			'FOREIGN KEY fk_skor_skala_skor (skala_skor_id) REFERENCES skala_skor (id)'
		]);
		echo "OK\n";
	}

	function down()
	{
		echo "  > remove skor 1-3 ... ";
		$this->db->delete('skor', ['id' => 11]);
		$this->db->delete('skor', ['id' => 12]);
		$this->db->delete('skor', ['id' => 13]);
		echo "OK\n";

		echo "  > rollback table skor ... ";
		$this->db->query('alter table skor drop foreign key fk_skor_skala_skor');
		$this->dbforge->drop_column('skor', 'skala_skor_id');
		echo "OK\n";

		echo "  > drop table skala_skor ... ";
		$this->dbforge->drop_table('skala_skor');
		echo "OK\n";
	}
}
