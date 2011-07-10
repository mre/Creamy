<?php

require_once("config.php");
require_once("file.php");
require_once("lib/twig/Twig/Autoloader.php");

class Backend {

  private $twig; // Templating engine
  private $messages; // Messages to user from interface

  /**
   * Instantiate the backend.
   */
  public function __construct() {
    $this->twig = $this->init_templating_engine();
    $this->messages = array();
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
    $cache_dir = $root . "/" . Config::$page_dir . "/" . Config::$cache;
    $twig = new Twig_Environment($loader, array('cache' => $cache_dir));
    return $twig;
  }

  /**
   * Delete index of editable content areas.
   */
  public function remove_contents_file() {
    // Remove old contents file if it exists.
    File::remove(Config::$contents_file);
  }

  /**
   * Create an index of editable content areas.
   */
  private function create_contents_file() {
    // Recursively find all content files in main folder 
    $root = $_SERVER["DOCUMENT_ROOT"];
    $search_dir = $root . "/" . Config::$page_dir;
    $contents = File::find("/" . Config::$extension . "/", $search_dir);
    foreach ($contents as $content_area) {
       File::write(Config::$contents_file, File::sanitized($content_area) . "\n");
    }
  }

  /**
   * Get the list of editable content areas.
   */
  private function parse_contents_file() {
    if (!File::exists(Config::$contents_file)) {
      // Recreate contents file.
      //$this->msg_box("Refreshed the list of editable content areas.");
      $this->create_contents_file();
    }

    // Load list of content areas
    $contents = File::read(Config::$contents_file);
    return $contents;
  }

  /**
   * Shows a list of all editable content of a page.
   */
  private function get_contents() {
    // Load content information
    $raw_contents = $this->parse_contents_file();
    // Content areas are separated by newline (omitting empty lines)
    $contents = preg_split("[\n|\r]", $raw_contents, -1, PREG_SPLIT_NO_EMPTY);

    // Create the links
    $links = array();
    foreach ( $contents as $content_area ) {
      $area = array(
        "name" => $content_area,
        "link" => "editor.php?file=" . $content_area . Config::$extension
      );
      array_push($links, $area);
    }
    return $links;
  }


  /**
   * The backend holds a list of messages that will be displayed on demand.
   */
  public function show_message($message) {
    array_push($this->messages, $message);
  }

  /**
   * Login page
   */
  public function show_login() {
    $this->display("cms_login", array("title" => "Login", "loginstatus" => "Not logged in."));
  }

  /**
   * Admin page
   */
  public function show_backend() {
    $content_areas = $this->get_contents();
    $this->display("cms_backend", array(
      'title' => 'Administration',
      'areas' => $content_areas,
      'loginstatus' => 'Logged in as '. $_SESSION['username'] . '.'));
  }

  /**
   * If the content file does not exist, create it.
   */
  public function init_content($name) {
      $fullname = $name . Config::$extension;
      File::create($fullname);
  }

  /**
   * Present content on page.
   */
  public function show_content($name) {
    $fullname = $name . Config::$extension;
    echo(Markdown(File::read($fullname)));
  }


  /*
   * Render part of website
   */
  public function display($template_name, $arguments) {
    $template = $this->twig->loadTemplate($template_name . Config::$template_extension);
    // Also show all messages
    $arguments["messages"] = $this->messages;
    print($template->render($arguments));
  }
}

?>
