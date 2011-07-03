<?php

/**
 * Here are some shortcut functions to phpHtmlWriter methods.
 * Include this file only if you want to use such non OO shortcuts.
 */

require_once(dirname(__FILE__).'/phpHtmlWriter.php');
$phpHtmlWriterInstance = new phpHtmlWriter();

/**
 * @see phpHtmlWriter::tag()
 */
function tag($cssExpression, $attributes = array(), $content = null)
{
  global $phpHtmlWriterInstance;
  return $phpHtmlWriterInstance->tag($cssExpression, $attributes, $content);
}

/**
 * @see phpHtmlWriter::open()
 */
function open($cssExpression, array $attributes = array())
{
  global $phpHtmlWriterInstance;
  return $phpHtmlWriterInstance->open($cssExpression, $attributes);
}

/**
 * @see phpHtmlWriter::close()
 */
function close($tagName)
{
  global $phpHtmlWriterInstance;
  return $phpHtmlWriterInstance->close($tagName);
}