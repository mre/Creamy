<?php

session_start();
require_once("config.php");
require_once("backend.php");
require_once("file.php");
require_once("metadata.php");
require_once("messagehandler.php");
require_once("indexer.php");
include("lib/common/datefunctions.php");

// Handle editor commands
if (isset($_POST["submit"])) {
    // Save file name
    $file = $_SESSION["file"];

    // Get metadata from input fields of layout
    $metadata = array();
    foreach ($_SESSION["metadata_fields"] as $field) {
      if (isset($_POST[$field])) {
        // Check if field has a proper value
        $metadata[$field] = Metadata::sanitize($field, $_POST[$field]);
      } else {
        $metadata[$field] = Metadata::get_default_value($field);
      }
    }
    $content = $_POST["post-text"];
    // Get existing metadata from file.
    $parts = explode(Config::$metadata_separator, $content, 2);
    if (count($parts) > 1) {
      // Some metadata values found. Match with corresponding keys.
      $metadata_from_file = $parts[0]; // Beginning of file is metadata.
      $metadata_values = Metadata::read($metadata_from_file); // Create array from metadata
      $metadata = array_merge($metadata, $metadata_values);
      $content = $parts[1];         // The rest is content.
    }
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
    $id = Metadata::get_id_from_filename($file);
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
        $indexer->increment_index($dir);
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
   * Directory from filename
   */
  private static function get_dir_from_filename($file) {
    $file = File::parent_dir($file);
    return $file;
  }

  /**
   * Edit a content entry
   */
  public static function edit($filename) {
    $file = $_SERVER["DOCUMENT_ROOT"] . "/" . Config::$page_dir . "/" . $filename;
    $_SESSION["file"] = $file; // Remember filename for saving.

    $content = File::read($file);

    // Create some input fields for metadata (like title or date) for the user.
    $metadata = array();
    if (isset($_GET["layout"])) {
      // Extract metadata from template (layout) file.
      $metadata_keys = Metadata::get_template_variables($_GET["layout"], self::$metadata_filter);
      $_SESSION["metadata_fields"] = $metadata_keys; // Remember input fields when saving.

      // Get existing metadata from file.
      $metadata_values = array();
      $parts = explode(Config::$metadata_separator, $content, 2);
      if (count($parts) > 1) {
        // Some metadata values found. Match with corresponding keys.
        $metadata_from_file = $parts[0]; // Beginning of file is metadata.
        $metadata_values = Metadata::read($metadata_from_file); // Create array from metadata
        $content = $parts[1];         // The rest is content.
      }
      // Get metadata values for input fields
      foreach($metadata_keys as $num => $key) {
        $field = array();
        $field["key"] = $key;
        if (array_key_exists($key, $metadata_values)) {
          $field["value"] = $metadata_values[$key];
          unset($metadata_values[$key]);
        } else {
          $field["value"] = Metadata::get_default_value($key);
        }
        array_push($metadata, $field);
      }
      // Put remaining metadata at beginning of edit area
      if (count($metadata_values) > 0) {
        $content = Metadata::create($metadata_values) . Config::$metadata_separator . $content;
      }
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
