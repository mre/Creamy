<?php

require_once("config.php");
require_once("lib/twig/Twig/Autoloader.php");
require_once("lib/spyc/spyc.php");
require_once("file.php");

define("TODAY", date("Y-m-d")); // Needed for default values array below.

class Metadata {

  private static $variable_suffix = "post.";

  // Default values and filters for metadata
  private static $defaults = array(
    "title" => array("value" => "Title", "check_function" => ""),
    "date"  => array("value" => TODAY, "check_function" => "valid_date")
  );

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
   * Get the default value for a metadata field.
   * E.g. return a default value for the field "title"
   */
  public static function get_default_value($metadata_field) {
    if (array_key_exists($metadata_field, self::$defaults)) {
      $field_defaults = self::$defaults[$metadata_field];
      if (array_key_exists("value", $field_defaults)) {
        return $field_defaults["value"];
      }
    }
    return "Insert " . $metadata_field; // Key does not exist.
  }

  /**
   * Run checks on metadata and return a proper value.
   */
  public static function sanitize($field, $value) {
    // Check if we have a default value for the field
    if (array_key_exists($field, self::$defaults)) {

      // Load default values and sanity checks for field.
      $field_defaults = self::$defaults[$field];

      // Do we have a sanity check function?
      if (isset($field_defaults["check_function"]) && $field_defaults["check_function"] != "") {
        // Run sanity check function
        $valid = call_user_func($field_defaults["check_function"], $value);
        if (!$valid) {
          // Field value is invalid. Return default.
          return $field_defaults["value"];
        }
      }
    }
    return $value;
  }

  /**
   * Load contents of a template file
   */
  private static function get_template_contents($file) {
    // Find file in template directories
    $root = $_SERVER["DOCUMENT_ROOT"] . "/";
    $custom_themes_dir = $root . Config::$theme_dir;
    $internal_themes_dir =  $root . Config::$creamy_theme_dir;

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
    return ""; // Hmm...no luck.
  }

  /**
   * Id from filename
   */
  public static function get_id_from_filename($file) {
    $filename = basename($file);
    $no_extension = explode("." , $filename);
    $id = explode("_", $no_extension[0]);
    return $id[0];
  }
}
?>
