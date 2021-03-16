{extends file='site_layout.tpl'}
{block name='head'}
	<link rel="stylesheet" href="{base_url('../assets/css/dataTables.bootstrap.min.css')}"/>
	<style type="text/css">
		.table {
			font-size: 14px;
		}

		.table > tbody > tr > td:last-child {
			width: 1%;
			white-space: nowrap;
		}
	</style>
{/block}
{block name='content'}
	<h2 class="page-header">Daftar Dosen PWMI</h2>
	{if isset($kegiatan)}
		<div class="alert alert-info" role="alert">
			Jadwal pengusulan PWMI Tahun {$kegiatan->tahun} mulai
			<strong>{$kegiatan->tgl_awal_upload|date_format:"%d %B %Y %H:%M"}
				s/d {$kegiatan->tgl_akhir_upload|date_format:"%d %B %Y %H:%M"} WIB</strong>
		</div>
	{/if}
	<div class="row">
		<div class="col-lg-12">
			<table class="table table-bordered">
				<thead>
				<tr>
					<th>Tahun</th>
					<th>NIDN</th>
					<th>Nama</th>
					<th>Program Studi</th>
					<th>Syarat</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				{foreach $data_set as $data}
					<tr>
						<td>{$data->tahun}</td>
						<td>{$data->nidn}</td>
						<td>{$data->nama}</td>
						<td>{$data->nama_program_studi}</td>
						<td>
							{if $data->jumlah_syarat <= $data->jumlah_upload}
								<span class="label label-success">Lengkap</span>
							{else}
								<span class="label label-default">Belum Lengkap</span>
							{/if}
						</td>
						<td>
							<a href="{site_url('pwmi/syarat')}/{$data->id}" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-upload"></i> Syarat</a>
							{if $kegiatan != null}
								{if $kegiatan->tahun == $data->tahun and $is_in_jadwal}
									<a href="{site_url('pwmi/delete')}/{$data->id}" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Hapus</a>
								{/if}
							{/if}
						</td>
					</tr>
				{/foreach}
				</tbody>
				<tfoot>
				<tr>
					<td colspan="6">
						<a href="{site_url('pwmi/add')}" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Tambah Dosen PWMI</a>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
{/block}
