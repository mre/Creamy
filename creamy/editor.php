<?php

session_start();
require_once("backend.php");
require_once("file.php");

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
    $backend->show_part("header", array("title" => "Saved file '" . $file));
    $backend->show_part("menu");
    $backend->msg_box($status . "<a href='index.php'>Go back</a>.");
    $backend->show_part("footer", array("loginstatus" => "Logged in as " . $_SESSION['username'] . "."));

} else {

  // Read file that should be edited
  if (isset($_GET["file"])) {
    $file = $_GET["file"];

    // Store file that will be edited in session
    $_SESSION["file"] = $file;

    $content = File::read($file);

    $backend = new Backend();
    $backend->show_part("header", array("title" => $status));
    $backend->show_part("menu");
    $backend->msg_box("You are editing $file. When you are done, click <i>Save</i> or <a href='index.php'>go back</a> without saving.");
    $backend->show_part("edit", array("content" => $content));
    $backend->show_part("footer", array("loginstatus" => "Logged in as " . $_SESSION['username'] . "."));
  }
}

?>
