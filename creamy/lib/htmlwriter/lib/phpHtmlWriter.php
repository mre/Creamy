<?php

/**
 * Simple PHP HTML Writer
 *
 * @link      http://github.com/ornicar/php-html-writer
 * @version   1.3
 * @author    Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @license   MIT License
 *
 * Documentation: http://github.com/ornicar/php-html-writer/blob/master/README.markdown
 * Tickets:       http://github.com/ornicar/php-html-writer/issues
 */

require_once(dirname(__FILE__).'/phpHtmlWriterElement.php');

class phpHtmlWriter
{
  /**
   * @var phpHtmlWriterCssExpressionParser  the CSS expression parser instance
   */
  protected $cssExpressionParser;

  /**
   * @var phpHtmlWriterAttributeStringParser the attribute string parser instance
   */
  protected $attributeStringParser;

  /**
   * @var phpHtmlWriterAttributeArrayParser the attribute array parser instance
   */
  protected $attributeArrayParser;
  
  /**
   * @var array                   the writer options
   */
  protected $options = array(
    'element_class'           => 'phpHtmlWriterElement',
    'encoding'                => 'UTF-8' // used by htmlentities
  );

  /**
   * Instanciate a new HTML Writer
   *
   * @param   array $options
   */
  public function __construct(array $options = array())
  {
    $this->options = array_merge($this->options, $options);
  }

  /**
   * Render a HTML tag
   *
   * Examples:
   * $view->tag('p', 'text content')
   * $view->tag('div#my_id.my_class', 'text content')
   * $view->tag('div', $view->tag('p', 'textual content'))
   * $view->tag('a', array('title' => 'my title'), 'text content')
   *
   * @param   string  $cssExpression      a valid CSS expression like "div.my_class"
   * @param   mixed   $attributes         additional HTML attributes, or tag content
   * @param   string  $content            tag content if attributes are provided
   * @return  string                      the rendered tag
   */
  public function tag($cssExpression, $attributes = array(), $content = null)
  {
    /**
     * use $attributes as $content if needed
     * allow to use 2 or 3 parameters when calling the method:
     * ->tag('div', 'content')
     * ->tag('div', array('id' => 'an_id'), 'content')
     */
    if(empty($content) && (!empty($attributes) || !is_array($attributes)) && !is_array($attributes))
    {
      $content    = $attributes;
      $attributes = array();
    }

    list($tag, $attributes) = $this->parseTagAndAttributes($cssExpression, $attributes);

    /**
     * create an element instance
     * @var phpHtmlWriterElement
     */
    $element = new $this->options['element_class']($tag, $attributes, $content, $this->options['encoding']);

    return $element->render();
  }

  /**
   * Open a HTML tag
   *
   * Examples:
   * $view->open('p')
   * $view->open('div#my_id.my_class')
   * $view->open('a', array('title' => 'my title'))
   *
   * @param   string  $cssExpression      a valid CSS expression like "div.my_class"
   * @param   array   $attributes         additional HTML attributes
   * @return  string                      the rendered opening tag
   */
  public function open($cssExpression, array $attributes = array())
  {
    list($tag, $attributes) = $this->parseTagAndAttributes($cssExpression, $attributes);
    
    /**
     * create an element instance
     * @var phpHtmlWriterElement
     */
    $element = new $this->options['element_class']($tag, $attributes, null, $this->options['encoding']);

    return $element->renderOpen();
  }

  /**
   * Close a HTML tag
   *
   * Examples:
   * $view->close('p')
   *
   * @param   string  $tagName      the tag name to close
   * @return  string                the rendered closing tag
   */
  public function close($tagName)
  {
    // remove eventual css expressions or inline attributes
    list($tag, $attributes) = $this->getCssExpressionParser()->parse($tagName);

    /**
     * create an element instance
     * @var phpHtmlWriterElement
     */
    $element = new $this->options['element_class']($tag, array(), null, $this->options['encoding']);

    return $element->renderClose();
  }

