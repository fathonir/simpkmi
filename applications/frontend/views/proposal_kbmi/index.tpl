{extends file='site_layout.tpl'}
{block name='head'}
	<link rel="stylesheet" href="{base_url('../assets/css/dataTables.bootstrap.min.css')}" />
	<style type="text/css">
		.table { font-size: 14px; }
		.table > tbody > tr > td:last-child { width: 1%; white-space: nowrap; }
		td > p { margin-bottom: 0 }
		p.judul { font-weight: bold; }
		p.sub-judul { font-size: 12px; }
	</style>
{/block}
{block name='content'}
	<h2 class="page-header">Daftar Proposal KBMI {if $kegiatan != NULL}{$kegiatan->tahun}{/if}</h2>
	<div class="row">
		<div class="col-lg-12">

			<table class="table table-bordered table-hover" id="table">
				<thead>
					<tr>
						<th></th>
						<th>Usulan</th>
						<th>Kelengkapan Isian</th>
						<th>Status</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					{foreach $data_set as $data}
						<tr>
							<td>{$data@index + 1}</td>
							<td>
								<p class="judul">{$data->judul}</p>
								<p class="sub-judul">{$data->nama} - {$data->nim} - {$data->nama_program_studi}</p>
								<p class="sub-judul">Pembimbing: {$data->nama_dosen}</p>
							</td>
							<td class="text-center">{$data->jumlah_terisi} dari {$data->jumlah_isian}</td>
							<td class="text-center">
								{if $data->is_reviewed == TRUE}
									<label class="label label-primary">Direview</label>
								{elseif $data->is_submited == TRUE}
									<label class="label label-success">Submit</label>
								{else}
									<label class="label label-default">Pengisian Form</label>
								{/if}
							</td>
							<td>
								<form action="{site_url('proposal-kbmi/send-login')}" method="post" style="display: inline">
									<input type="submit" value="Kirim Login" class="btn btn-xs btn-info" />
									<input type="hidden" name="mahasiswa_id" value="{$data->mahasiswa_id}" />
								</form>
								<a href="{site_url('proposal-kbmi/update')}/{$data->id}" class="btn btn-xs btn-success">Edit</a>
								{if $waktu_sekarang < $kegiatan->tgl_akhir_upload}
									{if $data->is_submited == 0}{* Jika belum disubmit, bisa dihapus *}
										<a href="{site_url('proposal-kbmi/delete')}/{$data->id}" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
									{elseif $data->is_reviewed == 0}{* Jika belum di review, bisa dibatalkan *}
										<a href="{site_url('proposal-kbmi/cancel-submit')}/{$data->id}" class="btn btn-xs btn-default" style="margin-top: 5px">Batalkan Submit</a>
									{/if}
								{/if}
							</td>
						</tr>
					{foreachelse}
						<tr>
							<td colspan="5">Data kosong</td>
						</tr>
					{/foreach}
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6">
							{if $kegiatan != null}
								{if $waktu_sekarang < $kegiatan->tgl_awal_upload}
									Masa pengusulan proposal belum dimulai
								{elseif $kegiatan->tgl_akhir_upload < $waktu_sekarang}
									Masa pengusulan proposal sudah selesai
								{elseif $kegiatan->proposal_per_pt > count($data_set)}
									<a href="{site_url('proposal-kbmi/create')}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Tambah</a>
								{/if}
							{else}
								Tidak ada kegiatan aktif.
							{/if}
						</td>
					</tr>
				</tfoot>
			</table>

			<p style="font-size: small">* Login mahasiswa bisa dilihat di menu Edit apabila email tidak terkirim melalui menu Kirim Login.</p>

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
