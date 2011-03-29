DELETE
    `a`
FROM
    `ass_user2unreadmessage` AS `a`
INNER JOIN
    `cmp_messagechannel2message` AS `b`
    ON `a`.`Target_MessageID` = `b`.`Target_MessageID`
WHERE
    b.`Source_MessageChannelID` = [MessageChannelID]
    AND
    a.`Source_UserID` = [UserID]