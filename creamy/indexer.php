<?php

require_once("config.php");
require_once("file.php");
require_once("messagehandler.php");
require_once("metadata.php");

define('METADATA_FILE' , "metadata");

class Indexer {

  private $metadata_file = METADATA_FILE; // Store metadata inside this file.
  private $messagehandler; // Show messages on admin page.
  private $page_path; // Path to root directory.

  /**
   * Create a new indexer object
   */
  public function __construct() {
    $this->messagehandler = MessageHandler::getInstance();
    $this->page_path = $_SERVER["DOCUMENT_ROOT"] . "/" . Config::$page_dir;

    // Absolute path to contents file
    $this->contents_file = $this->page_path . "/" . Config::$creamy_dir . "/" . Config::$contents_file;
  }

  /**
   * Delete index of editable content areas.
   */
  public function remove_contents_file() {
    // Remove old contents file if it exists.
    if (!File::remove($this->contents_file))
      $this->messagehandler->show("Something went wrong while deleting the content index file. "
      . "Please check file permissions for <code>" . $contents_file . "</code>.");
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
      File::write($this->contents_file, File::sanitized($content_area) . "\n");
    }
  }

  /**
   * Get the list of editable content areas.
   */
  public function parse_contents_file() {
    if (!File::exists($this->contents_file)) {
      // Recreate contents file.
      $this->create_contents_file();
      $this->messagehandler->show("Refreshed the list of editable content areas.");
    }

    // Load list of content areas
    $raw_contents = File::read($this->contents_file);

    // Content areas are separated by newline (omitting empty lines)
    $contents = preg_split("[\n|\r]", $raw_contents, -1, PREG_SPLIT_NO_EMPTY);

    return $contents;
  }

  /**
   * Shows a list of all editable contents of the website.
   */
  public function get_content_overview() {
    // Load content information
    $contents = $this->parse_contents_file();

    // Create links for each content area.
    return $this->create_links($contents);
  }

  /**
   * Create a bunch of links.
   */
  private function create_links($contents) {
    $links = array();

    // Cleanup for content area name (remove suffixes)
    $filter = array("/" . Config::$multi_content_suffix . "/");
    foreach ( $contents as $content_area ) {
      $base = basename($content_area);
      $name = File::strip($filter, $base);

      // Check if content area can have multiple entries
      $multi = strpos($base, Config::$multi_content_suffix);
      if ($multi === false) {
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

    // Create the links
    $links = array();
    foreach ( $contents as $content_area ) {
      $base = basename($content_area);
      $name = File::strip($pattern, $base);
      $is_dir = false;
      $link = File::sanitized($content_area) . Config::$extension;
      $area = array("name" => $name, "link" => $link, "is_dir" => $is_dir);
      array_push($links, $area);
    }
    return $links;
  }

  /**
   * If the content file does not exist, create it.
   */
  public function init_content($name, $options = array()) {
    if ($options["multi"]) {
      // Multiple entries
      $this->init_content_dir($name, $options);
    } else {
      // Single entry
      $fullname = $name . Config::$extension;
      File::create($fullname);
    }
  }

  /**
   * Every entry has an id. Get the next free one.
   */
  public function get_next_file_id($dir) {
    // Read metadata
    $dir_metadata = $this->get_dir_metadata($dir);
    if (isset($dir_metadata["id"])) {
      $id = $dir_metadata["id"];
    } else {
      $id = 1;
    }
    return $id;
  }

  /**
   * Get the layout used by a content area
   */
  public function layout($dir) {
    // Read metadata
    $dir_metadata = $this->get_dir_metadata($dir);
    if (isset($dir_metadata["layout"])) {
      return $dir_metadata["layout"];
    }
    return "";
  }

  /**
   * Get metadata for content area directory.
   */
  public function get_dir_metadata($metadata_dir) {
    // Read metadata file
    $metadata_file = $this->metadata_file. Config::$metadata_extension;
    $metadata_path = $metadata_dir . "/" . $metadata_file;
    $raw = File::read($metadata_path);
    return Metadata::read($raw);
  }

  /**
   * Set the next free file id.
   */
  public function increment_index($dir) {
    // Get old index
    $dir_metadata = $this->get_dir_metadata($dir);
    // Write new index
    $dir_metadata["id"] = isset($dir_metadata["id"]) ? $dir_metadata["id"]+1 : 1;
    $this->create_metadata($dir, $dir_metadata);
  }

  /**
   * Create a directory for all content entries of this content area.
   */
  private function init_content_dir($dir_name, $options = array()) {
    $this->create_content_dir($dir_name);

    // Create a file with metadata for our new content area.
    $metadata_path = $dir_name . Config::$multi_content_suffix;
    $metadata = array("layout" => $options["layout"], "id" => 1);
    $this->create_metadata($metadata_path, $metadata, false);
  }

  /**
   * Create a folder that contains all content entries.
   */
  private function create_content_dir($name) {
    // Multiple entries (i.e. posts) are possible for this content area.
    $content_dir = $name . Config::$multi_content_suffix;
    File::create_dir($content_dir);
  }

  /**
   * Create metadata. Overwrite existing metadata by default.
   */
  private function create_metadata($dir, $metadata, $overwrite = true) {
    // Create metadata
    $yaml = Metadata::create($metadata);

    // Write metadata
    $metadata_file = $dir . "/" . $this->metadata_file . Config::$metadata_extension;

    if (File::exists($metadata_file)) {
      if (!$overwrite) {
        return;
      }
    }
    File::write($metadata_file, $yaml, 'w');
  }
}

?>
