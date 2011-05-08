<!-- Include creamy into any static page to make a dynamic page out of it -->
<?php require_once("creamy.php"); ?>

<!-- Use dynamic template file and fill in values for the placeholders -->
<?php Creamy::theme("header", array("title" => "Welcome to Creamy")); ?>

<!-- Insert an editable content area -->
<?php Creamy::content("README"); ?>

<!-- Include a static template file without any placeholders for variable content -->
<?php Creamy::theme("footer"); ?>
