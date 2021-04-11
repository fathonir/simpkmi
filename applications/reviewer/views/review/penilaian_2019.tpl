{extends file='site_layout.tpl'}
{block name='head'}
	<style>
		h2.page-header { margin-bottom: 0 }
		h4.judul { color: #555555;}
		.table { font-size: 14px; }
		/* .form-horizontal .form-group { margin-bottom: 5px; } */
		.form-horizontal .control-label { padding-top: 5px; }
		input.form-control, .input-group-addon { padding: 4px 8px; height: auto; }
		.form-control-static { padding-top: 4px; padding-bottom:4px; }
		p, p.lead { margin-bottom: 5px; }
		.angka { font-size: 30px; font-weight: bold; }
		td.has-error { background-color: #f2dede; }
		td>ol { margin-bottom: 0 }
		.table>thead>tr>th { border-bottom: 2px solid black; }
		.table>tbody>tr>td { line-height: 155%; }
		.table>tbody>tr>td.kriteria, .table>tbody>tr>td.isian { vertical-align: top; }
		.table>tbody>tr>td.border-tebal { border-bottom: 2px solid black; }
	</style>
{/block}
{block name='content'}
	<h2 class="page-header">{$tahapan->tahapan}</h2>

	<div class="row">
		<div class="col-lg-12">
					
			{if isset($updated)}
				{if $updated}
					<div class="alert alert-success alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<strong>Berhasil Simpan !</strong> Data penilaian sudah berhasil disimpan. <br/>
						<p><a href="{site_url('review')}?kegiatan_id={$tahapan_proposal->kegiatan_id}&tahapan_id={$tahapan_proposal->tahapan_id}" class="btn btn-sm btn-default">Kembali Daftar Proposal</a></p>
					</div>
				{else}
					<div class="alert alert-danger alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<strong>Error!</strong> Terdapat isian yang tidak lengkap. Silahkan cek kembali.
					</div>
				{/if}
			{/if}
			
			<form method="post" action="{current_url()}" class="form-horizontal">
						
				<div class="form-group">
					<label class="col-md-2 col-sm-3 control-label">Judul</label>
					<div class="col-md-10 col-sm-9">
						<p class="form-control-static">{$proposal->judul}</p>
					</div>
				</div>
					
				<div class="form-group">
					<label class="col-md-2 col-sm-3 control-label">PT</label>
					<div class="col-md-10 col-sm-9">
						<p class="form-control-static">{$pt->nama_pt}</p>
					</div>
				</div>
					
				<div class="form-group">
					<label class="col-md-2 col-sm-3 control-label">Anggota</label>
					<div class="col-md-10 col-sm-9">
						<p class="form-control-static">{$proposal->jumlah_anggota}</p>
					</div>
				</div>
					
				<div class="form-group">
					<label class="col-md-2 col-sm-3 control-label">Lampiran</label>
					<div class="col-md-8 col-sm-7">
						{foreach $file_proposal_set as $file_proposal}
							<p class="form-control-static" style="min-height: 0; padding: 2px 0">
								<span class="label label-primary">{$file_proposal->syarat}</span>
								{$file_proposal->nama_asli} 
								&bull; <a class="btn btn-sm btn-default" href="{$download_url}?id={$file_proposal->id}&mode=download" target="_blank">Download <i class="glyphicon glyphicon-download-alt"></i></a> 
								&bull; <a class="btn btn-sm btn-default open-file" href="#" data-nama-file="{$file_proposal->nama_asli}" data-file="{$file_proposal->nama_file}">Buka</a>
							</p>
						{/foreach}
					</div>
					
				</div>
					
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-3 col-md-offset-2 col-md-2">
						<a href="javascript: setTidakLolos();" class="btn btn-sm btn-danger">Tidak Lolos Administrasi</a>
					</div>
				</div>

				<div class="panel panel-default" id="panelPreview" style="display: none">
					<div class="panel-heading"><span class="nama-file"></span> <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
					<div class="panel-body">
						<iframe style="position: relative; width: 100%; height: 100%"></iframe>
					</div>
				</div>

			
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="text-center">No</th>
							<th>Kriteria</th>
							<th>Isian</th>
							<th class="text-center">Nilai</th>
						</tr>
					</thead>
					<tbody>
						{foreach $penilaian_set as $penilaian}
							{$jumlah_isian = count($penilaian->isian_set)}
							<tr>
								<td class="text-center border-tebal" rowspan="{$jumlah_isian + 2}">{$penilaian->urutan}</td>
								<td class="kriteria" colspan="2">{$penilaian->kriteria}</td>
								<td class="text-center angka border-tebal" rowspan="{$jumlah_isian + 2}"><label name="nilai[{$penilaian->komponen_penilaian_id}]">{$penilaian->nilai}</label></td>
							</tr>
							{foreach $penilaian->isian_set as $komponen_isian}
								<tr>
									<td><i>{$komponen_isian->pertanyaan}</i></td>
									<td>{$komponen_isian->isian|nl2br}</td>
								</tr>
							{/foreach}
							<tr>
								<td {if form_error("skor[`$penilaian->komponen_penilaian_id`]")}class="text-center border-tebal has-error"{else}class="text-center border-tebal"{/if} colspan="2">
									<label>Bobot: {$penilaian->bobot} x </label>
									<select class="form-control" name="skor[{$penilaian->komponen_penilaian_id}]" 
											data-kpid="{$penilaian->komponen_penilaian_id}" data-bobot="{$penilaian->bobot}"
											style="width: auto; display: inline-block;">
										<option value="">-- Pilih Skor --</option>
										{$selected_skor = set_value("skor[`$penilaian->komponen_penilaian_id`]", $penilaian->skor)}
										{html_options options=$skor_option_set selected=$selected_skor}
									</select>
								</td>
							</tr>
						{/foreach}
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3" class="text-right" style="vertical-align: middle">Jumlah</td>
							<td class="text-center angka">
								<label name="nilai_reviewer">{$plot_reviewer->nilai_reviewer}</label>
							</td>
						</tr>
					</tfoot>
				</table>
							
				<div {if form_error('biaya_rekomendasi')}class="form-group has-error"{else}class="form-group"{/if} style="margin: 0 0 15px 0">
					<label class="control-label">Biaya Direkomendasikan</label>
					<div class="input-group">
						<span class="input-group-addon">Rp.</span>
						<input type="text" class="form-control number" name="biaya_rekomendasi" value="{$plot_reviewer->biaya_rekomendasi}"/>
					</div>
					<span class="help-block">Min Rp 10.000.000 , Maks Rp 25.000.000 </span>
					{if form_error('biaya_rekomendasi')}
						{form_error('biaya_rekomendasi')}
					{/if}
				</div>
					
				<div class="form-group" style="margin: 0 0 15px 0">
					<label for="komentar">Komentar</label>
					<textarea class="form-control" rows="5" name="komentar">{set_value('komentar', $plot_reviewer->komentar)}</textarea>
				</div>
					
				<button type="submit" class="btn btn-primary">Simpan</button>
				<a href="{site_url('review')}?kegiatan_id={$tahapan_proposal->kegiatan_id}&tahapan_id={$tahapan_proposal->tahapan_id}" class="btn btn-default">Kembali</a>
			</form>
		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/jquery.number.min.js')}"></script>
	<script type="text/javascript">
		$('.open-file').on('click', function() {
			$('#panelPreview').show();
			$('#panelPreview').find('.nama-file').html($(this).data('nama-file'));
			$('#panelPreview').find('.panel-body iframe').attr('src', '{$preview_url}'+$(this).data('file'));
		});
		$('.close').on('click', function() {
			$('#panelPreview').hide();
		});
		
		$('select[name^="skor\\["]').on('change', function() {
			var skor = $(this).val();
			var bobot = $(this).data('bobot');
			var kpid = $(this).data('kpid');
			var nilai = bobot * skor;
			$('label[name="nilai\\['+kpid+'\\]"]').html(nilai);
			
			var nilai_reviewer = 0;
			$('label[name^="nilai\\["]').each(function(index, element) {
				nilai_reviewer += (parseInt($(element).html()) || 0);
			});
			$('label[name=nilai_reviewer]').html(nilai_reviewer);
		});
		
		$('input.number').number(true, 0, ',', '.');
		
		function setTidakLolos() {
			$('select[name^="skor\\["]').val(1);
			$('select[name^="skor\\["]').trigger('change');
		}
	</script>
{/block}
