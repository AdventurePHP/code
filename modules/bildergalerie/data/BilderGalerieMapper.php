<?php
   import('modules::bildergalerie::biz','BildObjekt');
   import('modules::bildergalerie::biz','ThemaObjekt');
   import('modules::bildergalerie::biz','GalerieObjekt');
   import('core::singleton','Singleton');


   /**
   *  @package modules::bildergalerie::data
   *  @module BilderGalerieMapper
   *
   *  Implementiert den Mapper der Daten-Schicht
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 15.05.2005<br />
   *  Version 0.2, 15.05.2005<br />
   */
   class BilderGalerieMapper extends coreObject
   {

      function BilderGalerieMapper(){
      }


      /**
      *  @module ladeGalerieDatenPerIndex()
      *  @public
      *
      *  Läd eine Galerie per Index.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      */
      function ladeGalerieDatenPerIndex($Index){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT * FROM galerie_namen WHERE GNIndex = '".$Index."' ORDER BY Datum DESC, Name ASC";
         $result = $SQL->executeTextStatement($select);
         $data = $SQL->fetchData($result);

         $Galerie = $this->__mappeDatenAlsGalerieDomainObjekt($data);

         return $Galerie;

       // end function
      }


      /**
      *  @module ladeThemaDatenPerGalerie()
      *  @public
      *
      *  Läd Themen per Galerie-Index.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      */
      function ladeThemaDatenPerGalerie($Galerie){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT * FROM galerie_themen WHERE Galerie = '".$Galerie."' ORDER BY Datum DESC, Name ASC";
         $result = $SQL->executeTextStatement($select);

         $Themen = array();

         while($data = $SQL->fetchData($result)){
            $Themen[] = $this->__mappeDatenAlsThemaDomainObjekt($data);
          // end while
         }

         return $Themen;

       // end function
      }


      /**
      *  @module ladeBildDatenPerThema()
      *  @public
      *
      *  Läd BildObjekte nach einem Thema-Index.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      */
      function ladeBildDatenPerThema($Thema){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT * FROM galerie_bilder WHERE Thema = '".$Thema."'";
         $result = $SQL->executeTextStatement($select);

         $Bilder = array();

         while($data = $SQL->fetchData($result)){
            $Bilder[] = $this->__mappeDatenAlsBildDomainObjekt($data);
          // end while
         }

         return $Bilder;

       // end function
      }


      /**
      *  @module __mappeDatenAlsGalerieDomainObjekt()
      *  @private
      *
      *  Mappt ein ResultSet in ein GalerieObjekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      */
      function __mappeDatenAlsGalerieDomainObjekt($GalerieResultSet){

         $GalerieObjekt = new GalerieObjekt();

         if(isset($GalerieResultSet['Name'])){
            $GalerieObjekt->setzeName($GalerieResultSet['Name']);
          // end if
         }
         if(isset($GalerieResultSet['Beschreibung'])){
            $GalerieObjekt->setzeBeschreibung($GalerieResultSet['Beschreibung']);
          // end if
         }
         if(isset($GalerieResultSet['Datum'])){
            $GalerieObjekt->setzeDatum($GalerieResultSet['Datum']);
          // end if
         }
         if(isset($GalerieResultSet['GNIndex'])){
            $GalerieObjekt->setzeGNIndex($GalerieResultSet['GNIndex']);
          // end if
         }

         return $GalerieObjekt;

       // end function
      }


      /**
      *  @module __mappeDatenAlsThemaDomainObjekt()
      *  @private
      *
      *  Mappt ein ResultSet in ein ThemaObjekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      */
      function __mappeDatenAlsThemaDomainObjekt($ThemaResultSet){

         $ThemaObjekt = new ThemaObjekt();

         if(isset($ThemaResultSet['Name'])){
            $ThemaObjekt->setzeName($ThemaResultSet['Name']);
          // end if
         }
         if(isset($ThemaResultSet['Datum'])){
            $ThemaObjekt->setzeDatum($ThemaResultSet['Datum']);
          // end if
         }
         if(isset($ThemaResultSet['Galerie'])){
            $ThemaObjekt->setzeGalerie($ThemaResultSet['Galerie']);
          // end if
         }
         if(isset($ThemaResultSet['GTIndex'])){
            $ThemaObjekt->setzeGTIndex($ThemaResultSet['GTIndex']);
          // end if
         }

         return $ThemaObjekt;

       // end function
      }


      /**
      *  @module __mappeDatenAlsBildDomainObjekt()
      *  @private
      *
      *  Mappt ein ResultSet in ein BildObjekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      */
      function __mappeDatenAlsBildDomainObjekt($BildResultSet){

         $BildObjekt = new BildObjekt();

         if(isset($BildResultSet['Name'])){
            $BildObjekt->setzeName($BildResultSet['Name']);
          // end if
         }
         if(isset($BildResultSet['Text'])){
            $BildObjekt->setzeText($BildResultSet['Text']);
          // end if
         }
         if(isset($BildResultSet['Thema'])){
            $BildObjekt->setzeThema($BildResultSet['Thema']);
          // end if
         }
         if(isset($BildResultSet['Pictogramm'])){
            $BildObjekt->setzePictogramm($BildResultSet['Pictogramm']);
          // end if
         }
         if(isset($BildResultSet['Bild'])){
            $BildObjekt->setzeBild($BildResultSet['Bild']);
          // end if
         }
         if(isset($BildResultSet['GBIndex'])){
            $BildObjekt->setzeGBIndex($BildResultSet['GBIndex']);
          // end if
         }

         return $BildObjekt;

       // end function
      }

    // end class
   }
?>