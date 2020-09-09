<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Migration_Alter_table_komponen_penilaian_isian extends CI_Migration
{
	function up()
	{
		echo "  > alter table komponen_penilaian_isian ... ";
		$this->db->query("alter table komponen_penilaian_isian add column pertanyaan TEXT after isian_ke");
		$this->db->query("alter table komponen_penilaian_isian add column urutan INT DEFAULT '1' after pertanyaan");
		echo "OK\n";
		
		echo "  > seed komponen_penilaian ... ";
		$this->db->update('komponen_penilaian', [
			'kriteria' => <<<EOT
				<p class="lead">Noble Purpose, Topik Bisnis dan target specific customer</p>
				<p><strong>Indikator Keberhasilan :</strong> Pemilihan topik bisnis dan specific target market yang sangat kuat terkait dengan noble purpose, passion, expertise team dan potensi mitra yg dipilih.</p>
			
				<small class="text-info"><strong>Referensi Penilaian :</strong><br/>
					Keterkaitan kuat antara noble purpose dengan topik bisnis yang dipilih, dan ketepatan maupun keunikan dalam
					memilih target spesifik market yang disasar. Melampirkan data-data pendukung fakta dari topik bisnis yang dipilih.<br/>
					Lampiran berupa noble purpose setiap anggota team dan kesimpulannya menjadi noble purpose kelompok/team,
					lampiran berupa analisa tarikan pasar topik bisnis yang dipilih dengan korelasi kuat dengan noble purpose team.
					Data tenting topik bisnis berdasarkan data-data terpercaya yang menguatkan yang menunjukkan permintaan pasar yang
					sedang trend saat ini.</small>
EOT
		], ['id' => 29]);
		$this->db->update('komponen_penilaian', [
			'kriteria' => <<<EOT
				<p class="lead">Strategi manajemen SDM</p>
				<p>Cek Lampiran LAPORAN RISET PASAR (berupa riset tarikan pasar, analisa calon partner maupun analisa kompetitor)</p>
				<p><small class="text-info"><strong>Referensi 1:</strong><br/>Keterkaitan kuat antara topik bisnis dengan keahlian team dan
					ketepatan dalam membuat indikator keberhasilan. Pemilihan partner bisnis untuk menguatkan dan membuat efisien biaya produksi/marketing</small>
				</p>
				<p><small class="text-info"><strong>Referensi 2:</strong><br/>
					Lampiran : Profil masing-masing anggota team dengan segala keahlian maupun pengalaman yang dimiliki. Lampiran indikator
					keberhasilan yang mempunyai relasi kuat dengan keberhasilan bisnisnya. Lampiran analisa partner bisnis yang akan atau
					sudah dipilih yang membawa dampak efisiensi proses marketing maupun inovasi bisnisnya.</small>
				</p>
EOT
		], ['id' => 30]);
		$this->db->update('komponen_penilaian', [
			'kriteria' => <<<EOT
				<p class="lead">Menjelaskan kedalaman pemahaman pebisnis atas permasalahan yang dirasakan oleh pelaggan (DESIRABILITY)</p>
				<p>Cek LAMPIRAN RISET PASAR (riset berupa data NTD, pain, gain customer)</p>
				<p><small class="text-info"><strong>Referensi 1:</strong><br/>
					Pemilihan permasalahan ataupun harapan yang dialami oleh customer berdasarkan hasil riset interview kepada 30 target 
					customer dan validasi kepada 100 target customer via google form. Analisa permasalahan ataupun harapan yang diplih
					memiliki keunikan dan mempunyai peluang besar untuk menjadi trend.</small></p>
				<p><small class="text-info"><strong>Referensi 2:</strong><br/>
					Lampiran hasil riset kepada target customernya. Hasil interview kepada minimal 30 target customer untuk target publik
					dan 10 untuk target institusi/lembaga, ditemukan pola pain dan gain yang kuat kemudian divalidasi dengan survey online
					tentang pain gain tersebut kepada 100 target customernya untuk akhirnya menjadi pertimbangan masalah atau harapan
					mana dari target customernya yang akan diselesaikan oleh bisnisnya.</small></p>
EOT
		], ['id' => 31]);
		$this->db->update('komponen_penilaian', [
			'kriteria' => <<<EOT
				<p class="lead">Menjelaskan produk yang kompetitif dan mampu menyelesaikan masalah pelanggan. (FEASIBILITY)</p>
				<p>Cek Lampiran LAPORAN RISET PASAR (riset berupa data SWOT terhadap kompetitor dan pasar yang ada)</p>
				<p><small class="text-info"><strong>Referensi 1:</strong><br/>
					Produk/jasa yang mempunyai ketepatan solusi. Memilih fitur maupun business model yang berbeda dan kompetitif dengan
					competitor yang ada. Riset mendalam terkait kompetitor dan referensi lengkap ide2 yang sudah pernah ada. Disertai lampiran
					hasil riset tentang referensi maupun data kompetitor</small></p>
				<p><small class="text-info"><strong>Referensi 2:</strong><br/>
					Lampiran hasil riset yang mendalam tentang data2 kompetitor maupun produk/jasa substitusi. lampiran berupa hasil mentoring
					dan coaching kepada pakar2 di bidangnya. lampiran berupa jurnal atau artikel dari sumber yang terpercaya.
					<strong>LAMPIRAN PENTING</strong> : prototype atau video kehandalan produk/jasa yang dihasilkan, termasuk hasil test
					kepada target customernya, bagi yang punya produkt berupa testimoni di akun social medianya.</small></p>
EOT
		], ['id' => 32]);
		$this->db->update('komponen_penilaian', [
			'kriteria' => <<<EOT
				<p class="lead">Strategi pemasaran kepada customer (delivery)</p>
				<p><small class="text-info"><strong>Referensi:</strong><br/>
					Strategi pemasaran yang tepat dimulai dari memasarkan di pasar yg tepat dalam scope kecil dan target customer yang berpengaruh
					hingga rencana jangka panjang yang jelas dan terukur. Menggunakan pendekatan personal secara offline maupun menggunakan strategi
					digital yang tepat. Alasan yang sangat kuat kenapa team itu memilih daerah tersebut sebagai awal “pertempuran” bisnisnya.
					Kombinasi yang sangat kuat antara strategi direct selling / personal approach dengan digital marketing.</small></p>
EOT
		], ['id' => 33]);
				$this->db->update('komponen_penilaian', [
			'kriteria' => <<<EOT
				<p class="lead">Finansial (Strategi keuangan)</p>
				<p>Cek Lampiran Unggah berkas rancangan atau laporan finansial bisnis Anda.</p>
				<p><small class="text-info"><strong>Referensi 1:</strong><br/>
					Strategi mendapatkan finansial yang kuat secara cerdik dengan proyeksi pengeluaran yang terukur sehingga mempunyai potensi keuntungan.
					Proyeksi antara target dan realita sangat relevan dengan strategi marketing maupun rencana pengeluaran.</small></p>
				<p><small class="text-info"><strong>Referensi 2:</strong><br/>
					LAMPIRAN : proyeksi finansial yang menunjukkan rencana atau laporan pendapatan, biaya HPP, gross profit, biaya operational, net profit dan proyeksi ROI nya.</small></p>
EOT
		], ['id' => 34]);
		echo "OK\n";
		
		echo "  > seed komponen_penilaian_isian ... ";
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 1,
			'pertanyaan'	=> 'Hal mulia apa yang tim Anda ingin wujudkan dalam membangun bisnis?'
		], ['id' => 1]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 2,
			'pertanyaan'	=> 'Apa atau siapa yang menjadi pemicu hal mulia yang ingin diwujudkan tersebut?'
		], ['id' => 2]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 3,
			'pertanyaan'	=> 'Bisnis atau topik bisnis apa yang Anda ajukan?'
		], ['id' => 3]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 1,
			'pertanyaan'	=> 'Siapa saja anggota tim terbaik yang akan Anda libatkan dalam bisnis, dan apa keahlian masing-masing?'
		], ['id' => 4]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 2,
			'pertanyaan'	=> 'Apa saja tanggung jawab masing-masing tim Anda tersebut?'
		], ['id' => 5]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 3,
			'pertanyaan'	=> 'Apa indikator keberhasilan dari tanggung jawab masing-masing tim Anda tersebut?'
		], ['id' => 6]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 4,
			'pertanyaan'	=> 'Jika Anda harus bermitra dalam menyediakan produk/jasa Anda, pihak mana yang akan Anda ajak kerja sama?'
		], ['id' => 7]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 1,
			'pertanyaan'	=> 'Segmen spesifik pelanggan mana yang akan Anda sasar?'
		], ['id' => 8]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 2,
			'pertanyaan'	=> 'Coba Anda amati dan tanyakan kepada calon pelanggan yang Anda sasar. Aktifitas apa saja yang perlu mereka lakukan untuk mendapatkan produk/jasa yang menjadi konteks bisnis Anda?'
		], ['id' => 9]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 3,
			'pertanyaan'	=> 'Kesulitan apa saja yang benar-benar dirasakan oleh calon pelanggan Anda, terkait dengan hal-hal yang perlu dilakukan untuk mendapatkan produk/jasa yang menjadi konteks bisnis Anda?'
		], ['id' => 10]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 4,
			'pertanyaan'	=> 'Jika kesulitan-kesulitan tersebut dapat terselesaikan, harapan apa saja yang ingin diwujudkan oleh calon pelanggan Anda?'
		], ['id' => 11]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 5,
			'pertanyaan'	=> 'Dari semua kesulitan dan harapan calon pelanggan anda, produk/layanan anda akan menyelesaikan kesulitan dan memenuhi harapan yang mana?'
		], ['id' => 12]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 1,
			'pertanyaan'	=> 'Produk/jasa apa yang Anda tawarkan kepada calon pelanggan Anda?'
		], ['id' => 13]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 2,
			'pertanyaan'	=> 'Referensi produk/layanan apa saja atau hasil riset maupun jurnal dari pakar siapa yang Anda jadikan pertimbangan untuk membuat produk/layanan Anda?'
		], ['id' => 14]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 3,
			'pertanyaan'	=> 'Bagaimana produk/jasa Anda tersebut bekerja menyelesaikan masalah dan memenuhi keinginan pelanggan yang Anda sasar?'
		], ['id' => 15]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 4,
			'pertanyaan'	=> 'Menurut Anda, siapa saja yang akan menjadi kompetitor dalam menyediakan produk/jasa tersebut?'
		], ['id' => 16]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 5,
			'pertanyaan'	=> 'Apa saja keunggulan produk/jasa yang disediakan oleh kompetitor Anda?'
		], ['id' => 17]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 6,
			'pertanyaan'	=> 'Lalu, hal apa saja yang menjadi keunggulan kompetitif produk/jasa Anda dibandingkan dengan produk/jasa kompetitor?'
		], ['id' => 18]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 1,
			'pertanyaan'	=> 'Segmen spesifik pelanggan mana yang akan Anda sasar?'
		], ['id' => 19]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 2,
			'pertanyaan'	=> 'Area mana yang akan menjadi target ideal jangkauan bisnis Anda?'
		], ['id' => 20]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 3,
			'pertanyaan'	=> 'Dalam 4 bulan pertama bisnis Anda berjalan, daerah mana yang akan menjadi awal target pasar Anda?'
		], ['id' => 21]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 4,
			'pertanyaan'	=> 'Bagaimana strategi Anda untuk membuat calon pelanggan mengetahui produk/jasa yang Anda sediakan?'
		], ['id' => 22]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 5,
			'pertanyaan'	=> 'Bagaimana strategi Anda untuk membuat calon pelanggan tertarik dan akhirnya memutuskan membeli produk/jasa yang Anda sediakan?'
		], ['id' => 23]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 6,
			'pertanyaan'	=> 'Bagaimana caranya anda merespon pelanggan yang bertanya, membeli dan komplain terhadap layanan anda?'
		], ['id' => 24]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 7,
			'pertanyaan'	=> 'Strategi apa yang akan Anda lakukan untuk menjadikan pelanggan Anda loyal?'
		], ['id' => 25]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 8,
			'pertanyaan'	=> 'Dimana calon pelanggan dapat memperoleh produk/jasa Anda?'
		], ['id' => 26]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 1,
			'pertanyaan'	=> 'Goal/target omset dan net profit usaha Anda di tahun ini?'
		], ['id' => 27]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 2,
			'pertanyaan'	=> 'Realitas omset dan net profit usaha Anda di tahun ini?'
		], ['id' => 28]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 3,
			'pertanyaan'	=> 'Peralatan dan bahan utama apa saja yang Anda butuhkan untuk membuat produk/jasa tersebut?'
		], ['id' => 29]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 4,
			'pertanyaan'	=> 'Biaya apa saja yang Anda butuhkan dalam menyediakan, menjual, dan mengantarkan produk/jasa kepada pelanggan?'
		], ['id' => 30]);
		$this->db->update('komponen_penilaian_isian', [
			'urutan'		=> 5,
			'pertanyaan'	=> 'Dari sisi mana saja bisnis Anda akan mendapatkan revenue dari pelanggan?'
		], ['id' => 31]);
		echo "OK\n";
	}
	
	function down()
	{
		echo "  > rollback table komponen_penilaian_isian ... ";
		$this->db->query("alter table komponen_penilaian_isian drop column pertanyaan");
		$this->db->query("alter table komponen_penilaian_isian drop column urutan");
		echo "OK\n";
	}
}
