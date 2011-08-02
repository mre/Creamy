<!-- Include creamy into any static page to make a dynamic page out of it -->
<?php require_once("creamy.php"); ?>

<!-- Use dynamic template file and fill in values for the placeholders -->
<?php Creamy::theme("header", array("title" => "Creamy. A simple CMS.")); ?>

<!-- Insert an editable content area -->
<?php Creamy::content("README"); ?>

<!-- Include a static template file without any placeholders for variable content (short version). -->
<?php Creamy::theme("footer"); ?>
