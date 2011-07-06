<!-- Include creamy into any static page to make a dynamic page out of it -->
<?php require_once("creamy.php"); ?>

<!-- Use dynamic template file and fill in values for the placeholders -->
<?php 
  $header = array("theme" => "header", "title" => "Welcome to Creamy");
  Creamy::content($header);
?>

<!-- Insert an editable content area -->
<?php Creamy::content("README"); ?>

<!-- Include a static template file without any placeholders for variable content (short version). -->
<?php Creamy::content(array("theme" => "footer")); ?>
