<?php

# start session
session_start();

global $lang;
require 'lang/'.$_SESSION['lang'].'.php';

echo $_SERVER['REQUEST_URI'];


# if debug is on, show errors and warnings
function debug()
	{
	if (!isset($_SESSION['debug'])) $_SESSION['debug'] = false;
	if ($_SESSION['debug'])
		{
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		}
  }

function perform_config_health_check()
	{
    if ( !isset($_SESSION['lang']) & isset($config->data->lang) ) 
      { $_SESSION['lang'] = $config->data->lang; }
    else 
      { $_SESSION['lang'] = 'en'; }
	}

# load config file
function load_json_config($rel_path="")
	{
	$config_file_raw = file_get_contents($rel_path.'../config.json'); 
	$config = json_decode($config_file_raw); 
	if (empty($config)) die("failed to parse JSON config");
	perform_config_health_check();
	return $config;
	}

# check if logged in
function check_logged_in()
	{ if (empty($_SESSION['id'])) header('Location: /login/'); }

# save config from current data
function save_json_config($p_config,$p_rel_path="")
	{
    $save_json_file = json_encode($p_config);
    if ( !file_put_contents($p_rel_path.'../config.json', $save_json_file) ) die('ERROR: Couldnt write JSON to the config file!');
	}

?>