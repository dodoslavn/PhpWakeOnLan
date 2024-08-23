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
require '../functions.php'

check_logged_in();
debug();
load_json_config();


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
