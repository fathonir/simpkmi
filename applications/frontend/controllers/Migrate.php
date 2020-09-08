<?php

/**
 * @author Fathoni <m.fathoni@mail.com>
 */
class Migrate extends Frontend_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		if ( ! $this->input->is_cli())
		{
			echo "Migrate harus dijalankan melalui cli";
			exit();
		}
		
		$this->load->library('migration');
	}
	
	public function index()
	{
		// Get current version
		$migration = $this->db->get('migrations')->row();
		
		echo "Current Version : {$migration->version}\n";
		
		echo "List Migration :\n";
		
		$migrations = $this->migration->find_migrations();
		
		$i = 1;
		
		foreach ($migrations as $mig_ver => $mig_file)
		{
			$mig_file = str_replace(APPPATH.'migrations/', '', $mig_file);
			echo "[{$i}] {$mig_file}\n";
			$i++;
		}
	}
	
	public function up($target_version = null)
	{
		if ($target_version == null)
			$result = $this->migration->latest();
		else
			$result = $this->migration->version($target_version);
		
		if ($result === FALSE)
		{
			show_error($this->migration->error_string());
		}
		else
		{
			echo "  > Berhasil di migrate ke versi " . $result . "\n";
		}
	}
	
	public function down($target_version = null)
	{
		// get all migration
		$migrations = $this->migration->find_migrations();
		
		// get latest version
		$latest_version = count($migrations);
		
		// rollback 1 version
		if ($target_version == null)
			$target_version = $latest_version - 1;
		
		// do rollback
		$result = $this->migration->version($target_version);
		
		if ($result == TRUE)
			echo "  > Berhasil di migrate down ke versi " . $target_version . "\n";
	}
}
