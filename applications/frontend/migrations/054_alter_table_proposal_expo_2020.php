<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_proposal_expo_2020 extends CI_Migration
{
	function up()
	{
		echo "  > alter table proposal ... ";
		$this->dbforge->add_column('proposal', [
			'email VARCHAR(256) NULL COMMENT \'Email resmi usaha\' after kategori_id',
			'headline TEXT NULL COMMENT \'Headline / tagline usaha\' after email',
			'deskripsi TEXT NULL COMMENT \'Deskripsi usaha singkat\' after headline',
			'link_web VARCHAR(256) NULL after deskripsi',
			'link_instagram VARCHAR(256) NULL after link_web',
			'link_twitter VARCHAR(256) NULL after link_instagram',
			'link_youtube VARCHAR(256) NULL after link_twitter',
			'proposal_id_asal INT NULL COMMENT \'Asal proposal dari kegiatan sebelumnya\' after keterangan_ditolak',
			'kegiatan_id_asal INT NULL COMMENT \'Kegiatan asal proposal sebelumnya\' after proposal_id_asal',
			'FOREIGN KEY fk_proposal_proposal_asal (proposal_id_asal) REFERENCES proposal (id)',
			'FOREIGN KEY fk_proposal_kegiatan_asal (kegiatan_id_asal) REFERENCES kegiatan (id)',
		]);
		echo "OK\n";

		echo "  > tambah kategori: Digital ... ";
		$this->db->insert('kategori', ['program_id' => PROGRAM_EXPO, 'nama_kategori' => 'Digital']);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table proposal ... ";
		$this->dbforge->drop_column('proposal', 'email');
		$this->dbforge->drop_column('proposal', 'headline');
		$this->dbforge->drop_column('proposal', 'deskripsi');
		$this->dbforge->drop_column('proposal', 'link_web');
		$this->dbforge->drop_column('proposal', 'link_instagram');
		$this->dbforge->drop_column('proposal', 'link_twitter');
		$this->dbforge->drop_column('proposal', 'link_youtube');
		$this->db->query("alter table proposal drop foreign key fk_proposal_proposal_asal");
		$this->dbforge->drop_column('proposal', 'proposal_id_asal');
		$this->db->query("alter table proposal drop foreign key fk_proposal_kegiatan_asal");
		$this->dbforge->drop_column('proposal', 'kegiatan_id_asal');
		echo "OK\n";

		echo "  > hapus kategori: Digital ...";
		$this->db->delete('kategori', ['program_id' => PROGRAM_EXPO, 'nama_kategori' => 'Digital']);
		echo "OK\n";
	}
}
