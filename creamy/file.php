<?php

/**
 * A helper class for working with files
 */
class File {

  /**
   * Check if a file exists.
   */
  public static function exists($filename) {
    return (file_exists($filename));
  }

  /**
   * Read raw file content.
   */
  public static function read($filename) {
    $content = "";
    $filename = self::sanitize_path($filename);

    // File must exist
    if(!file_exists($filename))
      return $content;

    // File exists. Open and read.
    $f = fopen($filename, 'r');
    if($f != null) {
      $filesize = filesize($filename);
      if($filesize != 0)
        $content = fread($f, $filesize);
      fclose($f);
    }
    return $content;
  }

  /**
   * Get sanitized path to file.
   */
  public static function path($file) {
    return strtolower(realpath($file));
  }


  /**
   * Remove double slashes from path.
   */
  public static function sanitize_path($path) {
    return str_replace("//","/", $path);
  }

  /**
   * Write raw file content.
   */
  public static function write($filename, $content, $mode='a') {
    $filename = self::sanitize_path($filename);
    $f = fopen($filename, $mode);
    if($f != null) {
      fwrite($f, $content);
      fclose($f);
      return true;
    }
    return false;
  }

  /**
   * Remove a file.
   */
  public static function remove($filename) {
    if (!file_exists($filename))
      return false;

    // File exists. Delete it.
    if(!@unlink($filename))
        return false; // Something went wrong.

    // File deleted.
    return true;
  }

  /**
   * Remove matches of regular expressions from a string
   */
  public static function strip(array $patterns, $string) {
    foreach ($patterns as $pattern) {
      $string = preg_replace($pattern, "", $string);
    }
    return $string;
  }

  /**
   * Get relative path to file and remove file extension
   */
  public static function sanitized($path, $stripExtension = true) {

      // Remove server root path
      $prefix = $_SERVER["DOCUMENT_ROOT"] . Config::$page_dir . "/";

      // Remove duplicate path separators.
      $prefix = str_replace("//","/",$prefix);
      $path = str_replace("//","/", $path);

      if (substr($path, 0, strlen($prefix) ) == $prefix) {
        $path = substr($path, strlen($prefix), strlen($path) );
      }

      if ($stripExtension) {
        // Remove file extension
        $path = self::remove_extension($path);
      }
      return $path;
  }

  function remove_extension($path) {
    $parts = pathinfo($path);
    if (isset($parts["extension"])) {
      $ext = $parts["extension"];
      return substr($path, 0, -strlen($ext) - 1);
    }
    return $path;
  }

  /**
   * Create a new directory if it does not exist.
   */
  public static function create_dir($dir) {
    if (!file_exists($dir)) {
      // Create empty dir
      mkdir($dir);
    }
  }

  /**
   * Create a new file if it does not exist.
   */
  public static function create($file) {
    if (!file_exists($file)) {
      // Create empty file
      $f = fopen($file, 'a') or die("Can't create file: " . $file . ".");
      fclose($f);
    }
  }

  /**
   * Recursively scan a directory for files that match a pattern.
   * Returns an associative array of (relative) paths.
   *
   * @param $pattern    Search pattern (regular expression)
   * @param $path       The path to the directory that will be scanned.
   * @param $filtered   Filtered directories that will not be scanned.
   *
   * @return $matches   Search results
   */
  public static function find(array $patterns, $path = ".", $filtered = array()) {
    $path = rtrim(str_replace("\\", "/", $path), '/') . '/'; // Normalize file path
    $entries = self::read_dir($path); // All files inside path
    $matches = Array(); // Files that match the pattern

    // Check each file if it matches the pattern
    foreach ($entries as $entry) {
      // Check if directory should be omitted
      if (in_array($entry, $filtered)) continue;
      if ($entry == '.' || $entry == '..') continue;

      $fullname = $path . $entry;

      if (self::is_matching($patterns, $entry)) {
          // We've got a match. Store and continue
          $matches[$entry] = $fullname;
          continue;
      } else {
        // No match.
        if (is_dir($fullname)) // Search recursively inside subdirectory
          $matches = array_merge(self::find($patterns, $fullname), $matches);
      }
    }
    return $matches;
  }

  /**
   * Check if a string matches a list of given patterns
   */
  public static function is_matching(array $patterns, $string) {
    foreach ($patterns as $pattern) {
      if (preg_match($pattern, $string))
        return true;
    }
    // No match
    return false;
  }

  /**
   * Get the parent directory of a string.
   * E.g. if the input is "/this/long/path" it will return "path"
   */
  public static function parent_dir($path) {
    $pathinfo = pathinfo($path, PATHINFO_DIRNAME);
    $pathinfo = array_filter( explode('/', $pathinfo) );
    $result = array_pop($pathinfo);
    return $result;
  }

  /**
   * Read directory
   */
  public static function read_dir($path) {
    // Read directory
    $dir = dir($path);
    while (false !== ($entry = $dir->read())) {
      $entries[] = $entry;
    }
    $dir->close();
    return $entries;
  }
}
?>
