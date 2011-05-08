<?php

session_start();
require_once("backend.php");
require_once("file.php");

    print $_POST["post-text"];
// Check if file has been modified
if (isset($_POST["submit"])) {
    $file = $_SESSION["file"];
    $content = $_POST["post-text"];
    if (File::write($file, $content)) {
      $status = "File written. ";
    } else {
      $status = "An error occured while writing. ";
    }

    $backend = new Backend();
    $backend->show_part("header", array("title" => "Saved file '" . $file . "' | Creamy"));
    $backend->show_part("menu", array("user" => $_SESSION['username']));
    print($status);
    print("<a href='index.php'>Back</a>");
    $backend->show_part("footer");

} else {

  // Read file that should be edited
  if (isset($_GET["file"])) {
    $file = $_GET["file"];

    // Store file that will be edited in session
    $_SESSION["file"] = $file;

    $content = File::read($file);
    $status = "Editing file " . $file;

    $backend = new Backend();
    $backend->show_part("header", array("title" => $status));
    $backend->show_part("menu", array("user" => $_SESSION['username'], "status" => $status));
    $backend->show_part("edit", array("content" => $content));
    $backend->show_part("footer");
  }
}

?>
