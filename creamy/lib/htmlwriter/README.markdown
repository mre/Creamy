# PHP HTML Writer

Create HTML tags and render them efficiently.

## Overview

    $html->tag('div', 'some content')
    // returns <div>some content</div>

    $html->tag('div#my_id.my_class')
    // returns <div id="my_id" class="my_class"></div>

    $html->tag('a.my_class rel=nofollow', 'some content')
    // returns <a class="my_class" rel="nofollow">some content</a>

### Why you should use it

 - it always generates valid HTML and XHTML code
 - it makes templates cleaner
 - it's easy to use, fast to execute, fully tested and documented

## Instanciate an HTML Writer

    require_once('/path/to/php-html-writer/lib/phpHtmlWriter.php');

    $html = new phpHtmlWriter();

## Render tags

Use the ->tag() method to create a tag element.
The first argument is the HTML tag name, like div or span.
The second argument is the tag content.

### Simple tags

    echo $html->tag('div')
    <div></div>

    echo $html->tag('p', 'some content')
    <p>some content</p>

### CSS expressions

The first argument accepts CSS expressions.
It allows to specify very quickly the tag id and classes

    echo $html->tag('div#my_id')
    <div id="my_id"></div>

    echo $html->tag('div.my_class')
    <div class="my_class"></div>

    echo $html->tag('div#my_id.my_class.another_class')
    <div id="my_id" class="my_class another_class"></div>

### Inline attributes

The first argument also accepts inline attributes.
It allows to specify every HTML attribute like href or title.

    echo $html->tag('a href="http://github.com"')
    <a href="http://github.com"></a>

    echo $html->tag('a rel=nofollow href="http://github.com" title="Social Coding"')
    <a rel="nofollow" href="http://github.com" title="Social Coding"></a>

    echo $html->tag('span lang=es', 'Vamos a la playa, señor zorro')
    <span lang="es">Vamos a la playa, señor zorro</span>

    echo $html->tag('input type=text value="my value"')
    <input type="text" value="my value" />

You can use both CSS expressions and inline attributes:

    echo $html->tag('a#my_id.my_class.another_class href="http://github.com"', 'Github');
    <a id="my_id" class="my_class another_class" href="http://github.com">Github</a>

### Array attributes

If you prefer, you can pass HTML attributes like href and title with an array.
Pass the attributes array as the second argument, and the tag content as the third argument

    echo $html->tag('a', array('href'=>'http://github.com'), 'GitHub');
    <a href="http://github.com">GitHub</a>

You can use both CSS expressions, inline expressions and array attributes

    echo $html->tag('a#my_id.my_class rel=nofollow', array('href'=>'http://github.com'), 'GitHub');
    <a id="my_id" class="my_class" rel="nofollow" href="http://github.com">GitHub</a>

### Open and close tags

When you need to render only the opening tag, you can use ->open()

    echo $view->open('div')
    <div>

    echo $view->open('div#my_id.my_class lang=en')
    <div id="my_id" class='my_class" lang="en">

    echo $view->open('div#my_id.my_class lang=en', array('title'=>'my title'))
    <div id="my_id" class='my_class" lang="en" title="my title">

If you just want to close a tag, you can use ->close()

    echo $view->close('div')
    </div>

### Nest tags

The content parameter of the ->tag() method accepts anything that can be converted to a string.
It includes, of course, other tags.

    echo $html->tag('div.my_class', $html->tag('p', 'some content'))
    <div class="my_class"><p>some content</p></div>

## Shortcuts functions

If you think the actual syntax is too verbose, you should consider using shortcuts.
The phpHtmlWriterHelper.php file contains predefined shortcuts functions.

    include('/path/to/php-html-writer/lib/phpHtmlWriterHelper.php');

    echo tag('div#my_id.my_class', 'some content');
    <div id="my_id" class="my_class">some content</div>

## Run test suite

All code is fully unit tested. To run tests on your server, from a CLI, run

    php /path/to/php-html-writer/prove.php

You should see:

    AttributeArrayParserTest.............................................ok
    AttributeStringParserTest............................................ok
    CssExpressionParserTest..............................................ok
    HelperTest...........................................................ok
    PerformanceTest......................................................ok
    WriterCloseTest......................................................ok
    WriterOpenTest.......................................................ok
    WriterTagJsonTest....................................................ok
    WriterTagTest........................................................ok
     All tests successful.
     Files=9, Tests=162

## Customization

PHP HTML Writer is very extensible. You can replace easily each part of the implementation.

### Use your own HTML element implementation

When you call ->tag(), a phpHtmlWriterElement instance is created.
This class represents an HTML element: it has a tag name, some attributes and a content.
It is responsible for rendering itself.
You can change the element class to override it by changing the "element_class" option:

    // create your own implementation of an HTML element
    class myHtmlElement extends phpHtmlWriterElement
    {
      // override methods
    }

    // change the "element_class" option to use your implementation
    $view->setOption('element_class', 'myHtmlElement');

### Use your own parsers

Three different parsers are use to fetch the tag and the HTML attributes.
For all of them, you can inject your own implementation.

#### CSS expression parser

Responsible for parsing strings like "div#my_id.my_class.another_class".
phpHtmlWriterCssExpressionParser is used to parse CSS expressions.
You can inject a new CSS expression parser instance with the ->setCssExpressionParser() method:

    // create your own implementation of the CSS expression parser
    class myCssParser extends phpHtmlWriterCssExpressionParser
    {
      // override methods
    }

    // inject your CSS expression parser
    $view->setCssExpressionParser(new myCssParser());

#### Inline attribute parser

Responsible for parsing inline attributes like "rel=nofollow".
phpHtmlWriterAttributeStringParser is used to parse inline attributes.
You can inject a new inline attributes parser instance with the ->setAttributeStringParser() method:

    // create your own implementation of the inline attributes parser
    class myStringParser extends phpHtmlWriterAttributeStringParser
    {
      // override methods
    }

    // inject your inline attributes parser
    $view->setAttributeStringParser(new myStringParser());

#### Array attribute parser

Responsible for parsing array attributes like array("rel"=>"nofollow").
phpHtmlWriterAttributeArrayParser is used to parse array attributes.
You can inject a new array attributes parser instance with the ->setAttributeArrayParser() method:

    // create your own implementation of the array attributes parser
    class myArrayParser extends phpHtmlWriterAttributeStringParser
    {
      // override methods
    }

    // inject your own array attributes parser
    $view->setAttributeArrayParser(new myArrayParser());