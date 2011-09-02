<?php

function valid_date($str) {
  $timestamp = @strtotime($str);
  if ( $timestamp === false) {
    return @date("Y-m-d");
  } else {
    return @date("Y-m-d", $timestamp);
  }
}

?>
