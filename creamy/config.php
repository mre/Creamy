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
  // You don't need to edit this.

  // Extension for content files
  static $extension = ".mkdn";

  // Extension for html parts (like header, footer...)
  static $part_extension = ".html";

  // Paths
  static $creamy_dir = "creamy";
  static $theme_dir  = "theme";
}

?>
