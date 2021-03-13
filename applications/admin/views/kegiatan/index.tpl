{extends file='site_layout.tpl'}
{block name='content'}
	<h1 class="page-header">Master Jadwal Kegiatan</h1>
	
	<div class="row">
		<div class="col-lg-12">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Tahun</th>
						<th>Program</th>
						<th>Maks Proposal</th>
						<th>Maks Peserta</th>
						<th>Status</th>
						<th>Registrasi</th>
						<th>Review</th>
						<th>Pengumuman</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{foreach $data_set as $data}
						<tr>
							<td class="text-center">{$data->tahun}</td>
							<td>{$data->nama_program}</td>
							<td class="text-center">{$data->proposal_per_pt}</td>
							<td class="text-center">{$data->peserta_per_pt}</td>
							<td class="text-center">
								{if $data->is_aktif == 1}<span class="label label-success">AKTIF</span>{else}<span class="label label-default">NONAKTIF</span>{/if}
							</td>
							{if $data->program_id == PROGRAM_WORKSHOP or $data->program_id == PROGRAM_STARTUP_MEETUP}
								<td colspan="3" class="text-center">
									<a href="{site_url("kegiatan/lokasi")}?kegiatan_id={$data->id}" class="btn btn-sm btn-default">Lokasi</a>
								</td>
                            {elseif $data->program_id == PROGRAM_ONLINE_WORKSHOP}
                                <td colspan="3" class="text-center">
									<a href="{site_url("kegiatan/meeting")}?kegiatan_id={$data->id}" class="btn btn-sm btn-default">Jadwal Meeting</a>
								</td>
							{else}
								<td>{$data->tgl_awal_upload|date_format:"%d %b %Y %T"} - {$data->tgl_akhir_upload|date_format:"%d %b %Y %T"}</td>
								<td>{$data->tgl_awal_review|date_format:"%d %b %Y %T"} - {$data->tgl_akhir_review|date_format:"%d %b %Y %T"}</td>
								<td>{$data->tgl_pengumuman|date_format:"%d %b %Y %T"}</td>
							{/if}
							<td>
								<a href="{site_url("kegiatan/update/{$data->id}")}" class="btn btn-xs btn-default">Edit</a>
								{if $data->program_id != PROGRAM_WORKSHOP}
									<a href="{site_url("kegiatan/syarat")}?kegiatan_id={$data->id}" class="btn btn-xs btn-warning">Syarat</a>
								{/if}
							</td>
						</tr>
					{/foreach}
				</tbody>
				{if count($data_set) > 0}
					<tfoot>
						<tr>
							<td colspan="8">
								<a href="{site_url('kegiatan/add/')}" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Tambah Kegiatan</a>
							</td>
						</tr>
					</tfoot>
				{/if}
			</table>
		</div>
	</div>
{/block}
