<html>
  <head>
    <title>PhpWakeupOnLan</title>
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

echo '<table>
  <tr>
    <th>TITLE</th>
    <th>MAC</th>
    <th>Wake On Lan</th>
  </tr>
';
foreach ($config->data as $host)
  {
  if (isset($host->title) && isset($host->mac) )
    {
    echo '<tr><td>'.$host->title."</td><td>".$host->mac.'<td><a target="_new" href="api.php?title='.$host->title.'">EXECUTE</a></td></tr>';
    }
  }
echo '</table>';
 
?>
   </div>
  </body>
</html>
