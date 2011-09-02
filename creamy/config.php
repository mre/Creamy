<?php

/**
 * Creamy configuration
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
  static $extension = ".mkdn";          // Extension for content files
  static $template_extension = ".html"; // Using plain html files for templates
  static $metadata_extension = ".yaml"; // Metadata (like content layout etc.)

  static $page_dir = ""; // _Absolute_ path to page on server (leave blank if it is the root dir)
  static $theme_dir = "theme"; // Place for custom theme files (_absolute_ path from server root)

  ////////////////////
  // Internal settings.
  // Change only if you know what you are doing!

  // Paths _relative_ to page directory.
  static $creamy_dir = "creamy";      // Home of creamy on server
  static $creamy_theme_dir = "creamy/theme"; // Internal themes
  static $cache = "creamy/cache";     // Path to cache

  static $use_cache = false;          // Enable or disable internal template cache
  static $contents_file = "contents.txt"; // Store the list of editable contents inside this file

  static $default_options = array(    // Advanced options for complex content areas.
    "layout"    => "",                // Default layout
    "markdown"  => true,              // Markdown support
    "multi"     => false,             // Multiple entries for each content area by default.
    "truncate"  => 200,               // Number of words in truncated preview text
    "page"      => 1,                 // First page id (only for multiple entries)
    "limit"     => 4                  // Number of entries on one page
  );

  // Each content file may contain yaml metadata at the beginning.
  // This special token separates it from the main content:
  static $metadata_separator = "---";

  // If a content area can have multiple entries, creamy will create a
  // directory that contains all entries. The name of the directory is
  // the name of the content area followed by this suffix:
  static $multi_content_suffix = "_content";
}
?>
