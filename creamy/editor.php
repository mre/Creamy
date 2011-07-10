<?php

session_start();
require_once("config.php");
require_once("backend.php");
require_once("file.php");

// Check if file has been modified
if (isset($_POST["submit"])) {
    $file = $_SESSION["file"];
    $content = $_POST["post-text"];
    if (File::write($file, $content, 'w')) {
      $status = "Saved changes in file " . $file . ".";
    } else {
      $status = "An error occured while writing " . $file . ".";
    }

    $backend = new Backend();
    $backend->show_message($status);
    $backend->show_backend();
} else {

  // Read file that should be edited
  if (isset($_GET["file"])) {
    $filename = $_GET["file"];
    $file = $_SERVER["DOCUMENT_ROOT"] . "/" . Config::$page_dir . "/" . $filename;

    // Store file that will be edited in session
    $_SESSION["file"] = $file;

    $content = File::read($file);

    $backend = new Backend();
    $status = "Editing file " . $file;
    $backend->show_message("You are editing" . $file .
      ". When you are done, click <i>Save</i> or <a href='index.php'>go back</a> without saving.");
    $backend->display("cms_editor", array("title" => $status, 
      "edit" => $content, 
      "loginstatus" => "Logged in as " . $_SESSION['username'] . "."));
  }
}

?>
