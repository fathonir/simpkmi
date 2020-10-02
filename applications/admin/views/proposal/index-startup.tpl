{extends file='site_layout.tpl'}
{block name='head'}
	<link rel="stylesheet" href="{base_url('../assets/css/dataTables.bootstrap.min.css')}" />
	<style>
		.table>thead>tr>th, .table>tbody>tr>td { font-size: 13px; }
	</style>
{/block}
{block name='content'}
	<h2 class="page-header">Daftar Usulan Startup Masuk</h2>

	<div class="row">
		<div class="col-lg-12">
			
			<form class="form-inline" action="{current_url()}" method="get" style="margin-bottom: 20px">
				<div class="form-group">
					<select name="kegiatan_id" class="form-control input-sm">
						<option value="">-- Pilih Kegiatan --</option>
						{foreach $kegiatan_set as $kegiatan}
							<option value="{$kegiatan->id}" {if !empty($smarty.get.kegiatan_id)}{if $smarty.get.kegiatan_id == $kegiatan->id}selected{/if}{/if}>{$kegiatan->nama_program} {$kegiatan->tahun}</option>
						{/foreach}
					</select>
				</div>
				<button type="submit" class="btn btn-sm btn-default">
					Lihat
				</button>
			</form>
			
			<table class="table table-bordered table-striped table-condensed" id="table">
				<thead>
					<tr>
						<th>Usulan</th>
						<th>Perguruan Tinggi</th>
						<th class="text-center">Pitchdeck</th>
						<th class="text-center">Presentasi</th>
						<th class="text-center">Produk</th>
						<th class="text-center">Pitchdeck 2</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					{if isset($data_set)}
						{foreach $data_set as $data}
							<tr>
								<td>{$data->judul}</td>
								<td>{$data->nama_pt}</td>
								<td class="text-center">
									{if $data->file_pitchdeck != ''}
										<a href="{base_url()}../upload/lampiran/{$data->file_pitchdeck}" target="_blank"><i class="glyphicon glyphicon-paperclip"></i></a>
										{else}
										<span class="label label-default">Belum Upload</span>
									{/if}
								</td>
								<td class="text-center">
									{if $data->link_presentasi != ''}
										<a href="{$data->link_presentasi}" target="_blank"><i class="glyphicon glyphicon-film"></i></a>
										{else}
										<span class="label label-default">Belum Ada</span>
									{/if}
								</td>
								<td class="text-center">
									{if $data->link_produk != ''}
										<a href="{$data->link_produk}" target="_blank"><i class="glyphicon glyphicon-new-window"></i></a>
										{else}
										<span class="label label-default">Belum Ada</span>
									{/if}
								</td>
								<td class="text-center">
									{if $data->file_pitchdeck_2 != ''}
										<a href="{base_url()}../upload/lampiran/{$data->file_pitchdeck_2}" target="_blank"><i class="glyphicon glyphicon-paperclip"></i></a>
									{/if}
								</td>
								<td class="text-center">
									{if $data->is_reviewed == TRUE}
										<label class="label label-primary">Direview</label>
									{else if $data->is_submited == TRUE}
										<label class="label label-success">Submit</label>
									{else}
										<label class="label label-default">Pengisian</label>
									{/if}
								</td>
							</tr>
						{foreachelse}
							<tr>
								<td colspan="7">Tidak ada data ditemukan</td>
							</tr>
						{/foreach}
					{/if}
				</tbody>
			</table>

		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/jquery.dataTables.min.js')}"></script>
	<script src="{base_url('../assets/js/dataTables.bootstrap.min.js')}"></script>
	<script type="text/javascript">
		$('#table').DataTable({
			ordering: false,
			stateSave: true
		});
	</script>
{/block}
