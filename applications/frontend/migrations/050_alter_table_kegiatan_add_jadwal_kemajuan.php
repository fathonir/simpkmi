<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Alter_table_kegiatan_add_jadwal_kemajuan extends CI_Migration
{
	function up()
	{
		echo "  > alter table kegiatan ... ";
		$this->dbforge->add_column('kegiatan', [
			'tgl_awal_upload_kemajuan DATETIME AFTER tgl_pengumuman',
			'tgl_akhir_upload_kemajuan DATETIME AFTER tgl_awal_upload_kemajuan',
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table kegiatan ... ";
		$this->dbforge->drop_column('kegiatan', 'tgl_awal_upload_kemajuan');
		$this->dbforge->drop_column('kegiatan', 'tgl_akhir_upload_kemajuan');
		echo "OK\n";
	}
}
