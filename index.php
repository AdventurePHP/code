<?php
use APF\core\pagecontroller\Document;

Document::$knownExpressions[] = 'APF\core\pagecontroller\DynamicExpressionCreator';
Document::$knownExpressions[] = 'APF\core\pagecontroller\PlaceHolderExpressionCreator';
Document::$knownExpressions[] = 'APF\core\pagecontroller\GetStringExpressionCreator';
