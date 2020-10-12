<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Add_komponen_penilaian_monev_kbmi_2020 extends CI_Migration
{
	function up()
	{
		echo "  > insert komponen_penilaian KBMI tahun 2020 ... ";
		$kegiatan_kbmi_2020 = $this->db->get_where('kegiatan', [
			'tahun' => 2020,
			'program_id' => PROGRAM_KBMI
		])->first_row();

		$this->db->insert_batch('komponen_penilaian', [
			['urutan' => 1, 'kegiatan_id' => $kegiatan_kbmi_2020->id, 'tahapan_id' => TAHAPAN_MONEV,
				'kriteria' => 'Produk', 'bobot' => 1],
			['urutan' => 2, 'kegiatan_id' => $kegiatan_kbmi_2020->id, 'tahapan_id' => TAHAPAN_MONEV,
				'kriteria' => 'Pelanggan', 'bobot' => 1],
			['urutan' => 3, 'kegiatan_id' => $kegiatan_kbmi_2020->id, 'tahapan_id' => TAHAPAN_MONEV,
				'kriteria' => 'Pemasaran', 'bobot' => 1],
			['urutan' => 4, 'kegiatan_id' => $kegiatan_kbmi_2020->id, 'tahapan_id' => TAHAPAN_MONEV,
				'kriteria' => 'Operasional', 'bobot' => 1],
			['urutan' => 5, 'kegiatan_id' => $kegiatan_kbmi_2020->id, 'tahapan_id' => TAHAPAN_MONEV,
				'kriteria' => 'Keuangan (Cash Flow)', 'bobot' => 1],
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > remove tahapan Evaluasi Tahap 2 ... ";
		$kegiatan_kbmi_2020 = $this->db->get_where('kegiatan', [
			'tahun' => 2020,
			'program_id' => PROGRAM_KBMI
		])->first_row();
		$this->db->delete('komponen_penilaian', [
			'kegiatan_id' => $kegiatan_kbmi_2020->id,
			'tahapan_id' => TAHAPAN_MONEV
		]);
		echo "OK\n";
	}
}
