<?php   

session_start();
require_once("user.php");
require_once("backend.php");

/**
 * Creamy administration backend.
 */


$user = new User();
$user->check_logout();
$user->check_login();

$backend = new Backend();

if ($user->logged_in())
  $backend->show_backend();
else
  $backend->show_login();

?>
