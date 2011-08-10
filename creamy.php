<?php

// Load necessary classes.
require_once("creamy/config.php");
require_once("creamy/backend.php");
require_once("creamy/file.php");


// Check if backend got called.
if(!Creamy::is_included()) {
  // Redirect to backend
  header("Location: " . Config::$creamy_dir);
}

/**
 * This is the main class of creamy, a free and simple content
 * management system in the style of perch.
 */
class Creamy {

  /**
   * Return the parsed markdown content to the calling page.
   */
  public static function content($content_area, array $options = array()) {

    // Check for additional parameters via GET
    $url_params = self::get_parameters();
    // Merge with options passed by frontend and defaults
    $options = array_merge(Config::$default_options, $url_params, $options);

    // Check if content region is alread initialized.
    $indexer = new Indexer();
    $indexer->init_content($content_area, $options);
    // Show content on page.
    $backend = new Backend();
    $backend->show_content($content_area, $options);
  }

  /**
   * Show a theme (static code snippet)
   */
  public static function theme($theme_name, array $options = array()) {
    $backend = new Backend();
    $backend->display($theme_name, $options);
  }

  /**
   * Extract GET parameters from the current url.
   */
  private static function get_parameters() {
    if(!empty($_GET)) {
      return $_GET;
    } else {
      return array();
    }
  }

  /**
   * Check if this script was included by another script.
   */
  public static function is_included() {
    $current_script = File::path($_SERVER['SCRIPT_FILENAME']);
    $this_file = File::path(__FILE__);
    return ( $current_script !=  $this_file );
  }

 
 }
?>
