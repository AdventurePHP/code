<?php

import('extensions::htmlheader::biz', 'HtmlNode');

/**
 * Description of PackageNode
 *
 * @author Ralf Schubert
 */
class PackageNode extends HtmlNode {

   protected $__url = null;
   protected $__name = null;
   protected $__type = null;
   protected $__rewriting = null;

   public function PackageNode($url, $name, $type, $rewriting) {
      $this->__url = $url;
      $this->__name = $name;
      $this->__type = $type;
      $this->__rewriting = $rewriting;
      $this->__checksum = md5($url . $name . $type);
   }

   public function transform() {
      $link = $this->__buildPackageLink(
                      $this->__url,
                      $this->__name,
                      $this->__type,
                      $this->__rewriting
      );
      if ($this->__type === 'js') {
         return '<script src="' . $link . '" type="text/javascript"></script>' . PHP_EOL;
      }
      return '<link href="' . $link . '" rel="stylesheet" type="text/css" />' . PHP_EOL;
   }

   protected function __buildPackageLink($url, $name, $type, $rewriting) {

      if ($rewriting === null) {
         $rewriting = Registry::retrieve('apf::core', 'URLRewriting');
      }

      // Generate url if not given
      if ($url === null) {
         if ($rewriting) {
            $url = Registry::retrieve('apf::core', 'URLBasePath');
         } else {
            $tmpPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']);
            $slash = (substr($tmpPath, 0, 1) !== '/') ? '/' : '';
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $slash . $tmpPath;
         }
         // end if
      }

      if ($rewriting) {
         $actionParam = array(
             'extensions_jscsspackager_biz-action/jcp' => 'package/' . $name . '.' . $type
         );
         // end if
      } else {
         $actionParam = array(
             'extensions_jscsspackager_biz-action:jcp' => 'package:' . $name . '.' . $type
         );
         // end else
      }

      // return url
      return FrontcontrollerLinkHandler::generateLink($url, $actionParam);
   }

}
?>