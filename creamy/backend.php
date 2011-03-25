<?php

require_once("config.php");
require_once("template.php");

/**
 * Class for static html content of backend.
 */
class Backend {

  /**
   * Show part of page using the provided template file.
   */
  private function show_part($template_file, $values = array()) {
    $template = new Template($template_file . Config::$template_extension);
    foreach ( $values as $name => $value ) {
      $template->Set($name, $value);
    }
    echo $template->Display();
  }

  /**
   * Shows a list of all editable content of a page.
   */
  private static function list_contents() {
    // Find all content files.
    $contents = File::find("/" . Config::$extension . "/");
    foreach ( $contents as $content_name => $path ) {
      //self::print_list_item($content_name, $path);
    }
  }

  /**
   * Login page
   */
  public function show_login() {
    $this->show_part("header", array("title" => "Creamy Login"));
    $this->show_part("login");
    $this->show_part("footer");
  }

  /**
   * Admin page
   */
  public function show_backend() {
    $this->show_part("header", array("title" => "Creamy"));
    $this->show_part("status", array("user" => $_SESSION['username']));
    $this->list_contents();
    $this->show_part("footer");
  }
}
?>
