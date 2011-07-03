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
      $status = "File written. ";
    } else {
      $status = "An error occured while writing. ";
    }

    $backend = new Backend();
    $backend->show_backend_part("header", array("title" => "Saved file '" . $file));
    $backend->show_backend_part("menu");
    $backend->msg_box($status . "<a href='index.php'>Go back</a>.");
    $backend->show_backend_part("footer", array("loginstatus" => "Logged in as " . $_SESSION['username'] . "."));

} else {

  // Read file that should be edited
  if (isset($_GET["file"])) {
    $filename = $_GET["file"];
    $file = $_SERVER["DOCUMENT_ROOT"] . Config::$page_dir . "/" . $filename;

    // Store file that will be edited in session
    $_SESSION["file"] = $file;

    $content = File::read($file);

    $backend = new Backend();
    $status = "Editing file " . $file;
    $backend->show_backend_part("header", array("title" => $status));
    $backend->show_backend_part("menu");

    $backend->msg_box("You are editing $file. When you are done, click <i>Save</i> or <a href='index.php'>go back</a> without saving.");
    $backend->show_backend_part("edit", array("content" => $content));
    $backend->show_backend_part("footer", array("loginstatus" => "Logged in as " . $_SESSION['username'] . "."));
  }
}

?>
