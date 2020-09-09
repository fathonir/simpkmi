<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Add_program_online_workshop extends CI_Migration
{
	function up()
	{
		echo "  > create table meeting ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field('kegiatan_id INT NOT NULL');
		$this->dbforge->add_field('topik TEXT NOT NULL');
		$this->dbforge->add_field('pemateri VARCHAR(100) NOT NULL');
		$this->dbforge->add_field('meeting_url VARCHAR(250) NOT NULL');
		$this->dbforge->add_field('meeting_password VARCHAR(20) NULL');
		$this->dbforge->add_field('waktu_mulai DATETIME NOT NULL');
		$this->dbforge->add_field('kapasitas INT NOT NULL DEFAULT \'0\'');
		$this->dbforge->add_field('kode_kehadiran_1 VARCHAR(5) NULL');
		$this->dbforge->add_field('kode_kehadiran_2 VARCHAR(5) NULL');
		$this->dbforge->add_field('tgl_awal_registrasi DATETIME NOT NULL');
		$this->dbforge->add_field('tgl_akhir_registrasi DATETIME NOT NULL');
		$this->dbforge->add_field('created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->dbforge->add_field('updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP');
		$this->dbforge->add_field('FOREIGN KEY fk_meeting_kegiatan (kegiatan_id) REFERENCES kegiatan (id)');
		$this->dbforge->create_table('meeting', TRUE);
		echo "OK\n";
		
		echo "  > create table peserta_meeting ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field('meeting_id INT NOT NULL');
		$this->dbforge->add_field('mahasiswa_id INT NOT NULL');
		$this->dbforge->add_field('kehadiran_1 BOOLEAN NOT NULL DEFAULT \'0\'');
		$this->dbforge->add_field('kehadiran_2 BOOLEAN NOT NULL DEFAULT \'0\'');
		$this->dbforge->add_field('created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->dbforge->add_field('updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP');
		$this->dbforge->create_table('peserta_meeting', TRUE);
		echo "OK\n";
		
		echo "  > alter table program ... ";
		$this->db->query("alter table program modify nama_program_singkat varchar(20)");
		echo "OK\n";
		
		echo "  > insert program Peningkatan dan Pengembangan Kewirausahaan (Online Workshop) ... ";
		$this->db->insert('program', [
			'id' => PROGRAM_ONLINE_WORKSHOP, 
			'nama_program' => 'Peningkatan dan Pengembangan Kewirausahaan (Online Workshop)', 
			'nama_program_singkat' => 'Online Workshop'
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > remove program Peningkatan dan Pengembangan Kewirausahaan (Online Workshop) ... ";
		// Transaksi peserta_meeting
		$this->db->where_in('meeting_id', 'select id from meeting where kegiatan_id in (select id from kegiatan where program_id = ' . PROGRAM_ONLINE_WORKSHOP . ')', FALSE);
		$this->db->delete('peserta_meeting');
		// Transaksi meeting
		$this->db->where_in('kegiatan_id', 'select id from kegiatan where program_id = ' . PROGRAM_ONLINE_WORKSHOP, FALSE);
		$this->db->delete('meeting');
		// Transaksi Kegiatan
		$this->db->delete('kegiatan', ['program_id' => PROGRAM_ONLINE_WORKSHOP]);
		// Program
		$this->db->delete('program', ['id' => PROGRAM_ONLINE_WORKSHOP]);
		echo "OK\n";
		
		echo "  > drop table peserta_meeting ... ";
		$this->dbforge->drop_table('peserta_meeting');
		echo "OK\n";
		
		echo "  > drop table meeting ... ";
		$this->dbforge->drop_table('meeting');
		echo "OK\n";
	}
}
