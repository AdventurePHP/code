<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */
namespace APF\extensions\arraypager\pres\documentcontroller;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\pagecontroller\BaseDocumentController;
use APF\core\singleton\Singleton;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use APF\tools\request\RequestHandler;

/**
 *  Implements the document controller to display the paging bar. The bar includes:
 *  <ul>
 *    <li>display present pages</li>
 *    <li>Prev + next button</li>
 *    <li>Dynamic amount of pages</li>
 *  </ul>
 *
 * @author Lutz Mahlstedt
 * @version
 * Version 0.1, 20.12.2009<br />
 */
class ArrayPagerController extends BaseDocumentController {

   public function transformContent() {

      // fill document attributes to local variable
      $document = $this->getDocument();

      /* @var $config array */
      $config = $document->getAttribute('Config');

      $dataCount = $document->getAttribute('DataCount');
      $anchorName = $document->getAttribute('AnchorName');

      $urlParams = array($config['ParameterEntries'] => $config['Entries']);
      if ($config['EntriesChangeable'] === true) {
         $urlParams = RequestHandler::getValues(array($config['ParameterEntries'] => $config['Entries']));
      }

      // Pager leer zurückgeben, falls keine Seiten vorhanden sind.
      if ($dataCount == 0) {
         return;
      }

      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      $t->start('ArrayPager');

      $content = $this->getTemplate('pager');

      // Anzahl der Einträge
      $integerEntriesCount = $config['Entries'];

      // Anzahl der Seiten generieren
      $integerPageCount = ceil($dataCount / $integerEntriesCount);

      // Aktuelle Seite generieren
      $integerCurrentPage = intval(RequestHandler::getValue($config['ParameterPage'], 1));

      // Puffer initialisieren
      $stringBuffer = '';

      for ($integerPage = 1; $integerPage <= $integerPageCount; $integerPage++) {
         if ($integerPage == $integerCurrentPage) {
            // Referenz auf Template holen
            $objectTemplate = $this->getTemplate('Page_Selected');
         } else {
            // Referenz auf Template holen
            $objectTemplate = $this->getTemplate('Page');
         }

         $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
               array($config['ParameterPage'] => $integerPage)
         ));

         // Pager zusammenbauen
         if (!empty($anchorName)) {
            $objectTemplate->setPlaceHolder('URL', $stringURL . '#' . $anchorName);
         } else {
            $objectTemplate->setPlaceHolder('URL', $stringURL);
         }

         $objectTemplate->setPlaceHolder('Page', $integerPage);

         // Template transformieren
         $stringBuffer .= $objectTemplate->transformTemplate();

         unset($objectTemplate, $stringURL);
      }

      unset($integerPage);

      // Puffer in Inhalt einsetzen
      $content->setPlaceHolder('Pager', $stringBuffer);

      unset($stringBuffer);

      // VorherigeSeite
      if ($integerCurrentPage > 1) {
         // Template vorherige Seite ausgeben
         $objectTemplatePreviousPage = $this->getTemplate('PreviousPage_Active');

         $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
               array($config['ParameterPage'] => ($integerCurrentPage - 1))
         ));

         if (!empty($anchorName)) {
            $objectTemplatePreviousPage->setPlaceHolder('URL', $stringURL . '#' . $anchorName);
         } else {
            $objectTemplatePreviousPage->setPlaceHolder('URL', $stringURL);
         }

         unset($stringURL);
      } else {
         // Template vorherige Seite (inaktiv) ausgeben
         $objectTemplatePreviousPage = $this->getTemplate('PreviousPage_Inactive');
      }

      $content->setPlaceHolder('PreviousPage', $objectTemplatePreviousPage->transformTemplate());

      unset($objectTemplatePreviousPage);

      // NaechsteSeite
      if ($integerCurrentPage < $integerPageCount) {

         $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
               array($config['ParameterPage'] => ($integerCurrentPage + 1))
         ));

         $objectTemplateNextPage = $this->getTemplate('NextPage_Active');

         if (isset($anchorName) === true) {
            $objectTemplateNextPage->setPlaceHolder('URL', $stringURL . '#' . $anchorName);
         } else {
            $objectTemplateNextPage->setPlaceHolder('URL', $stringURL);
         }

         unset($stringURL);
      } else {
         $objectTemplateNextPage = $this->getTemplate('NextPage_Inactive');
      }

      $content->setPlaceHolder('NextPage', $objectTemplateNextPage->transformTemplate());

      unset($objectTemplateNextPage);

      if ($config['EntriesChangeable'] === true) {
         // Einträge / Seite
         $arrayEntries = explode('|',
               $config['EntriesPossible']
         );
         $stringBuffer = '';

         foreach ($arrayEntries AS &$integerEntries) {
            if ($urlParams[$config['ParameterEntries']] == $integerEntries) {
               $objectTemplateEntries = $this->getTemplate('Entries_Active');
            } else {
               $objectTemplateEntries = $this->getTemplate('Entries_Inactive');
            }

            $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
                  array($config['ParameterPage']    => 1,
                        $config['ParameterEntries'] => $integerEntries
                  )
            ));

            if (isset($anchorName) === true) {
               $objectTemplateEntries->setPlaceHolder('URL', $stringURL . '#' . $anchorName);
            } else {
               $objectTemplateEntries->setPlaceHolder('URL', $stringURL);
            }

            unset($stringURL);

            // Anzahl einsetzen
            $objectTemplateEntries->setPlaceHolder('Entries', $integerEntries);

            // Template in Puffer einsetzen
            $stringBuffer .= $objectTemplateEntries->transformTemplate();

            unset($objectTemplateEntries);
         }

         $objectTemplateEntries = $this->getTemplate('Entries');

         $objectTemplateEntries->setPlaceHolder('Entries', $stringBuffer);

         unset($stringBuffer);

         $content->setPlaceHolder('Entries', $objectTemplateEntries->transformTemplate());

         unset($objectTemplateEntries);
      }

      $content->transformOnPlace();

      $t->stop('ArrayPager');
   }

}
