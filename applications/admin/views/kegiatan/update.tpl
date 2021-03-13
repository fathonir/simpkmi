{extends file='site_layout.tpl'}
{block name='head'}
{/block}
{block name='content'}
	<h1 class="page-header">Update Kegiatan</h1>

	<div class="row">
		<div class="col-lg-12">

			<form class="form-horizontal" action="{current_url()}" method="post">
				<input type="hidden" name="id" value="{$data->id}" />
				<fieldset>

					<!-- Label-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="proposal_per_pt">Program</label>  
						<div class="col-md-5">
							<label class="form-control-static">{$data->nama_program} Tahun {$data->tahun}</label>
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="proposal_per_pt">Jumlah Proposal per PT</label>  
						<div class="col-md-1">
							<input id="proposal_per_pt" name="proposal_per_pt" placeholder="" class="form-control input-md" type="text" value="{set_value('proposal_per_pt', $data->proposal_per_pt)}">
						</div>
						<div class="col-md-3">
							<p>Di isi hanya untuk program PBBT, KBMI, &amp; Expo KMI. </p>
						</div>
					</div>
						
					<!-- Text input-->
					<div class="form-group peserta_per_pt">
						<label class="col-md-2 control-label" for="peserta_per_pt">Jumlah Peserta per PT</label>  
						<div class="col-md-1">
							<input id="peserta_per_pt" name="peserta_per_pt" placeholder="0" class="form-control input-md" type="text" value="{set_value('peserta_per_pt', $data->peserta_per_pt)}">
						</div>
						<div class="col-md-3">
							<p>Di isi hanya untuk program Workshop Kewirausahaan</p>
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_awal_upload">Tanggal Awal Registrasi</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="awal_upload_" time=$data->tgl_awal_upload year_as_text=TRUE all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="awal_upload_HMS" value="{$data->tgl_awal_upload|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_akhir_upload">Tanggal Akhir Registrasi</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="akhir_upload_" time=$data->tgl_akhir_upload year_as_text=TRUE all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="akhir_upload_HMS" value="{$data->tgl_akhir_upload|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_awal_review">Tanggal Awal Review</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="awal_review_" time=$data->tgl_awal_review year_as_text=TRUE all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="awal_review_HMS" value="{$data->tgl_awal_review|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_akhir_review">Tanggal Akhir Review</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="akhir_review_" time=$data->tgl_akhir_review year_as_text=TRUE all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="akhir_review_HMS" value="{$data->tgl_akhir_review|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_pengumuman">Tanggal Pengumuman</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="pengumuman_" time=$data->tgl_pengumuman year_as_text=TRUE all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="pengumuman_HMS" value="{$data->tgl_pengumuman|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_awal_upload_kemajuan">Tgl Awal Upload Kemajuan</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="awal_upload_kemajuan_" time=$data->tgl_awal_upload_kemajuan year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="awal_upload_kemajuan_HMS" value="{$data->tgl_awal_upload_kemajuan|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_akhir_upload_kemajuan">Tgl Akhir Upload Kemajuan</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="akhir_upload_kemajuan_" time=$data->tgl_akhir_upload_kemajuan year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="akhir_upload_kemajuan_HMS" value="{$data->tgl_akhir_upload_kemajuan|date_format:"%H:%M:%S"}" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>
						
					<!-- Select Box -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="is_aktif">Status Kegiatan</label>
						<div class="col-md-3">
							<select name="is_aktif" class="form-control input-md">
							{html_options options=$aktif_set selected=$data->is_aktif}
							</select>
						</div>
					</div>
					
					<!-- Button -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="singlebutton"></label>
						<div class="col-md-4">
							<a href="{site_url('kegiatan')}" class="btn btn-default">Kembali</a>
							<input type="submit" value="Simpan" class="btn btn-primary"/>
						</div>
					</div>

				</fieldset>
			</form>

		</div>
	</div>
{/block}
{block name='footer-script'}

{/block}
