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
   * Write raw file content.
   */
  public static function write($filename, $content, $mode='a') {
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
    if (file_exists($filename)) {
      unlink($filename);
      return true;
    } else {
      // File does not exist. Nothing to delete.
      return false;
    }
  }

  /**
   * Get relative path to file
   */
  public static function sanitized($file, $stripExtension = true) {

      // Remove server root path
      $prefix = $_SERVER["DOCUMENT_ROOT"] . Config::$page_dir . "/";
      if (substr($file, 0, strlen($prefix) ) == $prefix) {
        $file = substr($file, strlen($prefix), strlen($file) );
      }

      if ($stripExtension) {
        // Remove file extension
        $file = substr($file, 0,strrpos($file, '.'));
      }
      return $file;
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
