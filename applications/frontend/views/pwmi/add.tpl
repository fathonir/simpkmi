{extends file='site_layout.tpl'}
{block name='content'}
	<h2 class="page-header">Daftar Dosen PWMI</h2>
	<div class="row">
		<div class="col-lg-12">
			<form class="form-horizontal" method="post" action="{current_url()}">
				<fieldset>
					<legend>Tambah Usulan Dosen PWMI</legend>
					<div class="form-group">
						<label class="control-label col-lg-2">NIDN</label>
						<div class="col-lg-3">
							<select name="program_studi_id" class="form-control" id="program_studi_id">
								<option>Pilih Program Studi</option>
								{foreach $program_studi_set as $ps}
									<option value="{$ps->id}">{$ps->nama}</option>
								{/foreach}
							</select>
						</div>
						<div class="col-lg-3">
							<div class="input-group">
								<input type="hidden" name="dosen_id" value="" id="dosen_id"/>
								<input type="text" name="nidn" class="form-control" id="nidn"/>
								<span class="input-group-btn">
									<button class="btn btn-default btn-cari"><i class="glyphicon glyphicon-search"></i> Cari</button>
								</span>
							</div>
							<span class="help-block" id="alert-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-2">Nama</label>
						<div class="col-lg-4">
							<p class="form-control-static" id="nama"></p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-2">Program Studi</label>
						<div class="col-lg-4">
							<p class="form-control-static" id="program_studi"></p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-4">
							<a href="{site_url('pwmi')}" class="btn btn-default">Kembali</a>
							<button type="submit" class="btn btn-primary">Simpan</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
{/block}
{block name='footer-script'}
	<script type="text/javascript">
		$(document).ready(function () {
			$('.btn-cari').on('click', function (e) {
				e.preventDefault();
				var ps_id = $('#program_studi_id').val();
				var nidn = $('#nidn').val();
				$.ajax({
					url: '{site_url('pwmi/cari-dosen')}', type: 'POST', dataType: 'json',
					data: {
						program_studi_id: ps_id, nidn: nidn
					},
					beforeSend: function() {
						$('#dosen_id').val('');
						$('#nama').html('');
						$('#program_studi').html('');
						$('#alert-block').html('');
					}
				}).done(function (data) {
					if (data.result) {
						$('#dosen_id').val(data.dosen.id);
						$('#nama').html(data.dosen.nama);
						$('#program_studi').html(data.dosen.nama_program_studi);
					} else {
						$('#alert-block').html(data.message);
					}
				});
			});
		});
	</script>
{/block}
