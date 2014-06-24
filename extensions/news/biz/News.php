<?php
namespace APF\extensions\news\biz;

/**
 * @package APF\extensions\news\biz
 * @class News
 *
 * This class represents the "APF\extensions\news\biz\News" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class News extends NewsBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * use APF\extensions\news\biz\News;
    * $object = new News();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
