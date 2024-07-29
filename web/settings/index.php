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
      Account name: <? echo  $_SESSION['id']; ?> <br>
      Change password: <? echo  $_SESSION['id']; ?> <br>
      <h4>Website settings</h4>
      PHP debug <form target="#" method="post"><input type="checkbox" value="<? echo $_SESSION['debug']; ?>">  </form><br>
      Language: <? echo  $_SESSION['id']; ?> <br>
      Add account: contact admin to add account manually <br>
    </div>
  </body>
</html>
