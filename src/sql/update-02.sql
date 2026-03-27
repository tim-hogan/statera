CREATE TABLE IF NOT EXISTS `timesheet` (
  `idtimesheet` INT NOT NULL AUTO_INCREMENT,
  `timesheet_date` DATE NULL,
  `timesheet_staff` INT NULL,
  `timesheet_hours` DOUBLE NULL,
  `timesheet_processed` TINYINT NULL DEFAULT 0,
  `timesheet_entry_timestamp` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `timesheet_entry_user` INT NULL,
  PRIMARY KEY (`idtimesheet`),
  INDEX `fk_timesheet_staff1_idx` (`timesheet_staff` ASC),
  INDEX `fk_timesheet_user1_idx` (`timesheet_entry_user` ASC),
  CONSTRAINT `fk_timesheet_staff1`
	FOREIGN KEY (`timesheet_staff`)
	REFERENCES `staff` (`idstaff`)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION,
  CONSTRAINT `fk_timesheet_user1`
	FOREIGN KEY (`timesheet_entry_user`)
	REFERENCES `user` (`iduser`)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION)
ENGINE = InnoDB;