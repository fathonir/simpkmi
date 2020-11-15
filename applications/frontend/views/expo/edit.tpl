{extends file='site_layout.tpl'}
{block name='head'}
	<style>.table { font-size: 14px; }</style>
{/block}
{block name='content'}
	<h2 class="page-header">Edit usulan untuk ikut Expo KMI</h2>
	<div class="row">
		<div class="col-lg-12">

			<p>
				<a href="{site_url('expo')}">Kembali ke daftar usulan Expo</a>
			</p>
			
			{if isset($success)}
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					{$success['message']}
				</div>
			{/if}
			
			{if isset($error)}
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					{$error['message']}
				</div>
			{/if}
			
			<form action="{current_url()}" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="kegitan_id" value="{$proposal->is_kmi_award}" />
				<input type="hidden" name="is_kmi_award" value="1" />
				<fieldset>
					<legend>Detail Usaha</legend>

					<div class="form-group">
						<label for="kategori" class="col-lg-2 control-label">Kategori</label>
						<div class="col-lg-4"><p class="form-control-static">KMI Award Umum</p></div>
					</div>
					
					<div class="form-group" style="display: none">
						<label for="is_kmi_award" class="col-lg-2 control-label">Usulkan KMI Award</label>
						<div class="col-lg-4">
							<select name="is_kmi_award" class="form-control" {if $has_kmi_award == TRUE and $proposal->is_kmi_award == 0}disabled{/if}>
								<option value="0" {set_select('is_kmi_award', '0', ($proposal->is_kmi_award == 0))}>Tidak</option>
								<option value="1" {set_select('is_kmi_award', '1', ($proposal->is_kmi_award == 1))}>Ya</option>
							</select>
						</div>
					</div>
					
					<div class="form-group {if form_error('judul')}has-error{/if}">
						<label for="judul" class="col-lg-2 control-label">Nama Usaha</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="judul" value="{set_value('judul', $proposal->judul)}">
						</div>
					</div>

					<div class="form-group">
						<label for="kategori" class="col-lg-2 control-label">Sub-Kategori</label>
						<div class="col-lg-4">
							{$kategori_id=set_value('kategori_id', $proposal->kategori_id)}
							<select name="kategori_id" class="form-control">
								{html_options options=$kategori_set selected=$kategori_id}
							</select>
						</div>
					</div>

					<div class="form-group {if form_error('email')}has-error{/if}">
						<label for="email" class="col-lg-2 control-label">Email Usaha</label>
						<div class="col-lg-4">
							<input name="email" type="text" class="form-control" name="email" value="{set_value('email', $proposal->email)}" />
							{if form_error('email')}<span class="help-block text-danger">{form_error('email')}</span>{/if}
						</div>
					</div>

					<div class="form-group {if form_error('headline')}has-error{/if}">
						<label for="headline" class="col-lg-2 control-label">Headline</label>
						<div class="col-lg-10">
							<input name="headline" type="text" class="form-control" name="headline" value="{set_value('headline', $proposal->headline)}"/>
						</div>
					</div>

					<div class="form-group {if form_error('deskripsi')}has-error{/if}">
						<label for="deskripsi" class="col-lg-2 control-label">Deskripsi</label>
						<div class="col-lg-6">
							<textarea class="form-control" rows="2" name="deskripsi">{set_value('deskripsi', $proposal->deskripsi)}</textarea>
						</div>
					</div>

					<div class="form-group">
						<label for="link_web" class="col-lg-2 control-label">Website</label>
						<div class="col-lg-4">
							<input name="link_web" type="text" class="form-control" name="link_web" value="{set_value('link_web', $proposal->link_web)}"/>
							{if form_error('link_web')}<span class="help-block text-danger">{form_error('link_web')}</span>{/if}
						</div>
						<label for="link_instagram" class="col-lg-2 control-label">Instagram</label>
						<div class="col-lg-4">
							<input name="link_instagram" type="text" class="form-control" name="link_instagram" value="{set_value('link_instagram', $proposal->link_instagram)}"/>
							{if form_error('link_instagram')}<span class="help-block text-danger">{form_error('link_instagram')}</span>{/if}
						</div>
					</div>

					<div class="form-group">
						<label for="link_twitter" class="col-lg-2 control-label">Twitter</label>
						<div class="col-lg-4">
							<input name="link_twitter" type="text" class="form-control" name="link_twitter" value="{set_value('link_twitter', $proposal->link_twitter)}"/>
							{if form_error('link_twitter')}<span class="help-block text-danger">{form_error('link_twitter')}</span>{/if}
						</div>
						<label for="link_youtube" class="col-lg-2 control-label">Youtube</label>
						<div class="col-lg-4">
							<input name="link_youtube" type="text" class="form-control" name="link_youtube" value="{set_value('link_youtube', $proposal->link_youtube)}"/>
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
							<input type="text" class="form-control" name="nim_anggota_1" placeholder="NIM / NPM" value="{set_value('nim_anggota_1', $proposal->anggota_proposal_set[0]->nim)}">
						</div>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_anggota_1" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_1', $proposal->anggota_proposal_set[0]->nama)}">
						</div>
					</div>
						
					<div class="form-group {if isset($error_anggota_2)}has-error{/if}">
						<label class="col-lg-2 control-label">Anggota</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_2" placeholder="NIM / NPM" value="{set_value('nim_anggota_2', $proposal->anggota_proposal_set[1]->nim)}">
						</div>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_anggota_2" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_2', $proposal->anggota_proposal_set[1]->nama)}">
						</div>
					</div>
						
					<div class="form-group {if isset($error_anggota_3)}has-error{/if}">
						<label class="col-lg-2 control-label">Anggota</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_3" placeholder="NIM / NPM" value="{set_value('nim_anggota_3', $proposal->anggota_proposal_set[2]->nim)}">
						</div>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_anggota_3" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_3', $proposal->anggota_proposal_set[2]->nama)}">
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-2 control-label">Anggota</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_4" placeholder="NIM / NPM" value="{set_value('nim_anggota_4', $proposal->anggota_proposal_set[3]->nim)}">
						</div>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_anggota_4" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_4', $proposal->anggota_proposal_set[3]->nama)}">
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-2 control-label">Anggota</label>
						<div class="col-lg-2">
							<input type="text" class="form-control" name="nim_anggota_5" placeholder="NIM / NPM" value="{set_value('nim_anggota_5', $proposal->anggota_proposal_set[4]->nim)}">
						</div>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_anggota_5" placeholder="Nama Mahasiswa" value="{set_value('nama_anggota_5', $proposal->anggota_proposal_set[4]->nama)}">
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

							<div class="form-group fg-upload-{$syarat->id} {if isset($syarat->upload_error_msg)}has-error{/if}"
								 {if $syarat->file_proposal_id != ''}style="display: none"{/if}>
								<label class="col-lg-2 control-label">{$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
								<div class="col-lg-10">
									<input type="file" name="file_syarat_{$syarat->id}" class="filestyle" />
									<span class="help-block">{$syarat->keterangan} ({$syarat->allowed_types}) - Maks. {$syarat->max_size}MB</span>
									{if isset($syarat->upload_error_msg)}
										<span class="help-block">ERROR: {$syarat->upload_error_msg}</span>
									{/if}
									{if $syarat->file_proposal_id != ''}
										<a class="btn btn-sm btn-default btn-cancel-edit" data-id="{$syarat->id}">Batal</a>
									{/if}
								</div>
							</div>

							<div class="form-group fg-view-{$syarat->id}" {if $syarat->file_proposal_id == ''}style="display: none"{/if}>
								<label class="col-lg-2 control-label">{$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
								<div class="col-lg-10">
									<p class="form-control-static">
										<a href="{base_url()}/../../upload/lampiran/{$syarat->nama_file}" target="_blank">{$syarat->nama_asli}</a>
										{if $proposal->is_submited == FALSE}
											<a class="btn btn-xs btn-default btn-edit" data-id="{$syarat->id}" title="Ubah file"><i class="glyphicon glyphicon-edit"></i> Ubah</a>
										{/if}
									</p>
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

		$('.btn-cancel-edit').on('click', function() {
			var syarat_id = $(this).data('id');

			$('.fg-view-' + syarat_id).show();
			$('.fg-upload-' + syarat_id).hide();

		});
	</script>
{/block}
