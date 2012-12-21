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
   html_taglib_list::addList( {type:string} , {attributes:array} );
   html_taglib_list::getListById( {id:string} );

- Ordered/Unordered-List:
   list_taglib_ordered::addElement( {content:string} , {cssClass:string} );
   list_taglib_unordered::addElement( {content:string} , {cssClass:string} );

- Definition-List:
   list_taglib_definition::addDefinitionTerm( {content:string} , {cssClass:string} );
   list_taglib_definition::addDefinition( {content:string} , {cssClass:string} );





EXAMPLE:
================================================================================
================================================================================


import('extensions::htmllist::taglib','html_taglib_list');

...
$this->list = new html_taglib_list();
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
