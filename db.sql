CREATE TABLE `trades` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `initiator` varchar(45) NOT NULL,
  `recipient` varchar(45) NOT NULL,
  `subject` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `pw` varchar(65) NOT NULL,
  `priv` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `login_UNIQUE` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

CREATE TABLE `visitors` (
  `id` bigint(20) unsigned NOT NULL,
  `uid` varchar(45) DEFAULT NULL,
  `lastlogin` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `uid_UNIQUE` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*
-- Query: SELECT * FROM gaminas.users
-- Date: 2014-01-21 14:25
*/
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (1,'root','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',13);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (2,'haron','*07F4E7DF62605DF73F082F638C97597D9035438A',12);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (3,'casual','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',0);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (4,'registered','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',1);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (5,'verified','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',2);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (6,'wooden','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',3);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (7,'copper','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',4);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (8,'silver','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',5);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (9,'gold','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',6);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (10,'platinum','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',7);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (11,'diamond','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',8);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (12,'query_test','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',0);
INSERT INTO `users` (`id`,`login`,`pw`,`priv`) VALUES (17,'query_test_0','*8AAEE87E4CC4E7FE5601453C7D8C2136A3879AD9',0);
