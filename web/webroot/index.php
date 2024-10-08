<?php
require '../functions.php';


check_logged_in();
debug();

global $lang;
$config = load_json_config();

require '../header.php';

# header for the table
if ( count((array)$config->data) > 0 )
  {
  echo '<table id="wol">
    <tr>
      <th>'.$lang['main_table_title'].'</th>
      <th>MAC</th>
      <th>Wake On Lan</th>
      <th>'.$lang['main_table_lastused'].'</th>
    </tr>';
    
  # show the hosts
  $rest = "";
  # loop through data, display ordered records, then the rest
  foreach ( $config->data as $host => $info )
    {
    if (!isset($info->last_used)) $info->last_used = '';
    if ( isset($info->order) )
      { $ordered[$info->order] = '<tr><td>'.$info->title."</td><td>".$host.'<td><a target="_new" href="api.php?title='.$info->title.'">'.$lang['main_table_exec'].'</a></td><td>'.$info->last_used.'</td></tr>'; }
    else 
     { $rest = $rest." ".'<tr><td>'.$info->title."</td><td>".$host.'<td><a target="_new" href="api.php?title='.$info->title.'">'.$lang['main_table_exec'].'</a></td><td></td></tr>'; }
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