  /**
   * Get the CSS expression parser instance
   *
   * @return  phpHtmlWriterCssExpressionParser  the CSS expression parser
   */
  public function getCssExpressionParser()
  {
    if(null === $this->cssExpressionParser)
    {
      require_once(dirname(__FILE__).'/parser/phpHtmlWriterCssExpressionParser.php');
      $this->cssExpressionParser = new phpHtmlWriterCssExpressionParser();
    }

    return $this->cssExpressionParser;
  }

  /**
   * Inject another CSS expression parser
   *
   * @param phpHtmlWriterCssExpressionParser $cssExpressionParser a parser instance
   */
  public function setCssExpressionParser(phpHtmlWriterCssExpressionParser $cssExpressionParser)
  {
    $this->cssExpressionParser = $cssExpressionParser;
  }

  /**
   * Get the attribute string parser instance
   *
   * @return  phpHtmlWriterAttributeStringParser  the attribute string parser
   */
  public function getAttributeStringParser()
  {
    if(null === $this->attributeStringParser)
    {
      require_once(dirname(__FILE__).'/parser/phpHtmlWriterAttributeStringParser.php');
      $this->attributeStringParser = new phpHtmlWriterAttributeStringParser();
    }

    return $this->attributeStringParser;
  }

  /**
   * Inject another attribute array parser instance
   *
   * @param phpHtmlWriterAttributeStringParser $attributeStringParser an attribute string parser instance
   */
  public function setAttributeStringParser(phpHtmlWriterAttributeArrayParser $attributeStringParser)
  {
    $this->attributeStringParser = $attributeStringParser;
  }

  /**
   * Get the attribute array parser instance
   *
   * @return  phpHtmlWriterAttributeArrayParser  the attribute array parser
   */
  public function getAttributeArrayParser()
  {
    if(null === $this->attributeArrayParser)
    {
      require_once(dirname(__FILE__).'/parser/phpHtmlWriterAttributeArrayParser.php');
      $this->attributeArrayParser = new phpHtmlWriterAttributeArrayParser();
    }

    return $this->attributeArrayParser;
  }

  /**
   * Inject another attribute array parser instance
   *
   * @param phpHtmlWriterAttributeArrayParser $attributeArrayParser an attribute array parser instance
   */
  public function setAttributeArrayParser(phpHtmlWriterAttributeArrayParser $attributeArrayParser)
  {
    $this->attributeArrayParser = $attributeArrayParser;
  }

  /**
   * Get HTML tag and attributes from a CSS expression and an attribute array
   *
   * @param   string  $cssExpression
   * @param   array   $attributes
   * @return  array   array(string HTML tag, array HTML attributes)
   */
  protected function parseTagAndAttributes($cssExpression, $attributes)
  {
    // get the tag and attributes from the CSS expression
    list($tag, $attrs) = $this->getCssExpressionParser()->parse($cssExpression);

    // merge with the additional HTML attributes passed by the css expression as inline attributes
    if(strpos($cssExpression, ' '))
    {
      $attrs = $this->mergeAttributes($attrs, $this->getAttributeStringParser()->parse($cssExpression));
    }
    
    // merge with the additional HTML attributes passed by the attributes array
    if(!empty($attributes))
    {
      $attrs = $this->mergeAttributes($attrs, $this->getAttributeArrayParser()->parse($attributes));
    }
    
    return array($tag, $attrs);
  }

  protected function mergeAttributes(array $attributes1, array $attributes2)
  {
    // manually merge the class attribute
    if(isset($attributes1['class']) && isset($attributes2['class']))
    {
      $attributes2['class'] = $this->mergeClasses($attributes1['class'], $attributes2['class']);
      unset($attributes1['class']);
    }

    return array_merge($attributes1, $attributes2);
  }

  protected function mergeClasses($classes1, $classes2)
  {
    return implode(' ', array_unique(array_merge(
      str_word_count($classes1, 1, '0123456789-_'),
      str_word_count($classes2, 1, '0123456789-_')
    )));
  }
}
