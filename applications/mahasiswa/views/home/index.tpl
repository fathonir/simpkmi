{extends file='site_layout.tpl'}
{block name='content'}
	<div class="row">
		<div class="col-lg-12">

			<h2>Selamat datang, {$ci->session->user->mahasiswa->nama}</h2>
			
			<h3>Online Workshop Peningkatan dan Pengembangan Kewirausahaan</h3>
			<div class="panel panel-default">
				<div class="panel-body">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Topik</th>
								<th>Pemateri</th>
								<th>Waktu</th>
								<th>Meeting URL</th>
								<th>Password Meeting</th>
								<th>Presensi</th>
								<th>Sertifikat</th>
							</tr>
						</thead>
						<tbody>
							{foreach $meeting_set as $meeting}
								<tr>
									<td>{$meeting->topik}</td>
									<td>{$meeting->pemateri}</td>
									<td>{$meeting->waktu_mulai|date_format:"%d %B %Y %T"}</td>
									{if is_null($meeting->is_terpilih_meeting)}
										<td colspan="2"></td>
									{else}
										{if $meeting->is_terpilih_meeting == 1}
											<td><a href="{$meeting->meeting_url}" target="_blank">{$meeting->meeting_url}</a></td>
											<td class="text-center"><code>{$meeting->meeting_password}</code></td>
										{elseif $meeting->is_terpilih_meeting == 0}
											<td colspan="2">
												<a href="{$meeting->youtube_url}" target="_blank">{$meeting->youtube_url}</a>
											</td>
										{/if}
									{/if}
									<td>
										{if $meeting->waktu_mulai < date('Y-m-d H:i:s')}
											{if $meeting->kehadiran == 0}
												<form action="{site_url('online-workshop/presensi')}" method="post">
													<input type="hidden" name="meeting_id" value="{$meeting->id}" />
													<input type='text' class='form-control input-sm' style='width: 100px' name='kode_kehadiran' placeholder='Kode presensi' />
													<button type='submit' class="btn btn-primary btn-sm">Simpan</button>
												</form>
											{else}
												<span class="label label-success">HADIR</span>
												{if $meeting->kuesioner_url != null}
													<br/><a href="{$meeting->kuesioner_url}">Isi Kuesioner</a>
												{/if}
											{/if}
										{/if}
									</td>
									<td>
										{if $meeting->kehadiran}
											<a href="{site_url('online-workshop/cetak-sertifikat')}?meeting_id={$meeting->id}" target="_blank">Cetak Sertifikat</a>
										{/if}
									</td>
								</tr>
							{foreachelse}
								<tr>
									<td colspan="7"><i>Tidak ada data registrasi</i></td>
								</tr>
							{/foreach}
						</tbody>
					</table>
					<p>Informasi:<br/>
						<sup>1</sup> Waktu pelaksanaan menggunakan zona waktu WIB (Waktu Indonesia Barat).<br/>
						<sup>2</sup> Meeting URL akan muncul jika sudah diumumkan.<br/>
						<sup>3</sup> Harap mengisi kode presensi sebelum meeting selesai.
					</p>
				</div>
			</div>

			<h3>Program KBMI</h3>
			{if $kegiatan_kbmi != NULL}
				<p>Program Berjalan : {$kegiatan_kbmi->tahun}.
					Mulai unggah <strong>{strftime('%d %B %Y %H:%M:%S', strtotime($kegiatan_kbmi->tgl_awal_upload))}</strong>
					sampai <strong>{strftime('%d %B %Y %H:%M:%S', strtotime($kegiatan_kbmi->tgl_akhir_upload))}</strong></p>
			{/if}
			<div class="panel panel-default">
				<div class="panel-body">
					<table class="table table-striped">
						<thead>
							<tr>
								<th style="width: 1%">Tahun</th>
								<th>Judul</th>
								<th>Kelengkapan</th>
								<th>Upload</th>
								<th>Status</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							{foreach $proposal_kbmi_set as $proposal_kbmi}
								<tr>
									<td>{$proposal_kbmi->tahun}</td>
									<td>{$proposal_kbmi->judul|htmlentities}</td>
									<td>
										<span class="badge">{$proposal_kbmi->jumlah_terisi}</span> dari
										<span class="badge">{$proposal_kbmi->jumlah_isian}</span>
									</td>
									<td><span class="label label-info"></span></td>
									<td>
										{if $proposal_kbmi->is_submited}
											{if $proposal_kbmi->is_didanai}
												<span class="label label-success">Didanai</span>
											{else}
												<span class="label label-success">Sudah Submit</span>
											{/if}
										{else}
											<span class="label label-default">Belum Submit</span>
										{/if}
									</td>
									<td>
										{* Tampilkan tombol jika tahun sesuai dengan yg aktif *}
										{if $kegiatan_kbmi != NULL}
											{if $kegiatan_kbmi->tahun == $proposal_kbmi->tahun}
												<a href="{site_url('kbmi/identitas')}" class="btn btn-primary btn-xs">
													<i class="glyphicon glyphicon-pencil"></i> Identitas Proposal
												</a>
												{if $proposal_kbmi->is_didanai}
													<a href="{site_url('kbmi/upload-kemajuan')}" class="btn btn-primary btn-xs">
														<i class="glyphicon glyphicon-upload"></i> Upload Kemajuan
													</a>
												{/if}
											{/if}
										{/if}
										{* Tombol ajukan ke Expo *}
										{if $kegiatan_expo != NULL}
											<a href="{site_url('expo/register-from-kbmi')}/{$proposal_kbmi->id}" class="btn btn-success btn-xs">
												<i class="glyphicon glyphicon-export"></i> Daftar Expo
											</a>
										{/if}
									</td>
								</tr>
							{foreachelse}
								<tr>
									<td colspan="6"><i>Tidak ada judul terdaftar</i></td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>

			<h3>Program EXPO</h3>
			{if $kegiatan_expo != NULL}
				<p>Program Berjalan : {$kegiatan_expo->tahun}.
					Mulai unggah <strong>{strftime('%d %B %Y %H:%M:%S', strtotime($kegiatan_expo->tgl_awal_upload))}</strong>
					sampai <strong>{strftime('%d %B %Y %H:%M:%S', strtotime($kegiatan_expo->tgl_akhir_upload))}</strong></p>
			{/if}
			<div class="panel panel-default">
				<div class="panel-body">
					<table class="table table-striped">
						<thead>
						<tr>
							<th style="width: 1%">Tahun</th>
							<th>Judul</th>
							<th>Upload</th>
							<th>Status</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						{foreach $proposal_expo_set as $proposal_expo}
							<tr>
								<td>{$proposal_expo->tahun}</td>
								<td>{$proposal_expo->judul|htmlentities}</td>
								<td>
									{if $proposal_expo->is_submited}
										{if $proposal_expo->is_didanai}
											<span class="label label-success">Didanai</span>
										{else}
											<span class="label label-success">Sudah Submit</span>
										{/if}
									{else}
										<span class="label label-default">Belum Submit</span>
									{/if}
								</td>
								<td></td>
							</tr>
							{foreachelse}
							<tr>
								<td colspan="5"><i>Tidak ada judul terdaftar</i></td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
			</div>


			<h3 id="asmi">Program Akselerasi Startup</h3>
			{if $kegiatan_startup != NULL}
				<p>Program Berjalan : {$kegiatan_startup->tahun}</p>
				<p>Masa unggah usulan: {$kegiatan_startup->tgl_awal_upload|date_format:"%d %B %Y %T"} sampai {$kegiatan_startup->tgl_akhir_upload|date_format:"%d %B %Y %T"}</p>
				<p>Masa unggah kemajuan: {$kegiatan_startup->tgl_awal_upload_kemajuan|date_format:"%d %B %Y %T"} sampai {$kegiatan_startup->tgl_akhir_upload_kemajuan|date_format:"%d %B %Y %T"}</p>
			{/if}
			<div class="panel panel-default">
				<div class="panel-body">
					<table class="table table-striped">
						<thead>
							<tr>
								<th style="width: 1%">Tahun</th>
								<th>Judul</th>
								<th>Pitchdeck</th>
								<th>Presentasi</th>
								<th>Produk</th>
								<th>Pitckdeck Tahap 2</th>
								<th>Kemajuan</th>
								<th style="width: 1%;"></th>
							</tr>
						</thead>
						<tbody>
							{foreach $proposal_startup_set as $proposal_startup}
								<tr>
									<td>{$proposal_startup->tahun}</td>
									<td>{$proposal_startup->judul}</td>
									<td>
										{if $proposal_startup->file_pitchdeck != ''}
											<a href="{base_url()}../upload/lampiran/{$proposal_startup->file_pitchdeck}" target="_blank"><i class="glyphicon glyphicon-paperclip"></i></a>
											{else}
											<span class="label label-default">Belum Upload</span>
										{/if}
									</td>
									<td>
										{if $proposal_startup->link_presentasi != ''}
											<a href="{$proposal_startup->link_presentasi}" target="_blank"><i class="glyphicon glyphicon-film"></i></a>
											{else}
											<span class="label label-default">Belum Ada</span>
										{/if}
									</td>
									<td>
										{if $proposal_startup->link_produk != ''}
											<a href="{$proposal_startup->link_produk}" target="_blank"><i class="glyphicon glyphicon-new-window"></i></a>
											{else}
											<span class="label label-default">Belum Ada</span>
										{/if}
									</td>
									<td>
										{if $proposal_startup->is_lolos_tahap_2 == true}
											{if $proposal_startup->file_pitchdeck_2 != ''}
												<a href="{base_url()}../upload/lampiran/{$proposal_startup->file_pitchdeck_2}" target="_blank"><i class="glyphicon glyphicon-paperclip"></i></a>
											{else}
												<span class="label label-default">Belum Upload</span>
											{/if}
										{/if}
									</td>
									<td>
										{if $proposal_startup->jumlah_upload_kemajuan == 0}
											<span class="label label-default">Belum Upload</span>
										{elseif $proposal_startup->jumlah_upload_kemajuan < $proposal_startup->jumlah_syarat_kemajuan}
											<span class="label label-warning">Belum Lengkap</span>
										{elseif $proposal_startup->jumlah_upload_kemajuan >= $proposal_startup->jumlah_syarat_kemajuan}
											<span class="label label-success">Sudah</span>
										{/if}
									</td>
									<td style="white-space: nowrap">
										<a href="{site_url('startup/upload-syarat')}/{$proposal_startup->id}/1" class="btn btn-sm btn-success">Unggah</a>
										{if $proposal_startup->is_submited == 0}
											<a href="{site_url('startup/submit')}/{$proposal_startup->id}" class="btn btn-sm btn-primary">Submit</a>
										{/if}
										{if $proposal_startup->is_lolos_tahap_2 == true}
											<a href="{site_url('startup/pitchdeck-2')}/{$proposal_startup->id}" class="btn btn-sm btn-success">Unggah Tahap 2</a>
										{/if}
										{if $proposal_startup->is_didanai == 1}
											<a href="{site_url('startup/upload-syarat')}/{$proposal_startup->id}/2" class="btn btn-sm btn-success">Unggah Kemajuan</a>
										{/if}
									</td>
								</tr>
							{foreachelse}
								<tr>
									<td colspan="7"><i>Tidak ada judul terdaftar</i></td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
	</div>
{/block}
