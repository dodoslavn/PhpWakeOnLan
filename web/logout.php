<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

do_logout();
header('Location: login.php');
exit;
