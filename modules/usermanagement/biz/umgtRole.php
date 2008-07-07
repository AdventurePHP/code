<?php
   import('modules::usermanagement::biz','umgtBase');


   /**
   *  @package modules::usermanagement::biz
   *  @module umgtRole
   *
   *  Domain object for role.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.04.2008<br />
   */
   class umgtRole extends umgtBase
   {

      var $__PermissionSets = null;


      function umgtRole(){
      }


      function getPermissionByID($PermissionID){
         // Lazy load permission and return it
      }


      function getPermissionByName($PermissionName){
         // Lazy load permission and return it
      }


      function getUserList(){
         // Lazy load users
      }

    // end class
   }
?>