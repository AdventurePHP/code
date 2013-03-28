<?php
namespace APF\extensions\arraypager\pres\documentcontroller;

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
use APF\core\benchmark\BenchmarkTimer;
use APF\core\pagecontroller\BaseDocumentController;
use APF\core\singleton\Singleton;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use APF\tools\request\RequestHandler;

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

   private $localParameters;

   public function transformContent() {

      $this->localParameters = array($this->attributes['Config']['ParameterEntries'] => $this->attributes['Config']['Entries']);

      if ($this->attributes['Config']['EntriesChangeable'] === TRUE) {
         $this->localParameters = RequestHandler::getValues($this->localParameters);
      }

      // Pager leer zurückgeben, falls keine Seiten vorhanden sind.
      if ($this->attributes['DataCount'] == 0) {
         // Content des aktuellen Designs leeren
         $this->content = '';
         return;
      }

      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      $t->start('ArrayPager');

      // Anzahl der Einträge
      $integerEntriesCount = $this->attributes['Config']['Entries'];

      // Anzahl der Seiten generieren
      $integerPageCount = ceil($this->attributes['DataCount'] / $integerEntriesCount);

      // Aktuelle Seite generieren
      $integerCurrentPage = intval(RequestHandler::getValue($this->attributes['Config']['ParameterPage'],
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
            array($this->attributes['Config']['ParameterPage'] => $integerPage)
         ));

         // Pager zusammenbauen
         if (isset($this->attributes['AnchorName']) === TRUE) {
            $objectTemplate->setPlaceHolder('URL',
                  $stringURL . '#' . $this->attributes['AnchorName']
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
            array($this->attributes['Config']['ParameterPage'] => ($integerCurrentPage - 1))
         ));

         if (isset($this->attributes['AnchorName']) === TRUE) {
            $objectTemplatePreviousPage->setPlaceHolder('URL',
                  $stringURL . '#' . $this->attributes['AnchorName']
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
            array($this->attributes['Config']['ParameterPage'] => ($integerCurrentPage + 1))
         ));

         $objectTemplateNextPage = $this->getTemplate('NextPage_Active');

         if (isset($this->attributes['AnchorName']) === TRUE) {
            $objectTemplateNextPage->setPlaceHolder('URL',
                  $stringURL . '#' . $this->attributes['AnchorName']
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

      if ($this->attributes['Config']['EntriesChangeable'] === TRUE) {
         // Einträge / Seite
         $arrayEntries = explode('|',
            $this->attributes['Config']['EntriesPossible']
         );
         $stringBuffer = '';

         foreach ($arrayEntries AS &$integerEntries) {
            if ($this->localParameters[$this->attributes['Config']['ParameterEntries']] == $integerEntries) {
               $objectTemplateEntries = $this->getTemplate('Entries_Active');
            } else {
               $objectTemplateEntries = $this->getTemplate('Entries_Inactive');
            }

            // Link generieren
            $stringURL = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
               array($this->attributes['Config']['ParameterPage'] => 1,
                  $this->attributes['Config']['ParameterEntries'] => $integerEntries
               )
            ));

            if (isset($this->attributes['AnchorName']) === TRUE) {
               $objectTemplateEntries->setPlaceHolder('URL',
                     $stringURL . '#' . $this->attributes['AnchorName']
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

      $t->stop('ArrayPager');
   }

}
