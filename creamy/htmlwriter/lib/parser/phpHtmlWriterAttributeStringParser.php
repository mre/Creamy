<?php

/**
 * Responsible to parse user inline attributes like "rel=nofollow"
 *
 * @author    Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @license   MIT License
 */

class phpHtmlWriterAttributeStringParser
{

  /**
   * Entry point of the class, parse an attribute array
   *
   * @param   array    $attributes    the attribute array
   * @return  array                   the parsed attributes
   */
  public function parse($expression)
  {
    if(!is_string($expression))
    {
      throw new InvalidArgumentException('The inline attributes must be a string, '.gettype($expression).' given');
    }
    
    if(empty($expression) || false === strpos($expression, '='))
    {
      return array();
    }

    return $this->stringToArray($expression);
  }
  
  /**
   * Converts string to array
   *
   * @param  string $string  the value to convert to array
   *
   * @return array
   */
  protected function stringToArray($string)
  {
    // regex credits: symfony 1.4 http://symfony-project.org/
    preg_match_all('/
      \s*(\w+)              # key                               \\1
      \s*=\s*               # =
      (\'|")?               # values may be included in \' or " \\2
      (.*?)                 # value                             \\3
      (?(2) \\2)            # matching \' or " if needed        \\4
      \s*(?:
        (?=\w+\s*=) | \s*$  # followed by another key= or the end of the string
      )
    /x', $string, $matches, PREG_SET_ORDER);

    $attributes = array();
    foreach ($matches as $val)
    {
      $attributes[$val[1]] = $val[3];
    }

    return $attributes;
  }

}