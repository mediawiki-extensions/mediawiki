CREATE TABLE /*$wgDBprefix*/supergroups (
  `sgr_name` varbinary(255) NOT NULL,
  `sgr_user` int(11) NOT NULL,
  UNIQUE KEY `bil_name` (`bil_name`)
) TYPE=InnoDB;