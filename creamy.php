<?php

// Load necessary classes.
require_once("creamy/config.php");
require_once("creamy/file.php");
require_once("creamy/lib/markdown/markdown.php");
require_once("creamy/backend.php");

// Check if backend got called.
if(!Creamy::is_included()) {
  // Redirect to backend
  header("Location: " . Config::$creamy_dir);
}

/**
 * This is the main class of creamy, a simple content
 * management system in the style of perch. 
 */
class Creamy {

  /**
   * If this function is called from the frontend
   * it returns the parsed content to the calling page.
   */
  public static function content($name) {
    if (self::is_included()) {
      // Check if content region is alread initialized.
      self::init_content($name);
      // Show content on page.
      self::show_content($name);
    }
  }

  /**
   * If this function is called from the frontend
   * it returns a template.
   */
  public static function theme($name, $values = array()) {
    $backend = new Backend();
    $backend->show_part($name, $values);
  }

  /**
   * Check if this script was included by another script.
   */
  public static function is_included() {
    $current_script = File::path($_SERVER['SCRIPT_FILENAME']);
    $this_file = File::path(__FILE__);
    return ( $current_script !=  $this_file );
  }

  /**
   * If the content file does not exist, create it.
   */
  private static function init_content($name) {
      $fullname = $name . Config::$extension;
      File::create($fullname);
  }

  /**
   * Present content on page.
   */
  private static function show_content($name) {
    $fullname = $name . Config::$extension;
    echo(Markdown(File::read($fullname)));
  }
}
?>
