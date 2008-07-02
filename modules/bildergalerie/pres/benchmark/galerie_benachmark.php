<?php
   import('modules::bildergalerie::biz','BilderGalerieManager');
   import('core::benchmark','benchmarkTimer');


   $T = new benchmarkTimer();

   $T->start('ManagerAufruf');
   $BGManager = new BilderGalerieManager();
   $T->stop('ManagerAufruf');


   $T->start('GalerieBaumAufbauen');
   $Galerie = $BGManager->ladeGalerie('1');
   $T->stop('GalerieBaumAufbauen');
?>
<pre>
<?php
   $T->start('AusgabeObjekt');
   print_R($Galerie);
   $T->stop('AusgabeObjekt');
?>
</pre>
<?php
   $T->zeigeZeiten();
?>