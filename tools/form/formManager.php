<?php
   /**
   *  Package tools::form<br />
   *  Klasse formManager<br />
   *  Stellt eine Abstraktion für Formulare dar.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 10.02.2005<br />
   */
   class formManager
   {

      var $Button;       // Button-Objekte
      var $TextFeld;     // Textfeld-Objekte
      var $TextArea;     // TextArea-Objekte
      var $Checkbox;     // Checkbox-Objekte
      var $Select;       // Select-Feld-Objekte
      var $Radio;        // Radio-Button-Objekte
      var $DateiFeld;    // Datei-Felder
      var $HiddenFeld;   // Hidden-Felder
      var $PasswortFeld; // Passwort-Felder


      function MyFormClass(){

         $this->Button = array();
         $this->TextFeld = array();
         $this->TextArea = array();
         $this->Checkbox = array();
         $this->Select = array();
         $this->DateiFeld = array();
         $this->HiddenFeld = array();
         $this->PasswortFeld = array();

       // end function
      }


      /**
      *  Funktion FormBeginn() definiert den Anfang einer
      *  Form. Parameter:<br />
      *   - Name (opt)<br />
      *   - Methode (opt)<br />
      *   - EncType (opt)<br />
      *   - ID (opt)<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      */
      function FormBeginn($Name = '',$Aktion = '',$Methode = '',$EncType = '',$ID = ''){

         $Attribute = array();
         if(!empty($Name)){
            $Attribute[] = "name=\"".trim($Name)."\"";
          // end if
         }

         if(!empty($Methode)){
            $Attribute[] = "method=\"".trim($Methode)."\"";
          // end if
         }
         else{
            $Attribute[] = "method=\"post\"";
          // end else
         }

         if(!empty($EncType)){
            $Attribute[] = "enctype=\"".trim($EncType)."\"";
          // end if
         }

         if(!empty($ID)){
            $Attribute[] = "id=\"".trim($ID)."\"";
          // end if
         }

         if(!empty($Aktion)){
            $Attribute[] = "action=\"".$Aktion."\"";
          // end if
         }
         else{
            $Attribute[] = "action=\"".$_SERVER['PHP_SELF']."\"";
          // end else
         }

         // Form-Beginn zurückgeben
         return "<form ".implode(' ',$Attribute).">";

       // end function
      }


      /**
      *  Methode FormEnde() definiert das Ende einer Form.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      */
      function FormEnde(){
         return "</form>";
       // end function
      }


      /**
      *  Methode Button() erzeugt einen Button.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - Wert<br />
      *   - CSS-Klasse (opt)<br />
      *   - CSS-Style (opt)<br />
      *   - ErzeugeImplizit (opt): Element nicht sofort er-<br />
      *     zeugen, damit Attribute geändert werden können.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      */
      function Button($Name,$Wert,$Class = '',$Style = '',$ErzeugeImplizit = '1'){

         $this->Button[$Name] = new Form_Button();
         if(!empty($Name)){
            $this->Button[$Name]->setzeName($Name);
          // end if
         }
         if(!empty($Wert)){
            $this->Button[$Name]->setzeWert($Wert);
          // end if
         }
         if(!empty($Class)){
            $this->Button[$Name]->setzeCSSClass($Class);
          // end if
         }
         if(!empty($Style)){
            $this->Button[$Name]->setzeCSSStyle($Style);
          // end if
         }

         if($ErzeugeImplizit == '1'){
            return $this->Button[$Name]->generiereButton();
          // end if
         }

       // end function
      }


      /**
      *  Methode TextFeld() erzeugt ein TextFeld.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - Wert<br />
      *   - CSS-Klasse (opt)<br />
      *   - CSS-Style (opt)<br />
      *   - ErzeugeImplizit (opt): Element nicht sofort er-<br />
      *     zeugen, damit Attribute geändert werden können.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      */
      function TextFeld($Name,$Wert = '',$Class = '',$Style = '',$ErzeugeImplizit = '1'){

         $this->TextFeld[$Name] = new Form_TextFeld();
         if(!empty($Name)){
            $this->TextFeld[$Name]->setzeName($Name);
          // end if
         }

         if(isset($_REQUEST[$Name])){
            $this->TextFeld[$Name]->setzeWert($_REQUEST[$Name]);
          // end if
         }
         else{
            $this->TextFeld[$Name]->setzeWert($Wert);
          // end if
         }

         if(!empty($Class)){
            $this->TextFeld[$Name]->setzeCSSClass($Class);
          // end if
         }

         if(!empty($Style)){
            $this->TextFeld[$Name]->setzeCSSStyle($Style);
          // end if
         }

         if($ErzeugeImplizit == '1'){
            return $this->TextFeld[$Name]->generiereTextFeld();
          // end if
         }

       // end function
      }


      /**
      *  Methode DateiFeld() erzeugt ein DateiFeld.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - CSS-Klasse (opt)<br />
      *   - CSS-Style (opt)<br />
      *   - ErzeugeImplizit (opt): Element nicht sofort er-<br />
      *     zeugen, damit Attribute geändert werden können.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      */
      function DateiFeld($Name,$Wert,$Class = '',$Style = '',$ErzeugeImplizit = '1'){

         $this->DateiFeld[$Name] = new Form_DateiFeld();
         if(!empty($Name)){
            $this->DateiFeld[$Name]->setzeName($Name);
          // end if
         }

         if(isset($_REQUEST[$Name])){
            $this->DateiFeld[$Name]->setzeWert($_REQUEST[$Name]);
          // end if
         }
         else{
            $this->DateiFeld[$Name]->setzeWert($Wert);
          // end if
         }

         if(!empty($Class)){
            $this->DateiFeld[$Name]->setzeCSSClass($Class);
          // end if
         }

         if(!empty($Style)){
            $this->DateiFeld[$Name]->setzeCSSStyle($Style);
          // end if
         }

         if($ErzeugeImplizit == '1'){
            return $this->DateiFeld[$Name]->generiereDateiFeld();
          // end if
         }

       // end function
      }


      /**
      *  Methode TextArea() erzeugt eine TextArea.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - Wert<br />
      *   - CSS-Klasse (opt)<br />
      *   - CSS-Style (opt)<br />
      *   - ErzeugeImplizit (opt): Element nicht sofort er-<br />
      *     zeugen, damit Attribute geändert werden können.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      */
      function TextArea($Name,$Wert = '',$Class = '',$Style = '',$ErzeugeImplizit = '1'){

         $this->TextArea[$Name] = new Form_TextArea();
         if(!empty($Name)){
            $this->TextArea[$Name]->setzeName($Name);
          // end if
         }

         if(isset($_REQUEST[$Name])){
            $this->TextArea[$Name]->setzeWert($_REQUEST[$Name]);
          // end if
         }
         else{
            $this->TextArea[$Name]->setzeWert($Wert);
          // end if
         }

         if(!empty($Class)){
            $this->TextArea[$Name]->setzeCSSClass($Class);
          // end if
         }

         if(!empty($Style)){
            $this->TextArea[$Name]->setzeCSSStyle($Style);
          // end if
         }

         if($ErzeugeImplizit == '1'){
            return $this->TextArea[$Name]->generiereTextArea();
          // end if
         }

       // end function
      }


      /**
      *  Methode Radio() erzeugt einen Radio-Button und gibt diesen zurück.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - Wert<br />
      *   - CSS-Klasse (opt)<br />
      *   - CSS-Style (opt)<br />
      *   - Zusatz-Tag (opt): html-Tag, z.B. für 'checked'<br />
      *   - ID (opt)<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      *  Version 0.2, 15.08.2006 (Bug in der Implementierung behoben)<br />
      */
      function Radio($Name,$Wert = '',$Class = '',$Style = '',$ZusatzTag = array(),$ID = ''){

         $this->Radio[$Name] = new Form_Radio();
         if(!empty($Name)){
            $this->Radio[$Name]->setzeName($Name);
          // end if
         }
         if(isset($Wert) || !empty($Wert)){
            $this->Radio[$Name]->setzeWert($Wert);
          // end if
         }
         if(!empty($Class)){
            $this->Radio[$Name]->setzeCSSClass($Class);
          // end if
         }
         if(!empty($Style)){
            $this->Radio[$Name]->setzeCSSStyle($Style);
          // end if
         }
         if(count($ZusatzTag) > 0){
            foreach($ZusatzTag as $Key => $Value){
               $this->Radio[$Name]->setzeZusatzTag($Key,$Value);
             // end foreach
            }
          // end if
         }
         if(!empty($ID)){
            $this->Radio[$Name]->setzeID($ID);
          // end if
         }

         return $this->Radio[$Name]->generiereRadio();

       // end function
      }


      /**
      *  Methode Checkbox() erzeugt eine Checkbox.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - Wert<br />
      *   - CSS-Klasse (opt)<br />
      *   - CSS-Style (opt)<br />
      *   - Zusatz-Tag (opt): html-Tag, z.B. für 'checked'<br />
      *   - ErzeugeImplizit (opt): Element nicht sofort er-<br />
      *     zeugen, damit Attribute geändert werden können.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      *  Version 0.2, 20.02.2005<br />
      */
      function Checkbox($Name,$Wert = '',$Class = '',$Style = '',$ZusatzTag = array(),$ErzeugeImplizit = '1'){

         $this->Checkbox[$Name] = new Form_Checkbox();
         if(!empty($Name)){
            $this->Checkbox[$Name]->setzeName($Name);
          // end if
         }
         if(isset($Wert) || !empty($Wert)){
            $this->Checkbox[$Name]->setzeWert($Wert);
          // end if
         }
         if(!empty($Class)){
            $this->Checkbox[$Name]->setzeCSSClass($Class);
          // end if
         }
         if(!empty($Style)){
            $this->Checkbox[$Name]->setzeCSSStyle($Style);
          // end if
         }
         if(count($ZusatzTag) > 0){
            foreach($ZusatzTag as $Key => $Value){
               $this->Checkbox[$Name]->setzeZusatzTag($Key,$Value);
             // end foreach
            }
          // end if
         }

         if($ErzeugeImplizit == '1'){
            return $this->Checkbox[$Name]->generiereCheckbox();
          // end if
         }

       // end function
      }


      /**
      *  Methode Select() erzeugt ein SelectFeld.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - Wert<br />
      *   - CSS-Klasse (opt)<br />
      *   - CSS-Style (opt)<br />
      *   - Zusatz-Tag (opt): html-Tag, z.B. für 'checked'<br />
      *   - ErzeugeImplizit (opt): Element nicht sofort er-<br />
      *     zeugen, damit Attribute geändert werden können.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 10.02.2005<br />
      */
      function Select($Name,$Werte = array(),$Class = '',$Style = '',$ZusatzTag = array(),$ErzeugeImplizit = '1'){

         $this->Select[$Name] = new Form_SelectFeld();
         if(!empty($Name)){
            $this->Select[$Name]->setzeName($Name);
          // end if
         }
         if(count($Werte) > 0){
            foreach($Werte as $Key => $Value){
               $this->Select[$Name]->setzeWerte($Key,$Value);
             // end foreach
            }
          // end if
         }
         if(!empty($Class)){
            $this->Select[$Name]->setzeCSSClass($Class);
          // end if
         }
         if(!empty($Style)){
            $this->Select[$Name]->setzeCSSStyle($Style);
          // end if
         }
         if(count($ZusatzTag) > 0){
            foreach($ZusatzTag as $Key => $Value){
               $this->Select[$Name]->setzeZusatzTag($Key,$Value);
             // end foreach
            }
          // end if
         }

         if($ErzeugeImplizit == '1'){
            return $this->Select[$Name]->generiereSelectFeld();
          // end if
         }

       // end function
      }


      /**
      *  Methode HiddenFeld() erzeugt ein HiddenFeld.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - Wert<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 06.03.2005<br />
      */
      function HiddenFeld($Name,$Wert = ''){

         $this->HiddenFeld[$Name] = new Form_HiddenFeld();
         if(!empty($Name)){
            $this->HiddenFeld[$Name]->setzeName($Name);
          // end if
         }
         if(isset($Wert) || !empty($Wert)){
            $this->HiddenFeld[$Name]->setzeWert($Wert);
          // end if
         }

         return $this->HiddenFeld[$Name]->generiereHiddenFeld();

       // end function
      }


      /**
      *  Methode PasswortFeld() erzeugt ein TextFeld.<br />
      *  Parameter:<br />
      *   - Name<br />
      *   - Wert<br />
      *   - CSS-Klasse (opt)<br />
      *   - CSS-Style (opt)<br />
      *   - ErzeugeImplizit (opt): Element nicht sofort er-<br />
      *     zeugen, damit Attribute geändert werden können.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 22.03.2005<br />
      */
      function PasswortFeld($Name,$Wert = '',$Class = '',$Style = '',$ErzeugeImplizit = '1'){

         $this->PasswortFeld[$Name] = new Form_PasswortFeld();
         if(!empty($Name)){
            $this->PasswortFeld[$Name]->setzeName($Name);
          // end if
         }

         if(isset($_REQUEST[$Name])){
            $this->PasswortFeld[$Name]->setzeWert($_REQUEST[$Name]);
          // end if
         }
         else{
            $this->PasswortFeld[$Name]->setzeWert($Wert);
          // end if
         }

         if(!empty($Class)){
            $this->PasswortFeld[$Name]->setzeCSSClass($Class);
          // end if
         }

         if(!empty($Style)){
            $this->PasswortFeld[$Name]->setzeCSSStyle($Style);
          // end if
         }

         if($ErzeugeImplizit == '1'){
            return $this->PasswortFeld[$Name]->generierePasswortFeld();
          // end if
         }

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_BasisElement<br />
   *  Repräsentiert ein abstraktes GUI-Element.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_BasisElement
   {

      var $__Name;       // Name des Elements
      var $__Wert;       // Wert (String) des Elements
      var $__Werte;      // Wertepaare (Array) eines Elements
      var $__AnzWerte;   // Anzahl der Werte (falls $this->__Wert ein Array ist)
      var $__ID;         // ID des Elements
      var $__CSSClass;   // CSS-Klasse
      var $__CSSStyle;   // CSS-Style
      var $__ZusatzTag;  // Zusätzliche Tags
      var $__AnzZusTag;  // Anzahl der zusätzlichen Tags


      function Form_BasisElement(){
      }


      /**
      *  Funktion setzeName() [public/nonstatic]<br />
      *  Setzt den Namen des GUI-Elements.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function setzeName($Name){
        $this->__Name = trim($Name);
       // end function
      }


      /**
      *  Funktion setzeWert() [public/nonstatic]<br />
      *  Setzt den Wert des GUI-Elements.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function setzeWert($Wert){
         $this->__Wert = trim($Wert);
       // end function
      }


      /**
      *  Funktion setzeWerte() [public/nonstatic]<br />
      *  Setzt Werte GUI-Elements.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function setzeWerte($Anzeige,$Wert,$Selected = '0'){

         $this->__Werte[$this->__AnzWerte]['Anzeige'] = trim($Anzeige);
         $this->__Werte[$this->__AnzWerte]['Wert'] = trim($Wert);

         if($Selected == '1'){
            $this->__Werte[$this->__AnzWerte]['Vorselektiert'] = '1';
          // end if
         }

         $this->__AnzWerte++;

       // end function
      }


      /**
      *  Funktion setzeID() [public/nonstatic]<br />
      *  Setzt die ID des GUI-Elements.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function setzeID($ID){
        $this->__ID = trim($ID);
       // end function
      }


      /**
      *  Funktion setzeCSSClass() [public/nonstatic]<br />
      *  Setzt die CSS-Klasse des GUI-Elements.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function setzeCSSClass($Class){
        $this->__CSSClass = trim($Class);
       // end function
      }


      /**
      *  Funktion setzeCSSStyle() [public/nonstatic]<br />
      *  Setzt einen CSS-Style des GUI-Elements.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function setzeCSSStyle($Style){
        $this->__CSSStyle[] = trim($Style);
       // end function
      }


      /**
      *  Funktion setzeZusatzTag() [public/nonstatic]<br />
      *  Setzt einen Zusatz-Tag des GUI-Elements.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function setzeZusatzTag($Attribut,$Wert){
        $this->__ZusatzTag[$this->__AnzZusTag]['Attribut'] = trim($Attribut);
        $this->__ZusatzTag[$this->__AnzZusTag]['Wert'] = trim($Wert);
        $this->__AnzZusTag++;
       // end function
      }


      /**
      *  Funktion setzeValidator() [public/nonstatic]<br />
      *  Implementiert den Validator auf GUI-Element-Ebene. Entspricht der Wert des Feldes nicht den<br />
      *  Vorgaben, wird das Feld rot markiert.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function setzeValidator($Button,$ValidatorMethode = 'Text',$RegEXP = ''){

         // Test initialisieren
         $Test = false;

         if($ValidatorMethode == 'Text'){
            $Test = !empty($this->__Wert);
          // end if
         }
         if($ValidatorMethode == 'EMail'){
            $Test = ereg("^([a-zA-Z0-9\.\_\-]+)@([a-zA-Z0-9\.\-]+\.[A-Za-z][A-Za-z]+)$",$this->__Wert);
          // end if
         }
         if($ValidatorMethode == 'Telefon' || $ValidatorMethode == 'Fax'){
            // OLD: ereg("^([0-9\+\-\/\(\) ]+)$",$TelefonFax)
            $Test = preg_match("/^[0-9\-\+\(\)\/ ]{6,}+$/",trim($this->__Wert));
          // end if
         }
         if($ValidatorMethode == 'Zahl'){
            $Test = preg_match("/^[0-9]{1,}+$/",trim($this->__Wert));
          // end if
         }
         if($ValidatorMethode == 'Ordner'){
            $Test = preg_match("/^[a-zA-Z0-9\-\_]+$/",trim($this->__Wert));
          // end if
         }

         if($ValidatorMethode == 'RegExp'){
            $Test = preg_match(trim($RegEXP),trim($this->__Wert));
          // end if
         }

         if(!isset($_REQUEST[$Button]) || $Test){
          // end if
         }
         else{
            $this->setzeCSSStyle('border: 2px solid red;');
          // end else
         }

       // end function
      }

    // end class
   }



   /**
   *  Package tools::form<br />
   *  Klasse Form_TextFeld<br />
   *  Repräsentiert ein Text-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_TextFeld extends Form_BasisElement
   {


      function Form_TextFeld(){

         $this->__Name = (string)'';
         $this->__Wert = (string)'';
         $this->__ID = (string)'';
         $this->__CSSClass = (string)'';
         $this->__CSSStyle = array();
         $this->__ZusatzTag = array();
         $this->__AnzZusTag = 0;

       // end function
      }


      /**
      *  Funktion generiereTextFeld() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generiereTextFeld(){

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // Wert des Textfeldes
         $Wert = "value=\"".$this->__Wert."\"";
         $Attribute[] = $Wert;

         // ID des Textfeldes
         if(!empty($this->__ID)){
           $ID = "id=\"".$this->__ID."\"";
           $Attribute[] = $ID;
          // end if
         }

         // CSS-Klasse
         if(!empty($this->__CSSClass)){
           $CSSClass = "class=\"".$this->__CSSClass."\"";
           $Attribute[] = $CSSClass;
          // end if
         }

         // CSS-Style
         if(!empty($this->__CSSStyle) && count($this->__CSSStyle) > 0){
           $Temp = implode('; ',$this->__CSSStyle);
           $CSSStyle = "style=\"".$Temp."\"";
           $Attribute[] = $CSSStyle;
          // end if
         }

         // Werte der zusätzlichen Attribute
         $Tags = array();
         if(count($this->__ZusatzTag) > 0){
           for($i = 0; $i < count($this->__ZusatzTag); $i++){
              $Tags[] = $this->__ZusatzTag[$i]['Attribut']."=\"".$this->__ZusatzTag[$i]['Wert']."\"";
            // end for
           }
           $Attribute[] = implode(' ',$Tags);
          // end if
         }

         return "<input type=\"text\" ".implode(' ',$Attribute)." />";

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_DateiFeld<br />
   *  Repräsentiert ein Datei-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_DateiFeld extends Form_BasisElement
   {


      function Form_DateiFeld(){

        $this->__Name = (string)'';
        $this->__Wert = (string)'';
        $this->__ID = (string)'';
        $this->__CSSClass = (string)'';
        $this->__CSSStyle = array();
        $this->__ZusatzTag = array();
        $this->__AnzZusTag = 0;

       // end function
      }


      /**
      *  Funktion generiereDateiFeld() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generiereDateiFeld(){

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // Wert des Textfeldes
         $Wert = "value=\"".$this->__Wert."\"";
         $Attribute[] = $Wert;

         // ID des Textfeldes
         if(!empty($this->__ID)){
            $ID = "id=\"".$this->__ID."\"";
            $Attribute[] = $ID;
          // end if
         }

         // CSS-Klasse
         if(!empty($this->__CSSClass)){
            $CSSClass = "class=\"".$this->__CSSClass."\"";
            $Attribute[] = $CSSClass;
          // end if
         }

         // CSS-Style
         if(!empty($this->__CSSStyle) && count($this->__CSSStyle) > 0){
            $Temp = implode('; ',$this->__CSSStyle);
            $CSSStyle = "style=\"".$Temp."\"";
            $Attribute[] = $CSSStyle;
          // end if
         }

         // Werte der zusätzlichen Attribute
         $Tags = array();
         if(count($this->__ZusatzTag) > 0){
            for($i = 0; $i < count($this->__ZusatzTag); $i++){
               $Tags[] = $this->__ZusatzTag[$i]['Attribut']."=\"".$this->__ZusatzTag[$i]['Wert']."\"";
             // end for
            }
            $Attribute[] = implode(' ',$Tags);
          // end if
         }

         return "<input type=\"file\" ".implode(' ',$Attribute)." />";

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_SelectFeld<br />
   *  Repräsentiert ein Datei-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_SelectFeld extends Form_BasisElement
   {


     function Form_SelectFeld(){

        $this->__Name = (string)'';
        $this->__Werte = array();
        $this->__AnzWerte = (int) 0;
        $this->__ID = (string)'';
        $this->__CSSClass = (string)'';
        $this->__CSSStyle = array();
        $this->__ZusatzTag = array();
        $this->__AnzZusTag = 0;

      // end function
     }


      /**
      *  Funktion zeigeWerte() [public/nonstatic]<br />
      *  Gibt die Werte des Elements zurück (für z.B. Debugausgaben).<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function zeigeWerte(){
         return printObject($this->__Werte);
       // end function
      }

      /**
      *  Funktion generiereSelectFeld() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generiereSelectFeld(){

         $Attribute = array();

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // ID des Textfeldes
         if(!empty($this->__ID)){
            $ID = "id=\"".$this->__ID."\"";
            $Attribute[] = $ID;
          // end if
         }

         // CSS-Klasse
         if(!empty($this->__CSSClass)){
            $CSSClass = "class=\"".$this->__CSSClass."\"";
            $Attribute[] = $CSSClass;
          // end if
         }

         // CSS-Style
         if(!empty($this->__CSSStyle) && count($this->__CSSStyle) > 0){
            $Temp = implode('; ',$this->__CSSStyle);
            $CSSStyle = "style=\"".$Temp."\"";
            $Attribute[] = $CSSStyle;
          // end if
         }

         // Wert des Textfeldes
         $Werte = (string)'';
         for($i = 0; $i < count($this->__Werte); $i++){

            if(isset($this->__Werte[$i]['Vorselektiert']) && $this->__Werte[$i]['Vorselektiert'] == '1'){
               $Werte = $Werte."  <option value=\"".$this->__Werte[$i]['Wert']."\" selected=\"selected\">".$this->__Werte[$i]['Anzeige']."</option>\n";
             // end if
            }
            else{
               $Werte = $Werte."  <option value=\"".$this->__Werte[$i]['Wert']."\">".$this->__Werte[$i]['Anzeige']."</option>\n";
             // end else
            }

          // end for
         }

         // Werte der zusätzlichen Attribute
         $Tags = array();
         if(count($this->__ZusatzTag) > 0){
            for($i = 0; $i < count($this->__ZusatzTag); $i++){
               $Tags[] = $this->__ZusatzTag[$i]['Attribut']."=\"".$this->__ZusatzTag[$i]['Wert']."\"";
             // end for
            }
            $Attribute[] = implode(' ',$Tags);
          // end if
         }

         return "<select ".implode(' ',$Attribute).">\n".$Werte."\n</select>";

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_Checkbox<br />
   *  Repräsentiert ein Datei-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_Checkbox extends Form_BasisElement
   {


      function Form_Checkbox(){

         $this->__Name = (string)'';
         $this->__Wert = (string)'';
         $this->__ID = (string)'';
         $this->__CSSClass = (string)'';
         $this->__CSSStyle = array();
         $this->__ZusatzTag = array();
         $this->__AnzZusTag = 0;

       // end function
      }


      /**
      *  Funktion generiereCheckbox() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generiereCheckbox(){

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // Wert des Textfeldes
         $Wert = "value=\"".$this->__Wert."\"";
         $Attribute[] = $Wert;

         // ID des Textfeldes
         if(!empty($this->__ID)){
            $ID = "id=\"".$this->__ID."\"";
            $Attribute[] = $ID;
          // end if
         }

         // CSS-Klasse
         if(!empty($this->__CSSClass)){
            $CSSClass = "class=\"".$this->__CSSClass."\"";
            $Attribute[] = $CSSClass;
          // end if
         }

         // CSS-Style
         if(!empty($this->__CSSStyle) && count($this->__CSSStyle) > 0){
            $Temp = implode('; ',$this->__CSSStyle);
            $CSSStyle = "style=\"".$Temp."\"";
            $Attribute[] = $CSSStyle;
          // end if
         }

         // Werte der zusätzlichen Attribute
         $Tags = array();
         if(count($this->__ZusatzTag) > 0){
            for($i = 0; $i < count($this->__AnzZusTag); $i++){
               $Tags[] = $this->__ZusatzTag[$i]['Attribut']."=\"".$this->__ZusatzTag[$i]['Wert']."\"";
             // end for
            }
            $Attribute[] = implode(' ',$Tags);
          // end if
         }


         return "<input type=\"checkbox\" ".implode(' ',$Attribute)." />";

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_Radio<br />
   *  Repräsentiert ein Datei-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_Radio extends Form_BasisElement
   {


      function Form_Radio(){

         $this->__Name = (string)'';
         $this->__Wert = (string)'';
         $this->__ID = (string)'';
         $this->__CSSClass = (string)'';
         $this->__CSSStyle = array();
         $this->__ZusatzTag = array();
         $this->__AnzZusTag = 0;

       // end function
      }


      /**
      *  Funktion generiereRadio() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generiereRadio(){

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // Wert des Textfeldes
         $Wert = "value=\"".$this->__Wert."\"";
         $Attribute[] = $Wert;

         // ID des Textfeldes
         if(!empty($this->__ID)){
            $ID = "id=\"".$this->__ID."\"";
            $Attribute[] = $ID;
          // end if
         }

         // CSS-Klasse
         if(!empty($this->__CSSClass)){
            $CSSClass = "class=\"".$this->__CSSClass."\"";
            $Attribute[] = $CSSClass;
          // end if
         }

         // CSS-Style
         if(!empty($this->__CSSStyle) && count($this->__CSSStyle) > 0){
            $Temp = implode('; ',$this->__CSSStyle);
            $CSSStyle = "style=\"".$Temp."\"";
            $Attribute[] = $CSSStyle;
          // end if
         }

         // Werte der zusätzlichen Attribute
         $Tags = array();
         if(count($this->__ZusatzTag) > 0){
            for($i = 0; $i < count($this->__ZusatzTag); $i++){
               $Tags[] = $this->__ZusatzTag[$i]['Attribut']."=\"".$this->__ZusatzTag[$i]['Wert']."\"";
             // end for
            }
            $Attribute[] = implode(' ',$Tags);
          // end if
         }

         return "<input type=\"radio\" ".implode(' ',$Attribute)." />";

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_Button<br />
   *  Repräsentiert ein Datei-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_Button extends Form_BasisElement
   {


      function Form_Button(){

         $this->__Name = (string)'';
         $this->__Wert = (string)'';
         $this->__ID = (string)'';
         $this->__CSSClass = (string)'';
         $this->__CSSStyle = array();
         $this->__ZusatzTag = array();
         $this->__AnzZusTag = 0;

       // end function
      }


      /**
      *  Funktion generiereButton() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generiereButton(){

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // Wert des Textfeldes
         $Wert = "value=\"".$this->__Wert."\"";
         $Attribute[] = $Wert;

         // ID des Textfeldes
         if(!empty($this->__ID)){
            $ID = "id=\"".$this->__ID."\"";
            $Attribute[] = $ID;
          // end if
         }

         // CSS-Klasse
         if(!empty($this->__CSSClass)){
            $CSSClass = "class=\"".$this->__CSSClass."\"";
            $Attribute[] = $CSSClass;
          // end if
         }

         // CSS-Style
         if(!empty($this->__CSSStyle) && count($this->__CSSStyle) > 0){
            $Temp = implode('; ',$this->__CSSStyle);
            $CSSStyle = "style=\"".$Temp."\"";
            $Attribute[] = $CSSStyle;
          // end if
         }

         // Werte der zusätzlichen Attribute
         $Tags = array();
         if(count($this->__ZusatzTag) > 0){
            for($i = 0; $i < count($this->__ZusatzTag); $i++){
               $Tags[] = $this->__ZusatzTag[$i]['Attribut']."=\"".$this->__ZusatzTag[$i]['Wert']."\"";
             // end for
            }
            $Attribute[] = implode(' ',$Tags);
          // end if
         }

         return "<input type=\"submit\" ".implode(' ',$Attribute)." />";

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_Button<br />
   *  Repräsentiert ein Datei-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_TextArea extends Form_BasisElement
   {


      function Form_TextArea(){

         $this->__Name = (string)'';
         $this->__Wert = (string)'';
         $this->__ID = (string)'';
         $this->__CSSClass = (string)'';
         $this->__CSSStyle = array();
         $this->__ZusatzTag = array();
         $this->__AnzZusTag = 0;

       // end function
      }


      /**
      *  Funktion generiereButton() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generiereTextArea(){

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // ID des Textfeldes
         if(!empty($this->__ID)){
            $ID = "id=\"".$this->__ID."\"";
            $Attribute[] = $ID;
          // end if
         }

         // CSS-Klasse
         if(!empty($this->__CSSClass)){
            $CSSClass = "class=\"".$this->__CSSClass."\"";
            $Attribute[] = $CSSClass;
          // end if
         }

         // CSS-Style
         if(!empty($this->__CSSStyle) && count($this->__CSSStyle) > 0){
            $Temp = implode('; ',$this->__CSSStyle);
            $CSSStyle = "style=\"".$Temp."\"";
            $Attribute[] = $CSSStyle;
          // end if
         }

         // Werte der zusätzlichen Attribute
         $Tags = array();
         if(count($this->__ZusatzTag) > 0){
            for($i = 0; $i < count($this->__AnzZusTag); $i++){
               $Tags[] = $this->__ZusatzTag[$i]['Attribut']."=\"".$this->__ZusatzTag[$i]['Wert']."\"";
             // end for
            }
            $Attribute[] = implode(' ',$Tags);
          // end if
         }

         return "<textarea ".implode(' ',$Attribute).">".$this->__Wert."</textarea>";

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_HiddenFeld<br />
   *  Repräsentiert ein Hidden-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_HiddenFeld extends Form_BasisElement
   {

      function Form_HiddenFeld(){

         $this->Name = (string)'';
         $this->Wert = (string)'';

       // end function
      }


      /**
      *  Funktion generiereHiddenFeld() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generiereHiddenFeld(){

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // Wert des Textfeldes
         $Wert = "value=\"".$this->__Wert."\"";
         $Attribute[] = $Wert;

         return "<input type=\"hidden\" ".implode(' ',$Attribute)." />";

       // end function
      }

    // end class
   }


   /**
   *  Package tools::form<br />
   *  Klasse Form_HiddenFeld<br />
   *  Repräsentiert ein Hidden-Feld.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.06.2006<br />
   */
   class Form_PasswortFeld extends Form_BasisElement
   {

      function Form_PasswortFeld(){

         $this->__Name = (string)'';
         $this->__Wert = (string)'';
         $this->__ID = (string)'';
         $this->__CSSClass = (string)'';
         $this->__CSSStyle = array();
         $this->__ZusatzTag = array();
         $this->__AnzZusTag = 0;

       // end function
      }


      /**
      *  Funktion generierePasswortFeld() [public/nonstatic]<br />
      *  Generiert das GUI-Element.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 01.06.2006<br />
      */
      function generierePasswortFeld($AusgabeIndirekt = '0'){

         // Name des Textfeldes
         $Name = "name=\"".$this->__Name."\"";
         $Attribute[] = $Name;

         // Wert des Textfeldes
         $Wert = "value=\"".$this->__Wert."\"";
         $Attribute[] = $Wert;

         // ID des Textfeldes
         if(!empty($this->__ID)){
            $ID = "id=\"".$this->__ID."\"";
            $Attribute[] = $ID;
          // end if
         }

         // CSS-Klasse
         if(!empty($this->__CSSClass)){
            $CSSClass = "class=\"".$this->__CSSClass."\"";
            $Attribute[] = $CSSClass;
          // end if
         }

         // CSS-Style
         if(!empty($this->__CSSStyle) && count($this->__CSSStyle) > 0){
            $Temp = implode('; ',$this->__CSSStyle);
            $CSSStyle = "style=\"".$Temp."\"";
            $Attribute[] = $CSSStyle;
          // end if
         }

         // Werte der zusätzlichen Attribute
         $Tags = array();
         if(count($this->__ZusatzTag) > 0){
            for($i = 0; $i < count($this->__ZusatzTag); $i++){
               $Tags[] = $this->__ZusatzTag[$i]['Attribut']."=\"".$this->__ZusatzTag[$i]['Wert']."\"";
             // end for
            }
            $Attribute[] = implode(' ',$Tags);
          // end if
         }

         return "<input type=\"password\" ".implode(' ',$Attribute)." />";

       // end function
      }

    // end class
   }
?>