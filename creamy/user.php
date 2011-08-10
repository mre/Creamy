<?php

/**
 * User information and interaction.
 */
class User {

  /**
   * Check if user is logged in.
   */
  public function logged_in() {
    if (isset($_SESSION['username'])) return true;
    else return false;
  }

  /**
   * Check login data.
   */
  public function check_login() {
    if (!isset($_POST['username']))
      return; // No login process

    // Login process. Check user credentials.
    $password = @Config::$userinfo[$_POST['username']];
    if ($password != '' && $password == $_POST['password']) {
      // session_regenerate_id();
      $_SESSION['username'] = $_POST['username']; // Valid login
    }
  }

  public function check_logout() {
    if (isset($_GET['logout'])) {
      session_destroy();
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
  }
}
?>
