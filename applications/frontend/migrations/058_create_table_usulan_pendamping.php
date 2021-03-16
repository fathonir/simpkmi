<?php

/**
 * Class Migration_Create_table_kelompok_skor
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_usulan_pendamping extends CI_Migration
{
	function up()
	{
		echo "  > create table usulan_pendamping ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field([
			'kegiatan_id INT NOT NULL',
			'perguruan_tinggi_id INT NOT NULL',
			'dosen_id INT NOT NULL',
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP',
			'FOREIGN KEY fk_usulan_pendamping_kegiatan (kegiatan_id) REFERENCES kegiatan (id)',
			'FOREIGN KEY fk_usulan_pendamping_pt (perguruan_tinggi_id) REFERENCES perguruan_tinggi (id)',
			'FOREIGN KEY fk_usulan_pendamping_dosen (dosen_id) REFERENCES dosen (id)'
		]);
		$this->dbforge->create_table('usulan_pendamping');
		echo "OK\n";

		echo "  > create table file_usulan_pendamping ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field([
			'usulan_pendamping_id INT NOT NULL',
			'syarat_id INT NOT NULL',
			'nama_file VARCHAR(250) NOT NULL',
			'nama_asli VARCHAR(250) NOT NULL',
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP',
			'FOREIGN KEY fk_file_usulan_pendamping (usulan_pendamping_id) REFERENCES usulan_pendamping (id)',
			'FOREIGN KEY fk_file_usulan_pendamping_syarat (syarat_id) REFERENCES syarat (id)',
		]);
		$this->dbforge->create_table('file_usulan_pendamping');
		echo "OK\n";
	}

	function down()
	{
		echo "  > drop table file_usulan_pendamping ... ";
		$this->dbforge->drop_table('file_usulan_pendamping');
		echo "OK\n";

		echo "  > drop table usulan_pendamping ... ";
		$this->dbforge->drop_table('usulan_pendamping');
		echo "OK\n";
	}
}
