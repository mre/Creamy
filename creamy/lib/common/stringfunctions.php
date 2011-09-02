<?php

   // Common string functions

  /**
   * Get a smaller part of a long text
   */
  function truncate($text, $limit) {
    if ($limit < 2) return $text;

    if (strlen($text) > $limit) {
      $words = str_word_count($text, 2);
      $pos = array_keys($words);
      $text = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
  }

?>
