{extends file='site_layout.tpl'}
{block name='content'}
	<div class="panel panel-default">
		<div class="panel-body">
			
			<form action="{current_url()}" method="post" class="form-horizontal">
				<input type="hidden" name="isian_ke" value="{$step}" />
				
				<fieldset>
					<legend class="text-center"><h2>{if $isian}{$isian->kelompok_isian->kelompok_isian}{/if}</h2></legend>

					{if $isian}
						<div class="form-group">
							<div class="col-lg-12">
								<label class="control-label" for="isian" style="text-align: left">
									{$isian->judul_isian}
								</label>
								{if $isian->keterangan != null}
									<p class="help-block">{$isian->keterangan}</p>
								{/if}
								{if $isian->jenis == 'text'}
									<input type="text" class="form-control" name="isian" required
										   placeholder="{if $isian->placeholder}{$isian->placeholder}{/if}"
										   value="{$isian_proposal->isian|htmlspecialchars}" />
								{/if}
								{if $isian->jenis == 'textarea'}
									<textarea class="form-control" name="isian" rows="5"
											  required>{$isian_proposal->isian|htmlspecialchars}</textarea>
								{/if}
								{if $isian->jenis == 'radio'}
									{foreach explode('|', $isian->radio_options) as $radio_option}
										<div class="radio">
											<label>
												<input type="radio" name="isian" required value="{$radio_option}"
													   {if $radio_option == $isian_proposal->isian}checked{/if}> {$radio_option}
											</label>
										</div>
									{/foreach}
								{/if}
							</div>
						</div>
					{/if}

					{if $step == 0}
						<h4>Menjadi pengusaha membutuhkan yang tekad kuat, mampu memunculkan ide-ide inovatif, sekaligus determinasi tinggi dalam
							menghadapi tantangan. Seorang angel investor dalam dunia digital menyatakan sebanyak 9 dari 10 start-up gagal ditengah
							jalan dalam membangun bisnis yang berkelanjutan (Neil Patel, 2016).
							Untuk itu dibutuhkan alasan yang kuat untuk menjadi pengusaha.</h4>
					{/if}
					
					<div class="form-group">
						<div class="col-lg-6">
							<input type="submit" class="btn btn-primary" name="tombol" value="Sebelumnya" />
						</div>
						<div class="col-lg-6 text-right">
							<input type="submit" class="btn btn-primary" name="tombol" value="Berikutnya" />
						</div>
					</div>
					
					{if $proposal->is_submited}
						<div class="row">
							<div class="col-lg-12">
								<p class="text-danger text-center">Proposal sudah disubmit, perubahan tidak akan disimpan dalam sistem.</p>
							</div>
						</div>
					{/if}
					
				</fieldset>
				
			</form>
			
		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src='{base_url('../assets/js/jquery.validate.min.js')}' type='text/javascript'></script>
	<script type='text/javascript'>
		$(document).ready(function() {
			$('form').validate();
		});
	</script>
{/block}
