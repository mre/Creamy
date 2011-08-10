<?php

session_start();
require_once("config.php");
require_once("backend.php");
require_once("file.php");
require_once("metadata.php");
require_once("messagehandler.php");
require_once("indexer.php");

// Handle editor commands
if (isset($_POST["submit"])) {
    // Save file
    $file = $_SESSION["file"];

    // Get metadata from post
    $metadata = array();
    $metadata["title"] = $_POST["title"];
    $metadata["date"] = $_POST["date"];

    $content = $_POST["post-text"];
    Editor::save_post($file, $content, $metadata);
} else {
  // Read file that should be edited
  if (isset($_GET["file"])) {
    if (isset($_GET["new"])) {
      // Remember to update the number 
      // of posts (index) when saving
      $_SESSION["new"] = $_GET["new"];
    }
    Editor::edit($_GET["file"]);
  }
}

class Editor {

  // Template metadata fields that will not be shown on editor page.
  private static $metadata_filter = array("text", "author");

  public static function save_post($file, $content, $metadata) {

    // Delete old files with the same id
    $id = self::get_id_from_filename($file);
    $dir = dirname($file);
    self::delete_id($dir, $id);

    // Create new filename
    $new_file = $dir . "/" . $id . "_" . $metadata["title"] . Config::$extension;
    // Put metadata at the beginning of the file
    File::write($new_file, Metadata::create($metadata), 'w');
    File::write($new_file, Config::$metadata_separator);

    // Write content below metadata
    if (File::write($new_file, $content)) {
      $status = "Saved file " . basename($new_file) . ".";
      // Update file index
      $indexer = new Indexer();
      if (isset($_SESSION["new"]) && $_SESSION["new"] == true) {
        $content_dir = File::parent_dir($new_file);
        $indexer->increment_index($content_dir);
      }
    } else {
      $status = "An error occured while writing " . $file . ".";
    }
    $backend = new Backend();
    $messagehandler = MessageHandler::getInstance();
    $messagehandler->show($status);
    $backend->show_backend();
  }

  private static function delete_id($dir, $id) {
    // Find posts with this id
    $pattern = array("/^" . $id . "/");
    $files = File::find($pattern, $dir);
    foreach ($files as $file) {
      File::remove($file);
    }
  }

  /**
   * Id from filename
   */
  private static function get_id_from_filename($file) {
    $filename = basename($file);
    $no_extension = explode("." , $filename);
    $id = explode("_", $no_extension[0]);
    return $id[0];
  }

  /**
   * Directory from filename
   */
  private static function get_dir_from_filename($file) {
    $file = File::parent_dir($file);
    return $file;
  }

  public static function edit($filename) {
    $file = $_SERVER["DOCUMENT_ROOT"] . "/" . Config::$page_dir . "/" . $filename;
    $_SESSION["file"] = $file; // Remember filename for saving.

    $content = File::read($file);

    // Create some input fields for metadata (like title or date) for the user.
    $metadata = array();
    if (isset($_GET["layout"])) {
      $metadata = Metadata::get_template_variables($_GET["layout"], self::$metadata_filter);
    }

    $backend = new Backend();
    $status = "Editing file " . $file;
    $messagehandler = MessageHandler::getInstance();
    $messagehandler->show("You are editing " . File::sanitized($file) .
      ". When you are done, click <i>Save</i> or <a href='index.php'>go back</a> without saving.");

    $backend->display("cms_editor", array(
      "title" => $status,
      "edit" => $content,
      "metadata" => $metadata,
      "loginstatus" => "Logged in as " . $_SESSION['username'] . ".")
    );
  }
}

?>
