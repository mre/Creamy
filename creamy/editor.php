<?php

session_start();
require_once("backend.php");
require_once("file.php");

// Read file that should be edited
if (isset($_GET["file"])) {
  $file = $_GET["file"];
  $content = File::read($file);
  $backend = new Backend();
  $backend->show_part("header", array("title" => "Edit file '" . $file . "' | Creamy"));
  $backend->show_part("menu", array("user" => $_SESSION['username']));
  $backend->show_part("edit", array("content" => $content));
  $backend->show_part("footer");
}

?>
