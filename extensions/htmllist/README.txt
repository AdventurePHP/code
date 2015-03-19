README
======

HTMLList is a taglib-class to easily create dynamically lists in the format html.
You can build up three types of lists: unordered, ordered and definition.
So you can add more lists in succession, and get them via an identifier.
In an ordered/unordered list, you can add simple elements; in a definition list
you have to - to keep in html standard - add a definition term first followed by
a definition itself. The way to do that is always the same, just look through
the interfaces. An example gives a first way through.


INTERFACES:
===========

- List:
   HtmlListTag::addList( {type:string} , {attributes:array} );
   HtmlListTag::getListById( {id:string} );

- Ordered/Unordered-List:
   OrderedListTag::addElement( {content:string} , {cssClass:string} );
   UnorderedListTag::addElement( {content:string} , {cssClass:string} );

- Definition-List:
   DefinitionListTag::addDefinitionTerm( {content:string} , {cssClass:string} );
   DefinitionListTag::addDefinition( {content:string} , {cssClass:string} );


EXAMPLE:
========

use APF\extensions\htmllist\taglib\HtmlListTag;
use APF\extensions\htmllist\taglib\UnorderedListTag;

$listTag = new HtmlListTag();
$listTag->addList('list:unordered', ['id' => 'rsslist']);

/* @var $list UnorderedListTag */
$list = $listTag->getListById('rsslist');

// $lstFeeds is an array with a certain size
$feed = [];
foreach ($feed as $item) {
   $list->addElement($item, 'list_css_class');
}

echo $list->transform();
