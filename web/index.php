<html>
  <head>
    <title>PhpWakeOnLan</title>
    <link rel="stylesheet" type="text/css" href="features/main_page.css">
  </head>
  <body>
  <div id="header">
    <div id="title">PhpWakeOnLan</div>
    <div id="links">
      <a href="/arp/">ARP</a>
      <a href="/settings/">SETTINGS</a>
      <a href="/logout.php">LOGOUT</a>
    </div>
  </div>
<div id="content">
  
<?php

session_start();

# debug output switch
$debug = true;
if ($debug)
  {
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  }

# load config file
$config_file_raw = file_get_contents('../config.json'); 
$config = json_decode($config_file_raw); 
if (empty($config)) die("failed to parse JSON config");

# check if logged in
if (empty($_SESSION['id'])) header('Location: login/');

# header for the table
if ( count((array)$config->data) > 0 )
  {
  echo '<table id="wol">
    <tr>
      <th>TITLE</th>
      <th>MAC</th>
      <th>Wake On Lan</th>
      <th>Last used</th>
    </tr>';
    
  # show the hosts
  $rest = "";
  # loop through data, display ordered records, then the rest
  foreach ( $config->data as $host => $info )
    {
    if ( isset($info->order) )
      { $ordered[$info->order] = '<tr><td>'.$info->title."</td><td>".$host.'<td><a target="_new" href="api.php?title='.$info->title.'">EXECUTE</a></td><td></td></tr>'; }
    else 
     { $rest = $rest." ".'<tr><td>'.$info->title."</td><td>".$host.'<td><a target="_new" href="api.php?title='.$info->title.'">EXECUTE</a></td><td></td></tr>'; }
     }
  # show ordered
  foreach($ordered as $item)
    { echo $item; }
  # show rest
  echo $rest;
  echo '</table>';
  }
else
  { echo "List of hosts is empty."; }


 
?>
   </div>
  </body>
</html>
