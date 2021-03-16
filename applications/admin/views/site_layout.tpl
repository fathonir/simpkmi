{doctype('html5')}
<html lang="id">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Program Kewirausahaan Mahasiswa Indonesia</title>
		{if ENVIRONMENT == 'development'}
			<link href="{base_url('../vendor/twbs/bootstrap/dist/css/bootstrap.css')}" rel="stylesheet"/>
		{/if}
		{if ENVIRONMENT == 'production'}
			<link href="https://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Oswald|Comfortaa|Nunito+Sans:400,700" rel="stylesheet">
		{/if}
		<link href="{base_url('../assets/css/bootstrap-cerulean.min.css')}" rel="stylesheet"/>
		<link href="{base_url('../assets/css/site.css')}" rel="stylesheet"/>
		{block name='head'}
		{/block}
	</head>
	<body>
		<!-- Fixed navbar -->
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="{block name='container'}container{/block}">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					{if $ci->session->user}
						<a class="navbar-brand" href="{site_url('home')}">SIM-PKMI</a>
					{else}
						<a class="navbar-brand" href="{base_url()}">SIM-PKMI</a>
					{/if}
				</div>
				<div id="navbar" class="collapse navbar-collapse">
					{if $ci->session->user}
						<ul class="nav navbar-nav">
							<li>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Setting <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="{site_url('kegiatan')}">Jadwal Kegiatan</a></li>
									<li><a href="{site_url('kegiatan/lokasi')}">Lokasi Workshop</a></li>
                                    <li><a href="{site_url('kegiatan/meeting')}">Jadwal Online Workshop</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="{site_url('download')}">Download File</a></li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Master Data <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="{site_url('user/request')}">User Request</a></li>
									<li><a href="{site_url('user/request-rejected')}">User Request Ditolak</a></li>
									<li><a href="{site_url('user/request-approved')}">User Request Disetujui</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="{site_url('user')}">User</a></li>
									<li><a href="{site_url('pt')}">Perguruan Tinggi</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="{site_url('user-reviewer')}">Reviewer</a></li>
									<li><a href="{site_url('user-reviewer/plotting')}">Plotting Reviewer</a></li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Proposal &amp; Peserta <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="{site_url('proposal/index-pbbt')}">Proposal PBBT</a></li>
									<li><a href="{site_url('proposal/index-kbmi')}">Proposal KBMI (&leq; 2018)</a></li>
									<li><a href="{site_url('proposal/index-kbmi-v2')}">Proposal KBMI</a></li>
									<li><a href="{site_url('proposal/index-startup')}">Usulan Startup</a></li>
									<li role="separator" class="divider"></li>
									<li><h5 style="padding: 3px 20px; margin: 0"><i>Workshop</i></h5></li>
									<li><a href="{site_url('workshop/peserta')}">Daftar Peserta</a></li>
									<li><a href="{site_url('workshop/plotting')}">Plotting Reviewer</a></li>
									<li><a href="{site_url('workshop/proposal')}">Daftar Proposal</a></li>
									<li role="separator" class="divider"></li>
									<li><h5 style="padding: 3px 20px; margin: 0"><i>Expo KMI</i></h5></li>
									<li><a href="{site_url('expo')}">Usulan Expo KMI</a></li>
									<li><a href="{site_url('expo/rekap-per-pt')}">Rekap per PT</a></li>
									<li><a href="{site_url('expo/rekap-per-kategori')}">Rekap per Kategori</a></li>
                                    <li role="separator" class="divider"></li>
									<li><h5 style="padding: 3px 20px; margin: 0"><i>Online Workshop</i></h5></li>
                                    <li><a href="{site_url('online-workshop/peserta')}">Daftar Peserta</a></li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Penilaian <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="{site_url('penilaian')}">Hasil Penilaian Proposal</a></li>
									<li><a href="{site_url('penilaian/tahap2')}">Hasil Penilaian Proposal Tahap II</a></li>
									<li><a href="{site_url('penilaian/kmi-award')}">Hasil Penilaian KMI Award</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="{site_url('penilaian/didanai-pt')}">Didanai per PT</a></li>
									<li><a href="{site_url('penilaian/didanai-kategori')}">Didanai per Kategori</a></li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">PWMI <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="{site_url('pwmi/usulan')}">Usulan Pendamping</a></li>
									<li><a href="{site_url('pwmi')}">Daftar Pendamping</a></li>
									<li><a href="{site_url('pwmi/penetapan')}">Penetapan Pendamping</a></li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Buku Profil <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="{site_url('buku-profil')}">Daftar Isian</a></li>
									<li><a href="{site_url('buku-profil/export-pdf')}">Export PDF</a></li>
								</ul>
							</li>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							{if $ci->session->user}
								<li>
									<a href="{site_url('auth/logout')}">Logout ({$ci->session->user->username})</a>
								</li>
							{/if}
						</ul>
					{/if}

				</div><!--/.nav-collapse -->
			</div>
		</nav>

		<!-- Begin page content -->
		<div class="{block name='container'}container{/block}">
			{block name='content'}
			{/block}
		</div>

		<footer class="footer">
			<div class="container">
				<p class="text-center">&copy; Direktorat Jenderal Pendidikan Tinggi<br/>
					Jl. Jenderal Sudirman, Senayan, Jakarta 10270, Indonesia<br/>
                    <a href="http://dikti.kemdikbud.go.id">dikti.kemdikbud.go.id</a></p>
			</div>
		</footer>

		{if ENVIRONMENT == 'development'}
			<script src="{base_url('../vendor/components/jquery/jquery.js')}"></script>
			<script src="{base_url('../vendor/twbs/bootstrap/dist/js/bootstrap.js')}"></script>
		{/if}
		{if ENVIRONMENT == 'production'}
			<script src="https://ajax.aspnetcdn.com/ajax/jquery/jquery-1.12.4.min.js"></script>
			<script src="https://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/bootstrap.min.js"></script>
		{/if}
		{block name='footer-script'}
		{/block}
	</body>
</html>
