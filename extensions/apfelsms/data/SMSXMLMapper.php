<?php
namespace APF\extensions\apfelsms\data;

use APF\core\loader\RootClassLoader;
use APF\core\pagecontroller\APFObject;
use APF\core\registry\Registry;
use APF\extensions\apfelsms\biz\SMSConfigurationException;
use APF\extensions\apfelsms\biz\SMSException;
use APF\extensions\apfelsms\biz\SMSManager;
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\extensions\apfelsms\data\SMSMapperInterface;

/**
 * @package APFelSMS
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (06.06.12)
 */
class SMSXMLMapper extends APFObject implements SMSMapper {

   const PAGE_NODENAME = 'page';

   const PAGEDEC_NODENAME = 'pageDec';

   const PAGEDEC_TYPE_ATTRNAME = 'type';

   const ARRAYVAR_KEY_ATTRNAME = 'key';

   /**
    * @var string
    */
   protected $XMLFilename = '';


   /**
    * @var \DOMDocument
    */
   protected $XML_DOMDocument = null;


   /**
    * @throws SMSConfigurationException
    */
   public function setup() {

      $libPath =  RootClassLoader::getLoaderByVendor('APF')->getRootPath();
      $filename = $this->getXMLFilename();
      $fullPath = $libPath . '/' . $filename;

      if (!file_exists($fullPath)) {
         throw new SMSConfigurationException('[SMSXMLMapper::setup()] XML file "' . $filename . '" could not be found. (Full path: "' . $fullPath . '").', E_USER_ERROR);
      }

      $this->XML_DOMDocument = new \DOMDocument();
      $this->XML_DOMDocument->load($fullPath);

      // we need to validate the document, to let the DTD be parsed and the id attribute be recognized as id by DOMDocument::getElementById().
      // do not care about failures this time
      $this->XML_DOMDocument->validate();

   }


   /**
    * @param SMSPage $page
    * @return SMSPage
    * @throws SMSException
    */
   public function mapPage(SMSPage $page) {

      $mappedPage = $this->mapPageWithoutDecorators($page);

      $pageId = $mappedPage->getId();

      $pageDOMNode = $this->XML_DOMDocument->getElementById($pageId);

      if ($pageDOMNode === null) {
         throw new SMSException('[SMSXMLMapper::mapPage()] Could not found node for page id "' . $pageId . '".', E_USER_ERROR);
      }

      ////
      // get and inject level

      $node = $pageDOMNode;
      $lvlCount = -1;
      do {

         $parent = $node->parentNode;
         $parentNodeName = $parent->nodeName;
         $node = $parent;

         $lvlCount++;

      } while ($parentNodeName == self::PAGE_NODENAME);

      $mappedPage->setLevel($lvlCount);

      ////
      // wrap decorators around

      $decNodesList = $pageDOMNode->getElementsByTagName(self::PAGEDEC_NODENAME);

      if ($decNodesList->length < 1) {
         // no decorators found, nothing to do
         return $mappedPage;
      }

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      $outsitePageRepresentingObject = $mappedPage;

      // loop through defined decorators
      for ($i = 0; $i < $decNodesList->length; $i++) {

         /** @var $decNode \DOMElement */
         $decNode = $decNodesList->item($i);

         if ($decNode->parentNode->getAttribute('id') !== $pageId) {
            continue; // skip decorators of sub pages
         }

         $decType = $decNode->getAttribute(self::PAGEDEC_TYPE_ATTRNAME);

         $pageDec = $SMSM->getPageDec($decType, $pageId);

         // wrap the decorator around the page or other decorators like an onion
         $tmp = $outsitePageRepresentingObject;
         $pageDec->setPage($tmp);
         $outsitePageRepresentingObject = $pageDec;

      }

      return $outsitePageRepresentingObject;
   }


