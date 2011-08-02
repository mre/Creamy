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

  ////////////////////
  // Advanced configuration.
  // You might not need to edit this.
  static $extension = ".mkdn"; // Extension for content files
  static $template_extension = ".html"; // Using plain php files for templates

  static $page_dir = ""; // Absolute path to page on server (leave blank if root)
  static $theme_dir = "theme"; // Place for custom theme files (absolute path from server root)

  ////////////////////
  // Internal settings.
  // Change only if you know what you are doing!

  // Paths relative to server root.
  static $creamy_dir = "creamy"; // Home of creamy on server
  static $creamy_theme_dir = "creamy/theme"; // Internal theme
  static $cache = "creamy/cache"; // Path to cache

  static $use_cache = false; // Enable / disable internal template cache
  static $contents_file = "contents.txt"; // Store the list of editable contents inside this file
  static $defaults = array(   // Advanced options for content areas.
    "layout"   => "",         // Default layout for content area
    "markdown" => true        // Markdown support for content areas
  );

}
?>
