<?
require '../../header.php';
require '../../functions.php';
require '../../functions_settings.php';

check_logged_in();
debug();
$config = load_json_config('../');

$result;
if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
    switch ($_POST['form']) 
        {
        case 'pw_change':
            if ( strlen($_POST['password']) >= 6 )
                {
                print_r($config->users);
                $password_hashed = hash('sha256', $_POST['password']);
                $config->users->{$_SESSION['id']}->pass = $password_hashed;
                $config->users->{$_SESSION['id']}->password_hashed = true;
                save_json_config($config,"../");
                $result = new Result('Password updated!', true);
                }
            else $result = new Result('ERROR: New password too short!', false);
            break;
        }
    }

if ( isset($result) ) echo $result->message;

?>
      <h4>Your account</h4>
      <table>
      <tr><td>Account name:</td><td> <?php echo $_SESSION['id']; ?> </td></tr>
      <tr><td>Change password:</td><td> <form action="/settings/" method="post">
                         <input type="password" minlength="6" name="password" value=""> 
                         <button type="submit" name="form" value="pw_change">Save</button>
                       </form></td></tr>
      </table>
      <h4>Website settings</h4>
      <table>
      <tr><td>WoL binary:</td><td><? echo $config->configuration->wol_binary; ?></td></tr>
      <tr><td>Enable PHP debug:</td><td> <form target="/settings/" method="post"><input type="checkbox" id="debug" name="php_debug" value="<? echo $_SESSION['debug']; ?>"><label for="debug"> (only for this session)</label></form> </td></tr>
      <tr><td>Language: </td><td>
        <form action="#" method="post">
          <select name="lang">     
            <option value="en">English</option>
          </select>
        </form></td></tr>
      <tr><td>Add account:</td><td> contact admin to add account manually</td></tr>
      </table>
      <h4>Saved hosts for WoL</h4>
<?php
# header for the table
if ( count((array)$config->data) > 0 )
  {
  echo '<table id="wol">
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
require '../../footer.php';
?>
