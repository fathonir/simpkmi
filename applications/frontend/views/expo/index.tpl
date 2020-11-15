{extends file='site_layout.tpl'}
{block name='head'}
	<style>.table { font-size: 14px; }</style>
{/block}
{block name='content'}
	<h2 class="page-header">Daftar Delegasi Expo KMI</h2>
	<div class="row">
		<div class="col-lg-12">

			<div class="panel panel-default">
				<div class="panel-body">
					<form action="{current_url()}" method="get" class="form-inline">
						<div class="form-group">
							<label class="control-label">Filter Tahun</label>
							<select class="form-control" name="tahun">
								{html_options options=$tahun_set selected=$tahun_selected}
							</select>
							<input type="submit" class="btn btn-default" value="Lihat" />
						</div>
						<div class="form-group">
							{if isset($kegiatan)}
								<p class="form-control-static">
									Status Program {$kegiatan->program->nama_program_singkat} Tahun {$kegiatan->tahun}:
									{if $kegiatan->is_aktif}
										<span class="label label-success">Aktif</span>
										Masa Usulan : <span class="label label-info">{$kegiatan->tgl_awal_upload_dmy} WIB</span>
										s.d. <span class="label label-info">{$kegiatan->tgl_akhir_upload_dmy} WIB</span>
									{else}
										<span class="label label-default">Tidak Aktif</span>
									{/if}
								</p>
							{/if}
						</div>
					</form>
				</div>
			</div>
			<p></p>

			{* Form upload berkas per PT berlaku dibawah tahun 2020 *}
			{if $kegiatan->is_aktif and $kegiatan->is_masa_upload and $tahun_selected < 2020}
				<form action="{current_url()}" method="post" enctype="multipart/form-data" class="form-horizontal">
					<fieldset>
						<!-- Text input-->
						<div class="form-group" id="fileUpload" {if $file_expo != NULL}style="display: none"{/if}>
							<label class="col-lg-2 control-label" for="file1">File Proposal Expo</label>
							<div class="col-lg-5">
								<input id="file1" name="file1" class="form-control input-md" type="file">
								<span class="help-block text-info">File PDF. Format proposal bisa dilihat di <a href="{site_url('site/download')}" target="_blank">{site_url('site/download')}</a></span>
							</div>
							<div class="col-lg-2">
								<input type="submit" class="btn btn-primary" value="Upload" />
								{if $file_expo != NULL}
									<a class="btn btn-default" id="btnCancelChange">Batal</a>
								{/if}
							</div>
						</div>
						<div class="form-group" id="fileUploadDisplay" {if $file_expo == NULL}style="display: none"{/if}>
							<label class="col-lg-2 control-label" for="file1">File Proposal Expo</label>
							<div class="col-lg-5">
								<p class="form-control-static">
									<a href="{base_url()}upload/usulan-expo/{$file_expo->nama_file}">{$file_expo->nama_asli}</a>
									{if $kegiatan->is_masa_upload}
										<a class="btn btn-xs btn-warning" id="btnChangeFile">Ubah</a>
									{/if}
								</p>
							</div>
						</div>
					</fieldset>
				</form>
			{/if}

			{if $kegiatan->is_masa_upload}
				{if $kegiatan->tahun == 2020}
					{if $kegiatan->proposal_per_pt == 0 or $jumlah_proposal_umum < $kegiatan->proposal_per_pt}
						<p><a href="{site_url('expo/add')}?kegiatan_id={$kegiatan->id}" class="btn btn-primary">Tambah Usaha</a></p>
					{/if}
				{else}
					{if $kegiatan->proposal_per_pt == 0 or count($data_set) < $kegiatan->proposal_per_pt}
						<p><a href="{site_url('expo/add')}?kegiatan_id={$kegiatan->id}" class="btn btn-primary">Tambah Usaha</a></p>
					{/if}
				{/if}
			{/if}

			<table class="table table-bordered table-hover table-condensed">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Nama Usaha</th>
						<th>Kategori</th>
						<th>Sub-Kategori</th>
						<th class="text-center">Status</th>
						<th style="width: 220px"></th>
					</tr>
				</thead>
				<tbody>
					{foreach $data_set as $data}
						<tr>
							<td class="text-center">{$data@index + 1}</td>
							<td>{$data->judul} {if $data->is_kmi_award}<span class="label label-primary">KMI Award</span>{/if}</td>
							<td class="text-center">
								{if $data->program_id_asal == PROGRAM_KBMI}
									KBMI
								{elseif $data->program_id_asal == PROGRAM_STARTUP}
									ASMI
								{/if}
							</td>
							<td class="text-center">{$data->nama_kategori}</td>
							<td class="text-center">
								{if $data->is_submited == 1}
									{if $data->is_didanai == 1}
										<span class="label label-success">Ikut EXPO {$kegiatan->tahun}</span>
									{elseif $data->is_ditolak == 1}
										{* <span class="label label-danger">Ditolak</span> *}
									{else}
										<span class="label label-info">Seleksi Kelayakan</span>
									{/if}
								{else}
									<span class="label label-default">Draft</span>
								{/if}
							</td>
							<td>
								{if $kegiatan->is_aktif and $kegiatan->is_masa_upload and $data->is_submited == 0}
									<a href="{site_url('expo/edit')}/{$data->id}" class="btn btn-xs btn-success">Edit</a>
									{* Kalau dari KBMI / ASMI tidak bisa di hapus *}
									{if $data->program_id_asal == ''}
									<a href="{site_url('expo/hapus')}/{$data->id}" class="btn btn-xs btn-danger">Hapus</a>
									{/if}
									<a href="{site_url('expo/submit')}/{$data->id}" class="btn btn-xs btn-primary">Submit untuk Seleksi</a>
								{/if}
								{if $kegiatan->is_aktif and $kegiatan->is_masa_upload and $data->is_submited}
									<a href="{site_url('expo/unsubmit')}/{$data->id}" class="btn btn-xs btn-default">Batalkan Submit</a>
								{/if}
							</td>
						</tr>
					{foreachelse}
						<tr>
							<td colspan="6">Belum ada data</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
			<ul>
				<li>Expo KMI 2020 ketentuannya sebagai berikut:
					<ul>
						<li>Proposal kategori KBMI dan ASMI Lolos Tahap 1, tidak diperlukan usulan baru. Hanya
							mengkonfirmasi keikutsertaan pada Expo KMI 2020 saja.</li>
						<li>Upload proposal diperlukan untuk tiap judul usaha bagi kategori Umum</li>
						<li>Setiap perguruan tinggi hanya dapat mengajukan maksimal 3 sub-kategori (1 subkategori = 1 usaha)</li>
					</ul>
				</li>
				<li>Informasi Status : <br/>
					<span class="label label-default">Draft</span> : Usulan baru<br/>
					<span class="label label-info">Seleksi Kelayakan</span> : Dalam proses seleksi oleh tim penilai.<br/>
					<span class="label label-success">Ikut EXPO</span> : Usulan disetujui dan berhak mengikut Expo KMI<br/>
					<span class="label label-danger">Ditolak</span> : Usulan tidak disetujui<br/>
					<span class="label label-primary">KMI Award</span> : Usulan yang diikutkan lomba KMI Award
				</li>
			</ul>
		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src="{base_url('assets/js/bootstrap-filestyle.min.js')}" type='text/javascript'></script>
	<script>
		$(document).ready(function () {
			/* File Style */
			$(':file').filestyle();

			$('#btnChangeFile').on('click', function () {
				$('#fileUploadDisplay').hide();
				$('#fileUpload').show();
			});

			$('#btnCancelChange').on('click', function () {
				$('#fileUploadDisplay').show();
				$('#fileUpload').hide();
			});
		});
	</script>
{/block}
