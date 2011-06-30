<?php

/**
 * Class that holds all configuration parameters
 */
class Config {

  // Edit usernames and passwords
  static $userinfo = array(
    'user1'=>'pass1',
    'user2'=>'pass2'
  );

  // Advanced configuration.
  // You might not need to edit this.

  // Extension for content files
  static $extension = ".mkdn";

  // Using plain php files for templates
  static $template_extension = ".php";

  // Internal settings.
  // Change only if you know what you are doing.

  // Paths relative to server root.
  static $page_dir = ""; // Location of page on server (leave blank if root)
  static $theme_dir = "theme"; // Place for custom theme files
  static $creamy_dir = "creamy"; // Home of creamy on server
  static $creamy_theme_dir = "creamy/theme"; // Internal theme

  static $contents_file = "contents.txt";
}
?>
