<?php

class Result 
	{
    public $message;
    public $return_code;
	public function __construct(string $message, bool $return_code) 
		{
        $this->message = $message;
		$this->return_code = $return_code;
		}
	}

function string_check($str) 
	{ return preg_match('/[^a-zA-Z0-9.\/]/', $str) > 0; }

function change_wol_binary($path)
	{
	if string_check($path) return Result('ERROR: Invalid path!', false );
	
	exec('/bin/ls '.$path, $output, $returnCode);
	if ( $returnCode ) return Result('ERROR: File doesnt exist on system!', false );

	$config->configuration->wol_binary = $path;
	Result('Path to WoL binary was updated!', true );
	}
?>

