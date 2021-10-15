{extends file='site_layout.tpl'}
{block name='content'}
	<h1 class="page-header">Jadwal Pendampingan KBMI</h1>

	<div class="row">
		<div class="col-lg-12">
			<form class="form-inline" action="{current_url()}" method="get" style="margin-bottom: 20px">
				<div class="form-group">
					<select name="kegiatan_id" class="form-control">
						<option value="">-- Pilih Kegiatan --</option>
						{foreach $kegiatan_set as $kegiatan}
							<option value="{$kegiatan->id}" {if !empty($smarty.get.kegiatan_id)}{if $smarty.get.kegiatan_id == $kegiatan->id}selected{/if}{/if}>{$kegiatan->nama_program} {$kegiatan->tahun}</option>
						{/foreach}
					</select>
				</div>
				<button type="submit" class="btn btn-default">
					Lihat
				</button>
			</form>

			<table class="table table-bordered">
				<thead>
				<tr>
					<th>Nama Tahapan</th>
					<th>Tanggal Awal Laporan</th>
					<th>Tanggal Akhir Laporan</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				{foreach $tahapan_pendampingan_set as $data}
					<tr>
						<td>{$data->nama_tahapan}</td>
						<td>{$data->tgl_awal_laporan|date_format:"%d %b %Y %T"}</td>
						<td>{$data->tgl_akhir_laporan|date_format:"%d %b %Y %T"}</td>
						<td>
							<a href="{site_url('kegiatan/edit-pendampingan/')}{$data->id}" class="btn btn-sm btn-default">Edit</a>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
{/block}
