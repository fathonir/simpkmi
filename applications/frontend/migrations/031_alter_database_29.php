<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Alter_database_29 extends CI_Migration
{
	function up()
	{
		echo "  > alter table komponen_penilaian ... ";
		$this->db->query('alter table komponen_penilaian modify column kriteria text not null');
		echo "OK\n";
		
		echo "  > create table komponen_penilaian_isian ... ";
		$this->dbforge->add_field('id');
		$this->dbforge->add_field('komponen_penilaian_id INT NOT NULL');
		$this->dbforge->add_field('isian_ke INT NOT NULL');
		$this->dbforge->add_field('created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->dbforge->add_field('updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP');
		$this->dbforge->add_field('FOREIGN KEY fk_komp_pen_isi_komp_pen (komponen_penilaian_id) REFERENCES komponen_penilaian (id)');
		$this->dbforge->create_table('komponen_penilaian_isian', TRUE);
		echo "OK\n";
		
		echo "  > seed komponen penilaian ... ";
		// Ambil KBMI 2019
		$kegiatan = $this->db->get_where('kegiatan', ['tahun' => 2019, 'program_id' => PROGRAM_KBMI])->row();
		if ($kegiatan)
		{
			$this->db->insert('komponen_penilaian', [
				'id' => 29,
				'kegiatan_id' => $kegiatan->id, 'tahapan_id' => TAHAPAN_EVALUASI,
				'urutan' => 1, 'bobot' => 10.0,
				'kriteria' => <<<EOT
					<p>Noble Purpose, Topik Bisnis dan target specific customer</p>
					<ul>
						<li>Hal mulia apa yang tim Anda ingin wujudkan dalam membangun bisnis?</li>
						<li>Apa atau siapa yang menjadi pemicu hal mulia yang ingin diwujudkan tersebut?</li>
						<li>Bisnis atau topik bisnis apa yang Anda ajukan</li>
					</ul>
				
					<small class="text-info"><strong>Referensi:</strong><br/>
						Keterkaitan kuat antara noble purpose dengan topik bisnis yang dipilih, dan ketepatan maupun keunikan dalam
						memilih target spesifik market yang disasar. Melampirkan data-data pendukung fakta dari topik bisnis yang dipilih.<br/>
						Lampiran berupa noble purpose setiap anggota team dan kesimpulannya menjadi noble purpose kelompok/team,
						lampiran berupa analisa tarikan pasar topik bisnis yang dipilih dengan korelasi kuat dengan noble purpose team.
						Data tenting topik bisnis berdasarkan data-data terpercaya yang menguatkan yang menunjukkan permintaan pasar yang
						sedang trend saat ini.</small>
EOT
			]);
			
			$this->db->insert_batch('komponen_penilaian_isian', [
				['komponen_penilaian_id' => 29, 'isian_ke' => 1],
				['komponen_penilaian_id' => 29, 'isian_ke' => 2],
				['komponen_penilaian_id' => 29, 'isian_ke' => 3],
			]);
			
			$this->db->insert('komponen_penilaian', [
				'id' => 30,
				'kegiatan_id' => $kegiatan->id, 'tahapan_id' => TAHAPAN_EVALUASI,
				'urutan' => 2, 'bobot' => 10.0,
				'kriteria' => <<<EOT
					<p>Strategi manajemen SDM</p>
					<ul>
						<li>Siapa saja anggota tim terbaik yang akan Anda libatkan dalam bisnis, dan apa keahlian masing-masing?</li>
						<li>Apa saja tanggung jawab masing-masing tim Anda tersebut?</li>
						<li>Apa indikator keberhasilan dari tanggung jawab masing-masing tim Anda tersebut?</li>
						<li>Jika Anda harus bermitra dalam menyediakan produk/jasa Anda, pihak mana yang akan Anda ajak kerja sama?</li>
					</ul>
					<p><small class="text-info"><strong>Referensi:</strong><br/>Keterkaitan kuat antara topik bisnis dengan keahlian team dan
						ketepatan dalam membuat indikator keberhasilan. Pemilihan partner bisnis untuk menguatkan dan membuat efisien biaya produksi/marketing</small>
					</p>
				
					<ul><li>LAPORAN RISET PASAR (berupa riset tarikan pasar, analisa calon partner maupun analisa kompetitor) <strong>Cek Lampiran</strong></li></ul>
					<p><small class="text-info"><strong>Referensi:</strong><br/>
						Lampiran : Profil masing-masing anggota team dengan segala keahlian maupun pengalaman yang dimiliki. Lampiran indikator
						keberhasilan yang mempunyai relasi kuat dengan keberhasilan bisnisnya. Lampiran analisa partner bisnis yang akan atau
						sudah dipilih yang membawa dampak efisiensi proses marketing maupun inovasi bisnisnya.</small>
					</p>
EOT
			]);
			
			$this->db->insert_batch('komponen_penilaian_isian', [
				['komponen_penilaian_id' => 30, 'isian_ke' => 25],
				['komponen_penilaian_id' => 30, 'isian_ke' => 26],
				['komponen_penilaian_id' => 30, 'isian_ke' => 27],
				['komponen_penilaian_id' => 30, 'isian_ke' => 29],
			]);
			
			$this->db->insert('komponen_penilaian', [
				'id' => 31,
				'kegiatan_id' => $kegiatan->id, 'tahapan_id' => TAHAPAN_EVALUASI,
				'urutan' => 3, 'bobot' => 20.0,
				'kriteria' => <<<EOT
					
					<p>Menjelaskan kedalaman pemahaman pebisnis atas permasalahan yang dirasakan oleh pelaggan (DESIRABILITY)</p>
					<ul>
						<li>Segmen spesifik pelanggan mana yang akan Anda sasar?</li>
						<li>Coba Anda amati dan tanyakan kepada calon pelanggan yang Anda sasar. Aktifitas apa saja yang perlu
							mereka lakukan untuk mendapatkan produk/jasa yang menjadi konteks bisnis Anda?</li>
						<li>Kesulitan apa saja yang benar-benar dirasakan oleh calon pelanggan Anda, terkait dengan hal-hal yang perlu dilakukan
							untuk mendapatkan produk/jasa yang menjadi konteks bisnis Anda?</li>
						<li>Jika kesulitan-kesulitan tersebut dapat terselesaikan, harapan apa saja yang ingin diwujudkan oleh calon pelanggan Anda?</li>
						<li>Dari semua kesulitan dan harapan calon pelanggan anda, produk/layanan anda akan menyelesaikan kesulitan
							dan memenuhi harapan yang mana?</li>
					</ul>
					<p><small class="text-info"><strong>Referensi:</strong><br/>
						Pemilihan permasalahan ataupun harapan yang dialami oleh customer berdasarkan hasil riset interview kepada 30 target 
						customer dan validasi kepada 100 target customer via google form. Analisa permasalahan ataupun harapan yang diplih
						memiliki keunikan dan mempunyai peluang besar untuk menjadi trend.</small></p>
					
					<ul<li>LAMPIRAN RISET PASAR (riset berupa data NTD, pain, gain customer) <strong>Cek Lampiran</strong></li></ul>
					<p><small class="text-info"><strong>Referensi:</strong><br/>
						Lampiran hasil riset kepada target customernya. Hasil interview kepada minimal 30 target customer untuk target publik
						dan 10 untuk target institusi/lembaga, ditemukan pola pain dan gain yang kuat kemudian divalidasi dengan survey online
						tentang pain gain tersebut kepada 100 target customernya untuk akhirnya menjadi pertimbangan masalah atau harapan
						mana dari target customernya yang akan diselesaikan oleh bisnisnya.</small></p>
EOT
			]);
			
			$this->db->insert_batch('komponen_penilaian_isian', [
				['komponen_penilaian_id' => 31, 'isian_ke' => 6],
				['komponen_penilaian_id' => 31, 'isian_ke' => 9],
				['komponen_penilaian_id' => 31, 'isian_ke' => 10],
				['komponen_penilaian_id' => 31, 'isian_ke' => 11],
				['komponen_penilaian_id' => 31, 'isian_ke' => 12],
			]);
			
			$this->db->insert('komponen_penilaian', [
				'id' => 32,
				'kegiatan_id' => $kegiatan->id, 'tahapan_id' => TAHAPAN_EVALUASI,
				'urutan' => 4, 'bobot' => 20.0,
				'kriteria' => <<<EOT
					<p>Menjelaskan produk yang kompetitif dan mampu menyelesaikan masalah pelanggan. (FEASIBILITY)</p>
					<ul>
						<li>Produk/jasa apa yang Anda tawarkan kepada calon pelanggan Anda?</li>
						<li>Referensi produk/layanan apa saja atau hasil riset maupun jurnal dari pakar siapa yang Anda jadikan pertimbangan 
							untuk membuat produk/layanan Anda?</li>
						<li>Bagaimana produk/jasa Anda tersebut bekerja menyelesaikan masalah dan memenuhi keinginan pelanggan yang Anda sasar?</li>
						
						<li>Menurut Anda, siapa saja yang akan menjadi kompetitor dalam menyediakan produk/jasa tersebut?</li>
						<li>Apa saja keunggulan produk/jasa yang disediakan oleh kompetitor Anda?</li>
						<li>Lalu, hal apa saja yang menjadi keunggulan kompetitif produk/jasa Anda dibandingkan dengan produk/jasa kompetitor?</li>
						
					</ul>
					<p><small class="text-info"><strong>Referensi:</strong><br/>
						Produk/jasa yang mempunyai ketepatan solusi. Memilih fitur maupun business model yang berbeda dan kompetitif dengan
						competitor yang ada. Riset mendalam terkait kompetitor dan referensi lengkap ide2 yang sudah pernah ada. Disertai lampiran
						hasil riset tentang referensi maupun data kompetitor</small></p>
				
					<ul>
						<li>LAPORAN RISET PASAR (riset berupa data SWOT terhadap kompetitor dan pasar yang ada) <strong>Cek Lampiran</strong></li>
					</ul>
					<p><small class="text-info"><strong>Referensi:</strong><br/>
						Lampiran hasil riset yang mendalam tentang data2 kompetitor maupun produk/jasa substitusi. lampiran berupa hasil mentoring
						dan coaching kepada pakar2 di bidangnya. lampiran berupa jurnal atau artikel dari sumber yang terpercaya.
						<strong>LAMPIRAN PENTING</strong> : prototype atau video kehandalan produk/jasa yang dihasilkan, termasuk hasil test
						kepada target customernya, bagi yang punya produkt berupa testimoni di akun social medianya.</small></p>
EOT
			]);
			
			$this->db->insert_batch('komponen_penilaian_isian', [
				['komponen_penilaian_id' => 32, 'isian_ke' => 13],
				['komponen_penilaian_id' => 32, 'isian_ke' => 14],
				['komponen_penilaian_id' => 32, 'isian_ke' => 15],
				['komponen_penilaian_id' => 32, 'isian_ke' => 16],
				['komponen_penilaian_id' => 32, 'isian_ke' => 17],
				['komponen_penilaian_id' => 32, 'isian_ke' => 18],
			]);
			
			$this->db->insert('komponen_penilaian', [
				'id' => 33,
				'kegiatan_id' => $kegiatan->id, 'tahapan_id' => TAHAPAN_EVALUASI,
				'urutan' => 5, 'bobot' => 20.0,
				'kriteria' => <<<EOT
				<p>Strategi pemasaran kepada customer (delivery)</p>
				<ul>
					<li>Segmen spesifik pelanggan mana yang akan Anda sasar?</li>
					<li>Area mana yang akan menjadi target ideal jangkauan bisnis Anda?</li>
					<li>Dalam 4 bulan pertama bisnis Anda berjalan, daerah mana yang akan menjadi awal target pasar Anda?</li>
					<li>Bagaimana strategi Anda untuk membuat calon pelanggan mengetahui produk/jasa yang Anda sediakan?</li>
					<li>Bagaimana strategi Anda untuk membuat calon pelanggan tertarik dan akhirnya memutuskan membeli produk/jasa yang Anda sediakan?</li>
					<li>Bagaimana caranya anda merespon pelanggan yang bertanya, membeli dan komplain terhadap layanan anda?</li>
					<li>Strategi apa yang akan Anda lakukan untuk menjadikan pelanggan Anda loyal?</li>
					<li>Dimana calon pelanggan dapat memperoleh produk/jasa Anda?</li>
				</ul>
				<p><small class="text-info"><strong>Referensi:</strong><br/>
					Strategi pemasaran yang tepat dimulai dari memasarkan di pasar yg tepat dalam scope kecil dan target customer yang berpengaruh
					hingga rencana jangka panjang yang jelas dan terukur. Menggunakan pendekatan personal secara offline maupun menggunakan strategi
					digital yang tepat. Alasan yang sangat kuat kenapa team itu memilih daerah tersebut sebagai awal “pertempuran” bisnisnya.
					Kombinasi yang sangat kuat antara strategi direct selling / personal approach dengan digital marketing.</small></p>
EOT
			]);
			
			$this->db->insert_batch('komponen_penilaian_isian', [
				['komponen_penilaian_id' => 33, 'isian_ke' => 6],
				['komponen_penilaian_id' => 33, 'isian_ke' => 7],
				['komponen_penilaian_id' => 33, 'isian_ke' => 8],
				['komponen_penilaian_id' => 33, 'isian_ke' => 20],
				['komponen_penilaian_id' => 33, 'isian_ke' => 21],
				['komponen_penilaian_id' => 33, 'isian_ke' => 22],
				['komponen_penilaian_id' => 33, 'isian_ke' => 23],
				['komponen_penilaian_id' => 33, 'isian_ke' => 24],
			]);
			
			$this->db->insert('komponen_penilaian', [
				'id' => 34,
				'kegiatan_id' => $kegiatan->id, 'tahapan_id' => TAHAPAN_EVALUASI,
				'urutan' => 6, 'bobot' => 20.0,
				'kriteria' => <<<EOT
				<p>Finansial (Strategi keuangan)</p>
				<ul>
					<li>Goal/target omset dan net profit usaha Anda di tahun ini?</li>
					<li>Realitas omset dan net profit usaha Anda di tahun ini?</li>
					<li>Peralatan dan bahan utama apa saja yang Anda butuhkan untuk membuat produk/jasa tersebut?</li>
					<li>Biaya apa saja yang Anda butuhkan dalam menyediakan, menjual, dan mengantarkan produk/jasa kepada pelanggan?</li>
					<li>Dari sisi mana saja bisnis Anda akan mendapatkan revenue dari pelanggan?</li>
				</ul>
				<p><small class="text-info"><strong>Referensi:</strong><br/>
					Strategi mendapatkan finansial yang kuat secara cerdik dengan proyeksi pengeluaran yang terukur sehingga mempunyai potensi keuntungan.
					Proyeksi antara target dan realita sangat relevan dengan strategi marketing maupun rencana pengeluaran.</small></p>
				
				<ul>
					<li>Unggah berkas rancangan atau laporan finansial bisnis Anda. <strong>Cek Lampiran</strong></li>
				</ul>
				<p><small class="text-info"><strong>Referensi:</strong><br/>
					LAMPIRAN : proyeksi finansial yang menunjukkan rencana atau laporan pendapatan, biaya HPP, gross profit, biaya operational, net profit dan proyeksi ROI nya.</small></p>
EOT
			]);
			
			$this->db->insert_batch('komponen_penilaian_isian', [
				['komponen_penilaian_id' => 34, 'isian_ke' => 4],
				['komponen_penilaian_id' => 34, 'isian_ke' => 5],
				['komponen_penilaian_id' => 34, 'isian_ke' => 28],
				['komponen_penilaian_id' => 34, 'isian_ke' => 30],
				['komponen_penilaian_id' => 34, 'isian_ke' => 19],
			]);

		}
		echo "OK\n";
		
		echo "  > seed data skor ... ";
		$this->db->insert_batch('skor', [
			['id' => 7, 'skor' => 1, 'keterangan' => 'Kurang'],
			['id' => 8, 'skor' => 2, 'keterangan' => 'Cukup'],
			['id' => 9, 'skor' => 4, 'keterangan' => 'Baik'],
			['id' => 10, 'skor' => 5, 'keterangan' => 'Sangat Baik'],
		]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > drop table komponen_penilaian_isian ... ";
		$this->dbforge->drop_table('komponen_penilaian_isian');
		echo "OK\n";
		
		echo "  > clear seed komponen penilaian ... ";
		// Ambil KBMI 2019
		$kegiatan = $this->db->get_where('kegiatan', ['tahun' => 2019, 'program_id' => PROGRAM_KBMI])->row();
		$this->db->delete('komponen_penilaian', ['kegiatan_id' => $kegiatan->id, 'tahapan_id' => TAHAPAN_EVALUASI]);
		echo "OK\n";
		
		echo "  > clear seed data skor ... ";
		$this->db->where_in('id', [7, 8, 9, 10])->delete('skor');
		echo "OK\n";
		
		echo "  > rollback table komponen_penilaian ... ";
		$this->db->query('alter table komponen_penilaian modify column kriteria varchar(250) not null');
		echo "OK\n";
	}
}
