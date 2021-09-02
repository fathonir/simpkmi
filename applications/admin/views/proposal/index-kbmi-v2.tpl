{extends file='site_layout.tpl'}
{block name='head'}
	<link rel="stylesheet" href="{base_url('../assets/css/dataTables.bootstrap.min.css')}" />
	<style>
		.table>thead>tr>th, .table>tbody>tr>td { font-size: 13px; }
	</style>
{/block}
{block name='content'}
	<h2 class="page-header">Daftar Proposal KBMI <small>Format Isi Kuesioner</small></h2>
	
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
				<div class="form-group">
					<select name="tampilan" class="form-control input-sm">
						<option value="all" {if $tampilan == 'all'}selected{/if}>Semua Usulan</option>
						<option value="submited" {if $tampilan == 'submited'}selected{/if}>Sudah Submit</option>
						<option value="didanai" {if $tampilan == 'didanai'}selected{/if}>Didanai</option>
					</select>
				</div>
				<button type="submit" class="btn btn-sm btn-default">
					Lihat
				</button>
			</form>

			<table class="table table-bordered table-condensed table-striped table-hover" id="table">
				<thead>
					<tr>
						<th>Judul</th>
						<th>Pengusul</th>
						<th>Perguruan Tinggi</th>
						<th>Submit</th>
						<th>Didanai</th>
						<th>Pendanaan</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					
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
			stateSave: true,
			ajax: { url: '{site_url('proposal/index-kbmi-v2-data')}/{$kegiatan_id}/{$tampilan}', type: 'POST' },
			processing: true,
			serverSide: true,
			columns: [
				{ data: 'judul' },
				{ data: 'nama' },
				{ data: 'nama_pt' },
				{
					data: 'is_submited',
					className: 'text-center',
					render: (data, type, row) => { return (data === '1') ? '<span class="label label-primary"><i class="glyphicon glyphicon-ok"></i></span>' : '' }
				},
				{
					data: 'is_didanai',
					className: 'text-center',
					render: (data, type, row) => { return (data === '1') ? '<span class="label label-success"><i class="glyphicon glyphicon-ok"></i></span>' : null }
				},
				{
					data: 'dana_disetujui',
					className: 'text-right',
					render: $.fn.dataTable.render.number('.', ',', 0, '')
				},
				{ render: () => ''}
			],
			language: {
				searchPlaceholder: 'Judul, Ketua, PT ...'
			},
			searchDelay: 800,
			orderMulti: false,
			order: [ [3, 'desc'] ]
		});
	</script>
{/block}
