<html>
  <head>
    <title>PhpWakeOnLan</title>
    <link rel="stylesheet" type="text/css" href="features/main_page.css">
  </head>
  <body>
  <div id="header">
    <a href="#">SETTINGS</a>
    <a href="./logout.php">LOGOUT</a>
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

# order the json based on order value
//$config_ordered = (array)$config->data;
//usort($config_ordered, function($a, $b) { return $a->order - $b->order; });

# header for the table
if ( count((array)$config->data) > 0 )
  {
  echo '<table>
    <tr>
      <th>TITLE</th>
      <th>MAC</th>
      <th>Wake On Lan</th>
      <th>Last used</th>
    </tr>';
  # show the hosts
  $rest = "";
  foreach ( $config->data as $host => $info )
    {
    if ( isset($info->order) )
      { $ordered[$info->order] = '<tr><td>'.$info->title."</td><td>".$host.'<td><a target="_new" href="api.php?title='.$info->title.'">EXECUTE</a></td><td></td></tr>'; }
    else 
     { $rest = $rest." ".'<tr><td>'.$info->title."</td><td>".$host.'<td><a target="_new" href="api.php?title='.$info->title.'">EXECUTE</a></td><td></td></tr>'; }
     }
  foreach($ordered as $item)
    { echo $item; }
  echo $rest;
  echo '</table>';
  }
else
  { echo "List of hosts is empty."; }


 
?>
   </div>
  </body>
</html>