   /**
    * @param SMSPage $page
    * @return SMSPage
    * @throws SMSWrongParameterException
    * @throws SMSUnknownTypeException
    */
   public function mapPageWithoutDecorators(SMSPage $page) {

      $pageId = (string)$page->getId();

      $pageDOMNode = $this->XML_DOMDocument->getElementById($pageId);

      if ($pageDOMNode === null) {
         throw new SMSWrongParameterException('[SMSXMLMapper::mapPageWithoutDecorators()] Node with id "' . $pageId . '" could not be found. Other reason may be, that you have no or a invalid DTD file defined in your XML. ', E_USER_ERROR);
      }

      $pageNodeName = self::PAGE_NODENAME;
      if ($pageDOMNode->nodeName != $pageNodeName) {
         throw new SMSUnknownTypeException('[SMSXMLMapper::mapPageWithoutDecorators()] Node with id "' . $pageId . '" has node name different to node name for pages, which is: "' . $pageNodeName . '".', E_USER_WARNING);
      }


      $mapVars = ''; // work around IDEs stupidness
      $pageClassName = get_class($page);
      $mapVarsArray = $pageClassName::$mapVars;

      // get vars from XML file
      $mapVarsBuffer = $this->extractVarsFromXML($mapVarsArray, $pageDOMNode, $pageId);

      $page->mapData($mapVarsBuffer);

      return $page;

   }


   /**
    * @param SMSPageDec $pageDec
    * @param string|integer $pageId
    * @return SMSPageDec
    * @throws SMSWrongDataException
    */
   public function mapPageDec(SMSPageDec $pageDec, $pageId) {

      $decType = $pageDec->getDecType();

      $pageDOMNode = $this->XML_DOMDocument->getElementById($pageId);

      $childNodeList = $pageDOMNode->childNodes;

      $decList = array();
      for ($i = 0; $i < $childNodeList->length; $i++) {

         $childNode = $childNodeList->item($i);

         if (!($childNode instanceof DOMElement)) {
            continue;
         }

         /** @var $childNode DOMElement */

         if ($childNode->nodeName != self::PAGEDEC_NODENAME) {
            continue;
         }

         if ($childNode->getAttribute(self::PAGEDEC_TYPE_ATTRNAME) == $decType) {
            $decList[] = $childNode;
         }

      }
      unset($decNode);

      if (count($decList) != 1) {
         throw new SMSWrongDataException('[SMSXMLMapper::mapPageDec] Page decorator of type "' . $decType . '" is not or multiple existent for page id "' . $pageId . '".', E_USER_ERROR);
      }

      $decNode = $decList[0];

      $mapVars = ''; // work around IDEs stupidness
      $pageDecClassName = get_class($pageDec);
      $mapVarsArray = $pageDecClassName::$mapVars;

      $mapVarsBuffer = $this->extractVarsFromXML($mapVarsArray, $decNode, $pageId);

      $pageDec->mapData($mapVarsBuffer);

      return $pageDec;
   }


   /**
    * @param array $varArray
    * @param DOMElement $nodeInXML
    * @param string|integer $pageId
    * @return array
    * @throws SMSWrongDataException
    */
   protected function extractVarsFromXML(array $varArray, DOMElement $nodeInXML, $pageId) {

      $mapVarsBuffer = array();


      foreach ($varArray as $varName => $default) {

         // loop through mapVars and fetch values from XML

         // get all childs
         $allChildNodes = $nodeInXML->childNodes;

         // fetch childs with varName
         $varNodeList = array();
         for ($i = 0; $i < $allChildNodes->length; $i++) {

            $node = $allChildNodes->item($i);

            if (!($node instanceof DOMElement)) {
               continue;
            }

            if ($node->nodeName != $varName) {
               continue;
            }

            $varNodeList[] = $node;

         }

         if (count($varNodeList) < 1) {
            if ($default === null) { // default value 'null' indicates an neccessary variable (without default value)
               $nodeName = $nodeInXML->nodeName;
               throw new SMSWrongDataException('[SMSXMLMapper::extractVarsFromXML()] Neccessary variable "' . $varName . '" could not be found in "' . $this->getXMLFilename() . '" for page id "' . $pageId . '" contained in parent element with node name "' . $nodeName . '".', E_USER_WARNING);
            }

            $mapVarsBuffer[$varName] = $default;
            continue;
         }

         if (!is_array($default)) {
            $varNode = $varNodeList[0];
            $value = $varNode->nodeValue;
            $mapVarsBuffer[$varName] = $value;
         } else {
            $arrayBuffer = array();
            for ($i = 0; $i < count($varNodeList); $i++) {
               $varNode = $varNodeList[$i];

               /** @var $varNode DOMElement */

               $value = $varNode->nodeValue;
               $key = $varNode->getAttribute(self::ARRAYVAR_KEY_ATTRNAME);

               if (!empty($key)) {
                  $arrayBuffer[$key] = $value;
               } else {
                  $arrayBuffer[] = $value;
               }
            }
            $mapVarsBuffer[$varName] = $arrayBuffer;
         }
      }

      return $mapVarsBuffer;

   }


