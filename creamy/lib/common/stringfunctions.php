<?php

  // Common string functions

  /**
   * Prettify a string for human beings.
   */
  function prettify($string, $remove_parts = array(), $capitalize = true) {
    // Remove string parts
    foreach ($remove_parts as $part) {
      $string = str_replace($part, "", $string);
    }
    // Capitalize string
    if ($capitalize) {
      $string = ucwords($string);
    }

    // Remove underscores and dashes
    $string = str_replace("-", " ", trim($string));
    $string = str_replace("_", " ", $string);
    return $string;
  }

  /**
   * Get a smaller part of a long text
   */
  function truncate($text, $limit = 200, $ellipsis = "") {
    if (strlen($text) > $limit) {
        $text = wordwrap($text, $limit);
        $text = substr($text, 0, strpos($text, "\n"));
        // Add optional ellipsis
        $text .= " " . $ellipsis;
    }
    return $text;
  }

?>
