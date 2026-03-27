DROP TABLE IF EXISTS `taxbracket` ;

CREATE TABLE IF NOT EXISTS `taxbracket` (
  `idtaxbracket` INT NOT NULL AUTO_INCREMENT,
  `taxbracket_deleted` TINYINT NULL DEFAULT 0,
  `taxbracket_from_date` DATE NULL,
  `taxbracket_amount` DECIMAL(11,2) NULL,
  `taxbracket_percent` DOUBLE NULL,
  `taxbracket_product` DECIMAL(11,2) NULL,
  PRIMARY KEY (`idtaxbracket`))
ENGINE = InnoDB;

