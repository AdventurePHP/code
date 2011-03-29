SELECT
    `ent_messagechannel`.`MessageChannelID`
FROM
    `ent_messagechannel`
INNER JOIN
    `ass_user2messagechannel`
    ON  `ass_user2messagechannel`.`Target_MessageChannelID` = `ent_messagechannel`.`MessageChannelID`
LEFT JOIN
    `ass_postboxfolder2messagechannel`
    ON `ass_postboxfolder2messagechannel`.`Target_MessageChannelID` = `ent_messagechannel`.`MessageChannelID`
WHERE
   `ass_user2messagechannel`.`Source_UserID` = [UserID]
   AND
   `ass_postboxfolder2messagechannel`.`Source_PostboxFolderID` IS NULL