   /**
    * @param SMSPage $page
    * @return array
    * @throws SMSWrongParameterException
    */
   public function getChildrenIds(SMSPage $page) {

      $pageId = $page->getId();

      $pageDOMNode = $this->XML_DOMDocument->getElementById($pageId);

      if ($pageDOMNode === null) {
         throw new SMSWrongParameterException('[SMSXMLMapper::getChildrenIds()] Could not find node for page id "' . $pageId . '".', E_USER_ERROR);
      }

      $childNodeList = $pageDOMNode->childNodes;

      $idList = array();

      for ($i = 0; $i < $childNodeList->length; $i++) {

         $childNode = $childNodeList->item($i);

         if (!($childNode instanceof DOMElement)) {
            continue;
         }

         /** @var $childNode DOMElement */

         if ($childNode->nodeName != self::PAGE_NODENAME) {
            continue;
         }

         $id = $childNode->getAttribute('id');
         $idList[] = $id;
      }

      return $idList;
   }


   /**
    * @param SMSPage $page
    * @return array
    * @throws SMSWrongParameterException
    */
   public function getSiblingAndOwnIds(SMSPage $page) {

      $pageId = $page->getId();

      $pageDOMNode = $this->XML_DOMDocument->getElementById($pageId);

      if ($pageDOMNode === null) {
         throw new SMSWrongParameterException('[SMSXMLMapper::getChildrenIds()] Could not find node for page id "' . $pageId . '".', E_USER_ERROR);
      }

      $parentNode = $pageDOMNode->parentNode;
      $parentChildNodes = $parentNode->childNodes;

      if ($parentChildNodes->length < 1) {
         return array();
      }

      $siblingIds = array();

      for ($i = 0; $i < $parentChildNodes->length; $i++) {

         $node = $parentChildNodes->item($i);

         if (!($node instanceof DOMElement)) {
            continue;
         }

         /** @var $node DOMElement */
         if ($node->nodeName != self::PAGE_NODENAME) {
            continue;
         }

         $nodeId = $node->getAttribute('id');
         $siblingIds[] = $nodeId;
      }

      return $siblingIds;
   }


   /**
    * @param SMSPage $page
    * @return string|null
    * @throws SMSWrongParameterException
    */
   public function getParentId(SMSPage $page) {

      $pageId = $page->getId();

      $pageDOMNode = $this->XML_DOMDocument->getElementById($pageId);

      if ($pageDOMNode === null) {
         throw new SMSWrongParameterException('[SMSXMLMapper::getParentId()] Could not find node for page id "' . $pageId . '".', E_USER_ERROR);
      }

      $parentNode = $pageDOMNode->parentNode;

      if (!($parentNode instanceof DOMElement)) {
         return null; // no parent node
      }

      $parentNodeName = $parentNode->nodeName;
      if ($parentNodeName != self::PAGE_NODENAME) {
         return null; // no parent node, which is a page (e.g. XML documentRoot)
      }

      return $parentNode->getAttribute('id');

   }


   /**
    * @return string Filename of XML data-source file
    */
   public function getXMLFilename() {

      return $this->XMLFilename;
   }


   /**
    * @param string $filename Filename of XML data-source file
    */
   public function setXMLFilename($filename) {

      $this->XMLFilename = $filename;
   }

}
