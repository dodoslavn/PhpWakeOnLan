<?php
session_start();

if (isset($_SESSION['id'])) header('Location: ..');

$debug = true;

if ($debug)
  {
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  }

  if ( !isset($_SESSION["form_user"]) )
    { $_SESSION["form_user"] = ""; }

if ( isset($_POST) )
  {
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
  $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_SPECIAL_CHARS);
  if ( !empty($username) && !empty($_POST['pass']) )
    {
    $_SESSION["form_user"] = ' value="'.$username.'" ';

    # load config file
    $config_file_raw = file_get_contents('../../config.json');
    $config = json_decode($config_file_raw);
    if (empty($config)) die("failed to parse JSON config");

    $password_hashed = password_hash($pass, PASSWORD_DEFAULT);

    if ( !isset($config->users->$username) )
        {
        #user doesnt exist in json
        header('Location: #');
        }
    if ( !isset($config->users->$username->id) )
        {
        #user doesnt have ID
        header('Location: #');
        }
    if ( !isset($config->users->$username->pass) )
        {
        #user doesnt have password
        header('Location: #');
        }
    if ( !isset($config->users->$username->password_hashed) )
        { $password = $pass; }
    if ( isset($config->users->$username->password_hashed) )
        {
        if ( $config->users->$username->password_hashed == true )
            { $password = $password_hashed; }
        else
            { $password = $pass; }
        }
    echo $config->users->$username->pass."-----".$password ;
    if ( $config->users->$username->pass == $password )
        {
        #login successful
        if ( $password_hashed != $password )
            {
            $config->users->$username->pass = $password_hashed;
            $config->users->$username->password_hashed = true;

            $save_json_file = json_encode($config);
            file_put_contents('../../config.json', $save_json_file);
            }
        $_SESSION["id"] = $config->users->$username->id;
        header('Location: ..');
        }
    }
  }
#else FORM WAS NOT SUBMITED YET


?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>Login - PhpWakeOnLan</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="icon" type="image/png" href="../features/favicon.ico" />
      <link rel="stylesheet" type="text/css" href="../features/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="../features/font-awesome.min.css">
      <link rel="stylesheet" type="text/css" href="../features/icon-font.min.css">
      <link rel="stylesheet" type="text/css" href="../features/animate.css">
      <link rel="stylesheet" type="text/css" href="../features/hamburgers.min.css">
      <link rel="stylesheet" type="text/css" href="../features/animsition.min.css">
      <link rel="stylesheet" type="text/css" href="../features/select2.min.css">
      <link rel="stylesheet" type="text/css" href="../features/daterangepicker.css">
      <link rel="stylesheet" type="text/css" href="../features/util.css">
      <link rel="stylesheet" type="text/css" href="../features/main.css">
      <meta name="robots" content="noindex, follow">
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login100 p-t-50 p-b-90">
               <form class="login100-form validate-form flex-sb flex-w" action="" method="post">
                  <span class="login100-form-title p-b-51">
                  PHP Wake On Lan
                  </span>
                  <div class="wrap-input100 validate-input m-b-16" data-validate="Username is required">
                     <input class="input100" type="text" name="username" placeholder="Username" <? echo $_SESSION["form_user"]; ?>  >
                     <span class="focus-input100"></span>
                  </div>
                  <div class="wrap-input100 validate-input m-b-16" data-validate="Password is required">
                     <input class="input100" type="password" name="pass" placeholder="Password">
                     <span class="focus-input100"></span>
                  </div>
                  <div class="container-login100-form-btn m-t-17">
                     <button class="login100-form-btn" type="submit">
                     Login
                     </button>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <div id="dropDownSelect1"></div>
      <script src="../features/jquery-3.2.1.min.js"></script>
      <script src="../features/animsition.min.js"></script>
      <script src="../features/popper.js"></script>
      <script src="../features/bootstrap.min.js"></script>
      <script src="../features/select2.min.js"></script>
      <script src="../features/moment.min.js"></script>
      <script src="../features/daterangepicker.js"></script>
      <script src="../features/countdowntime.js"></script>
      <script src="../features/main.js"></script>
   </body>
</html>
