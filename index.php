<?php
use APF\core\frontcontroller\Frontcontroller;
use APF\core\singleton\Singleton;

include('./core/bootstrap.php');

/* @var $fC Frontcontroller */
$fC = Singleton::getInstance(Frontcontroller::class);

// How to add content generation action?
$fC->addAction(
      'APF\core\frontcontroller',
      'PageGeneration',
      [
            'namespace' => 'DOCS\pre\templates',
            'template' => 'main'
      ]
);

$fC->start();
