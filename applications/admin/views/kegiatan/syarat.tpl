{extends file='site_layout.tpl'}
{block name='head'}
	<style type='text/css'>
		.table>tbody>tr>td:last-child { width: 1%; white-space: nowrap; }
	</style>
{/block}
{block name='content'}
	<h1 class="page-header">Syarat Upload <small>{$kegiatan->nama_program} {$kegiatan->tahun}</small></h1>
	
	<div class="row">
		<div class="col-lg-12">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Tahapan</th>
						<th>Urut</th>
						<th>Syarat File</th>
						<th>Keterangan</th>
						<th>Jenis File</th>
						<th>Wajib</th>
						<th>Aktif</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{foreach $data_set as $data}
						<tr>
							<td>{$data->tahapan}</td>
							<td class="text-center">{$data->urutan}</td>
							<td>{$data->syarat}</td>
							<td>{$data->keterangan}</td>
							<td>{$data->allowed_types}</td>
							<td class="text-center">{if $data->is_wajib}<span class="label label-primary">Wajib</span>{else}<span class="label label-default">Tidak Wajib</span>{/if}</td>
							<td class="text-center">{if $data->is_aktif}<span class="label label-success">Aktif</span>{/if}</td>
							<td>
								<a href="{site_url('kegiatan/edit-syarat')}/{$data->id}" class="btn btn-sm btn-default">Edit</a>
								{if $data->is_deletable}
									<a href="{site_url('kegiatan/hapus-syarat')}/{$data->id}" class="btn btn-sm btn-danger">Hapus</a>
								{/if}
							</td>
						</tr>
					{/foreach}
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8">
							<a href="{site_url('kegiatan/add-syarat')}?kegiatan_id={$kegiatan->id}" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Tambah Syarat</a>
							<a href="{site_url('kegiatan')}" class="btn btn-sm btn-default">Kembali ke Jadwal Kegiatan</a>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
{/block}
