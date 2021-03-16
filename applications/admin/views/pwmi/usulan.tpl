{extends file='site_layout.tpl'}
{block name='head'}
	<link rel="stylesheet" href="{base_url('../assets/css/dataTables.bootstrap.min.css')}"/>
{/block}
{block name='content'}
	<h2 class="page-header">Usulan Dosen PWMI</h2>
	<div class="row">
		<div class="col-lg-12">
			<form class="form-inline" action="{current_url()}" method="get" style="margin-bottom: 10px">
				<div class="form-group">
					<label for="kegiatan_id">Kegiatan</label>
					<select name="kegiatan_id" class="form-control input-sm">
						<option value="">-- Pilih Kegiatan --</option>
						{html_options options=$kegiatan_option_set selected=$kegiatan_id}
					</select>
				</div>
				<button type="submit" class="btn btn-sm btn-default">Lihat</button>
			</form>

			{if isset($data_set)}
				<table class="table table-bordered" id="table">
					<thead>
					<tr>
						<th>Perguruan Tinggi</th>
						<th>Nama Dosen</th>
						<th>NIDN</th>
						<th>File Syarat</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					{foreach $data_set as $data}
						<tr>
							<td>{$data->nama_pt}</td>
							<td>{$data->nama}</td>
							<td>{$data->nidn}</td>
							<td>
								{if $data->jumlah_syarat <= $data->jumlah_upload}
									<span class="label label-success">Lengkap</span>
								{else}
									<span class="label label-default">Belum Lengkap</span>
								{/if}
							</td>
							<td></td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			{/if}
		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/jquery.dataTables.min.js')}"></script>
	<script src="{base_url('../assets/js/dataTables.bootstrap.min.js')}"></script>
	<script type="text/javascript">
		$('#table').DataTable({
			stateSave: true
		});
	</script>
{/block}
