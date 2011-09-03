<?php

function valid_date($str) {
  $timestamp = @strtotime($str);
  return ($timestamp === true);
}

?>
