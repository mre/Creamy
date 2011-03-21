<?php

// Load necessary classes.
require_once("creamy/includes.php");

/**
 * This is the main class of creamy, a simple content
 * management system in the style of perch. 
 */
class Creamy {

  /**
   * Check if this script was included by another script.
   */
  public static function is_included() {
    $current_script = File::path($_SERVER['SCRIPT_FILENAME']);
    $this_file = File::path(__FILE__);
    return ( $current_script !=  $this_file );
  }

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
   * If the content file does not exist, create it.
   */
  public static function init_content($name) {
      $fullname = $name . Config::$extension;
      File::create($fullname);
  }

  /**
   * Present content on page.
   */
  public static function show_content($name) {
    $fullname = $name . Config::$extension;
    echo(Markdown(htmlentities(File::read($fullname))));
  }

  /**
   * Call to administration backend
   */
  public static function administration() {
    session_start();
    $user = new User();
    $user->check_logout();
    $user->check_login();

    if ($user->logged_in())
      Backend::show_backend();
    else
      Backend::show_login();
  }
}

// Check if backend got called.
if(!Creamy::is_included())
  Creamy::administration();

?>
