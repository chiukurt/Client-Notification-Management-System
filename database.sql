DROP TABLE client;
CREATE TABLE `client` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CompanyName` varchar(50) NOT NULL,
  `BusinessNumber` varchar(50) NOT NULL,
  `ContactFirstName` varchar(50) NOT NULL,
  `ContactLastName` varchar(50) NOT NULL,
  `PhoneNumber` varchar(50) NOT NULL,
  `CellNumber` varchar(50) NOT NULL,
  `Wesbsite` varchar(50) NOT NULL DEFAULT 'None',
  `Status` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
INSERT INTO client VALUES("1","ABC Clinic","4161111111","Bobo","bobobobo","4162222222","4163333333","http://www.website.com","Active");
INSERT INTO client VALUES("2","Fox","4161234567","Bart","Simpson","4161111111","4161111111","http://www.aycarumba.com","Active");
INSERT INTO client VALUES("12","companyA","4161111111","aaa","aaa","4161111111","4161111111","http://webbo.com","Archive");
INSERT INTO client VALUES("17","aaa","123","aa","aa","123","123","http://aaa.pl","Active");

DROP TABLE clientevent;
CREATE TABLE `clientevent` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClientID` int(11) NOT NULL,
  `NotificationID` int(11) NOT NULL,
  `StartDate` varchar(50) NOT NULL,
  `Frequency` int(11) NOT NULL,
  `Status` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
INSERT INTO clientevent VALUES("10","1","6","10-10-10","35","Active");
INSERT INTO clientevent VALUES("19","2","6","10-10-10","10","Active");
INSERT INTO clientevent VALUES("18","1","7","10-10-10","20","Active");
INSERT INTO clientevent VALUES("15","1","6","10-10-10","111","Archive");

DROP TABLE log;
CREATE TABLE `log` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `Page` varchar(50) NOT NULL,
  `Action` varchar(50) NOT NULL,
  `DateTime` varchar(50) NOT NULL,
  `IP` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1038 DEFAULT CHARSET=latin1;
INSERT INTO log VALUES("1030","admin","db","view","12/03/2019 08:28:15 pm","99.237.216.79");
INSERT INTO log VALUES("1029","admin","user","view","12/03/2019 08:28:07 pm","99.237.216.79");
INSERT INTO log VALUES("1028","admin","user","doUpdate","12/03/2019 08:28:07 pm","99.237.216.79");
INSERT INTO log VALUES("1027","admin","user","view","12/03/2019 08:27:32 pm","99.237.216.79");
INSERT INTO log VALUES("1026","admin","user","doAdd","12/03/2019 08:27:32 pm","99.237.216.79");
INSERT INTO log VALUES("1025","admin","user","view","12/03/2019 08:26:59 pm","99.237.216.79");
INSERT INTO log VALUES("1024","admin","client","view","12/03/2019 08:26:28 pm","99.237.216.79");
INSERT INTO log VALUES("1023","admin","client","doAdd","12/03/2019 08:26:27 pm","99.237.216.79");
INSERT INTO log VALUES("1022","admin","client","view","12/03/2019 08:25:56 pm","99.237.216.79");
INSERT INTO log VALUES("1021","uname","log","view","12/01/2019 04:08:46 pm","99.239.170.156");
INSERT INTO log VALUES("1020","uname","log","view","12/01/2019 04:08:28 pm","99.239.170.156");
INSERT INTO log VALUES("1019","uname","log","view","11/27/2019 04:15:28 pm","198.96.85.104");
INSERT INTO log VALUES("1018","uname","db","view","11/27/2019 04:15:25 pm","198.96.85.104");
INSERT INTO log VALUES("1017","uname","db","download","11/27/2019 04:15:17 pm","198.96.85.104");
INSERT INTO log VALUES("1016","uname","db","view","11/27/2019 04:15:15 pm","198.96.85.104");
INSERT INTO log VALUES("1015","admin","log","view","11/27/2019 04:15:06 pm","198.96.85.104");
INSERT INTO log VALUES("1031","admin","db","upload","12/03/2019 08:31:35 pm","99.237.216.79");
INSERT INTO log VALUES("1032","admin","user","view","12/03/2019 08:31:39 pm","99.237.216.79");
INSERT INTO log VALUES("1033","admin","log","view","12/03/2019 08:31:43 pm","99.237.216.79");
INSERT INTO log VALUES("1034","admin","log","view","12/04/2019 07:20:23 pm","198.96.84.204");
INSERT INTO log VALUES("1035","admin","log","view","12/04/2019 07:21:24 pm","198.96.84.204");
INSERT INTO log VALUES("1036","uname","log","view","12/05/2019 08:23:53 pm","99.239.170.156");
INSERT INTO log VALUES("1037","uname","db","view","12/05/2019 08:24:40 pm","99.239.170.156");

DROP TABLE notification;
CREATE TABLE `notification` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Status` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
INSERT INTO notification VALUES("6","Greetings","SMS","Enabled");
INSERT INTO notification VALUES("7","Happy Birthday!","Email","Enabled");
INSERT INTO notification VALUES("9","Give us your money","Threat","Enabled");
INSERT INTO notification VALUES("10","Happy Holidays!","Spam","Enabled");

DROP TABLE user;
CREATE TABLE `user` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `E-mail` varchar(50) NOT NULL,
  `CellNumber` varchar(50) NOT NULL,
  `Position` varchar(50) NOT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `Photo` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
INSERT INTO user VALUES("1","user1","one","admin@gblearn.com","416-111-1111","Admin","admin","$2y$10$dzTQSlVHfxGXIfs4F0YazOttcu3EzFxoQfAXQm9hplcGhIX56Fqxm","Active","<img src = \'uploads/1.png\' width=55 height=55>");
INSERT INTO user VALUES("3","user222","two","usertwo@gmail.com","416-999-999","CoolDude","uname","$2y$10$MmSUh6Po8TYJ/nAfFcj5MeU7OSKZ9394dg0e6QamIkawwLTHDAawG","Active","<img src = \'uploads/3.jpg\' width=55 height=55>");
INSERT INTO user VALUES("4","!@#$%^&*()_+","#!!!#$!@#$!%","*-/!%#4@gmail.com","416-111-1111","specialcharacter","a","$2y$10$cDPZBeZYFHFgKGOVEGjgn.sSCAAIT1e8DOL0bwnZltDr2qndSYxDi","Active","<img src = \'uploads/4.jpg\' width=55 height=55>");
INSERT INTO user VALUES("5","default","example","defaultexample@gb.com","416-111-111","Tester","a","$2y$10$19mAoejnYIeCAcqtQiO/qu.HVbQBeGEvFKBXeeAeKku33HRp55DXO","Active","<img src=\'/comp1230/websitetesting/assignment2/public/../app//data//photos/0.png\' width=55 height=55>");
INSERT INTO user VALUES("7","aa","aa","pp@pp.pl","123","sitting","test","$2y$10$skwhu3XNpkjpcM803DskPuo3gWsMiXmgGIueGV6uK8lVevy/1qu7.","Active","<img src = \'uploads/7.png\' width=55 height=55>");

