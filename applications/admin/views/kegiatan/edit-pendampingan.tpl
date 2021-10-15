{extends file='site_layout.tpl'}
{block name='content'}
	<h1 class="page-header">Edit Jadwal Pendampingan KBMI</h1>

	<div class="row">
		<div class="col-lg-12">
			<form class="form-horizontal" method="post" action="{current_url()}" id="addMeetingForm">
				<fieldset>

					<!-- Static -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="kegiatan">Kegiatan</label>
						<div class="col-md-10">
							<p class="form-control-static">{$kegiatan->nama_program} {$kegiatan->tahun}</p>
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="nama">Nama Tahapan</label>
						<div class="col-md-4">
							<input id="nama" name="nama_tahapan" placeholder="" class="form-control input-md" type="text" value="{$data->nama_tahapan}">
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="awal_laporan">Awal Laporan</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="awal_laporan_"
							time=$data->tgl_awal_laporan year_as_text=TRUE
							all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="awal_laporan_time" value="{$data->tgl_awal_laporan|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="akhir_laporan">Akhir Laporan</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="akhir_laporan_"
							time=$data->tgl_akhir_laporan year_as_text=TRUE
							all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="akhir_laporan_time" value="{$data->tgl_akhir_laporan|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Button -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="singlebutton"></label>
						<div class="col-md-4">
							<a href="{site_url('kegiatan/pendampingan')}?kegiatan_id={$data->kegiatan_id}" class="btn btn-default">Kembali</a>
							<input type="submit" value="Simpan" class="btn btn-primary"/>
							<input type="hidden" name="kegiatan_id" value="{$data->kegiatan_id}" />
						</div>
					</div>

				</fieldset>
			</form>
		</div>
	</div>
{/block}
