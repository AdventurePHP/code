<?php

use APF\tests\suites\core\service\DummyService;
use APF\tests\suites\core\service\DummyServiceTwo;

return [
      'DummyService-constructor' => [
            'class' => DummyService::class,
            'servicetype' => 'SINGLETON',
            'construct'   => [
                  'string'  => [
                        'value' => 'foo'
                  ],
                  'service' => [
                     // using vendor TEST mapped to folder test-config
                        'namespace' => 'TEST',
                        'name'      => 'DummyServiceTwo'
                  ]
            ]
      ],
      'DummyServiceTwo'          => [
            'class' => DummyServiceTwo::class,
            'servicetype' => 'SINGLETON'
      ]
];
