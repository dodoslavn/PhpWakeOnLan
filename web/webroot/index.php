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
require '../functions.php';
check_logged_in();
debug();
load_json_config();



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
   <div id="footer"> PhpWakeOnLan : Dodoslav Novak : <a style="color: rgb(50, 168, 82);" href="https://github.com/dodoslavn/PhpWakeOnLan/">Github</a> </div>
  </body>
</html>
