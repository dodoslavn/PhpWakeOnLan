<?php

# start session
session_start();

# if debug is on, show errors and warnings
function debug()
	{
	if ($_SESSION['debug'])
		{
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		}
  }

# load config file
function load_json_config()
	{
	$config_file_raw = file_get_contents('../config.json'); 
	$config = json_decode($config_file_raw); 
	if (empty($config)) die("failed to parse JSON config");
	}

# check if logged in
function check_logged_in()
	{ if (empty($_SESSION['id'])) header('Location: login/'); }



?>