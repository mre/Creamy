<?php

session_start();
require_once("config.php");
require_once("user.php");
require_once("backend.php");

/**
 * Creamy administration backend.
 */

$user = new User();
$user->check_logout();
$user->check_login();


$backend = new Backend();

if ($user->logged_in()) {
  if (isset($_GET["reload"])) {
    $backend->remove_contents_file();
  }
  $backend->show_backend();
} else {
  $backend->show_login();
}


?>
