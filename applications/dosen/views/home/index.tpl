{extends file='site_layout.tpl'}
{block name='content'}
	<div class="row">
		<div class="col-lg-12">
			<h2>Selamat datang dosen pendamping {$ci->session->user->dosen->nama}</h2>
			<h3>Program PWMI {$kegiatan->tahun} <small>Daftar Judul Didanai</small></h3>

			<div class="panel panel-default">
				<div class="panel-body">
					{if $tahapan_pendampingan}
						<p>Masa Pelaporan : <span class="label label-danger" style="font-size: 14px">{$tahapan_pendampingan->nama_tahapan}</span>
							<span class="badge">{$tahapan_pendampingan->tgl_awal_laporan_dmy}</span> sampai dengan
							<span class="badge">{$tahapan_pendampingan->tgl_akhir_laporan_dmy}</span>
						</p>
					{else}
						<p>Masa Pelaporan : <strong>Belum Masa Pelaporan</strong></p>
					{/if}
					<table class="table table-striped">
						<table class="table table-striped">
							<thead>
							<tr>
								<th style="width: 1%">Tahun</th>
								<th>Judul</th>
								<th>Nama Ketua</th>
								<th>Dosen Pembimbing</th>
								<th class="text-center">Status Pelaporan</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							{foreach $proposal_set as $proposal}
							<tr>
								<td>{$proposal->tahun}</td>
								<td>{$proposal->judul}</td>
								<td>{$proposal->ketua} ({$proposal->nim_ketua})</td>
								<td>{$proposal->pembimbing} ({$proposal->nidn})</td>
								<td class="text-center">
									{if $proposal->laporan_pendampingan}
										<span class="label label-success">Sudah</span>
									{else}
										<span class="label label-default">Belum</span>
									{/if}
								</td>
								<td>
									{if $tahapan_pendampingan}
										<a class="btn btn-sm btn-primary" href="{site_url('pwmi/view')}/{$proposal->id}">Update</a>
									{/if}
								</td>
							</tr>
							{/foreach}
							</tbody>
						</table>
					</table>
				</div>
			</div>
		</div>
	</div>
{/block}
