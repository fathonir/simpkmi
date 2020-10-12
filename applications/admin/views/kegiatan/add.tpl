{extends file='site_layout.tpl'}
{block name='head'}
{/block}
{block name='content'}
	<h1 class="page-header">Tambah Kegiatan</h1>

	<div class="row">
		<div class="col-lg-12">

			<form class="form-horizontal" method="post" action="{current_url()}" id="addKegiatanForm">
				<fieldset>
					
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tahun">Tahun Kegiatan</label>  
						<div class="col-md-1">
							<input id="tahun" name="tahun" placeholder="{date('Y')}" class="form-control input-md" type="text" value="">
						</div>
					</div>

					<!-- Select input -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="nama_program">Program</label>  
						<div class="col-md-5">
							<select name="program_id" class="form-control">
								<option value="">-- Pilih Program --</option>
								{foreach $program_set as $program}
									<option value="{$program->id}">{$program->nama_program}</option>
								{/foreach}
							</select>
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group proposal_per_pt">
						<label class="col-md-2 control-label" for="proposal_per_pt">Jumlah Proposal per PT</label>  
						<div class="col-md-1">
							<input id="proposal_per_pt" name="proposal_per_pt" placeholder="0" class="form-control input-md" type="text" value="">
						</div>
						<div class="col-md-3">
							<p>Di isi hanya untuk program PBBT, KBMI, &amp; Expo KMI. </p>
						</div>
					</div>
					
					<!-- Text input-->
					<div class="form-group peserta_per_pt">
						<label class="col-md-2 control-label" for="peserta_per_pt">Jumlah Peserta per PT</label>  
						<div class="col-md-1">
							<input id="peserta_per_pt" name="peserta_per_pt" placeholder="0" class="form-control input-md" type="text" value="">
						</div>
						<div class="col-md-3">
							<p>Di isi hanya untuk program Workshop Kewirausahaan</p>
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_awal_upload">Tanggal Awal Upload</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="awal_upload_" year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="awal_upload_HMS" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_akhir_upload">Tanggal Akhir Upload</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="akhir_upload_" year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="akhir_upload_HMS" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_awal_review">Tanggal Awal Review</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="awal_review_" year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="awal_review_HMS" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_akhir_review">Tanggal Akhir Review</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="akhir_review_" year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="akhir_review_HMS" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_pengumuman">Tanggal Pengumuman</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="pengumuman_" year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="pengumuman_HMS" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_awal_upload_kemajuan">Tgl Awal Upload Kemajuan</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="awal_upload_kemajuan_" year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="awal_upload_kemajuan_HMS" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tgl_akhir_upload_kemajuan">Tgl Akhir Upload Kemajuan</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="akhir_upload_kemajuan_" year_as_text=true all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" name="akhir_upload_kemajuan_HMS" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
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
							
					<!-- Button -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="singlebutton"></label>
						<div class="col-md-6">
							<p>* Tanggal jadwal untuk program Workshop Kewirausahaan di tentukan ketika
							melakukan setting lokasi di menu Lokasi Workshop Kewirausahaan</p>
						</div>
					</div>

				</fieldset>
			</form>

		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/jquery.validate.min.js')}" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#addKegiatanForm').validate({
				rules: {
					tahun: 'required',
					program_id: 'required',
					proposal_per_pt: 'required',
					peserta_per_pt: 'required',
					awal_upload_time: 'required',
					akhir_upload_time: 'required',
					awal_review_time: 'required',
					akhir_review_time: 'required',
					pengumuman_time: 'required'
				},
				messages: {
					tahun: 'Required',
					program_id: 'Required',
					proposal_per_pt: 'Required. Min: 0',
					peserta_per_pt: 'Required. Min: 0',
					awal_upload_time: 'Required',
					akhir_upload_time: 'Required',
					awal_review_time: 'Required',
					akhir_review_time: 'Required',
					pengumuman_time: 'Required'
				},
				errorElement: "em",
				errorPlacement: function (error, element) {
					error.addClass("help-block");
					error.insertAfter(element);
				},
				highlight: function (element, errorClass, validClass) {
					$(element).parent().addClass("has-error").removeClass("has-success");
				},
				unhighlight: function (element, errorClass, validClass) {
					$(element).parent().addClass("has-success").removeClass("has-error");
				}
			});
		});
	</script>
{/block}
