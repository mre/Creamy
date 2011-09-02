<?php

require_once("config.php");
require_once("file.php");
require_once("indexer.php");
require_once("lib/twig/Twig/Autoloader.php");
require_once("lib/markdown/markdown.php");
require_once("messagehandler.php");
require_once("metadata.php");
include("lib/common/stringfunctions.php");

class Backend {

  private $twig; // Templating engine
  /**
   * Instantiate the backend.
   */
  public function __construct() {
    $this->twig = $this->init_templating_engine();
    $this->indexer = new Indexer();
    $this->messagehandler = MessageHandler::getInstance();
  }

  /**
   * Create a new instance for the templating engine
   */
  private function init_templating_engine() {
    Twig_Autoloader::register();
    $root = $_SERVER["DOCUMENT_ROOT"];

    // Custom themes
    $theme_dir = $root . "/" . Config::$theme_dir;

    // System themes
    $creamy_theme_dir = $root . "/" . Config::$creamy_theme_dir;

    // Look for themes in this order: Custom themes, system themes.
    $loader = new Twig_Loader_Filesystem(array($theme_dir, $creamy_theme_dir));

    // Compilation cache
    if (Config::$use_cache) {
      $cache_dir = $root . "/" . Config::$page_dir . "/" . Config::$cache;
      $twig = new Twig_Environment($loader, array('cache' => $cache_dir));
    } else {
      $twig = new Twig_Environment($loader);
    }
    return $twig;
  }

  /**
   * Delete index of editable content areas.
   */
  public function remove_contents_file() {
    $this->indexer->remove_contents_file();
  }

  /**
   * Login page
   */
  public function show_login() {
    $options = array("title" => "Login", "loginstatus" => "Not logged in.");
    $this->display("cms_login", $options);
  }

  /**
   * Admin page
   */
  public function show_backend() {
    if (isset($_GET["dir"])) {
      $listing = $this->listing_content_area($_GET["dir"]);
    } else {
      $listing = $this->listing_overview();
    }
    $this->display("cms_backend", $listing);
  }

  /**
   * Get an overview of all content areas.
   */
  private function listing_overview() {
    $listing = array();
    $listing["title"]  = "Dashboard";
    $listing["desc"]   = "Select content area to edit";
    $listing["areas"]  = $this->indexer->get_content_overview();
    $listing["buttons"] = array(
      array("name" => "Refresh list", "link" => "index.php?refresh=1", "icon" => "refresh"));
    return $listing;
  }

  /**
   * Get all entries for a specific content area
   */
  private function listing_content_area($dir) {
    $listing = array();
    $listing["title"] = $dir;
    $listing["desc"]  = "Entries in " . $dir . ":";
    $posts = $this->indexer->get_posts($dir);


    // Absolute path on server
    $root = $_SERVER["DOCUMENT_ROOT"];
    $absolute_dir = $root . "/" . $dir;
    $meta = $this->indexer->get_dir_metadata($absolute_dir);

    if (isset($meta["layout"])) {
      $layoutpart = "&layout=" . $meta["layout"];
    } else {
      $layoutpart = "";
    }
    $listing["areas"] = $posts;
    if(empty($listing["areas"])) {
      $this->messagehandler->show("Create your first post by clicking on <em>New entry</em> below.");
    }

    if (isset($meta["id"])) {
      $next_id = $meta["id"];
    } else {
      $next_id = 1;
    }
    $new_file = $dir . "/" . $next_id . Config::$extension;

    // Button to create new entry.
    $button_new = array("name" => "New entry", 
      "link" => "editor.php?file=" . $new_file . "&new=true" . $layoutpart, "icon" => "new");
    // Button to go back to overview
    $button_back = array("name" => "Back", "link" => "index.php", "icon" => "back");

    $listing["buttons"] = array($button_back, $button_new);

    return $listing;
  }

  /**
   * Show content on frontend.
   */
  public function show_content($name, $options = array()) {
    if ($options["multi"]) {
      $this->show_multi($name, $options);
    } else {
      $fullname = $name . Config::$extension;
      $raw = File::read($fullname);
      if ($options["markdown"])
        echo(Markdown($raw));
      else
        echo($raw);
    }
  }

  /**
   * Show multiple contents (i.e. posts) at once
   */
  private function show_multi($name, $options = array()) {
    // Load contents from this directory
    $dir = $name . Config::$multi_content_suffix;

    // Show archive of content entries?
    // if ($options["archive"]) {}

    // Check for single content id
    //if (@$options["id"]) { ... }

    // Otherwise show a bunch of current entries
    $this->paginate($dir, $options);
  }

  /**
   * Show requested entries of content area
   */
  private function paginate($dir, $options) {
    // How many entries per page?
    $limit = $options["limit"];
    // Which page?
    $page = $options["page"] > 0 ? $options["page"] : 1;
    // Calculate first entry to show
    $offset = ($page - 1) * $limit;
    // Find entries in content dir
    $patterns = array("/" . Config::$extension . "/");
    $entries = File::find($patterns, $dir);
    // Select the requested entries
    $requested = array_slice($entries, $offset, $limit);

    $this->show_entries($requested, $options);
  }

  /**
   * Process a bunch of requested entries of a content area.
   */
  private function show_entries($entries, $options = array()) {

    if (empty($entries)) {
      $this->display("cms_404", array("error" => "No posts with this index"));
      return;
    }

    // All entries will be collected and sent to the templating engine
    // afterwards if a layout is given.
    $posts = array();
    $truncate_length = $options["truncate"];

    // Show each entry on frontend
    foreach($entries as $entry) {
      $raw = File::read($entry);

      // Split raw input into metadata and content
      $data = explode(Config::$metadata_separator, $raw, 2);
      if (count($data) > 1) {
        // Extract metadata
        $entry = Metadata::read($data[0]);
        $entry["text"] = $data[1];
      } else {
        // No metadata
        $entry = array();
        $entry["text"] = $data[0];
      }
      if ($truncate_length > 0) {
        $entry["text"] = truncate($entry["text"], $truncate_length);
      }
      if ($options["markdown"])
        $entry["text"] = Markdown($entry["text"]);

      // Show layout?
      if(empty($options["layout"])) {
        // Nope. Direct output.
        echo $entry["text"];
      } else {
        // Store for later
        array_push($posts, $entry);
      }
    }

    if(!empty($options["layout"])) {
      // Send to templating engine
      $vars = array();
      $vars["posts"] = $posts;
      $vars["options"] = $options;
      $this->display($options["layout"], $vars);
    }
  }

  /*
   * Render part of website
   */
  public function display($template_name, array $arguments) {
    $template = $this->twig->loadTemplate($template_name . Config::$template_extension);
    // Also show all messages
    $arguments["messages"] = $this->messagehandler->messages;
    if (isset($_SESSION["username"]))
      $arguments["loginstatus"] = 'Logged in as '. $_SESSION['username'] . '.';
    print($template->render($arguments));
  }
}
?>
