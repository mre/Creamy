<?php

require_once("config.php");
require_once("file.php");

/**
 * Class for static html content of backend.
 */
class Backend {

  /**
   * Show static part of a website 
   */
  private static function part($name) {
    $root = File::path(".");
    $relative_path = Config::$creamy_dir . "/" . Config::$theme_dir;
    $theme_path = $root . "/" . $relative_path;
    $file = $theme_path . "/" . $name . Config::$part_extension;
    print(File::read($file));
  }

  /**
   * Print status information of current user 
   */
  private static function show_status() { 
    ?>
      <p>You are logged in as <?php echo($_SESSION['username']); ?></p>
      <p><a href="?logout=1">Logout</a></p>
    <?php
  }

  /**
   * Shows a list of all editable content of a page.
   */
  private static function list_contents() {
    // Find all content files.
    $contents = File::find("/" . Config::$extension . "/");
    foreach ( $contents as $content_name => $path ) {
      //self::print_list_item($content_name, $path);
    }
  }

  /**
   * Login page
   */
  public static function show_login() {
    self::part("header");
    self::part("login");
    self::part("footer");
  }

  /**
   * Admin page
   */
  public static function showBackend() {
    self::part("header");
    self::show_status();
    self::list_contents();
    self::part("footer");
  }
}
?>
