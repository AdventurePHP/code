<?php
/**
 * In order to use this script for database initialization, include this file after including the
 * <em>pagecontroller.php</em> file and setting the context and connection key.
 *
 * @example
 * <code>
 * include('./apps/core/pagecontroller/pagecontroller.php');
 * $context = 'mycontext';
 * $connectionKey = 'mysql-db';
 * include('./apps/modules/guestbook2009/data/setup/init.php');
 * </code>
 *
 * Please note, that you may have to adapt the include path for the <em>pagecontroller.php</em>.
 */
import('modules::genericormapper::data', 'GenericORRelationMapper');

$orm = new GenericORRelationMapper();
$orm->addMappingConfiguration('modules::guestbook2009::data', 'guestbook2009');
$orm->addRelationConfiguration('modules::guestbook2009::data', 'guestbook2009');
$orm->setConnectionName($connectionKey);
$orm->setup();

// --- setup available languages ----------------------------------------------------------------
$langDe = new GenericDomainObject('Language');
$langDe->setProperty('ISOCode', 'de');
$langDe->setProperty('DisplayName', 'Deutsch');
$langDeId = $orm->saveObject($langDe);
$langDe->setProperty('LanguageID', $langDeId);

$langEn = new GenericDomainObject('Language');
$langEn->setProperty('ISOCode', 'en');
$langEn->setProperty('DisplayName', 'English');
$langEnId = $orm->saveObject($langEn);
$langEn->setProperty('LanguageID', $langEnId);

// --- create one guestbook instance ------------------------------------------------------------
$guestbook = new GenericDomainObject('Guestbook');

// --- create admin account ---------------------------------------------------------------------
$user = new GenericDomainObject('User');
$user->setProperty('Username', 'admin');
$user->setProperty('Password', md5('admin'));
$user->setProperty('Name', 'Admin');
$user->setProperty('Email', 'root@localhost');
$userId = $orm->saveObject($user);
$user->setProperty('UserID', $userId);

// --- english attributes of the guestbook ------------------------------------------------------
$titleEn = new GenericDomainObject('Attribute');
$titleEn->setProperty('Name', 'title');
$titleEn->setProperty('Value', 'My guestbook');
$titleEn->addRelatedObject('Attribute2Language', $langEn);

$descriptionEn = new GenericDomainObject('Attribute');
$descriptionEn->setProperty('Name', 'description');
$descriptionEn->setProperty('Value', 'This is my first guestbook instance of the guestbook2009 module!');
$descriptionEn->addRelatedObject('Attribute2Language', $langEn);

// --- german attributes of the guestbook -------------------------------------------------------
$titleDe = new GenericDomainObject('Attribute');
$titleDe->setProperty('Name', 'title');
$titleDe->setProperty('Value', 'Mein Gästebuch');
$titleDe->addRelatedObject('Attribute2Language', $langDe);

$descriptionDe = new GenericDomainObject('Attribute');
$descriptionDe->setProperty('Name', 'description');
$descriptionDe->setProperty('Value', 'Dies ist die erste Instanz des neuen guestbook2009 Moduls!');
$descriptionDe->addRelatedObject('Attribute2Language', $langDe);

// --- save guestbook with attributes -----------------------------------------------------------
$guestbook->addRelatedObject('Guestbook2LangDepValues', $titleEn);
$guestbook->addRelatedObject('Guestbook2LangDepValues', $descriptionEn);
$guestbook->addRelatedObject('Guestbook2LangDepValues', $titleDe);
$guestbook->addRelatedObject('Guestbook2LangDepValues', $descriptionDe);

$orm->saveObject($guestbook);
