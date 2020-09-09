<?php

/**
 * Class Tools
 * @property CI_Config $config
 * @property CI_Email $email
 */
class Tools extends CI_Controller
{
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
