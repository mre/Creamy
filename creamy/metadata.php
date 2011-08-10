<?php

require_once("config.php");
require_once("lib/twig/Twig/Autoloader.php");
require_once("lib/spyc/spyc.php");
require_once("file.php");

class Metadata {

  private static $variable_suffix = "post.";

  /**
   * Find variable tags in twig templates
   *
   * @param $template  The template file to scan
   * @param $filter    The variables to omit
   *
   * @return $variables The variables used inside the template
   */
  public static function get_template_variables($template, $filter = array()) {
    $file = $template . Config::$template_extension;
    $data =  self::get_template_contents($file);

    // Example tag that matches: {{ myvariable | filter1 | filter2 }}
    preg_match_all('~{{\s' . self::$variable_suffix . '(.+?)}}~i', $data, $matches);

    $variables = array(); // The variables found in the template
    foreach ($matches[1] as $match) {
      $match = explode("|" , $match); // Remove variable filters
      $match = strtolower($match[0]); // The first field is the variable name
      $match = trim($match);          // Remove whitespace from variable name

      // Filter variables
      if (!in_array($match, $filter)) {
        array_push($variables, $match);
      }
    }
    return $variables;
  }

  /**
   * Get YAML metadata from string
   */
  public static function read($string) {
    $metadata = Spyc::YAMLLoad($string);
    return $metadata;
  }

  /**
   * Create YAML metadata.
   */
  public static function create($metadata) {
    $yaml = Spyc::YAMLDump($metadata);
    return $yaml;
  }

  /**
   * Load contents of a template file
   */
  private static function get_template_contents($file) {
    // Find file in template directories
    $custom_themes_dir = Config::$theme_dir;
    $internal_themes_dir = $_SERVER["DOCUMENT_ROOT"] . "/" . Config::$creamy_theme_dir;

    // Is there a custom template with this name?
    $custom_file = $custom_themes_dir . "/" . $file;
    if (file_exists($custom_file)) {
      return file_get_contents($custom_file);
    }
    // Maybe we have luck in the internal theme directory
    $internal_file = $internal_themes_dir. "/" . $file;
    if (file_exists($internal_file)) {
      return file_get_contents($internal_file);
    }
    return ""; // Not found
  }
}
?>
