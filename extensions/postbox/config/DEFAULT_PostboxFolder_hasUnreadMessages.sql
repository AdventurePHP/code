SELECT
    `ent_message`.`MessageID`
FROM
    `ent_message`
INNER JOIN
    `cmp_messagechannel2message`
    ON `cmp_messagechannel2message`.`Target_MessageID` = `ent_message`.`MessageID`
INNER JOIN
    `ass_postboxfolder2messagechannel`
    ON `ass_postboxfolder2messagechannel`.`Target_MessageChannelID` = `cmp_messagechannel2message`.`Source_MessageChannelID`
WHERE
    `ass_postboxfolder2messagechannel`.`Source_PostboxFolderID` = [PostboxFolderID]
LIMIT 1