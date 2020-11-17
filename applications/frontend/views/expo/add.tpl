{extends file='site_layout.tpl'}
{block name='head'}
	<style>.table { font-size: 14px; }</style>
{/block}
{block name='content'}
	<h2 class="page-header">Tambah usulan untuk ikut Expo KMI</h2>
	<div class="row">
		<div class="col-lg-12">

			<p><a href="{site_url('expo')}">Kembali ke daftar usulan Expo</a></p>
			
			<form action="{current_url()}?kegiatan_id={$smarty.get.kegiatan_id}" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="kegitan_id" value="{$smarty.get.kegiatan_id}" />
				<fieldset>
					<legend>Detail Usaha</legend>

					<div class="form-group">
						<label for="kategori" class="col-lg-2 control-label">Kategori</label>
						<div class="col-lg-4"><p class="form-control-static">KMI Award Kategori Umum</p></div>
					</div>
					
					<div class="form-group">
						<label for="is_kmi_award" class="col-lg-2 control-label">Usulkan KMI Award</label>
						<div class="col-lg-2">
							<select name="is_kmi_award" class="form-control">
								<option value="0" {set_select('is_kmi_award', '0')}>Tidak</option>
								<option value="1" {set_select('is_kmi_award', '1')}>Ya</option>
							</select>
						</div>
					</div>
					
					<div class="form-group {if form_error('judul')}has-error{/if}">
						<label for="judul" class="col-lg-2 control-label">Nama Usaha</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="judul" value="{set_value('judul')}">
						</div>
					</div>

					<div class="form-group">
						<label for="kategori" class="col-lg-2 control-label">Sub-Kategori</label>
						<div class="col-lg-4">
							{$kategori_id=set_value('kategori_id')}
							<select name="kategori_id" class="form-control">
								{html_options options=$kategori_set selected=$kategori_id}
							</select>
						</div>
					</div>

					<div class="form-group {if form_error('email')}has-error{/if}">
						<label for="email" class="col-lg-2 control-label">Email Usaha</label>
						<div class="col-lg-4">
							<input name="email" type="text" class="form-control" name="email" value="{set_value('email')}" />
							{if form_error('email')}<span class="help-block text-danger">{form_error('email')}</span>{/if}
						</div>
					</div>

					<div class="form-group {if form_error('headline')}has-error{/if}">
						<label for="headline" class="col-lg-2 control-label">Headline</label>
						<div class="col-lg-10">
							<input name="headline" type="text" class="form-control" name="headline" value="{set_value('headline')}"/>
						</div>
					</div>

					<div class="form-group {if form_error('deskripsi')}has-error{/if}">
						<label for="deskripsi" class="col-lg-2 control-label">Deskripsi</label>
						<div class="col-lg-6">
							<textarea class="form-control" rows="2" name="deskripsi">{set_value('deskripsi')}</textarea>
						</div>
					</div>

					<div class="form-group {if form_error('link_web') or form_error('link_instagram')}has-error{/if}">
						<label for="link_web" class="col-lg-2 control-label">Website</label>
						<div class="col-lg-4">
							<input name="link_web" type="text" class="form-control" name="link_web" value="{set_value('link_web')}"/>
							{if form_error('link_web')}<span class="help-block text-danger">{form_error('link_web')}</span>{/if}
						</div>
						<label for="link_instagram" class="col-lg-2 control-label">Instagram</label>
						<div class="col-lg-4">
							<input name="link_instagram" type="text" class="form-control" name="link_instagram" value="{set_value('link_instagram')}"/>
							{if form_error('link_instagram')}<span class="help-block text-danger">{form_error('link_instagram')}</span>{/if}
						</div>
					</div>

					<div class="form-group {if form_error('link_twitter') or form_error('link_youtube')}has-error{/if}">
						<label for="link_twitter" class="col-lg-2 control-label">Twitter</label>
						<div class="col-lg-4">
							<input name="link_twitter" type="text" class="form-control" name="link_twitter" value="{set_value('link_twitter')}"/>
							{if form_error('link_twitter')}<span class="help-block text-danger">{form_error('link_twitter')}</span>{/if}
						</div>
						<label for="link_youtube" class="col-lg-2 control-label">Youtube</label>
						<div class="col-lg-4">
							<input name="link_youtube" type="text" class="form-control" name="link_youtube" value="{set_value('link_youtube')}"/>
							{if form_error('link_youtube')}<span class="help-block text-danger">{form_error('link_youtube')}</span>{/if}
						</div>
					</div>

					<div class="form-group">
						<div class="col-lg-10 col-lg-offset-2">
							<p>Untuk Website, Instagram, Twitter, dan Youtube harap dimasukkan full linknya.</p>
						</div>
					</div>

					<div class="form-group {if isset($error_anggota_1)}has-error{/if}">
						<label class="col-lg-2 control-label">Ketua Pengusul</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_1" placeholder="NIM / NPM" value="{set_value('nim_anggota_1')}">
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="nama_anggota_1" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_1')}">
						</div>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="hp_anggota_1" placeholder="No HP" value="{set_value('hp_anggota_1')}">
						</div>
					</div>

					<div class="form-group {if isset($error_anggota_2)}has-error{/if}">
						<label class="col-lg-2 control-label">Anggota</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_2" placeholder="NIM / NPM" value="{set_value('nim_anggota_2')}">
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="nama_anggota_2" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_2')}">
						</div>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="hp_anggota_2" placeholder="No HP" value="{set_value('hp_anggota_2')}">
						</div>
					</div>
						
					<div class="form-group {if isset($error_anggota_3)}has-error{/if}">
						<label class="col-lg-2 control-label">Anggota</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_3" placeholder="NIM / NPM" value="{set_value('nim_anggota_3')}">
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="nama_anggota_3" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_3')}">
						</div>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="hp_anggota_3" placeholder="No HP" value="{set_value('hp_anggota_3')}">
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-2 control-label">Anggota</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_4" placeholder="NIM / NPM" value="{set_value('nim_anggota_4')}">
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="nama_anggota_4" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_4')}">
						</div>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="hp_anggota_4" placeholder="No HP" value="{set_value('hp_anggota_4')}">
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-2 control-label">Anggota</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_5" placeholder="NIM / NPM" value="{set_value('nim_anggota_5')}">
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="nama_anggota_5" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_5')}">
						</div>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="hp_anggota_5" placeholder="No HP" value="{set_value('hp_anggota_5')}">
						</div>
					</div>

					<div class="form-group">
						<div class="col-lg-10 col-lg-offset-2">
							<p>Anggota minimum 3 orang (termasuk ketua kelompok)</p>
						</div>
					</div>

				</fieldset>

				<fieldset>
					{if count($syarat_set) > 0}
						<legend>Upload</legend>
					{/if}

					{foreach $syarat_set as $syarat}
						{if $syarat->is_aktif}
							<div class="form-group fg-upload-{$syarat->id} {if isset($syarat->upload_error_msg)}has-error{/if}">
								<label class="col-lg-2 control-label">{$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
								<div class="col-lg-10">
									<input type="file" name="file_syarat_{$syarat->id}" class="filestyle" />
									<span class="help-block">{$syarat->keterangan} ({$syarat->allowed_types}) - Maks. {$syarat->max_size}MB</span>
									{if isset($syarat->upload_error_msg)}
										<span class="help-block">ERROR: {$syarat->upload_error_msg}</span>
									{/if}
								</div>
							</div>
						{/if}
					{/foreach}

					<div class="form-group">
						<div class="col-lg-2"></div>
						<div class="col-lg-10">
							<input type="submit" value="Simpan" class="btn btn-primary"/>
							<a href="{site_url('expo')}" class="btn btn-default">Kembali</a>
						</div>
					</div>

				</fieldset>

			</form>

		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/bootstrap-filestyle.min.js')}" type='text/javascript'></script>
	<script type="text/javascript">
		$(':file').filestyle();

		$('.btn-edit').on('click', function() {
			var syarat_id = $(this).data('id');

			$('.fg-view-' + syarat_id).hide();
			$('.fg-upload-' + syarat_id).show();

		});
	</script>
{/block}
