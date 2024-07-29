<?
session_start();

# debug output switch
$debug = true;
if ($debug)
  {
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  }

# load config file
$config_file_raw = file_get_contents('../../config.json'); 
$config = json_decode($config_file_raw); 
if (empty($config)) die("failed to parse JSON config");

# check if logged in
if (empty($_SESSION['id'])) header('Location: login/');


?>
<html>
  <head>
    <title>PhpWakeOnLan</title>
    <link rel="stylesheet" type="text/css" href="../features/main_page.css">
  </head>
  <body>
    <div id="header">
      <a href="#">SETTINGS</a>
      <a href="./logout.php">LOGOUT</a>
    </div>
    <div id="content">
      <h4>Your account</h4>
      <table>
      <tr><td>Account name:</td><td> <? echo  $_SESSION['id']; ?> </td></tr>
      <tr><td>Change password:</td><td> <form action="#" method="post">
                         <input type="input" name="pw_reset value=""> 
                         <input type="submit" value="Save">
                       </form></td></tr>
      </table>
      <h4>Website settings</h4>
      <table>
      <tr><td>Enable PHP debug:</td><td> <form target="#" method="post"><input type="checkbox" name="php_debug" value="<? echo $_SESSION['debug']; ?>"></form></td></tr>
      <tr><td>Language: </td><td>
        <form action="#" method="post">
          <select name="lang">     
            <option value="en">English</option>
          </select>
        </form></td></tr>
      <tr><td>Add account:</td><td> contact admin to add account manually</td></tr>
      </table>

<?
# header for the table
if ( count((array)$config->data) > 0 )
  {
  echo '<table>
    <tr>
      <th>TITLE</th>
      <th>MAC</th>
      <th></th>
      <th></th>
    </tr>';
    
  # show the hosts
  $rest = "";
  # loop through data, display ordered records, then the rest
  foreach ( $config->data as $host => $info )
    {
    if ( isset($info->order) )
      { $ordered[$info->order] = '<tr><td>'.$info->title."</td><td>".$host.'<td>Modify</td><td>Remove</td></tr>'; }
    else 
     { $rest = $rest." ".'<tr><td>'.$info->title."</td><td>".$host.'<td>Modify</td><td>Remove</td></tr>'; }
     }
  # show ordered
  foreach($ordered as $item)
    { echo $item; }
  # show rest
  echo $rest;
  echo '<tr><td>name</td><td>mac<td></td><td>Add</td></tr>';
  echo '</table>';
  }
else
  { echo "List of hosts is empty."; }
?>
    </div>
  </body>
</html>
