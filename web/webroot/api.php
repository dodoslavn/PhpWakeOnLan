<?php

require '../functions.php';

debug();
$config = load_json_config();

# set default, many ways to authenticate
$auth = false;

# check if user is authenticated
if (isset($_SESSION['id'])) $auth = true; 
# basic auth and bearer token auth here in future

if ( !$auth ) die("ERROR: Not authenticated!");

if ( !isset($config->configuration->wol_binary) ) die("ERROR: WoL binary is not set!");
if ( isset($_GET['title']) && isset($_GET['mac']) ) die("ERROR: Both arguments are used!");

# get MAC from saved list, via title
if ( isset($_GET['title']) )
  {
  foreach ($config->data as $host)
    {
    if (isset($host->title))
      { 
      if ( $host->title == $_GET['title'] )
        { $mac = $host->mac; }
      }
    }
  }

# get mac diractly from request, any MAC can be used
if ( isset($_GET['mac']) )
  {
  if ( isset($config->configuration->any_mac) )
    {
    if ( $config->configuration->any_mac == "true" )
      { $mac = $_GET['mac']; }
    }
  }

# check if we got the MAC or not
if ( !isset($mac) ) die("ERROR: Missing MAC!");


echo "Running command: ".$config->configuration->wol_binary." ".$mac ;
exec($config->configuration->wol_binary." ".$mac, $output, $retval);
print_r($output[0]);
echo $retval;

?>
