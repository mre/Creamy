<?php

require_once("config.php");
require_once("file.php");
require_once("messagehandler.php");

define('METADATA_FILE' , "metadata");

class Indexer {

  private $metadata_file = METADATA_FILE;
  private $messagehandler;
  private $page_path; // Root directory

  public function __construct() {
    $this->messagehandler = MessageHandler::getInstance();
    $this->page_path = $_SERVER["DOCUMENT_ROOT"] . "/" . Config::$page_dir;
  }
  /**
   * Delete index of editable content areas.
   */
  public function remove_contents_file() {
    // Remove old contents file if it exists.
    if (!File::remove(Config::$contents_file))
      $this->messagehandler->show("Something went wrong while deleting the content index file. "
      . "Please check file permissions for <code>" . Config::$contents_file . "</code>.");
  }

  /**
   * Create an index of editable content areas.
   */
  public function create_contents_file() {
    // Recursively find all content files in main directory
    $search_dir = $this->page_path;
    $patterns = array(
      "/" . Config::$extension . "/",             // Find all content files
      "/" . Config::$multi_content_suffix . "$/"  // Find all content directories
    );
    $contents = File::find($patterns, $search_dir);

    foreach ($contents as $content_area) {
      File::write(Config::$contents_file, File::sanitized($content_area) . "\n");
    }
  }

  /**
   * Get the list of editable content areas.
   */
  public function parse_contents_file() {
    if (!File::exists(Config::$contents_file)) {
      // Recreate contents file.
      $this->create_contents_file();
      $this->messagehandler->show("Refreshed the list of editable content areas.");
    }

    // Load list of content areas
    $contents = File::read(Config::$contents_file);
    return $contents;
  }

  /**
   * Shows a list of all editable contents of a page.
   */
  public function get_content_overview() {
    // Load content information
    $raw_contents = $this->parse_contents_file();
    // Content areas are separated by newline (omitting empty lines)
    $contents = preg_split("[\n|\r]", $raw_contents, -1, PREG_SPLIT_NO_EMPTY);

    // Create the links
    $links = array();
    // Cleanup for content area name (remove suffixes)
    $filter = array("/" . Config::$multi_content_suffix . "/");
    foreach ( $contents as $content_area ) {
      $base = basename($content_area);
      $name = File::strip($filter, $base);

      // Check if content area can have multiple entries
      $pos = strpos($base, Config::$multi_content_suffix);
      if ($pos === false) {
        // Simple content area. Single entry.
        $is_dir = false;
        $link = $content_area . Config::$extension;
      } else {
        // Complex content area. Multiple entries.
        $is_dir = true;
        $link = $content_area;
      }
      $area = array("name" => $name, "link" => $link, "is_dir" => $is_dir);

      array_push($links, $area);
    }
    return $links;
  }

  /**
   * Get all posts for a content area
   */
  public function get_posts($content_area) {
    // Find all posts for this area.
    $content_dir = $this->page_path . "/" . $content_area;
    $pattern = array("/" . Config::$extension . "/");
    $contents = File::find($pattern, $content_dir);

    $dir_metadata = $this->get_dir_metadata($content_area);

    // Create the links
    $links = array();
    foreach ( $contents as $content_area ) {
      $base = basename($content_area);
      $name = File::strip($pattern, $base);
      $is_dir = false;
      $link = File::sanitized($content_area) . Config::$extension;
      $area = array("name" => $name, "link" => $link, "is_dir" => $is_dir, "metadata" => $dir_metadata);
      array_push($links, $area);
    }
    return $links;
  }

  /**
   * If the content file does not exist, create it.
   */
  public function init_content($name, $options = array()) {
    if ($options["multi"]) {
      $this->init_content_dir($name, $options);
    } else {
      $fullname = $name . Config::$extension;
      File::create($fullname);
    }
    $this->register($name, $options["layout"]);
  }

  public function get_next_file_id($dir) {
    // Read metadata
    $dir_metadata = $this->get_dir_metadata($dir);
    if (isset($dir_metadata["id"]))
      $id = $dir_metadata["id"];
    else
      $id = 1;
    return $id;
  }

  public function layout($dir) {
    // Read metadata
    $dir_metadata = $this->get_dir_metadata($dir);
    if (isset($dir_metadata["layout"]))
      return $dir_metadata["layout"];
    return "";
  }

  private function register($name, $layout) {
    // Put an entry into the list of content areas.
    if (!File::exists(Config::$contents_file)) {
      // Recreate contents file.
      $this->create_contents_file();
    } else {
      // Put a reference to the content area into index file
      File::write(Config::$contents_file, $name . " " . $layout);
    }
  }

  public function get_dir_metadata($dir) {
    // Read metadata file
    $metadata_path = $this->page_path . "/" . $dir . "/" . $this->metadata_file. Config::$metadata_extension;
    $raw = File::read($metadata_path);
    return Metadata::read($raw);
  }

  public function increment_index($dir) {
    // Get old index
    $dir_metadata = $this->get_dir_metadata($dir);
    // Write new index
    $dir_metadata["id"] = isset($dir_metadata["id"]) ? $dir_metadata["id"]+1 : 1;
    $metadata_path = $this->page_path . "/" . $dir . "/" . $this->metadata_file. Config::$metadata_extension;
    $this->create_metadata($metadata_path, $dir_metadata);
  }

  private function init_content_dir($dir_name, $options = array()) {
    $this->create_content_dir($dir_name);

    // Create a file with metadata for our new content area.
    $metadata = array("layout" => $options["layout"]);
    $metadata_path = $dir_name . $this->metadata_file . Config::$metadata_extension;
    $this->create_metadata($metadata_path, $metadata);
  }

  private function create_content_dir($name) {
    // Multiple entries (i.e. posts) are possible for this content area.
    // Therefore we create a folder that contains all entries.
    $content_dir = $name . Config::$multi_content_suffix;
    File::create_dir($content_dir);
  }

  private function create_metadata($metadata_path, $metadata) {
    $yaml = Metadata::create($metadata);
    File::write($metadata_path, $yaml, 'w');
  }
}

?>
