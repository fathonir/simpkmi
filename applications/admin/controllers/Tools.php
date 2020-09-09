<?php

/**
 * Class Tools
 * @property CI_Config $config
 * @property CI_Email $email
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property Smarty_wrapper $smarty
 */
class Tools extends CI_Controller
{
	public function kirim_login_pendamping($tahun_kegiatan)
	{
		$this->load->library('email');
		$this->config->load('email');

		$user_set = $this->db->select('d.nama, u.username, u.password, u.id')
			->from('dosen_pendamping dp')
			->join('kegiatan k', 'k.id = dp.kegiatan_id')
			->join('dosen d', 'd.id = dp.dosen_id')
			->join('user u', 'u.dosen_id = d.id AND u.is_sent = 0')
			->where('k.tahun', $tahun_kegiatan)
			->get()->result();

		foreach ($user_set as $user)
		{
			$now = date('Y-m-d H:i:s');
			echo "[{$now}] Pengiriman ke {$user->nama} ({$user->username}) ... ";

			$this->smarty->assign('nama', $user->nama);
			$this->smarty->assign('login_link', 'https://sim-pkmi.kemdikbud.go.id/auth/login');
			$this->smarty->assign('username', $user->username);
			$this->smarty->assign('password', $user->password);
			$body = $this->smarty->fetch('email/dosen_pendamping_user.tpl');

			$this->email->from($this->config->item('email_from'), $this->config->item('email_from_name'));
			$this->email->to($user->username);
			$this->email->subject('Account Login Pendamping');
			$this->email->message($body);
			$send_result = $this->email->send(FALSE);

			if ($send_result)
			{
				$this->db->update('user', ['is_sent' => 1], ['id' => $user->id]);
				echo "Berhasil!\n";
			}
			else
			{
				echo "Gagal!\n";
			}
		}
	}

	public function test_send_email()
	{
		$this->load->library('email');
		$this->config->load('email');

		$protocol	= $this->config->item('protocol');
		$from		= $this->config->item('email_from');
		$from_name	= $this->config->item('email_from_name');
		$to			= ['m.fathoni@mail.com', 'mokhammad.fathoni.rokhman@gmail.com', 'rokhman@dosen.umaha.ac.id'];

		echo "Pengiriman test send email \n";
		echo "  From      : {$from} ({$from_name})\n";
		echo "  To        : " . implode(', ', $to) . "\n";
		echo "  Protocol  : {$protocol}\n";
		echo "  SMTP Host : " . $this->config->item('smtp_host') . "\n";
		echo "  SMTP User : " . $this->config->item('smtp_user') . "\n";
		echo "  SMTP Pass : " . $this->config->item('smtp_pass') . "\n";
		echo "  SMTP Port : " . $this->config->item('smtp_port') . "\n";
		echo "  SMTP Cryp : " . $this->config->item('smtp_crypto') . "\n";
		echo "  SMTP Time : " . $this->config->item('smtp_timeout') . "\n";
		echo "  SMTP KeepAlive : " . ($this->config->item('smtp_keepalive') ? 'TRUE' : 'FALSE') . "\n";

		$this->email->from($from, $from_name);
		$this->email->to($to);
		$this->email->subject('Ujicoba pengiriman email [' . date('ymdHis') . ']');
		$this->email->message("Hai, email ini adalah email uji coba. Jika email ini sampai berarti berhasil.");
		$send_result = $this->email->send(FALSE);

		echo $send_result ? 'Berhasil !' : 'Gagal !';
	}
}
