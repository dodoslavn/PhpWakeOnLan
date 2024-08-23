<?php
require '../header.php';
require '../functions.php';

check_logged_in();
debug();
$config = load_json_config();

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

require '../footer.php';
?>