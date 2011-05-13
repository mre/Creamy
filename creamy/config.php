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

  // Internal paths relative to server root. 
  // Change only if you know what you are doing.
  static $theme_dir = "theme";
  static $creamy_dir = "creamy";
  static $creamy_theme_dir = "creamy/theme";

} 
?>
