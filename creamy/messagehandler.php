<?php

/**
 * MessageHandler
 *
 * Manages the messages shown on the frontend.
 * Implemented as singleton.
 */
final class MessageHandler {

  public $messages; // Messages to user from interface

  // Create instance
   private static $instance = NULL;

  private function __construct() {
    $this->messages = array();
  }

  // Return instance
  public static function getInstance() {
     if (NULL === self::$instance) {
         self::$instance = new self;
     }
     return self::$instance;
  }
  // Prevent clone
  private function __clone() {}

  /**
   * The handler holds a list of messages that can be displayed on demand.
   */
  public function show($message) {
    array_push($this->messages, $message);
  }
}


?>
