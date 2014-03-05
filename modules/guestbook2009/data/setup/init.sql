INSERT INTO `ass_attribute2language` (`Source_AttributeID`, `Target_LanguageID`) VALUES
(1, 2),
(2, 2),
(3, 1),
(4, 1);

INSERT INTO `cmp_guestbook2langdepvalues` (`Source_GuestbookID`, `Target_AttributeID`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4);

INSERT INTO `ent_attribute` (`AttributeID`, `Name`, `Value`, `CreationTimestamp`, `ModificationTimestamp`) VALUES
(1, 'title', 'My guestbook', '2011-03-06 23:00:49', '0000-00-00 00:00:00'),
(2, 'description', 'This is my first guestbook instance of the guestbook2009 module!', '2011-03-06 23:00:49', '0000-00-00 00:00:00'),
(3, 'title', 'Mein Gästebuch', '2011-03-06 23:00:49', '0000-00-00 00:00:00'),
(4, 'description', 'Dies ist die erste Instanz des neuen guestbook2009 Moduls!', '2011-03-06 23:00:49', '0000-00-00 00:00:00');

INSERT INTO `ent_guestbook` (`GuestbookID`, `CreationTimestamp`, `ModificationTimestamp`) VALUES
(1, '2011-03-06 23:00:49', '0000-00-00 00:00:00');

INSERT INTO `ent_language` (`LanguageID`, `DisplayName`, `ISOCode`, `CreationTimestamp`, `ModificationTimestamp`) VALUES
(1, 'Deutsch', 'de', '2011-03-06 23:00:49', '2011-03-06 23:00:49'),
(2, 'English', 'en', '2011-03-06 23:00:49', '2011-03-06 23:00:49');

INSERT INTO `ent_user` (`UserID`, `Name`, `Email`, `Website`, `Username`, `Password`, `CreationTimestamp`, `ModificationTimestamp`) VALUES
(1, 'Admin', 'root@localhost', '', 'admin', '21232f297a57a5a743894a0e4a801fc3', '2011-03-06 23:00:49', '0000-00-00 00:00:00');