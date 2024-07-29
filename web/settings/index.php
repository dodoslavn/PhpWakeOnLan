<?
session_start();

$config_file_raw = file_get_contents('../../config.json');
$config = json_decode($config_file_raw);
if (empty($config)) die("failed to parse JSON config");

# set default, many ways to authenticate
$auth = false;

# authenticate via logging in, user+pass 
if (isset($_SESSION['id'])) $auth = true; 


?>
