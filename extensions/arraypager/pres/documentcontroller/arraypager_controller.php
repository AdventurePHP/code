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
import('tools::link', 'LinkGenerator');
import('tools::request', 'RequestHandler');

/**
 * @package extensions::arraypager::pres::documentcontroller
 * @class arraypager_controller
 *
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
class arraypager_controller extends BaseDocumentController {

   private $_LOCALS;

   public function transformContent() {
      // LOCALS füllen
      $this->_LOCALS = array($this->__Attributes['Config']['ParameterEntries'] => $this->__Attributes['Config']['Entries']);

      if ($this->__Attributes['Config']['EntriesChangeable'] === TRUE) {
         $this->_LOCALS = RequestHandler::getValues($this->_LOCALS);
      }

      // Pager leer zurückgeben, falls keine Seiten vorhanden sind.
      if ($this->__Attributes['DataCount'] == 0) {
         // Content des aktuellen Designs leeren
         $this->__Content = '';
         return;
      }

      /* @var $objectBenchmark BenchmarkTimer */
      $objectBenchmark = Singleton::getInstance('BenchmarkTimer');
      $objectBenchmark->start('ArrayPager');

      // Anzahl der Einträge
      $integerEntriesCount = $this->__Attributes['Config']['Entries'];

      // Anzahl der Seiten generieren
      $integerPageCount = ceil($this->__Attributes['DataCount'] / $integerEntriesCount);

      // Aktuelle Seite generieren
      $integerCurrentPage = intval(RequestHandler::getValue($this->__Attributes['Config']['ParameterPage'],
            1
         )
      );

      // Puffer initialisieren
      $stringBuffer = '';

      for ($integerPage = 1; $integerPage <= $integerPageCount; $integerPage++
      ) {
         if ($integerPage == $integerCurrentPage) {
            // Referenz auf Template holen
            $objectTemplate = $this->getTemplate('Page_Selected');
         } else {
            // Referenz auf Template holen
            $objectTemplate = $this->getTemplate('Page');
         }

         $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
            array($this->__Attributes['Config']['ParameterPage'] => $integerPage)
         ));

         // Pager zusammenbauen
         if (isset($this->__Attributes['AnchorName']) === TRUE) {
            $objectTemplate->setPlaceHolder('URL',
                  $stringURL . '#' . $this->__Attributes['AnchorName']
            );
         } else {
            $objectTemplate->setPlaceHolder('URL',
               $stringURL
            );
         }

         $objectTemplate->setPlaceHolder('Page',
            $integerPage
         );

         // Template transformieren
         $stringBuffer .= $objectTemplate->transformTemplate();

         unset($objectTemplate,
         $stringURL
         );
      }

      unset($integerPage);

      // Puffer in Inhalt einsetzen
      $this->setPlaceHolder('Pager',
         $stringBuffer
      );

      unset($stringBuffer);

      // VorherigeSeite
      if ($integerCurrentPage > 1) {
         // Template vorherige Seite ausgeben
         $objectTemplatePreviousPage = $this->getTemplate('PreviousPage_Active');

         // Link generieren
         $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
            array($this->__Attributes['Config']['ParameterPage'] => ($integerCurrentPage - 1))
         ));

         if (isset($this->__Attributes['AnchorName']) === TRUE) {
            $objectTemplatePreviousPage->setPlaceHolder('URL',
                  $stringURL . '#' . $this->__Attributes['AnchorName']
            );
         } else {
            $objectTemplatePreviousPage->setPlaceHolder('URL',
               $stringURL
            );
         }

         unset($stringURL);
      } else {
         // Template vorherige Seite (inaktiv) ausgeben
         $objectTemplatePreviousPage = $this->getTemplate('PreviousPage_Inactive');
      }

      $this->setPlaceHolder('PreviousPage',
         $objectTemplatePreviousPage->transformTemplate()
      );

      unset($objectTemplatePreviousPage);

      // NaechsteSeite
      if ($integerCurrentPage < $integerPageCount) {
         // Link generieren
         $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
            array($this->__Attributes['Config']['ParameterPage'] => ($integerCurrentPage + 1))
         ));

         $objectTemplateNextPage = $this->getTemplate('NextPage_Active');

         if (isset($this->__Attributes['AnchorName']) === TRUE) {
            $objectTemplateNextPage->setPlaceHolder('URL',
                  $stringURL . '#' . $this->__Attributes['AnchorName']
            );
         } else {
            $objectTemplateNextPage->setPlaceHolder('URL',
               $stringURL
            );
         }

         unset($stringURL);
      } else {
         $objectTemplateNextPage = $this->getTemplate('NextPage_Inactive');
      }

      $this->setPlaceHolder('NextPage',
         $objectTemplateNextPage->transformTemplate()
      );

      unset($objectTemplateNextPage);

      if ($this->__Attributes['Config']['EntriesChangeable'] === TRUE) {
         // Einträge / Seite
         $arrayEntries = explode('|',
            $this->__Attributes['Config']['EntriesPossible']
         );
         $stringBuffer = '';

         foreach ($arrayEntries AS &$integerEntries) {
            if ($this->_LOCALS[$this->__Attributes['Config']['ParameterEntries']] == $integerEntries) {
               $objectTemplateEntries = $this->getTemplate('Entries_Active');
            } else {
               $objectTemplateEntries = $this->getTemplate('Entries_Inactive');
            }

            // Link generieren
            $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
               array($this->__Attributes['Config']['ParameterPage'] => 1,
                  $this->__Attributes['Config']['ParameterEntries'] => $integerEntries
               )
            ));

            if (isset($this->__Attributes['AnchorName']) === TRUE) {
               $objectTemplateEntries->setPlaceHolder('URL',
                     $stringURL . '#' . $this->__Attributes['AnchorName']
               );
            } else {
               $objectTemplateEntries->setPlaceHolder('URL',
                  $stringURL
               );
            }

            unset($stringURL);

            // Anzahl einsetzen
            $objectTemplateEntries->setPlaceHolder('Entries',
               $integerEntries
            );

            // Template in Puffer einsetzen
            $stringBuffer .= $objectTemplateEntries->transformTemplate();

            unset($objectTemplateEntries);
         }

         $objectTemplateEntries = $this->getTemplate('Entries');

         $objectTemplateEntries->setPlaceHolder('Entries',
            $stringBuffer
         );

         unset($stringBuffer);

         $this->setPlaceHolder('Entries',
            $objectTemplateEntries->transformTemplate()
         );

         unset($objectTemplateEntries);
      }

      $objectBenchmark->stop('ArrayPager');
   }

}
