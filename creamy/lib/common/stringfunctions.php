<?php

  // Common string functions

  /**
   * Get a smaller part of a long text
   */
  function truncate($text, $limit = 200, $ellipsis = "...") {
    if (strlen($text) > $limit) {
        $text = wordwrap($text, $limit);
        $text = substr($text, 0, strpos($text, "\n")) . $ellipsis;
    }
    return $text;
  }

?>
