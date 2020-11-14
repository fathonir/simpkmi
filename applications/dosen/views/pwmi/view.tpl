{extends file='site_layout.tpl'}
{block name='content'}
	<div class="row">
		<div class="col-lg-12">

			<h2 class="page-header">Detail Pelaporan</h2>

			<div class="form-horizontal">
				<div class="form-group">
					<label class="control-label col-md-2">Tahun</label>
					<div class="col-md-10">
						<p class="form-control-static">{$kegiatan->tahun}</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">Judul</label>
					<div class="col-md-10">
						<p class="form-control-static">{$proposal->judul}</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">Ketua</label>
					<div class="col-md-10">
						<p class="form-control-static">{$ketua->nama} ({$ketua->nim})</p>
					</div>
				</div>
			</div>

			<p><a class="btn btn-default" href="{site_url('home/index')}">Kembali</a></p>

			<table class="table table-bordered">
				<thead>
				<tr>
					<th>Tahap</th>
					<th>Masa Pelaporan</th>
					<th class="text-center">Status Pelaporan</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				{foreach $lap_pendampingan_set as $lap_pendampingan}
					<tr>
						<td>{$lap_pendampingan->nama_tahapan}</td>
						<td>
							{$lap_pendampingan->tgl_awal_laporan_dmy} s/d {$lap_pendampingan->tgl_akhir_laporan_dmy}
						</td>
						<td class="text-center">
							{if $lap_pendampingan->laporan_pendampingan}
								<span class="label label-success">Sudah</span>
							{else}
								<span class="label label-default">Belum</span>
							{/if}
						</td>
						<td class="text-center">
							{if $lap_pendampingan->is_masa_laporan}
								<a class="btn btn-primary btn-sm" href="{site_url('pwmi/update/')}{$proposal->id}/{$lap_pendampingan->tahapan_pendampingan_id}">Update</a>
							{else}
								<a class="btn btn-primary btn-sm" href="{site_url('pwmi/update/')}{$proposal->id}/{$lap_pendampingan->tahapan_pendampingan_id}">Lihat</a>
							{/if}
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
{/block}
