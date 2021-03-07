{extends file='site_layout.tpl'}
{block name='head'}
	<link rel="stylesheet" href="{base_url('../assets/css/dataTables.bootstrap.min.css')}" />
	<style>.table>thead>tr>th, .table>tbody>tr>td { font-size: 13px }</style>
{/block}
{block name='content'}
	<h2 class="page-header">Daftar Peserta Online Workshop</h2>

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
					<select name="meeting_id" class="form-control">
						<option value="">-- Pilih Sesi Meeting --</option>
						{if isset($meeting_set)}
							{foreach $meeting_set as $meeting}
								<option value="{$meeting->id}" {if $smarty.get.meeting_id == $meeting->id}selected{/if}>{$meeting->waktu_mulai|date_format:"%d %B %Y"} - {$meeting->topik}</option>
							{/foreach}
						{/if}
					</select>
				</div>
				<button type="submit" class="btn btn-default">
					Lihat
				</button>
			</form>
			
			{if !empty($smarty.get.kegiatan_id)}
				<table class="table table-bordered table-condensed table-striped" id="pesertaTable" style="display: none;">
					<thead>
						<tr>
							<th>Perguruan Tinggi</th>
                            <th>NIM</th>
							<th>Nama</th>
							<th style="width: 25px">Status Kehadiran</th>
							<th>Username</th>
							<th>Password</th>
						</tr>
					</thead>
					<tbody>
						{foreach $data_set as $data}
							<tr>
								<td>{$data->nama_pt}</td>
                                <td>{$data->nim}</td>
								<td>{$data->nama}</td>
								<td class="text-center">{if $data->kehadiran == true}<span class="label label-primary">Ya</span>{/if}</td>
								<td>{$data->username}</td>
								<td>{$data->password}</td>
							</tr>
						{/foreach}
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6">
								<a href="{site_url('online-workshop/excel-pendaftar')}?kegiatan_id={$smarty.get.kegiatan_id}&meeting_id={$smarty.get.meeting_id}">Download Excel</a>
							</td>
						</tr>
					</tfoot>
				</table>
			{/if}
		</div>
	</div>
	
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/jquery.dataTables.min.js')}"></script>
	<script src="{base_url('../assets/js/dataTables.bootstrap.min.js')}"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			
			$('#pesertaTable').DataTable({ stateSave: true });
			
			$('#pesertaTable').show();
			
			$('[name="kegiatan_id"]').on('change', function() {
				$('[name="meeting_id"]').html('<option value="">-- Pilih Sesi Meeting --</option>');
				$.getJSON('{site_url('online-workshop/data-meeting')}/' + $(this).val(), function(data) {
					$.each(data, function(key, val) {
						$('[name="meeting_id"]').append('<option value="' + val.id + '">' + val.waktu_mulai + ' - ' + val.topik + '</option>');
					});
				});
			});
			
		});
	</script>
{/block}
