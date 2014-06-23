README


HTMLList is a taglib-class to easily create dynamically lists in the format html.
You can build up three types of lists: unordered, ordered and definition.
So you can add more lists in succession, and get them via an identifier.
In an ordered/unordered list, you can add simple elements; in a definition list
you have to - to keep in html standard - add a defintion term first followed by
a definition itself. The way to do that is always the same, just look through
the interfaces. An example gives a first way through.


INSTALLATION:
Unpack the zipped file into 'extensions/' of the APF.


INTERFACES:

- List:
   HtmlListTag::addList( {type:string} , {attributes:array} );
   HtmlListTag::getListById( {id:string} );

- Ordered/Unordered-List:
   OrderedListTag::addElement( {content:string} , {cssClass:string} );
   UnorderedListTag::addElement( {content:string} , {cssClass:string} );

- Definition-List:
   ListDefinitionTag::addDefinitionTerm( {content:string} , {cssClass:string} );
   ListDefinitionTag::addDefinition( {content:string} , {cssClass:string} );





EXAMPLE:
================================================================================
================================================================================


use APF\extensions\htmllist\taglib\HtmlListTag;
...
$this->list = new HtmlListTag();
$this->list->addList( 'list:unordered' , array( 'id' => 'rsslist' ) );
$list = $this->list->getListById( 'rsslist' );

// --- $lstFeeds is an array with a certain size
foreach( $lstFeeds as $f)
{
   $list->addElement( $f , "list_css_class" );
}

return $this->list->transform();

================================================================================
================================================================================
