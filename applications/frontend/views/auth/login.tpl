{extends file='site_layout.tpl'}
{block name='content'}
    <h2 class="page-header">Login SIM-PKMI</h2>

    <div class="row">
        <div class="col-sm-6 col-lg-5">
            <form action="{current_url()}" method="post">
                <div class="panel panel-default">
                    <div class="panel-heading"><h3 class="panel-title"><strong>Login</strong></h3></div>
                    <div class="panel-body">
                        {if $ci->session->flashdata('failed_login')}
                            <div class="alert alert-danger" role="alert">{$ci->session->flashdata('failed_login')}</div>
                        {/if}
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="captcha">Captcha</label>
                            <p class="form-control-static">{$img_captcha}</p>
                            <input type="text" class="form-control" id="captcha" name="captcha">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                        <div class="form-group">
                            <a href="{site_url('auth/registrasi-pt')}" class="btn btn-info">Registrasi Perguruan Tinggi</a>
                            <a href="{site_url('auth/registrasi-mahasiswa')}" class="btn btn-success">Registrasi Mahasiswa</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-sm-6 col-lg-5">
            <h3>Hal yang perlu diperhatikan</h3>
            <ol>
                <li>Username & password yang Resmi hanya yang berasal dari sistem SIM-PKMI.</li>
                <li>Pastikan mengisi isian captcha agar bisa login</li>
                <li>Ketika mengganti password selalu gunakan password yang sulit ditebak</li>
                <li>Jangan membagikan password ke sembarang orang</li>
                <li>Selalu Logout setelah menggunakan sistem</li>
				<li class="text-danger">Bagi mahasiswa yang belum mendapatkan email, silahkan masuk
					ke halaman <a href="{site_url('auth/registrasi-mahasiswa')}">Registrasi Mahasiswa</a>
					untuk Reset Password dengan memasukkan data yang pernah diinputkan sebelumnya.</li>
				<li>Untuk pertanyaan lebih lanjut, bisa menghubungi WA : 0878-7363-0275 (Arya)</li>
            </ol>
        </div>
    </div>
{/block}
