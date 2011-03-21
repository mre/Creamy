<?php

/**
 * A helper class for working with files
 */
class File {

  /**
   * Read raw file content.
   */
  public static function read($name) {
    $content = ""; 

    // Only read files with a given extension.
    $filename = $name . Config::$extension;
    $f = fopen($name, 'r');
    if($f != null) {
      $filesize = filesize($name);
      if($filesize != 0)
        $content = fread($f, $filesize);
    }
    fclose($f);

    return $content;
  }

  /**
   * Get sanitized path to file.
   */
  public static function path($file) { 
    return strtolower(realpath($file));
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
   */
  public static function find($pattern, $path = ".") {
    // Normalize file path
    $path = rtrim(str_replace("\\", "/", $path), '/') . '/';

    $matches = Array(); // Files that match the pattern
    $entries = Array(); // Files inside a directory

    // Read directory
    $dir = dir($path);
    while (false !== ($entry = $dir->read())) {
      $entries[] = $entry;
    }
    $dir->close();

    // Check each file if it matches the pattern
    foreach ($entries as $entry) {
      $fullname = $path . $entry;
      if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
        // Search recursively inside subdirectory
        $matches = array_merge(self::find($pattern, $fullname), $matches);
      } else if (is_file($fullname) && preg_match($pattern, $entry)) {
          // Found a file that matches the given pattern
          // Store as "entry" -> "path"
          $path_parts = pathinfo($entry);
          $matches[$path_parts['filename']] = $fullname;
      }
    }
    return $matches;
  }
}
?>
