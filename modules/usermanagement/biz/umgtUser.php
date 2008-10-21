<?php
   import('modules::genericormapper::biz','GenericDomainObject');


   /**
   *  @package modules::usermanagement::biz
   *  @module umgtUser
   *
   *  Domain object for user.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.04.2008<br />
   *  Version 0.2, 01.06.2008<br />
   */
   class umgtUser extends GenericDomainObject
   {

      function umgtUser(){
         $this->__ObjectName = 'User';
       // end function
      }

    // end class
   }
?>