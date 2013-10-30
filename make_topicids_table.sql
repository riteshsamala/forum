CREATE  TABLE IF NOT EXISTS `laits_forum`.`topicIds` (
  `topic_id` INT NOT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `problemName` VARCHAR(45) NOT NULL ,
  `section` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`topic_id`) )
ENGINE = InnoDB
