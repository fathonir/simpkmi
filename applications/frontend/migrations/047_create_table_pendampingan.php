<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_pendampingan extends CI_Migration
{
	function up()
	{
		echo "  > create table dosen_pendamping ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field([
			'kegiatan_id int NOT NULL',
			'perguruan_tinggi_id int NOT NULL',
			'dosen_id int NOT NULL',
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP',
			'FOREIGN KEY fk_dosen_pendamping_kegiatan (kegiatan_id) REFERENCES kegiatan (id)',
			'FOREIGN KEY fk_dosen_pendamping_perguruan_tinggi (perguruan_tinggi_id) REFERENCES perguruan_tinggi (id)',
			'FOREIGN KEY fk_dosen_pendamping_dosen (dosen_id) REFERENCES dosen (id)'
		]);
		$this->dbforge->create_table('dosen_pendamping');
		echo "OK\n";

		echo "  > create table tahapan_pendampingan ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field([
			'kegiatan_id int NOT NULL',
			'nama_tahapan TEXT NOT NULL',
			'tgl_awal_laporan DATETIME NOT NULL',
			'tgl_akhir_laporan DATETIME NOT NULL',
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP',
			'FOREIGN KEY fk_tahapan_pendampingan_kegiatan (kegiatan_id) REFERENCES kegiatan (id)',
		]);
		$this->dbforge->create_table('tahapan_pendampingan');
		echo "OK\n";

		echo "  > insert Tahapan Pendampingan Tahun 2020 ... ";
		$this->db->query(
			"insert into tahapan_pendampingan (kegiatan_id, nama_tahapan, tgl_awal_laporan, tgl_akhir_laporan) " .
			"values (14, 'Tahap 1', '2020-09-01 00:00:00', '2020-09-07 00:00:00'), " .
			"(14, 'Tahap 2', '2020-10-01 00:00:00', '2020-10-07 00:00:00'), " .
			"(14, 'Tahap 3', '2020-11-01 00:00:00', '2020-11-07 00:00:00')"
		);
		echo "OK\n";

		echo "  > create table laporan_pendampingan ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field([
			'dosen_pendamping_id INT NOT NULL',
			'tahapan_pendampingan_id INT NOT NULL',
			'proposal_id INT NOT NULL',
			'laporan TEXT NOT NULL',
			'attachment_nama_file TEXT',
			'attachment_nama_asli TEXT',
			'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP',
			'FOREIGN KEY fk_laporan_pendampingan_dosen_pendamping (dosen_pendamping_id) REFERENCES dosen_pendamping (id)',
			'FOREIGN KEY fk_laporan_pendampingan_tahapan_pendampingan (tahapan_pendampingan_id) REFERENCES tahapan_pendampingan (id)',
			'FOREIGN KEY fk_laporan_pendampingan_proposal (proposal_id) REFERENCES proposal (id)'
		]);
		$this->dbforge->create_table('laporan_pendampingan');
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > drop table laporan_pendampingan ... ";
		$this->dbforge->drop_table('laporan_pendampingan');
		echo "OK\n";

		echo "  > drop table dosen_pendamping ... ";
		$this->dbforge->drop_table('dosen_pendamping');
		echo "OK\n";

		echo "  > drop table tahapan_pendampingan ... ";
		$this->dbforge->drop_table('tahapan_pendampingan');
		echo "OK\n";
	}
}
