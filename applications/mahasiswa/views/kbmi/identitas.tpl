{extends file='site_layout.tpl'}
{block name='head'}
	<style type='text/css'>
		.form-group > div > p { display: inline-block; }
	</style>
{/block}
{block name='content'}
	<div class="panel panel-default">
		<div class="panel-body">
			
			<form action="{current_url()}" method="post" class="form-horizontal">
				<input type="hidden" name="proposal_id" value="{$proposal->id}" />
				<fieldset>
					<legend>Informasi Umum</legend>
					
					<div class="form-group">
						<label class="col-lg-2 control-label">Judul Usaha</label>
						<div class="col-lg-8 col-md-10">
							{if $proposal->is_submited == FALSE}
								<input type="text" name="judul" class="form-control" value="{$proposal->judul}" />
							{else}
								<p class="form-control-static">{$proposal->judul}</p>
							{/if}
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-2 control-label">Berjalan Sejak</label>
						<div class="col-lg-4 col-md-6">
							{if $proposal->is_submited == FALSE}
								<input type="text" name="mulai_berjalan" class="form-control" value="{$proposal->mulai_berjalan}" />
								<span class="help-block">Tulis informasi kapan usaha mulai dijalankan.
								Misal: Januari 2020</span>
							{else}
								<p class="form-control-static">{$proposal->mulai_berjalan}</p>
							{/if}
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-2 control-label">Kategori</label>
						<div class="col-lg-4 col-lg-6">
							<select name="kategori_id" class="form-control">
								<option value=""></option>
								{html_options options=$kategori_set selected=$proposal->kategori_id}
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-lg-2 control-label">Ketua</label>
						<div class="col-lg-8 col-md-10">
							<p class="form-control-static">{$mahasiswa->nama} - {$mahasiswa->nim} - {$mahasiswa->program_studi->nama}</p>
						</div>
					</div>
					
					{for $i = 1 to 4}
						{if isset($proposal->anggota_proposal_set[$i])}
							{$anggota = $proposal->anggota_proposal_set[$i]}
						{else}
							{$anggota = null}
						{/if}
						{$no_urut = $i + 1}
						<div class="form-group anggota-{$no_urut}" {if $i > count($proposal->anggota_proposal_set)}style="display: none"{/if}>
							<input type="hidden" name="anggota_id[{$no_urut}]" value="{if isset($anggota)}{$anggota->id}{/if}" />
							
							<label class="col-lg-2 col-md-2 control-label">Anggota</label>
							
							<div class="col-lg-8 col-md-10" id="anggota-view-{$no_urut}" {if !isset($anggota)}style="display: none"{/if}>
								<p class="form-control-static label-view-{$no_urut}" style="width: 75%">{if isset($anggota)}{$anggota->nama} - {$anggota->nim} - {$anggota->nama_program_studi}{/if}</p>
								{if $proposal->is_submited == FALSE}
									<button class="btn btn-default btn-ganti" data-no-urut="{$no_urut}">Ganti</button>
									<button class="btn btn-danger btn-hapus" data-no-urut="{$no_urut}"><i class="glyphicon glyphicon-remove"></i></button>
								{/if}
							</div>
							<div class="col-lg-8 col-md-10" id="anggota-tambah-{$no_urut}" {if isset($anggota)}style="display: none"{/if}>
								{if $proposal->is_submited == FALSE}
									<button class="btn btn-default btn-tambah" data-no-urut="{$no_urut}">Tambah</button>
								{/if}
							</div>
							
							<div class="col-lg-8 col-md-10" id="anggota-cari-{$no_urut}" style="display: none">
								<select name="program_studi_id_{$no_urut}" class="form-control" style="width: 50%; display: inline">
									{html_options options=$program_studi_set}
								</select>
								<input type="text" name="nim_{$no_urut}" class="form-control" style="width: 25%; display:inline" placeholder="NIM" />
								<button class="btn btn-info btn-cari" data-no-urut="{$no_urut}"><i class="glyphicon glyphicon-search"></i> Cari</button>
								<button class="btn btn-default btn-batal-cari" data-no-urut="{$no_urut}">Batal</button>
							</div>
							<div class="col-lg-8 col-md-10" id="anggota-confirm-{$no_urut}" style="display: none">
								<input type="hidden" name="mahasiswa_id_{$no_urut}" value="" />
								<p class="form-control-static label-confirm-{$no_urut}" style="width: 75%"></p>
								<button class="btn btn-primary btn-simpan" data-no-urut="{$no_urut}"><i class="glyphicon glyphicon-floppy-disk"></i> Simpan</button>
								<button class="btn btn-default btn-batal-confirm" data-no-urut="{$no_urut}">Batal</button>
							</div>
							<div class="col-lg-8 col-md-10" id="anggota-notfound-{$no_urut}" style="display: none">
								<p class="form-control-static text-danger label-notfound-{$no_urut}" style="width: 75%"></p>
								<button class="btn btn-default btn-cari-ulang" data-no-urut="{$no_urut}">Cari Ulang</button>
							</div>
						</div>
					{/for}
					
					<div class="form-group">
						<input type="hidden" name="dosen_id" value="{$proposal->dosen_id}" />
						<label class="col-lg-2 control-label">Dosen Pembimbing</label>
						
						<div class="col-lg-8 col-md-10" id="dosen-view">
							{if $proposal->dosen_id != ''}
								<p class="form-control-static label-dosen-view">{$proposal->dosen->nama} - {$proposal->dosen->nidn}</p>
							{else}
								<p class="form-control-static label-dosen-view" style="display:none"></p>
							{/if}
							{if $proposal->is_submited == FALSE}
								<button class="btn btn-default btn-dosen-ganti">Ubah</button>
							{/if}
						</div>
						<div class="col-lg-8 col-md-10" id="dosen-cari" style="display: none">
							<select name="program_studi_id_dosen" class="form-control" style="width: 50%; display: inline">
								{html_options options=$program_studi2_set}
							</select>
							<input type="text" name="nidn" class="form-control" style="width: 25%; display:inline" placeholder="NIDN" />
							<button class="btn btn-info btn-dosen-cari"><i class="glyphicon glyphicon-search"></i> Cari</button>
							<button class="btn btn-default btn-dosen-batal-cari">Batal</button>
						</div>
						<div class="col-lg-8 col-md-10" id="dosen-confirm" style="display: none">
							<input type="hidden" name="dosen_id_confirm" value="" />
							<p class="form-control-static label-dosen-confirm" style="width: 75%"></p>
							<button class="btn btn-primary btn-dosen-simpan"><i class="glyphicon glyphicon-floppy-disk"></i> Simpan</button>
							<button class="btn btn-default btn-dosen-batal-confirm">Batal</button>
						</div>
						<div class="col-lg-8 col-md-10" id="dosen-notfound" style="display: none">
							<p class="form-control-static text-danger label-dosen-notfound" style="width: 75%"></p>
							<button class="btn btn-default btn-dosen-cari-ulang">Cari Ulang</button>
						</div>
					</div>
				</fieldset>
							
				<div class="row">
					<div class="col-lg-6">
						<a class="btn btn-primary" href="{site_url('home')}">Kembali</a>
					</div>
					<div class="col-lg-6 text-right">
						<button class="btn btn-primary">Selanjutnya</button>
					</div>
				</div>
					
				{if $proposal->is_submited}
					<div class="row">
						<div class="col-lg-12">
							<p class="text-danger text-center">Proposal sudah disubmit, perubahan tidak akan disimpan dalam sistem.</p>
						</div>
					</div>
				{/if}
				
			</form>			

		</div>
	</div>
{/block}
{block name='footer-script'}
	<script type="text/javascript">
		$(document).ready(function() {
			$('.btn-tambah').on('click', function(e) {
				e.preventDefault();
				var no_urut = $(this).data('no-urut');
				$('#anggota-tambah-' + no_urut).hide();
				$('#anggota-cari-' + no_urut).show();
			});
			
			$('.btn-batal-cari').on('click', function(e) {
				e.preventDefault();
				var no_urut = $(this).data('no-urut');
				// if New Kembali ke tombol Tambah
				if ($('input[name="anggota_id['+no_urut+']"]').val() === '') {
					$('#anggota-tambah-' + no_urut).show();
					$('#anggota-cari-' + no_urut).hide();
				}
				// If Update Kembali ke tampilan view
				else {
					$('#anggota-cari-' + no_urut).hide();
					$('#anggota-view-' + no_urut).show();
				}
			});
			
			$('.btn-cari').on('click', function(e) {
				e.preventDefault();
				var no_urut = $(this).data('no-urut');
				var program_studi_id = $('select[name="program_studi_id_'+no_urut+'"]').val();
				var nim = $('input[name="nim_'+no_urut+'"]').val();
				
				$.ajax({
					url: "{site_url('kbmi/cari-mahasiswa')}",
					data: { program_studi_id: program_studi_id, nim: nim },
					type: 'POST',
					dataType: 'json'
				}).done(function(data) {
					if (data.result === true) {
						var mahasiswa = data.mahasiswa;
						$('.label-confirm-' + no_urut).html(mahasiswa.nama + ' - ' + mahasiswa.nim + ' - ' + mahasiswa.program_studi.nama);
						$('input[name="mahasiswa_id_'+no_urut+'"]').val(mahasiswa.id);
						$('#anggota-confirm-' + no_urut).show();
						$('#anggota-cari-' + no_urut).hide();
					}
					else {
						$('.label-notfound-' + no_urut).html(data.message);
						$('#anggota-notfound-' + no_urut).show();
						$('#anggota-cari-' + no_urut).hide();
					}
				});
			});
			
			$('.btn-cari-ulang').on('click', function(e) {
				e.preventDefault();
				var no_urut = $(this).data('no-urut');
				$('#anggota-notfound-' + no_urut).hide();
				$('#anggota-cari-' + no_urut).show();
			});
			
			$('.btn-batal-confirm').on('click', function(e) {
				e.preventDefault();
				var no_urut = $(this).data('no-urut');
				$('#anggota-confirm-' + no_urut).hide();
				$('#anggota-cari-' + no_urut).show();
			});
			
			$('.btn-simpan').on('click', function(e) {
				e.preventDefault();
				var no_urut = $(this).data('no-urut');
				
				// PROSES SIMPAN if New
				if ($('input[name="anggota_id['+no_urut+']"]').val() === '') {
					var mahasiswa_id = $('input[name="mahasiswa_id_'+no_urut+'"]').val();
					var proposal_id = $('input[name="proposal_id"]').val();
					$.ajax({
						url: "{site_url('kbmi/tambah-anggota')}", type: 'POST', dataType: 'json',
						data: { proposal_id: proposal_id, no_urut: no_urut, mahasiswa_id: mahasiswa_id }
					}).done(function(data) {
						if (data.result === true) {
							$('input[name="anggota_id['+no_urut+']"]').val(data.anggota_id);
							$('.label-view-' + no_urut).html($('.label-confirm-' + no_urut).html());
							// Baris Anggota dibawahnya dimunculkan
							if (no_urut < 5) {
								$('.anggota-' + (no_urut + 1)).show();
							}
						}
					});
				}
				// If Update
				else {
					var anggota_id = $('input[name="anggota_id['+no_urut+']"]').val();
					var mahasiswa_id = $('input[name="mahasiswa_id_'+no_urut+'"]').val();
					$.ajax({
						url: "{site_url('kbmi/update-anggota')}", type: 'POST', dataType: 'json',
						data: { anggota_id: anggota_id, mahasiswa_id: mahasiswa_id }
					}).done(function(data) {
						if (data.result === true) {
							$('.label-view-' + no_urut).html($('.label-confirm-' + no_urut).html());
						}
					});
				}
			
				$('#anggota-confirm-' + no_urut).hide();
				$('#anggota-view-' + no_urut).show();
			});
			
			$('.btn-ganti').on('click', function(e) {
				e.preventDefault();
				var no_urut = $(this).data('no-urut');
				$('input[name="nim_'+no_urut+'"]').val('');
				$('#anggota-view-' + no_urut).hide();
				$('#anggota-cari-' + no_urut).show();
			});
			
			$('.btn-hapus').on('click', function(e) {
				e.preventDefault();
				var no_urut = $(this).data('no-urut');
				var anggota_id = $('input[name="anggota_id['+no_urut+']"]').val();
				$.ajax({
					url: "{site_url('kbmi/delete-anggota')}", type: 'POST', dataType: 'json',
					data: { anggota_id: anggota_id } 
				}).done(function(data) {
					$('input[name="anggota_id['+no_urut+']"]').val(null);
					$('#anggota-tambah-' + no_urut).show();
					$('#anggota-view-' + no_urut).hide();
				});
			});
		});
	</script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.btn-dosen-ganti').on('click', function(e) {
				e.preventDefault();
				$('#dosen-view').hide();
				$('#dosen-cari').show();
			});
			
			$('.btn-dosen-batal-cari').on('click', function(e) {
				e.preventDefault();
				$('#dosen-view').show();
				$('#dosen-cari').hide();
			});
			
			$('.btn-dosen-cari').on('click', function(e) {
				e.preventDefault();
				var program_studi_id = $('select[name="program_studi_id_dosen"]').val();
                var nidn = $('input[name="nidn"]').val();
                
                $.ajax({
                    url: "{site_url('kbmi/cari-dosen')}",
                    data: { program_studi_id: program_studi_id, nidn: nidn },
                    type: 'POST', dataType: 'json'
                }).done(function(data) {
                    if (data.result === true) {
						var dosen = data.dosen;
                        $('.label-dosen-confirm').html(dosen.nama + ' - ' + dosen.nidn);
                        $('input[name="dosen_id_confirm"]').val(dosen.id);
                        $('#dosen-confirm').show();
                        $('#dosen-cari').hide();
					}
					else {
						$('.label-dosen-notfound').html(data.message);
						$('#dosen-notfound').show();
						$('#dosen-cari').hide();
					}
                });
			});
			
			$('.btn-dosen-simpan').on('click', function(e) {
				e.preventDefault();
				
				// PROSES SIMPAN
				var dosen_id = $('input[name="dosen_id_confirm"]').val();
				var proposal_id = $('input[name="proposal_id"]').val();
				$.ajax({
					url: "{site_url('kbmi/update-dosen')}",
					data: { proposal_id: proposal_id, dosen_id: dosen_id },
					type: 'POST', dataType: 'json'
				}).done(function(data) {
					if (data.result === true) {
						$('.label-dosen-view').html($('.label-dosen-confirm').html()).show();
						$('#dosen-confirm').hide();
						$('#dosen-view').show();
					}
				});
			});
			
			$('.btn-dosen-batal-confirm').on('click', function(e) {
				e.preventDefault();
				$('#dosen-cari').show();
				$('#dosen-confirm').hide();
			});
			
			$('.btn-dosen-cari-ulang').on('click', function(e) {
				e.preventDefault();
				$('#dosen-notfound').hide();
				$('#dosen-cari').show();
			});
		});
	</script>
{/block}
