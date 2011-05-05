<?php

/**
 * A helper class for working with files
 */
class File {

  /**
   * Read raw file content.
   */
  public static function read($filename) {
    $content = ""; 

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
   * Write raw file content.
   */
  public static function write($filename, $content) {
    $f = fopen($filename, 'w');
    if($f != null) {
      fwrite($f, $content);
      fclose($f);
      return true;
    }
    return false;
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
