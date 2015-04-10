<?php
return [
      'DummyService-constructor' => [
            'class'       => 'APF\tests\suites\core\service\DummyService',
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
            'class'       => 'APF\tests\suites\core\service\DummyServiceTwo',
            'servicetype' => 'SINGLETON'
      ]
];
