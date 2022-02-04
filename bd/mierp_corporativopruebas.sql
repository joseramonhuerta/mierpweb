/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.6.20 : Database - erp_corporativopruebas
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `cat_agentes` */

CREATE TABLE `cat_agentes` (
  `id_agente` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_agente` varchar(200) NOT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_agente`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `cat_agentes` */

insert  into `cat_agentes`(`id_agente`,`nombre_agente`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'RAMON HUERTA C','A',0,'2018-01-17 12:29:02',NULL,NULL),(2,'LUIS HUERTA C','A',0,'2018-01-17 12:29:13',NULL,NULL),(3,'ARTURO KASHO DAMIAN','A',2,'2019-07-04 09:35:48',NULL,NULL);

/*Table structure for table `cat_almacenes` */

CREATE TABLE `cat_almacenes` (
  `id_almacen` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `codigo_almacen` varchar(20) DEFAULT NULL,
  `nombre_almacen` varchar(255) DEFAULT NULL,
  `tipo_almacen` tinyint(1) DEFAULT '1' COMMENT '1= Almacena y vende, 2= solo vende, 3= Solo almacena, 4=Produccion, 5=Traslado / en ruta',
  `status` varchar(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `esdefault` tinyint(1) DEFAULT '0' COMMENT '0=No, 1=Si',
  PRIMARY KEY (`id_almacen`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `cat_almacenes` */

insert  into `cat_almacenes`(`id_almacen`,`id_empresa`,`id_sucursal`,`codigo_almacen`,`nombre_almacen`,`tipo_almacen`,`status`,`esdefault`) values (1,1,1,'ALM1','ALMACEN 1',1,'A',1),(2,2,2,'ALMR','ALMACEN RAMON',1,'A',1);

/*Table structure for table `cat_certificados` */

CREATE TABLE `cat_certificados` (
  `id_certificado` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `archivo_certificado` varchar(100) NOT NULL,
  `archivo_llave` varbinary(100) DEFAULT NULL,
  `numero_certificado` varchar(20) DEFAULT NULL,
  `fecha_solicitud` datetime DEFAULT NULL,
  `fecha_vencimiento` datetime DEFAULT NULL,
  `rfc_certificado` varchar(15) DEFAULT NULL,
  `razonsocial_certificado` varchar(100) DEFAULT NULL,
  `pass_certificado` blob,
  `pem_certificado` blob,
  `pem_llave` blob,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `esdefault` tinyint(1) DEFAULT '0' COMMENT '0=No, 1=Si',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_certificado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `cat_certificados` */

/*Table structure for table `cat_chequeras` */

CREATE TABLE `cat_chequeras` (
  `id_chequera` bigint(20) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_chequera`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `cat_chequeras` */

insert  into `cat_chequeras`(`id_chequera`,`descripcion`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'TARJETA BANAMEX 544545454','A',2,'2019-07-21 14:58:45',NULL,NULL);

/*Table structure for table `cat_ciudades` */

CREATE TABLE `cat_ciudades` (
  `id_ciu` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID de la ciudad',
  `nom_ciu` varchar(60) DEFAULT NULL COMMENT 'Nombre de la ciudad',
  `key_est_ciu` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Estado',
  `key_pai_ciu` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'País',
  `uso_ciu` bigint(20) unsigned DEFAULT '0' COMMENT 'Indice de uso de la ciudad',
  PRIMARY KEY (`id_ciu`,`key_est_ciu`,`key_pai_ciu`)
) ENGINE=MyISAM AUTO_INCREMENT=4049 DEFAULT CHARSET=utf8;

/*Data for the table `cat_ciudades` */

insert  into `cat_ciudades`(`id_ciu`,`nom_ciu`,`key_est_ciu`,`key_pai_ciu`,`uso_ciu`) values (1,'Aguascalientes',1,146,0),(2,'Asientos',1,146,0),(3,'Calvillo',1,146,0),(5,'Cosío',1,146,0),(6,'Jesús María',1,146,0),(7,'Pabellón de Arteaga',1,146,0),(8,'Rincón de Romos',1,146,0),(9,'San José de Gracia',1,146,0),(10,'Tepezalá',1,146,0),(11,'El Llano',1,146,0),(12,'San Francisco de los Romo',1,146,0),(13,'Ensenada',2,146,0),(14,'Mexicali',2,146,0),(15,'Tecate',2,146,0),(16,'Tijuana',2,146,0),(17,'Playas de Rosarito',2,146,0),(18,'Comondú',3,146,0),(19,'Mulegé',3,146,0),(20,'La Paz',3,146,0),(21,'Los Cabos',3,146,0),(22,'Loreto',3,146,0),(23,'Calkiní',4,146,0),(24,'Campeche',4,146,0),(25,'Carmen',4,146,0),(26,'Champotón',4,146,0),(27,'Hecelchakán',4,146,0),(28,'Hopelchén',4,146,0),(29,'Palizada',4,146,0),(30,'Tenabo',4,146,0),(31,'Escárcega',4,146,0),(32,'Calakmul',4,146,0),(33,'Candelaria',4,146,0),(34,'Abasolo',5,146,0),(35,'Acuña',5,146,0),(36,'Allende',5,146,0),(37,'Arteaga',5,146,0),(38,'Candela',5,146,0),(39,'Castaños',5,146,0),(40,'Cuatro Ciénegas',5,146,0),(41,'Escobedo',5,146,0),(42,'Francisco I. Madero',5,146,0),(43,'Frontera',5,146,0),(44,'General Cepeda',5,146,0),(45,'Guerrero',5,146,0),(46,'Hidalgo',5,146,0),(47,'Jiménez',5,146,0),(48,'Juárez',5,146,0),(49,'Lamadrid',5,146,0),(50,'Matamoros',5,146,0),(51,'Monclova',5,146,0),(52,'Morelos',5,146,0),(53,'Múzquiz',5,146,0),(54,'Nadadores',5,146,0),(55,'Nava',5,146,0),(56,'Ocampo',5,146,0),(57,'Parras',5,146,0),(58,'Piedras Negras',5,146,0),(59,'Progreso',5,146,0),(60,'Ramos Arizpe',5,146,0),(61,'Sabinas',5,146,0),(62,'Sacramento',5,146,0),(63,'Saltillo',5,146,0),(64,'San Buenaventura',5,146,0),(65,'San Juan de Sabinas',5,146,0),(66,'San Pedro',5,146,0),(67,'Sierra Mojada',5,146,0),(68,'Torreón',5,146,0),(69,'Viesca',5,146,0),(70,'Villa Unión',5,146,0),(71,'Zaragoza',5,146,0),(72,'Armería',6,146,0),(73,'Colima',6,146,0),(74,'Comala',6,146,0),(75,'Coquimatlán',6,146,0),(76,'Cuauhtémoc',6,146,0),(77,'Ixtlahuacán',6,146,0),(78,'Manzanillo',6,146,0),(79,'Minatitlán',6,146,0),(80,'Tecomán',6,146,0),(81,'Villa de Álvarez',6,146,0),(82,'Acacoyagua',7,146,0),(83,'Acala',7,146,0),(84,'Acapetahua',7,146,0),(85,'Altamirano',7,146,0),(86,'Amatán',7,146,0),(87,'Amatenango de la Frontera',7,146,0),(88,'Amatenango del Valle',7,146,0),(89,'Angel Albino Corzo',7,146,0),(90,'Arriaga',7,146,0),(91,'Bejucal de Ocampo',7,146,0),(92,'Bella Vista',7,146,0),(93,'Berriozábal',7,146,0),(94,'Bochil',7,146,0),(95,'El Bosque',7,146,0),(96,'Cacahoatán',7,146,0),(97,'Catazajá',7,146,0),(98,'Cintalapa',7,146,0),(99,'Coapilla',7,146,0),(100,'Comitán de Domínguez',7,146,0),(101,'La Concordia',7,146,0),(102,'Copainalá',7,146,0),(103,'Chalchihuitán',7,146,0),(104,'Chamula',7,146,0),(105,'Chanal',7,146,0),(106,'Chapultenango',7,146,0),(107,'Chenalhó',7,146,0),(108,'Chiapa de Corzo',7,146,0),(109,'Chiapilla',7,146,0),(110,'Chicoasén',7,146,0),(111,'Chicomuselo',7,146,0),(112,'Chilón',7,146,0),(113,'Escuintla',7,146,0),(114,'Francisco León',7,146,0),(115,'Frontera Comalapa',7,146,0),(116,'Frontera Hidalgo',7,146,0),(117,'La Grandeza',7,146,0),(118,'Huehuetán',7,146,0),(119,'Huixtán',7,146,0),(120,'Huitiupán',7,146,0),(121,'Huixtla',7,146,0),(122,'La Independencia',7,146,0),(123,'Ixhuatán',7,146,0),(124,'Ixtacomitán',7,146,0),(125,'Ixtapa',7,146,0),(126,'Ixtapangajoya',7,146,0),(127,'Jiquipilas',7,146,0),(128,'Jitotol',7,146,0),(129,'Juárez',7,146,0),(130,'Larráinzar',7,146,0),(131,'La Libertad',7,146,0),(132,'Mapastepec',7,146,0),(133,'Las Margaritas',7,146,0),(134,'Mazapa de Madero',7,146,0),(135,'Mazatán',7,146,0),(136,'Metapa',7,146,0),(137,'Mitontic',7,146,0),(138,'Motozintla',7,146,0),(139,'Nicolás Ruíz',7,146,0),(140,'Ocosingo',7,146,0),(141,'Ocotepec',7,146,0),(142,'Ocozocoautla de Espinosa',7,146,0),(143,'Ostuacán',7,146,0),(144,'Osumacinta',7,146,0),(145,'Oxchuc',7,146,0),(146,'Palenque',7,146,0),(147,'Pantelhó',7,146,0),(148,'Pantepec',7,146,0),(149,'Pichucalco',7,146,0),(150,'Pijijiapan',7,146,0),(151,'El Porvenir',7,146,0),(152,'Villa Comaltitlán',7,146,0),(153,'Pueblo Nuevo Solistahuacán',7,146,0),(154,'Rayón',7,146,0),(155,'Reforma',7,146,0),(156,'Las Rosas',7,146,0),(157,'Sabanilla',7,146,0),(158,'Salto de Agua',7,146,0),(159,'San Cristóbal de las Casas',7,146,0),(160,'San Fernando',7,146,0),(161,'Siltepec',7,146,0),(162,'Simojovel',7,146,0),(163,'Sitalá',7,146,0),(164,'Socoltenango',7,146,0),(165,'Solosuchiapa',7,146,0),(166,'Soyaló',7,146,0),(167,'Suchiapa',7,146,0),(168,'Suchiate',7,146,0),(169,'Sunuapa',7,146,0),(170,'Tapachula',7,146,0),(171,'Tapalapa',7,146,0),(172,'Tapilula',7,146,0),(173,'Tecpatán',7,146,0),(174,'Tenejapa',7,146,0),(175,'Teopisca',7,146,0),(176,'Tila',7,146,0),(177,'Tonalá',7,146,0),(178,'Totolapa',7,146,0),(179,'La Trinitaria',7,146,0),(180,'Tumbalá',7,146,0),(181,'Tuxtla Gutiérrez',7,146,0),(182,'Tuxtla Chico',7,146,0),(183,'Tuzantán',7,146,0),(184,'Tzimol',7,146,0),(185,'Unión Juárez',7,146,0),(186,'Venustiano Carranza',7,146,0),(187,'Villa Corzo',7,146,0),(188,'Villaflores',7,146,0),(189,'Yajalón',7,146,0),(190,'San Lucas',7,146,0),(191,'Zinacantán',7,146,0),(192,'San Juan Cancuc',7,146,0),(193,'Aldama',7,146,0),(194,'Benemérito de las Américas',7,146,0),(195,'Maravilla Tenejapa',7,146,0),(196,'Marqués de Comillas',7,146,0),(197,'Montecristo de Guerrero',7,146,0),(198,'San Andrés Duraznal',7,146,0),(199,'Santiago el Pinar',7,146,0),(200,'Ahumada',8,146,0),(201,'Aldama',8,146,0),(202,'Allende',8,146,0),(203,'Aquiles Serdán',8,146,0),(204,'Ascensión',8,146,0),(205,'Bachíniva',8,146,0),(206,'Balleza',8,146,0),(207,'Batopilas',8,146,0),(208,'Bocoyna',8,146,0),(209,'Buenaventura',8,146,0),(210,'Camargo',8,146,0),(211,'Carichí',8,146,0),(212,'Casas Grandes',8,146,0),(213,'Coronado',8,146,0),(214,'Coyame del Sotol',8,146,0),(215,'La Cruz',8,146,0),(216,'Cuauhtémoc',8,146,0),(217,'Cusihuiriachi',8,146,0),(218,'Chihuahua',8,146,0),(219,'Chínipas',8,146,0),(220,'Delicias',8,146,0),(221,'Dr. Belisario Domínguez',8,146,0),(222,'Galeana',8,146,0),(223,'Santa Isabel',8,146,0),(224,'Gómez Farías',8,146,0),(225,'Gran Morelos',8,146,0),(226,'Guachochi',8,146,0),(227,'Guadalupe',8,146,0),(228,'Guadalupe y Calvo',8,146,0),(229,'Guazapares',8,146,0),(230,'Guerrero',8,146,0),(231,'Hidalgo del Parral',8,146,0),(232,'Huejotitán',8,146,0),(233,'Ignacio Zaragoza',8,146,0),(234,'Janos',8,146,0),(235,'Jiménez',8,146,0),(236,'Juárez',8,146,0),(237,'Julimes',8,146,0),(238,'López',8,146,0),(239,'Madera',8,146,0),(240,'Maguarichi',8,146,0),(241,'Manuel Benavides',8,146,0),(242,'Matachí',8,146,0),(243,'Matamoros',8,146,0),(244,'Meoqui',8,146,0),(245,'Morelos',8,146,0),(246,'Moris',8,146,0),(247,'Namiquipa',8,146,0),(248,'Nonoava',8,146,0),(249,'Nuevo Casas Grandes',8,146,0),(250,'Ocampo',8,146,0),(251,'Ojinaga',8,146,0),(252,'Praxedis G. Guerrero',8,146,0),(253,'Riva Palacio',8,146,0),(254,'Rosales',8,146,0),(255,'Rosario',8,146,0),(256,'San Francisco de Borja',8,146,0),(257,'San Francisco de Conchos',8,146,0),(258,'San Francisco del Oro',8,146,0),(259,'Santa Bárbara',8,146,0),(260,'Satevó',8,146,0),(261,'Saucillo',8,146,0),(262,'Temósachic',8,146,0),(263,'El Tule',8,146,0),(264,'Urique',8,146,0),(265,'Uruachi',8,146,0),(266,'Valle de Zaragoza',8,146,0),(267,'Azcapotzalco',9,146,0),(268,'Coyoacán',9,146,0),(269,'Cuajimalpa de Morelos',9,146,0),(270,'Gustavo A. Madero',9,146,0),(271,'Iztacalco',9,146,0),(272,'Iztapalapa',9,146,0),(273,'La Magdalena Contreras',9,146,0),(274,'Milpa Alta',9,146,0),(275,'Álvaro Obregón',9,146,0),(276,'Tláhuac',9,146,0),(277,'Tlalpan',9,146,0),(278,'Xochimilco',9,146,0),(279,'Benito Juárez',9,146,0),(280,'Cuauhtémoc',9,146,0),(281,'Miguel Hidalgo',9,146,0),(282,'Venustiano Carranza',9,146,0),(283,'Canatlán',10,146,0),(284,'Canelas',10,146,0),(285,'Coneto de Comonfort',10,146,0),(286,'Cuencamé',10,146,0),(287,'Durango',10,146,0),(288,'General Simón Bolívar',10,146,0),(289,'Gómez Palacio',10,146,0),(290,'Guadalupe Victoria',10,146,0),(291,'Guanaceví',10,146,0),(292,'Hidalgo',10,146,0),(293,'Indé',10,146,0),(294,'Lerdo',10,146,0),(295,'Mapimí',10,146,0),(296,'Mezquital',10,146,0),(297,'Nazas',10,146,0),(298,'Nombre de Dios',10,146,0),(299,'Ocampo',10,146,0),(300,'El Oro',10,146,0),(301,'Otáez',10,146,0),(302,'Pánuco de Coronado',10,146,0),(303,'Peñón Blanco',10,146,0),(304,'Poanas',10,146,0),(305,'Pueblo Nuevo',10,146,0),(306,'Rodeo',10,146,0),(307,'San Bernardo',10,146,0),(308,'San Dimas',10,146,0),(309,'San Juan de Guadalupe',10,146,0),(310,'San Juan del Río',10,146,0),(311,'San Luis del Cordero',10,146,0),(312,'San Pedro del Gallo',10,146,0),(313,'Santa Clara',10,146,0),(314,'Santiago Papasquiaro',10,146,0),(315,'Súchil',10,146,0),(316,'Tamazula',10,146,0),(317,'Tepehuanes',10,146,0),(318,'Tlahualilo',10,146,0),(319,'Topia',10,146,0),(320,'Vicente Guerrero',10,146,0),(321,'Nuevo Ideal',10,146,0),(322,'Abasolo',11,146,0),(323,'Acámbaro',11,146,0),(324,'San Miguel de Allende',11,146,0),(325,'Apaseo el Alto',11,146,0),(326,'Apaseo el Grande',11,146,0),(327,'Atarjea',11,146,0),(328,'Celaya',11,146,0),(329,'Manuel Doblado',11,146,0),(330,'Comonfort',11,146,0),(331,'Coroneo',11,146,0),(332,'Cortazar',11,146,0),(333,'Cuerámaro',11,146,0),(334,'Doctor Mora',11,146,0),(335,'Dolores Hidalgo Cuna de la Independencia Nacional',11,146,0),(336,'Guanajuato',11,146,0),(337,'Huanímaro',11,146,0),(338,'Irapuato',11,146,0),(339,'Jaral del Progreso',11,146,0),(340,'Jerécuaro',11,146,0),(341,'León',11,146,0),(342,'Moroleón',11,146,0),(343,'Ocampo',11,146,0),(344,'Pénjamo',11,146,0),(345,'Pueblo Nuevo',11,146,0),(346,'Purísima del Rincón',11,146,0),(347,'Romita',11,146,0),(348,'Salamanca',11,146,0),(349,'Salvatierra',11,146,0),(350,'San Diego de la Unión',11,146,0),(351,'San Felipe',11,146,0),(352,'San Francisco del Rincón',11,146,0),(353,'San José Iturbide',11,146,0),(354,'San Luis de la Paz',11,146,0),(355,'Santa Catarina',11,146,0),(356,'Santa Cruz de Juventino Rosas',11,146,0),(357,'Santiago Maravatío',11,146,0),(358,'Silao',11,146,0),(359,'Tarandacuao',11,146,0),(360,'Tarimoro',11,146,0),(361,'Tierra Blanca',11,146,0),(362,'Uriangato',11,146,0),(363,'Valle de Santiago',11,146,0),(364,'Victoria',11,146,0),(365,'Villagrán',11,146,0),(366,'Xichú',11,146,0),(367,'Yuriria',11,146,0),(368,'Acapulco de Juárez',12,146,0),(369,'Ahuacuotzingo',12,146,0),(370,'Ajuchitlán del Progreso',12,146,0),(371,'Alcozauca de Guerrero',12,146,0),(372,'Alpoyeca',12,146,0),(373,'Apaxtla',12,146,0),(374,'Arcelia',12,146,0),(375,'Atenango del Río',12,146,0),(376,'Atlamajalcingo del Monte',12,146,0),(377,'Atlixtac',12,146,0),(378,'Atoyac de Álvarez',12,146,0),(379,'Ayutla de los Libres',12,146,0),(380,'Azoyú',12,146,0),(381,'Benito Juárez',12,146,0),(382,'Buenavista de Cuéllar',12,146,0),(383,'Coahuayutla de José María Izazaga',12,146,0),(384,'Cocula',12,146,0),(385,'Copala',12,146,0),(386,'Copalillo',12,146,0),(387,'Copanatoyac',12,146,0),(388,'Coyuca de Benítez',12,146,0),(389,'Coyuca de Catalán',12,146,0),(390,'Cuajinicuilapa',12,146,0),(391,'Cualác',12,146,0),(392,'Cuautepec',12,146,0),(393,'Cuetzala del Progreso',12,146,0),(394,'Cutzamala de Pinzón',12,146,0),(395,'Chilapa de Álvarez',12,146,0),(396,'Chilpancingo de los Bravo',12,146,0),(397,'Florencio Villarreal',12,146,0),(398,'General Canuto A. Neri',12,146,0),(399,'General Heliodoro Castillo',12,146,0),(400,'Huamuxtitlán',12,146,0),(401,'Huitzuco de los Figueroa',12,146,0),(402,'Iguala de la Independencia',12,146,0),(403,'Igualapa',12,146,0),(404,'Ixcateopan de Cuauhtémoc',12,146,0),(405,'Zihuatanejo de Azueta',12,146,0),(406,'Juan R. Escudero',12,146,0),(407,'Leonardo Bravo',12,146,0),(408,'Malinaltepec',12,146,0),(409,'Mártir de Cuilapan',12,146,0),(410,'Metlatónoc',12,146,0),(411,'Mochitlán',12,146,0),(412,'Olinalá',12,146,0),(413,'Ometepec',12,146,0),(414,'Pedro Ascencio Alquisiras',12,146,0),(415,'Petatlán',12,146,0),(416,'Pilcaya',12,146,0),(417,'Pungarabato',12,146,0),(418,'Quechultenango',12,146,0),(419,'San Luis Acatlán',12,146,0),(420,'San Marcos',12,146,0),(421,'San Miguel Totolapan',12,146,0),(422,'Taxco de Alarcón',12,146,0),(423,'Tecoanapa',12,146,0),(424,'Técpan de Galeana',12,146,0),(425,'Teloloapan',12,146,0),(426,'Tepecoacuilco de Trujano',12,146,0),(427,'Tetipac',12,146,0),(428,'Tixtla de Guerrero',12,146,0),(429,'Tlacoachistlahuaca',12,146,0),(430,'Tlacoapa',12,146,0),(431,'Tlalchapa',12,146,0),(432,'Tlalixtaquilla de Maldonado',12,146,0),(433,'Tlapa de Comonfort',12,146,0),(434,'Tlapehuala',12,146,0),(435,'La Unión de Isidoro Montes de Oca',12,146,0),(436,'Xalpatláhuac',12,146,0),(437,'Xochihuehuetlán',12,146,0),(438,'Xochistlahuaca',12,146,0),(439,'Zapotitlán Tablas',12,146,0),(440,'Zirándaro',12,146,0),(441,'Zitlala',12,146,0),(442,'Eduardo Neri',12,146,0),(443,'Acatepec',12,146,0),(444,'Marquelia',12,146,0),(445,'Cochoapa el Grande',12,146,0),(446,'José Joaquin de Herrera',12,146,0),(447,'Juchitán',12,146,0),(448,'Iliatenco',12,146,0),(449,'Acatlán',13,146,0),(450,'Acaxochitlán',13,146,0),(451,'Actopan',13,146,0),(452,'Agua Blanca de Iturbide',13,146,0),(453,'Ajacuba',13,146,0),(454,'Alfajayucan',13,146,0),(455,'Almoloya',13,146,0),(456,'Apan',13,146,0),(457,'El Arenal',13,146,0),(458,'Atitalaquia',13,146,0),(459,'Atlapexco',13,146,0),(460,'Atotonilco el Grande',13,146,0),(461,'Atotonilco de Tula',13,146,0),(462,'Calnali',13,146,0),(463,'Cardonal',13,146,0),(464,'Cuautepec de Hinojosa',13,146,0),(465,'Chapantongo',13,146,0),(466,'Chapulhuacán',13,146,0),(467,'Chilcuautla',13,146,0),(468,'Eloxochitlán',13,146,0),(469,'Emiliano Zapata',13,146,0),(470,'Epazoyucan',13,146,0),(471,'Francisco I. Madero',13,146,0),(472,'Huasca de Ocampo',13,146,0),(473,'Huautla',13,146,0),(474,'Huazalingo',13,146,0),(475,'Huehuetla',13,146,0),(476,'Huejutla de Reyes',13,146,0),(477,'Huichapan',13,146,0),(478,'Ixmiquilpan',13,146,0),(479,'Jacala de Ledezma',13,146,0),(480,'Jaltocán',13,146,0),(481,'Juárez Hidalgo',13,146,0),(482,'Lolotla',13,146,0),(483,'Metepec',13,146,0),(484,'San Agustín Metzquititlán',13,146,0),(485,'Metztitlán',13,146,0),(486,'Mineral del Chico',13,146,0),(487,'Mineral del Monte',13,146,0),(488,'La Misión',13,146,0),(489,'Mixquiahuala de Juárez',13,146,0),(490,'Molango de Escamilla',13,146,0),(491,'Nicolás Flores',13,146,0),(492,'Nopala de Villagrán',13,146,0),(493,'Omitlán de Juárez',13,146,0),(494,'San Felipe Orizatlán',13,146,0),(495,'Pacula',13,146,0),(496,'Pachuca de Soto',13,146,0),(497,'Pisaflores',13,146,0),(498,'Progreso de Obregón',13,146,0),(499,'Mineral de la Reforma',13,146,0),(500,'San Agustín Tlaxiaca',13,146,0),(501,'San Bartolo Tutotepec',13,146,0),(502,'San Salvador',13,146,0),(503,'Santiago de Anaya',13,146,0),(504,'Santiago Tulantepec de Lugo Guerrero',13,146,0),(505,'Singuilucan',13,146,0),(506,'Tasquillo',13,146,0),(507,'Tecozautla',13,146,0),(508,'Tenango de Doria',13,146,0),(509,'Tepeapulco',13,146,0),(510,'Tepehuacán de Guerrero',13,146,0),(511,'Tepeji del Río de Ocampo',13,146,0),(512,'Tepetitlán',13,146,0),(513,'Tetepango',13,146,0),(514,'Villa de Tezontepec',13,146,0),(515,'Tezontepec de Aldama',13,146,0),(516,'Tianguistengo',13,146,0),(517,'Tizayuca',13,146,0),(518,'Tlahuelilpan',13,146,0),(519,'Tlahuiltepa',13,146,0),(520,'Tlanalapa',13,146,0),(521,'Tlanchinol',13,146,0),(522,'Tlaxcoapan',13,146,0),(523,'Tolcayuca',13,146,0),(524,'Tula de Allende',13,146,0),(525,'Tulancingo de Bravo',13,146,0),(526,'Xochiatipan',13,146,0),(527,'Xochicoatlán',13,146,0),(528,'Yahualica',13,146,0),(529,'Zacualtipán de Ángeles',13,146,0),(530,'Zapotlán de Juárez',13,146,0),(531,'Zempoala',13,146,0),(532,'Zimapán',13,146,0),(533,'Acatic',14,146,0),(534,'Acatlán de Juárez',14,146,0),(535,'Ahualulco de Mercado',14,146,0),(536,'Amacueca',14,146,0),(537,'Amatitán',14,146,0),(538,'Ameca',14,146,0),(539,'San Juanito de Escobedo',14,146,0),(540,'Arandas',14,146,0),(541,'El Arenal',14,146,0),(542,'Atemajac de Brizuela',14,146,0),(543,'Atengo',14,146,0),(544,'Atenguillo',14,146,0),(545,'Atotonilco el Alto',14,146,0),(546,'Atoyac',14,146,0),(547,'Autlán de Navarro',14,146,0),(548,'Ayotlán',14,146,0),(549,'Ayutla',14,146,0),(550,'La Barca',14,146,0),(551,'Bolaños',14,146,0),(552,'Cabo Corrientes',14,146,0),(553,'Casimiro Castillo',14,146,0),(554,'Cihuatlán',14,146,0),(555,'Zapotlán el Grande',14,146,0),(556,'Cocula',14,146,0),(557,'Colotlán',14,146,0),(558,'Concepción de Buenos Aires',14,146,0),(559,'Cuautitlán de García Barragán',14,146,0),(560,'Cuautla',14,146,0),(561,'Cuquío',14,146,0),(562,'Chapala',14,146,0),(563,'Chimaltitán',14,146,0),(564,'Chiquilistlán',14,146,0),(565,'Degollado',14,146,0),(566,'Ejutla',14,146,0),(567,'Encarnación de Díaz',14,146,0),(568,'Etzatlán',14,146,0),(569,'El Grullo',14,146,0),(570,'Guachinango',14,146,0),(571,'Guadalajara',14,146,0),(572,'Hostotipaquillo',14,146,0),(573,'Huejúcar',14,146,0),(574,'Huejuquilla el Alto',14,146,0),(575,'La Huerta',14,146,0),(576,'Ixtlahuacán de los Membrillos',14,146,0),(577,'Ixtlahuacán del Río',14,146,0),(578,'Jalostotitlán',14,146,0),(579,'Jamay',14,146,0),(580,'Jesús María',14,146,0),(581,'Jilotlán de los Dolores',14,146,0),(582,'Jocotepec',14,146,0),(583,'Juanacatlán',14,146,0),(584,'Juchitlán',14,146,0),(585,'Lagos de Moreno',14,146,0),(586,'El Limón',14,146,0),(587,'Magdalena',14,146,0),(588,'Santa María del Oro',14,146,0),(589,'La Manzanilla de la Paz',14,146,0),(590,'Mascota',14,146,0),(591,'Mazamitla',14,146,0),(592,'Mexticacán',14,146,0),(593,'Mezquitic',14,146,0),(594,'Mixtlán',14,146,0),(595,'Ocotlán',14,146,0),(596,'Ojuelos de Jalisco',14,146,0),(597,'Pihuamo',14,146,0),(598,'Poncitlán',14,146,0),(599,'Puerto Vallarta',14,146,0),(600,'Villa Purificación',14,146,0),(601,'Quitupan',14,146,0),(602,'El Salto',14,146,0),(603,'San Cristóbal de la Barranca',14,146,0),(604,'San Diego de Alejandría',14,146,0),(605,'San Juan de los Lagos',14,146,0),(606,'San Julián',14,146,0),(607,'San Marcos',14,146,0),(608,'San Martín de Bolaños',14,146,0),(609,'San Martín Hidalgo',14,146,0),(610,'San Miguel el Alto',14,146,0),(611,'Gómez Farías',14,146,0),(612,'San Sebastián del Oeste',14,146,0),(613,'Santa María de los Ángeles',14,146,0),(614,'Sayula',14,146,0),(615,'Tala',14,146,0),(616,'Talpa de Allende',14,146,0),(617,'Tamazula de Gordiano',14,146,0),(618,'Tapalpa',14,146,0),(619,'Tecalitlán',14,146,0),(620,'Tecolotlán',14,146,0),(621,'Techaluta de Montenegro',14,146,0),(622,'Tenamaxtlán',14,146,0),(623,'Teocaltiche',14,146,0),(624,'Teocuitatlán de Corona',14,146,0),(625,'Tepatitlán de Morelos',14,146,0),(626,'Tequila',14,146,0),(627,'Teuchitlán',14,146,0),(628,'Tizapán el Alto',14,146,0),(629,'Tlajomulco de Zúñiga',14,146,0),(630,'Tlaquepaque',14,146,0),(631,'Tolimán',14,146,0),(632,'Tomatlán',14,146,0),(633,'Tonalá',14,146,0),(634,'Tonaya',14,146,0),(635,'Tonila',14,146,0),(636,'Totatiche',14,146,0),(637,'Tototlán',14,146,0),(638,'Tuxcacuesco',14,146,0),(639,'Tuxcueca',14,146,0),(640,'Tuxpan',14,146,0),(641,'Unión de San Antonio',14,146,0),(642,'Unión de Tula',14,146,0),(643,'Valle de Guadalupe',14,146,0),(644,'Valle de Juárez',14,146,0),(645,'San Gabriel',14,146,0),(646,'Villa Corona',14,146,0),(647,'Villa Guerrero',14,146,0),(648,'Villa Hidalgo',14,146,0),(649,'Cañadas de Obregón',14,146,0),(650,'Yahualica de González Gallo',14,146,0),(651,'Zacoalco de Torres',14,146,0),(652,'Zapopan',14,146,0),(653,'Zapotiltic',14,146,0),(654,'Zapotitlán de Vadillo',14,146,0),(655,'Zapotlán del Rey',14,146,0),(656,'Zapotlanejo',14,146,0),(657,'San Ignacio Cerro Gordo',14,146,0),(658,'Acambay',15,146,0),(659,'Acolman',15,146,0),(660,'Aculco',15,146,0),(661,'Almoloya de Alquisiras',15,146,0),(662,'Almoloya de Juárez',15,146,0),(663,'Almoloya del Río',15,146,0),(664,'Amanalco',15,146,0),(665,'Amatepec',15,146,0),(666,'Amecameca',15,146,0),(667,'Apaxco',15,146,0),(668,'Atenco',15,146,0),(669,'Atizapán',15,146,0),(670,'Atizapán de Zaragoza',15,146,0),(671,'Atlacomulco',15,146,0),(672,'Atlautla',15,146,0),(673,'Axapusco',15,146,0),(674,'Ayapango',15,146,0),(675,'Calimaya',15,146,0),(676,'Capulhuac',15,146,0),(677,'Coacalco de Berriozábal',15,146,0),(678,'Coatepec Harinas',15,146,0),(679,'Cocotitlán',15,146,0),(680,'Coyotepec',15,146,0),(681,'Cuautitlán',15,146,0),(682,'Chalco',15,146,0),(683,'Chapa de Mota',15,146,0),(684,'Chapultepec',15,146,0),(685,'Chiautla',15,146,0),(686,'Chicoloapan',15,146,0),(687,'Chiconcuac',15,146,0),(688,'Chimalhuacán',15,146,0),(689,'Donato Guerra',15,146,0),(690,'Ecatepec de Morelos',15,146,0),(691,'Ecatzingo',15,146,0),(692,'Huehuetoca',15,146,0),(693,'Hueypoxtla',15,146,0),(694,'Huixquilucan',15,146,0),(695,'Isidro Fabela',15,146,0),(696,'Ixtapaluca',15,146,0),(697,'Ixtapan de la Sal',15,146,0),(698,'Ixtapan del Oro',15,146,0),(699,'Ixtlahuaca',15,146,0),(700,'Xalatlaco',15,146,0),(701,'Jaltenco',15,146,0),(702,'Jilotepec',15,146,0),(703,'Jilotzingo',15,146,0),(704,'Jiquipilco',15,146,0),(705,'Jocotitlán',15,146,0),(706,'Joquicingo',15,146,0),(707,'Juchitepec',15,146,0),(708,'Lerma',15,146,0),(709,'Malinalco',15,146,0),(710,'Melchor Ocampo',15,146,0),(711,'Metepec',15,146,0),(712,'Mexicaltzingo',15,146,0),(713,'Morelos',15,146,0),(714,'Naucalpan de Juárez',15,146,0),(715,'Nezahualcóyotl',15,146,0),(716,'Nextlalpan',15,146,0),(717,'Nicolás Romero',15,146,0),(718,'Nopaltepec',15,146,0),(719,'Ocoyoacac',15,146,0),(720,'Ocuilan',15,146,0),(721,'El Oro',15,146,0),(722,'Otumba',15,146,0),(723,'Otzoloapan',15,146,0),(724,'Otzolotepec',15,146,0),(725,'Ozumba',15,146,0),(726,'Papalotla',15,146,0),(727,'La Paz',15,146,0),(728,'Polotitlán',15,146,0),(729,'Rayón',15,146,0),(730,'San Antonio la Isla',15,146,0),(731,'San Felipe del Progreso',15,146,0),(732,'San Martín de las Pirámides',15,146,0),(733,'San Mateo Atenco',15,146,0),(734,'San Simón de Guerrero',15,146,0),(735,'Santo Tomás',15,146,0),(736,'Soyaniquilpan de Juárez',15,146,0),(737,'Sultepec',15,146,0),(738,'Tecámac',15,146,0),(739,'Tejupilco',15,146,0),(740,'Temamatla',15,146,0),(741,'Temascalapa',15,146,0),(742,'Temascalcingo',15,146,0),(743,'Temascaltepec',15,146,0),(744,'Temoaya',15,146,0),(745,'Tenancingo',15,146,0),(746,'Tenango del Aire',15,146,0),(747,'Tenango del Valle',15,146,0),(748,'Teoloyucán',15,146,0),(749,'Teotihuacán',15,146,0),(750,'Tepetlaoxtoc',15,146,0),(751,'Tepetlixpa',15,146,0),(752,'Tepotzotlán',15,146,0),(753,'Tequixquiac',15,146,0),(754,'Texcaltitlán',15,146,0),(755,'Texcalyacac',15,146,0),(756,'Texcoco',15,146,0),(757,'Tezoyuca',15,146,0),(758,'Tianguistenco',15,146,0),(759,'Timilpan',15,146,0),(760,'Tlalmanalco',15,146,0),(761,'Tlalnepantla de Baz',15,146,0),(762,'Tlatlaya',15,146,0),(763,'Toluca',15,146,0),(764,'Tonatico',15,146,0),(765,'Tultepec',15,146,0),(766,'Tultitlán',15,146,0),(767,'Valle de Bravo',15,146,0),(768,'Villa de Allende',15,146,0),(769,'Villa del Carbón',15,146,0),(770,'Villa Guerrero',15,146,0),(771,'Villa Victoria',15,146,0),(772,'Xonacatlán',15,146,0),(773,'Zacazonapan',15,146,0),(774,'Zacualpan',15,146,0),(775,'Zinacantepec',15,146,0),(776,'Zumpahuacán',15,146,0),(777,'Zumpango',15,146,0),(778,'Cuautitlán Izcalli',15,146,0),(779,'Valle de Chalco Solidaridad',15,146,0),(780,'Luvianos',15,146,0),(781,'San José del Rincón',15,146,0),(782,'Tonanitla',15,146,0),(783,'Acuitzio',16,146,0),(784,'Aguililla',16,146,0),(785,'Álvaro Obregón',16,146,0),(786,'Angamacutiro',16,146,0),(787,'Angangueo',16,146,0),(788,'Apatzingán',16,146,0),(789,'Aporo',16,146,0),(790,'Aquila',16,146,0),(791,'Ario',16,146,0),(792,'Arteaga',16,146,0),(793,'Briseñas',16,146,0),(794,'Buenavista',16,146,0),(795,'Carácuaro',16,146,0),(796,'Coahuayana',16,146,0),(797,'Coalcomán de Vázquez Pallares',16,146,0),(798,'Coeneo',16,146,0),(799,'Contepec',16,146,0),(800,'Copándaro',16,146,0),(801,'Cotija',16,146,0),(802,'Cuitzeo',16,146,0),(803,'Charapan',16,146,0),(804,'Charo',16,146,0),(805,'Chavinda',16,146,0),(806,'Cherán',16,146,0),(807,'Chilchota',16,146,0),(808,'Chinicuila',16,146,0),(809,'Chucándiro',16,146,0),(810,'Churintzio',16,146,0),(811,'Churumuco',16,146,0),(812,'Ecuandureo',16,146,0),(813,'Epitacio Huerta',16,146,0),(814,'Erongarícuaro',16,146,0),(815,'Gabriel Zamora',16,146,0),(816,'Hidalgo',16,146,0),(817,'La Huacana',16,146,0),(818,'Huandacareo',16,146,0),(819,'Huaniqueo',16,146,0),(820,'Huetamo',16,146,0),(821,'Huiramba',16,146,0),(822,'Indaparapeo',16,146,0),(823,'Irimbo',16,146,0),(824,'Ixtlán',16,146,0),(825,'Jacona',16,146,0),(826,'Jiménez',16,146,0),(827,'Jiquilpan',16,146,0),(828,'Juárez',16,146,0),(829,'Jungapeo',16,146,0),(830,'Lagunillas',16,146,0),(831,'Madero',16,146,0),(832,'Maravatío',16,146,0),(833,'Marcos Castellanos',16,146,0),(834,'Lázaro Cárdenas',16,146,0),(835,'Morelia',16,146,0),(836,'Morelos',16,146,0),(837,'Múgica',16,146,0),(838,'Nahuatzen',16,146,0),(839,'Nocupétaro',16,146,0),(840,'Nuevo Parangaricutiro',16,146,0),(841,'Nuevo Urecho',16,146,0),(842,'Numarán',16,146,0),(843,'Ocampo',16,146,0),(844,'Pajacuarán',16,146,0),(845,'Panindícuaro',16,146,0),(846,'Parácuaro',16,146,0),(847,'Paracho',16,146,0),(848,'Pátzcuaro',16,146,0),(849,'Penjamillo',16,146,0),(850,'Peribán',16,146,0),(851,'La Piedad',16,146,0),(852,'Purépero',16,146,0),(853,'Puruándiro',16,146,0),(854,'Queréndaro',16,146,0),(855,'Quiroga',16,146,0),(856,'Cojumatlán de Régules',16,146,0),(857,'Los Reyes',16,146,0),(858,'Sahuayo',16,146,0),(859,'San Lucas',16,146,0),(860,'Santa Ana Maya',16,146,0),(861,'Salvador Escalante',16,146,0),(862,'Senguio',16,146,0),(863,'Susupuato',16,146,0),(864,'Tacámbaro',16,146,0),(865,'Tancítaro',16,146,0),(866,'Tangamandapio',16,146,0),(867,'Tangancícuaro',16,146,0),(868,'Tanhuato',16,146,0),(869,'Taretan',16,146,0),(870,'Tarímbaro',16,146,0),(871,'Tepalcatepec',16,146,0),(872,'Tingambato',16,146,0),(873,'Tingüindín',16,146,0),(874,'Tiquicheo de Nicolás Romero',16,146,0),(875,'Tlalpujahua',16,146,0),(876,'Tlazazalca',16,146,0),(877,'Tocumbo',16,146,0),(878,'Tumbiscatío',16,146,0),(879,'Turicato',16,146,0),(880,'Tuxpan',16,146,0),(881,'Tuzantla',16,146,0),(882,'Tzintzuntzan',16,146,0),(883,'Tzitzio',16,146,0),(884,'Uruapan',16,146,0),(885,'Venustiano Carranza',16,146,0),(886,'Villamar',16,146,0),(887,'Vista Hermosa',16,146,0),(888,'Yurécuaro',16,146,0),(889,'Zacapu',16,146,0),(890,'Zamora',16,146,0),(891,'Zináparo',16,146,0),(892,'Zinapécuaro',16,146,0),(893,'Ziracuaretiro',16,146,0),(894,'Zitácuaro',16,146,0),(895,'José Sixto Verduzco',16,146,0),(896,'Amacuzac',17,146,0),(897,'Atlatlahucan',17,146,0),(898,'Axochiapan',17,146,0),(899,'Ayala',17,146,0),(900,'Coatlán del Río',17,146,0),(901,'Cuautla',17,146,0),(902,'Cuernavaca',17,146,0),(903,'Emiliano Zapata',17,146,0),(904,'Huitzilac',17,146,0),(905,'Jantetelco',17,146,0),(906,'Jiutepec',17,146,0),(907,'Jojutla',17,146,0),(908,'Jonacatepec',17,146,0),(909,'Mazatepec',17,146,0),(910,'Miacatlán',17,146,0),(911,'Ocuituco',17,146,0),(912,'Puente de Ixtla',17,146,0),(913,'Temixco',17,146,0),(914,'Tepalcingo',17,146,0),(915,'Tepoztlán',17,146,0),(916,'Tetecala',17,146,0),(917,'Tetela del Volcán',17,146,0),(918,'Tlalnepantla',17,146,0),(919,'Tlaltizapán',17,146,0),(920,'Tlaquiltenango',17,146,0),(921,'Tlayacapan',17,146,0),(922,'Totolapan',17,146,0),(923,'Xochitepec',17,146,0),(924,'Yautepec',17,146,0),(925,'Yecapixtla',17,146,0),(926,'Zacatepec',17,146,0),(927,'Zacualpan',17,146,0),(928,'Temoac',17,146,0),(929,'Acaponeta',18,146,0),(930,'Ahuacatlán',18,146,0),(931,'Amatlán de Cañas',18,146,0),(932,'Compostela',18,146,0),(933,'Huajicori',18,146,0),(934,'Ixtlán del Río',18,146,0),(935,'Jala',18,146,0),(936,'Xalisco',18,146,0),(937,'Del Nayar',18,146,0),(938,'Rosamorada',18,146,0),(939,'Ruíz',18,146,0),(940,'San Blas',18,146,0),(941,'San Pedro Lagunillas',18,146,0),(942,'Santa María del Oro',18,146,0),(943,'Santiago Ixcuintla',18,146,0),(944,'Tecuala',18,146,0),(945,'Tepic',18,146,0),(946,'Tuxpan',18,146,0),(947,'La Yesca',18,146,0),(948,'Bahía de Banderas',18,146,0),(949,'Abasolo',19,146,0),(950,'Agualeguas',19,146,0),(951,'Los Aldamas',19,146,0),(952,'Allende',19,146,0),(953,'Anáhuac',19,146,0),(954,'Apodaca',19,146,0),(955,'Aramberri',19,146,0),(956,'Bustamante',19,146,0),(957,'Cadereyta Jiménez',19,146,0),(958,'Carmen',19,146,0),(959,'Cerralvo',19,146,0),(960,'Ciénega de Flores',19,146,0),(961,'China',19,146,0),(962,'Dr. Arroyo',19,146,0),(963,'Dr. Coss',19,146,0),(964,'Dr. González',19,146,0),(965,'Galeana',19,146,0),(966,'García',19,146,0),(967,'San Pedro Garza García',19,146,0),(968,'Gral. Bravo',19,146,0),(969,'Gral. Escobedo',19,146,0),(970,'Gral. Terán',19,146,0),(971,'Gral. Treviño',19,146,0),(972,'Gral. Zaragoza',19,146,0),(973,'Gral. Zuazua',19,146,0),(974,'Guadalupe',19,146,0),(975,'Los Herreras',19,146,0),(976,'Higueras',19,146,0),(977,'Hualahuises',19,146,0),(978,'Iturbide',19,146,0),(979,'Juárez',19,146,0),(980,'Lampazos de Naranjo',19,146,0),(981,'Linares',19,146,0),(982,'Marín',19,146,0),(983,'Melchor Ocampo',19,146,0),(984,'Mier y Noriega',19,146,0),(985,'Mina',19,146,0),(986,'Montemorelos',19,146,0),(987,'Monterrey',19,146,0),(988,'Parás',19,146,0),(989,'Pesquería',19,146,0),(990,'Los Ramones',19,146,0),(991,'Rayones',19,146,0),(992,'Sabinas Hidalgo',19,146,0),(993,'Salinas Victoria',19,146,0),(994,'San Nicolás de los Garza',19,146,0),(995,'Hidalgo',19,146,0),(996,'Santa Catarina',19,146,0),(997,'Santiago',19,146,0),(998,'Vallecillo',19,146,0),(999,'Villaldama',19,146,0),(1000,'Abejones',20,146,0),(1001,'Acatlán de Pérez Figueroa',20,146,0),(1002,'Asunción Cacalotepec',20,146,0),(1003,'Asunción Cuyotepeji',20,146,0),(1004,'Asunción Ixtaltepec',20,146,0),(1005,'Asunción Nochixtlán',20,146,0),(1006,'Asunción Ocotlán',20,146,0),(1007,'Asunción Tlacolulita',20,146,0),(1008,'Ayotzintepec',20,146,0),(1009,'El Barrio de la Soledad',20,146,0),(1010,'Calihualá',20,146,0),(1011,'Candelaria Loxicha',20,146,0),(1012,'Ciénega de Zimatlán',20,146,0),(1013,'Ciudad Ixtepec',20,146,0),(1014,'Coatecas Altas',20,146,0),(1015,'Coicoyán de las Flores',20,146,0),(1016,'La Compañía',20,146,0),(1017,'Concepción Buenavista',20,146,0),(1018,'Concepción Pápalo',20,146,0),(1019,'Constancia del Rosario',20,146,0),(1020,'Cosolapa',20,146,0),(1021,'Cosoltepec',20,146,0),(1022,'Cuilápam de Guerrero',20,146,0),(1023,'Cuyamecalco Villa de Zaragoza',20,146,0),(1024,'Chahuites',20,146,0),(1025,'Chalcatongo de Hidalgo',20,146,0),(1026,'Chiquihuitlán de Benito Juárez',20,146,0),(1027,'Heroica Ciudad de Ejutla de Crespo',20,146,0),(1028,'Eloxochitlán de Flores Magón',20,146,0),(1029,'El Espinal',20,146,0),(1030,'Tamazulápam del Espíritu Santo',20,146,0),(1031,'Fresnillo de Trujano',20,146,0),(1032,'Guadalupe Etla',20,146,0),(1033,'Guadalupe de Ramírez',20,146,0),(1034,'Guelatao de Juárez',20,146,0),(1035,'Guevea de Humboldt',20,146,0),(1036,'Mesones Hidalgo',20,146,0),(1037,'Villa Hidalgo',20,146,0),(1038,'Heroica Ciudad de Huajuapan de León',20,146,0),(1039,'Huautepec',20,146,0),(1040,'Huautla de Jiménez',20,146,0),(1041,'Ixtlán de Juárez',20,146,0),(1042,'Heroica Ciudad de Juchitán de Zaragoza',20,146,0),(1043,'Loma Bonita',20,146,0),(1044,'Magdalena Apasco',20,146,0),(1045,'Magdalena Jaltepec',20,146,0),(1046,'Santa Magdalena Jicotlán',20,146,0),(1047,'Magdalena Mixtepec',20,146,0),(1048,'Magdalena Ocotlán',20,146,0),(1049,'Magdalena Peñasco',20,146,0),(1050,'Magdalena Teitipac',20,146,0),(1051,'Magdalena Tequisistlán',20,146,0),(1052,'Magdalena Tlacotepec',20,146,0),(1053,'Magdalena Zahuatlán',20,146,0),(1054,'Mariscala de Juárez',20,146,0),(1055,'Mártires de Tacubaya',20,146,0),(1056,'Matías Romero Avendaño',20,146,0),(1057,'Mazatlán Villa de Flores',20,146,0),(1058,'Miahuatlán de Porfirio Díaz',20,146,0),(1059,'Mixistlán de la Reforma',20,146,0),(1060,'Monjas',20,146,0),(1061,'Natividad',20,146,0),(1062,'Nazareno Etla',20,146,0),(1063,'Nejapa de Madero',20,146,0),(1064,'Ixpantepec Nieves',20,146,0),(1065,'Santiago Niltepec',20,146,0),(1066,'Oaxaca de Juárez',20,146,0),(1067,'Ocotlán de Morelos',20,146,0),(1068,'La Pe',20,146,0),(1069,'Pinotepa de Don Luis',20,146,0),(1070,'Pluma Hidalgo',20,146,0),(1071,'San José del Progreso',20,146,0),(1072,'Putla Villa de Guerrero',20,146,0),(1073,'Santa Catarina Quioquitani',20,146,0),(1074,'Reforma de Pineda',20,146,0),(1075,'La Reforma',20,146,0),(1076,'Reyes Etla',20,146,0),(1077,'Rojas de Cuauhtémoc',20,146,0),(1078,'Salina Cruz',20,146,0),(1079,'San Agustín Amatengo',20,146,0),(1080,'San Agustín Atenango',20,146,0),(1081,'San Agustín Chayuco',20,146,0),(1082,'San Agustín de las Juntas',20,146,0),(1083,'San Agustín Etla',20,146,0),(1084,'San Agustín Loxicha',20,146,0),(1085,'San Agustín Tlacotepec',20,146,0),(1086,'San Agustín Yatareni',20,146,0),(1087,'San Andrés Cabecera Nueva',20,146,0),(1088,'San Andrés Dinicuiti',20,146,0),(1089,'San Andrés Huaxpaltepec',20,146,0),(1090,'San Andrés Huayápam',20,146,0),(1091,'San Andrés Ixtlahuaca',20,146,0),(1092,'San Andrés Lagunas',20,146,0),(1093,'San Andrés Nuxiño',20,146,0),(1094,'San Andrés Paxtlán',20,146,0),(1095,'San Andrés Sinaxtla',20,146,0),(1096,'San Andrés Solaga',20,146,0),(1097,'San Andrés Teotilálpam',20,146,0),(1098,'San Andrés Tepetlapa',20,146,0),(1099,'San Andrés Yaá',20,146,0),(1100,'San Andrés Zabache',20,146,0),(1101,'San Andrés Zautla',20,146,0),(1102,'San Antonino Castillo Velasco',20,146,0),(1103,'San Antonino el Alto',20,146,0),(1104,'San Antonino Monte Verde',20,146,0),(1105,'San Antonio Acutla',20,146,0),(1106,'San Antonio de la Cal',20,146,0),(1107,'San Antonio Huitepec',20,146,0),(1108,'San Antonio Nanahuatípam',20,146,0),(1109,'San Antonio Sinicahua',20,146,0),(1110,'San Antonio Tepetlapa',20,146,0),(1111,'San Baltazar Chichicápam',20,146,0),(1112,'San Baltazar Loxicha',20,146,0),(1113,'San Baltazar Yatzachi el Bajo',20,146,0),(1114,'San Bartolo Coyotepec',20,146,0),(1115,'San Bartolomé Ayautla',20,146,0),(1116,'San Bartolomé Loxicha',20,146,0),(1117,'San Bartolomé Quialana',20,146,0),(1118,'San Bartolomé Yucuañe',20,146,0),(1119,'San Bartolomé Zoogocho',20,146,0),(1120,'San Bartolo Soyaltepec',20,146,0),(1121,'San Bartolo Yautepec',20,146,0),(1122,'San Bernardo Mixtepec',20,146,0),(1123,'San Blas Atempa',20,146,0),(1124,'San Carlos Yautepec',20,146,0),(1125,'San Cristóbal Amatlán',20,146,0),(1126,'San Cristóbal Amoltepec',20,146,0),(1127,'San Cristóbal Lachirioag',20,146,0),(1128,'San Cristóbal Suchixtlahuaca',20,146,0),(1129,'San Dionisio del Mar',20,146,0),(1130,'San Dionisio Ocotepec',20,146,0),(1131,'San Dionisio Ocotlán',20,146,0),(1132,'San Esteban Atatlahuca',20,146,0),(1133,'San Felipe Jalapa de Díaz',20,146,0),(1134,'San Felipe Tejalápam',20,146,0),(1135,'San Felipe Usila',20,146,0),(1136,'San Francisco Cahuacuá',20,146,0),(1137,'San Francisco Cajonos',20,146,0),(1138,'San Francisco Chapulapa',20,146,0),(1139,'San Francisco Chindúa',20,146,0),(1140,'San Francisco del Mar',20,146,0),(1141,'San Francisco Huehuetlán',20,146,0),(1142,'San Francisco Ixhuatán',20,146,0),(1143,'San Francisco Jaltepetongo',20,146,0),(1144,'San Francisco Lachigoló',20,146,0),(1145,'San Francisco Logueche',20,146,0),(1146,'San Francisco Nuxaño',20,146,0),(1147,'San Francisco Ozolotepec',20,146,0),(1148,'San Francisco Sola',20,146,0),(1149,'San Francisco Telixtlahuaca',20,146,0),(1150,'San Francisco Teopan',20,146,0),(1151,'San Francisco Tlapancingo',20,146,0),(1152,'San Gabriel Mixtepec',20,146,0),(1153,'San Ildefonso Amatlán',20,146,0),(1154,'San Ildefonso Sola',20,146,0),(1155,'San Ildefonso Villa Alta',20,146,0),(1156,'San Jacinto Amilpas',20,146,0),(1157,'San Jacinto Tlacotepec',20,146,0),(1158,'San Jerónimo Coatlán',20,146,0),(1159,'San Jerónimo Silacayoapilla',20,146,0),(1160,'San Jerónimo Sosola',20,146,0),(1161,'San Jerónimo Taviche',20,146,0),(1162,'San Jerónimo Tecóatl',20,146,0),(1163,'San Jorge Nuchita',20,146,0),(1164,'San José Ayuquila',20,146,0),(1165,'San José Chiltepec',20,146,0),(1166,'San José del Peñasco',20,146,0),(1167,'San José Estancia Grande',20,146,0),(1168,'San José Independencia',20,146,0),(1169,'San José Lachiguiri',20,146,0),(1170,'San José Tenango',20,146,0),(1171,'San Juan Achiutla',20,146,0),(1172,'San Juan Atepec',20,146,0),(1173,'Ánimas Trujano',20,146,0),(1174,'San Juan Bautista Atatlahuca',20,146,0),(1175,'San Juan Bautista Coixtlahuaca',20,146,0),(1176,'San Juan Bautista Cuicatlán',20,146,0),(1177,'San Juan Bautista Guelache',20,146,0),(1178,'San Juan Bautista Jayacatlán',20,146,0),(1179,'San Juan Bautista Lo de Soto',20,146,0),(1180,'San Juan Bautista Suchitepec',20,146,0),(1181,'San Juan Bautista Tlacoatzintepec',20,146,0),(1182,'San Juan Bautista Tlachichilco',20,146,0),(1183,'San Juan Bautista Tuxtepec',20,146,0),(1184,'San Juan Cacahuatepec',20,146,0),(1185,'San Juan Cieneguilla',20,146,0),(1186,'San Juan Coatzóspam',20,146,0),(1187,'San Juan Colorado',20,146,0),(1188,'San Juan Comaltepec',20,146,0),(1189,'San Juan Cotzocón',20,146,0),(1190,'San Juan Chicomezúchil',20,146,0),(1191,'San Juan Chilateca',20,146,0),(1192,'San Juan del Estado',20,146,0),(1193,'San Juan del Río',20,146,0),(1194,'San Juan Diuxi',20,146,0),(1195,'San Juan Evangelista Analco',20,146,0),(1196,'San Juan Guelavía',20,146,0),(1197,'San Juan Guichicovi',20,146,0),(1198,'San Juan Ihualtepec',20,146,0),(1199,'San Juan Juquila Mixes',20,146,0),(1200,'San Juan Juquila Vijanos',20,146,0),(1201,'San Juan Lachao',20,146,0),(1202,'San Juan Lachigalla',20,146,0),(1203,'San Juan Lajarcia',20,146,0),(1204,'San Juan Lalana',20,146,0),(1205,'San Juan de los Cués',20,146,0),(1206,'San Juan Mazatlán',20,146,0),(1207,'San Juan Mixtepec -Dto. 08 -',20,146,0),(1208,'San Juan Mixtepec -Dto. 26 -',20,146,0),(1209,'San Juan Ñumí',20,146,0),(1210,'San Juan Ozolotepec',20,146,0),(1211,'San Juan Petlapa',20,146,0),(1212,'San Juan Quiahije',20,146,0),(1213,'San Juan Quiotepec',20,146,0),(1214,'San Juan Sayultepec',20,146,0),(1215,'San Juan Tabaá',20,146,0),(1216,'San Juan Tamazola',20,146,0),(1217,'San Juan Teita',20,146,0),(1218,'San Juan Teitipac',20,146,0),(1219,'San Juan Tepeuxila',20,146,0),(1220,'San Juan Teposcolula',20,146,0),(1221,'San Juan Yaeé',20,146,0),(1222,'San Juan Yatzona',20,146,0),(1223,'San Juan Yucuita',20,146,0),(1224,'San Lorenzo',20,146,0),(1225,'San Lorenzo Albarradas',20,146,0),(1226,'San Lorenzo Cacaotepec',20,146,0),(1227,'San Lorenzo Cuaunecuiltitla',20,146,0),(1228,'San Lorenzo Texmelúcan',20,146,0),(1229,'San Lorenzo Victoria',20,146,0),(1230,'San Lucas Camotlán',20,146,0),(1231,'San Lucas Ojitlán',20,146,0),(1232,'San Lucas Quiaviní',20,146,0),(1233,'San Lucas Zoquiápam',20,146,0),(1234,'San Luis Amatlán',20,146,0),(1235,'San Marcial Ozolotepec',20,146,0),(1236,'San Marcos Arteaga',20,146,0),(1237,'San Martín de los Cansecos',20,146,0),(1238,'San Martín Huamelúlpam',20,146,0),(1239,'San Martín Itunyoso',20,146,0),(1240,'San Martín Lachilá',20,146,0),(1241,'San Martín Peras',20,146,0),(1242,'San Martín Tilcajete',20,146,0),(1243,'San Martín Toxpalan',20,146,0),(1244,'San Martín Zacatepec',20,146,0),(1245,'San Mateo Cajonos',20,146,0),(1246,'Capulálpam de Méndez',20,146,0),(1247,'San Mateo del Mar',20,146,0),(1248,'San Mateo Yoloxochitlán',20,146,0),(1249,'San Mateo Etlatongo',20,146,0),(1250,'San Mateo Nejápam',20,146,0),(1251,'San Mateo Peñasco',20,146,0),(1252,'San Mateo Piñas',20,146,0),(1253,'San Mateo Río Hondo',20,146,0),(1254,'San Mateo Sindihui',20,146,0),(1255,'San Mateo Tlapiltepec',20,146,0),(1256,'San Melchor Betaza',20,146,0),(1257,'San Miguel Achiutla',20,146,0),(1258,'San Miguel Ahuehuetitlán',20,146,0),(1259,'San Miguel Aloápam',20,146,0),(1260,'San Miguel Amatitlán',20,146,0),(1261,'San Miguel Amatlán',20,146,0),(1262,'San Miguel Coatlán',20,146,0),(1263,'San Miguel Chicahua',20,146,0),(1264,'San Miguel Chimalapa',20,146,0),(1265,'San Miguel del Puerto',20,146,0),(1266,'San Miguel del Río',20,146,0),(1267,'San Miguel Ejutla',20,146,0),(1268,'San Miguel el Grande',20,146,0),(1269,'San Miguel Huautla',20,146,0),(1270,'San Miguel Mixtepec',20,146,0),(1271,'San Miguel Panixtlahuaca',20,146,0),(1272,'San Miguel Peras',20,146,0),(1273,'San Miguel Piedras',20,146,0),(1274,'San Miguel Quetzaltepec',20,146,0),(1275,'San Miguel Santa Flor',20,146,0),(1276,'Villa Sola de Vega',20,146,0),(1277,'San Miguel Soyaltepec',20,146,0),(1278,'San Miguel Suchixtepec',20,146,0),(1279,'Villa Talea de Castro',20,146,0),(1280,'San Miguel Tecomatlán',20,146,0),(1281,'San Miguel Tenango',20,146,0),(1282,'San Miguel Tequixtepec',20,146,0),(1283,'San Miguel Tilquiápam',20,146,0),(1284,'San Miguel Tlacamama',20,146,0),(1285,'San Miguel Tlacotepec',20,146,0),(1286,'San Miguel Tulancingo',20,146,0),(1287,'San Miguel Yotao',20,146,0),(1288,'San Nicolás',20,146,0),(1289,'San Nicolás Hidalgo',20,146,0),(1290,'San Pablo Coatlán',20,146,0),(1291,'San Pablo Cuatro Venados',20,146,0),(1292,'San Pablo Etla',20,146,0),(1293,'San Pablo Huitzo',20,146,0),(1294,'San Pablo Huixtepec',20,146,0),(1295,'San Pablo Macuiltianguis',20,146,0),(1296,'San Pablo Tijaltepec',20,146,0),(1297,'San Pablo Villa de Mitla',20,146,0),(1298,'San Pablo Yaganiza',20,146,0),(1299,'San Pedro Amuzgos',20,146,0),(1300,'San Pedro Apóstol',20,146,0),(1301,'San Pedro Atoyac',20,146,0),(1302,'San Pedro Cajonos',20,146,0),(1303,'San Pedro Coxcaltepec Cántaros',20,146,0),(1304,'San Pedro Comitancillo',20,146,0),(1305,'San Pedro el Alto',20,146,0),(1306,'San Pedro Huamelula',20,146,0),(1307,'San Pedro Huilotepec',20,146,0),(1308,'San Pedro Ixcatlán',20,146,0),(1309,'San Pedro Ixtlahuaca',20,146,0),(1310,'San Pedro Jaltepetongo',20,146,0),(1311,'San Pedro Jicayán',20,146,0),(1312,'San Pedro Jocotipac',20,146,0),(1313,'San Pedro Juchatengo',20,146,0),(1314,'San Pedro Mártir',20,146,0),(1315,'San Pedro Mártir Quiechapa',20,146,0),(1316,'San Pedro Mártir Yucuxaco',20,146,0),(1317,'San Pedro Mixtepec -Dto. 22 -',20,146,0),(1318,'San Pedro Mixtepec -Dto. 26 -',20,146,0),(1319,'San Pedro Molinos',20,146,0),(1320,'San Pedro Nopala',20,146,0),(1321,'San Pedro Ocopetatillo',20,146,0),(1322,'San Pedro Ocotepec',20,146,0),(1323,'San Pedro Pochutla',20,146,0),(1324,'San Pedro Quiatoni',20,146,0),(1325,'San Pedro Sochiápam',20,146,0),(1326,'San Pedro Tapanatepec',20,146,0),(1327,'San Pedro Taviche',20,146,0),(1328,'San Pedro Teozacoalco',20,146,0),(1329,'San Pedro Teutila',20,146,0),(1330,'San Pedro Tidaá',20,146,0),(1331,'San Pedro Topiltepec',20,146,0),(1332,'San Pedro Totolápam',20,146,0),(1333,'Villa de Tututepec de Melchor Ocampo',20,146,0),(1334,'San Pedro Yaneri',20,146,0),(1335,'San Pedro Yólox',20,146,0),(1336,'San Pedro y San Pablo Ayutla',20,146,0),(1337,'Villa de Etla',20,146,0),(1338,'San Pedro y San Pablo Teposcolula',20,146,0),(1339,'San Pedro y San Pablo Tequixtepec',20,146,0),(1340,'San Pedro Yucunama',20,146,0),(1341,'San Raymundo Jalpan',20,146,0),(1342,'San Sebastián Abasolo',20,146,0),(1343,'San Sebastián Coatlán',20,146,0),(1344,'San Sebastián Ixcapa',20,146,0),(1345,'San Sebastián Nicananduta',20,146,0),(1346,'San Sebastián Río Hondo',20,146,0),(1347,'San Sebastián Tecomaxtlahuaca',20,146,0),(1348,'San Sebastián Teitipac',20,146,0),(1349,'San Sebastián Tutla',20,146,0),(1350,'San Simón Almolongas',20,146,0),(1351,'San Simón Zahuatlán',20,146,0),(1352,'Santa Ana',20,146,0),(1353,'Santa Ana Ateixtlahuaca',20,146,0),(1354,'Santa Ana Cuauhtémoc',20,146,0),(1355,'Santa Ana del Valle',20,146,0),(1356,'Santa Ana Tavela',20,146,0),(1357,'Santa Ana Tlapacoyan',20,146,0),(1358,'Santa Ana Yareni',20,146,0),(1359,'Santa Ana Zegache',20,146,0),(1360,'Santa Catalina Quierí',20,146,0),(1361,'Santa Catarina Cuixtla',20,146,0),(1362,'Santa Catarina Ixtepeji',20,146,0),(1363,'Santa Catarina Juquila',20,146,0),(1364,'Santa Catarina Lachatao',20,146,0),(1365,'Santa Catarina Loxicha',20,146,0),(1366,'Santa Catarina Mechoacán',20,146,0),(1367,'Santa Catarina Minas',20,146,0),(1368,'Santa Catarina Quiané',20,146,0),(1369,'Santa Catarina Tayata',20,146,0),(1370,'Santa Catarina Ticuá',20,146,0),(1371,'Santa Catarina Yosonotú',20,146,0),(1372,'Santa Catarina Zapoquila',20,146,0),(1373,'Santa Cruz Acatepec',20,146,0),(1374,'Santa Cruz Amilpas',20,146,0),(1375,'Santa Cruz de Bravo',20,146,0),(1376,'Santa Cruz Itundujia',20,146,0),(1377,'Santa Cruz Mixtepec',20,146,0),(1378,'Santa Cruz Nundaco',20,146,0),(1379,'Santa Cruz Papalutla',20,146,0),(1380,'Santa Cruz Tacache de Mina',20,146,0),(1381,'Santa Cruz Tacahua',20,146,0),(1382,'Santa Cruz Tayata',20,146,0),(1383,'Santa Cruz Xitla',20,146,0),(1384,'Santa Cruz Xoxocotlán',20,146,0),(1385,'Santa Cruz Zenzontepec',20,146,0),(1386,'Santa Gertrudis',20,146,0),(1387,'Santa Inés del Monte',20,146,0),(1388,'Santa Inés Yatzeche',20,146,0),(1389,'Santa Lucía del Camino',20,146,0),(1390,'Santa Lucía Miahuatlán',20,146,0),(1391,'Santa Lucía Monteverde',20,146,0),(1392,'Santa Lucía Ocotlán',20,146,0),(1393,'Santa María Alotepec',20,146,0),(1394,'Santa María Apazco',20,146,0),(1395,'Santa María la Asunción',20,146,0),(1396,'Heroica Ciudad de Tlaxiaco',20,146,0),(1397,'Ayoquezco de Aldama',20,146,0),(1398,'Santa María Atzompa',20,146,0),(1399,'Santa María Camotlán',20,146,0),(1400,'Santa María Colotepec',20,146,0),(1401,'Santa María Cortijo',20,146,0),(1402,'Santa María Coyotepec',20,146,0),(1403,'Santa María Chachoápam',20,146,0),(1404,'Villa de Chilapa de Díaz',20,146,0),(1405,'Santa María Chilchotla',20,146,0),(1406,'Santa María Chimalapa',20,146,0),(1407,'Santa María del Rosario',20,146,0),(1408,'Santa María del Tule',20,146,0),(1409,'Santa María Ecatepec',20,146,0),(1410,'Santa María Guelacé',20,146,0),(1411,'Santa María Guienagati',20,146,0),(1412,'Santa María Huatulco',20,146,0),(1413,'Santa María Huazolotitlán',20,146,0),(1414,'Santa María Ipalapa',20,146,0),(1415,'Santa María Ixcatlán',20,146,0),(1416,'Santa María Jacatepec',20,146,0),(1417,'Santa María Jalapa del Marqués',20,146,0),(1418,'Santa María Jaltianguis',20,146,0),(1419,'Santa María Lachixío',20,146,0),(1420,'Santa María Mixtequilla',20,146,0),(1421,'Santa María Nativitas',20,146,0),(1422,'Santa María Nduayaco',20,146,0),(1423,'Santa María Ozolotepec',20,146,0),(1424,'Santa María Pápalo',20,146,0),(1425,'Santa María Peñoles',20,146,0),(1426,'Santa María Petapa',20,146,0),(1427,'Santa María Quiegolani',20,146,0),(1428,'Santa María Sola',20,146,0),(1429,'Santa María Tataltepec',20,146,0),(1430,'Santa María Tecomavaca',20,146,0),(1431,'Santa María Temaxcalapa',20,146,0),(1432,'Santa María Temaxcaltepec',20,146,0),(1433,'Santa María Teopoxco',20,146,0),(1434,'Santa María Tepantlali',20,146,0),(1435,'Santa María Texcatitlán',20,146,0),(1436,'Santa María Tlahuitoltepec',20,146,0),(1437,'Santa María Tlalixtac',20,146,0),(1438,'Santa María Tonameca',20,146,0),(1439,'Santa María Totolapilla',20,146,0),(1440,'Santa María Xadani',20,146,0),(1441,'Santa María Yalina',20,146,0),(1442,'Santa María Yavesía',20,146,0),(1443,'Santa María Yolotepec',20,146,0),(1444,'Santa María Yosoyúa',20,146,0),(1445,'Santa María Yucuhiti',20,146,0),(1446,'Santa María Zacatepec',20,146,0),(1447,'Santa María Zaniza',20,146,0),(1448,'Santa María Zoquitlán',20,146,0),(1449,'Santiago Amoltepec',20,146,0),(1450,'Santiago Apoala',20,146,0),(1451,'Santiago Apóstol',20,146,0),(1452,'Santiago Astata',20,146,0),(1453,'Santiago Atitlán',20,146,0),(1454,'Santiago Ayuquililla',20,146,0),(1455,'Santiago Cacaloxtepec',20,146,0),(1456,'Santiago Camotlán',20,146,0),(1457,'Santiago Comaltepec',20,146,0),(1458,'Santiago Chazumba',20,146,0),(1459,'Santiago Choápam',20,146,0),(1460,'Santiago del Río',20,146,0),(1461,'Santiago Huajolotitlán',20,146,0),(1462,'Santiago Huauclilla',20,146,0),(1463,'Santiago Ihuitlán Plumas',20,146,0),(1464,'Santiago Ixcuintepec',20,146,0),(1465,'Santiago Ixtayutla',20,146,0),(1466,'Santiago Jamiltepec',20,146,0),(1467,'Santiago Jocotepec',20,146,0),(1468,'Santiago Juxtlahuaca',20,146,0),(1469,'Santiago Lachiguiri',20,146,0),(1470,'Santiago Lalopa',20,146,0),(1471,'Santiago Laollaga',20,146,0),(1472,'Santiago Laxopa',20,146,0),(1473,'Santiago Llano Grande',20,146,0),(1474,'Santiago Matatlán',20,146,0),(1475,'Santiago Miltepec',20,146,0),(1476,'Santiago Minas',20,146,0),(1477,'Santiago Nacaltepec',20,146,0),(1478,'Santiago Nejapilla',20,146,0),(1479,'Santiago Nundiche',20,146,0),(1480,'Santiago Nuyoó',20,146,0),(1481,'Santiago Pinotepa Nacional',20,146,0),(1482,'Santiago Suchilquitongo',20,146,0),(1483,'Santiago Tamazola',20,146,0),(1484,'Santiago Tapextla',20,146,0),(1485,'Villa Tejúpam de la Unión',20,146,0),(1486,'Santiago Tenango',20,146,0),(1487,'Santiago Tepetlapa',20,146,0),(1488,'Santiago Tetepec',20,146,0),(1489,'Santiago Texcalcingo',20,146,0),(1490,'Santiago Textitlán',20,146,0),(1491,'Santiago Tilantongo',20,146,0),(1492,'Santiago Tillo',20,146,0),(1493,'Santiago Tlazoyaltepec',20,146,0),(1494,'Santiago Xanica',20,146,0),(1495,'Santiago Xiacuí',20,146,0),(1496,'Santiago Yaitepec',20,146,0),(1497,'Santiago Yaveo',20,146,0),(1498,'Santiago Yolomécatl',20,146,0),(1499,'Santiago Yosondúa',20,146,0),(1500,'Santiago Yucuyachi',20,146,0),(1501,'Santiago Zacatepec',20,146,0),(1502,'Santiago Zoochila',20,146,0),(1503,'Nuevo Zoquiápam',20,146,0),(1504,'Santo Domingo Ingenio',20,146,0),(1505,'Santo Domingo Albarradas',20,146,0),(1506,'Santo Domingo Armenta',20,146,0),(1507,'Santo Domingo Chihuitán',20,146,0),(1508,'Santo Domingo de Morelos',20,146,0),(1509,'Santo Domingo Ixcatlán',20,146,0),(1510,'Santo Domingo Nuxaá',20,146,0),(1511,'Santo Domingo Ozolotepec',20,146,0),(1512,'Santo Domingo Petapa',20,146,0),(1513,'Santo Domingo Roayaga',20,146,0),(1514,'Santo Domingo Tehuantepec',20,146,0),(1515,'Santo Domingo Teojomulco',20,146,0),(1516,'Santo Domingo Tepuxtepec',20,146,0),(1517,'Santo Domingo Tlatayápam',20,146,0),(1518,'Santo Domingo Tomaltepec',20,146,0),(1519,'Santo Domingo Tonalá',20,146,0),(1520,'Santo Domingo Tonaltepec',20,146,0),(1521,'Santo Domingo Xagacía',20,146,0),(1522,'Santo Domingo Yanhuitlán',20,146,0),(1523,'Santo Domingo Yodohino',20,146,0),(1524,'Santo Domingo Zanatepec',20,146,0),(1525,'Santos Reyes Nopala',20,146,0),(1526,'Santos Reyes Pápalo',20,146,0),(1527,'Santos Reyes Tepejillo',20,146,0),(1528,'Santos Reyes Yucuná',20,146,0),(1529,'Santo Tomás Jalieza',20,146,0),(1530,'Santo Tomás Mazaltepec',20,146,0),(1531,'Santo Tomás Ocotepec',20,146,0),(1532,'Santo Tomás Tamazulapan',20,146,0),(1533,'San Vicente Coatlán',20,146,0),(1534,'San Vicente Lachixío',20,146,0),(1535,'San Vicente Nuñú',20,146,0),(1536,'Silacayoápam',20,146,0),(1537,'Sitio de Xitlapehua',20,146,0),(1538,'Soledad Etla',20,146,0),(1539,'Villa de Tamazulápam del Progreso',20,146,0),(1540,'Tanetze de Zaragoza',20,146,0),(1541,'Taniche',20,146,0),(1542,'Tataltepec de Valdés',20,146,0),(1543,'Teococuilco de Marcos Pérez',20,146,0),(1544,'Teotitlán de Flores Magón',20,146,0),(1545,'Teotitlán del Valle',20,146,0),(1546,'Teotongo',20,146,0),(1547,'Tepelmeme Villa de Morelos',20,146,0),(1548,'Tezoatlán de Segura y Luna',20,146,0),(1549,'San Jerónimo Tlacochahuaya',20,146,0),(1550,'Tlacolula de Matamoros',20,146,0),(1551,'Tlacotepec Plumas',20,146,0),(1552,'Tlalixtac de Cabrera',20,146,0),(1553,'Totontepec Villa de Morelos',20,146,0),(1554,'Trinidad Zaachila',20,146,0),(1555,'La Trinidad Vista Hermosa',20,146,0),(1556,'Unión Hidalgo',20,146,0),(1557,'Valerio Trujano',20,146,0),(1558,'San Juan Bautista Valle Nacional',20,146,0),(1559,'Villa Díaz Ordaz',20,146,0),(1560,'Yaxe',20,146,0),(1561,'Magdalena Yodocono de Porfirio Díaz',20,146,0),(1562,'Yogana',20,146,0),(1563,'Yutanduchi de Guerrero',20,146,0),(1564,'Villa de Zaachila',20,146,0),(1565,'Zapotitlán del Río',20,146,0),(1566,'Zapotitlán Lagunas',20,146,0),(1567,'Zapotitlán Palmas',20,146,0),(1568,'Santa Inés de Zaragoza',20,146,0),(1569,'Zimatlán de Álvarez',20,146,0),(1570,'Acajete',21,146,0),(1571,'Acateno',21,146,0),(1572,'Acatlán',21,146,0),(1573,'Acatzingo',21,146,0),(1574,'Acteopan',21,146,0),(1575,'Ahuacatlán',21,146,0),(1576,'Ahuatlán',21,146,0),(1577,'Ahuazotepec',21,146,0),(1578,'Ahuehuetitla',21,146,0),(1579,'Ajalpan',21,146,0),(1580,'Albino Zertuche',21,146,0),(1581,'Aljojuca',21,146,0),(1582,'Altepexi',21,146,0),(1583,'Amixtlán',21,146,0),(1584,'Amozoc',21,146,0),(1585,'Aquixtla',21,146,0),(1586,'Atempan',21,146,0),(1587,'Atexcal',21,146,0),(1588,'Atlixco',21,146,0),(1589,'Atoyatempan',21,146,0),(1590,'Atzala',21,146,0),(1591,'Atzitzihuacán',21,146,0),(1592,'Atzitzintla',21,146,0),(1593,'Axutla',21,146,0),(1594,'Ayotoxco de Guerrero',21,146,0),(1595,'Calpan',21,146,0),(1596,'Caltepec',21,146,0),(1597,'Camocuautla',21,146,0),(1598,'Caxhuacan',21,146,0),(1599,'Coatepec',21,146,0),(1600,'Coatzingo',21,146,0),(1601,'Cohetzala',21,146,0),(1602,'Cohuecan',21,146,0),(1603,'Coronango',21,146,0),(1604,'Coxcatlán',21,146,0),(1605,'Coyomeapan',21,146,0),(1606,'Coyotepec',21,146,0),(1607,'Cuapiaxtla de Madero',21,146,0),(1608,'Cuautempan',21,146,0),(1609,'Cuautinchán',21,146,0),(1610,'Cuautlancingo',21,146,0),(1611,'Cuayuca de Andrade',21,146,0),(1612,'Cuetzalan del Progreso',21,146,0),(1613,'Cuyoaco',21,146,0),(1614,'Chalchicomula de Sesma',21,146,0),(1615,'Chapulco',21,146,0),(1616,'Chiautla',21,146,0),(1617,'Chiautzingo',21,146,0),(1618,'Chiconcuautla',21,146,0),(1619,'Chichiquila',21,146,0),(1620,'Chietla',21,146,0),(1621,'Chigmecatitlán',21,146,0),(1622,'Chignahuapan',21,146,0),(1623,'Chignautla',21,146,0),(1624,'Chila',21,146,0),(1625,'Chila de la Sal',21,146,0),(1626,'Honey',21,146,0),(1627,'Chilchotla',21,146,0),(1628,'Chinantla',21,146,0),(1629,'Domingo Arenas',21,146,0),(1630,'Eloxochitlán',21,146,0),(1631,'Epatlán',21,146,0),(1632,'Esperanza',21,146,0),(1633,'Francisco Z. Mena',21,146,0),(1634,'General Felipe Ángeles',21,146,0),(1635,'Guadalupe',21,146,0),(1636,'Guadalupe Victoria',21,146,0),(1637,'Hermenegildo Galeana',21,146,0),(1638,'Huaquechula',21,146,0),(1639,'Huatlatlauca',21,146,0),(1640,'Huauchinango',21,146,0),(1641,'Huehuetla',21,146,0),(1642,'Huehuetlán el Chico',21,146,0),(1643,'Huejotzingo',21,146,0),(1644,'Hueyapan',21,146,0),(1645,'Hueytamalco',21,146,0),(1646,'Hueytlalpan',21,146,0),(1647,'Huitzilan de Serdán',21,146,0),(1648,'Huitziltepec',21,146,0),(1649,'Atlequizayan',21,146,0),(1650,'Ixcamilpa de Guerrero',21,146,0),(1651,'Ixcaquixtla',21,146,0),(1652,'Ixtacamaxtitlán',21,146,0),(1653,'Ixtepec',21,146,0),(1654,'Izúcar de Matamoros',21,146,0),(1655,'Jalpan',21,146,0),(1656,'Jolalpan',21,146,0),(1657,'Jonotla',21,146,0),(1658,'Jopala',21,146,0),(1659,'Juan C. Bonilla',21,146,0),(1660,'Juan Galindo',21,146,0),(1661,'Juan N. Méndez',21,146,0),(1662,'Lafragua',21,146,0),(1663,'Libres',21,146,0),(1664,'La Magdalena Tlatlauquitepec',21,146,0),(1665,'Mazapiltepec de Juárez',21,146,0),(1666,'Mixtla',21,146,0),(1667,'Molcaxac',21,146,0),(1668,'Cañada Morelos',21,146,0),(1669,'Naupan',21,146,0),(1670,'Nauzontla',21,146,0),(1671,'Nealtican',21,146,0),(1672,'Nicolás Bravo',21,146,0),(1673,'Nopalucan',21,146,0),(1674,'Ocotepec',21,146,0),(1675,'Ocoyucan',21,146,0),(1676,'Olintla',21,146,0),(1677,'Oriental',21,146,0),(1678,'Pahuatlán',21,146,0),(1679,'Palmar de Bravo',21,146,0),(1680,'Pantepec',21,146,0),(1681,'Petlalcingo',21,146,0),(1682,'Piaxtla',21,146,0),(1683,'Puebla',21,146,0),(1684,'Quecholac',21,146,0),(1685,'Quimixtlán',21,146,0),(1686,'Rafael Lara Grajales',21,146,0),(1687,'Los Reyes de Juárez',21,146,0),(1688,'San Andrés Cholula',21,146,0),(1689,'San Antonio Cañada',21,146,0),(1690,'San Diego la Mesa Tochimiltzingo',21,146,0),(1691,'San Felipe Teotlalcingo',21,146,0),(1692,'San Felipe Tepatlán',21,146,0),(1693,'San Gabriel Chilac',21,146,0),(1694,'San Gregorio Atzompa',21,146,0),(1695,'San Jerónimo Tecuanipan',21,146,0),(1696,'San Jerónimo Xayacatlán',21,146,0),(1697,'San José Chiapa',21,146,0),(1698,'San José Miahuatlán',21,146,0),(1699,'San Juan Atenco',21,146,0),(1700,'San Juan Atzompa',21,146,0),(1701,'San Martín Texmelucan',21,146,0),(1702,'San Martín Totoltepec',21,146,0),(1703,'San Matías Tlalancaleca',21,146,0),(1704,'San Miguel Ixitlán',21,146,0),(1705,'San Miguel Xoxtla',21,146,0),(1706,'San Nicolás Buenos Aires',21,146,0),(1707,'San Nicolás de los Ranchos',21,146,0),(1708,'San Pablo Anicano',21,146,0),(1709,'San Pedro Cholula',21,146,0),(1710,'San Pedro Yeloixtlahuaca',21,146,0),(1711,'San Salvador el Seco',21,146,0),(1712,'San Salvador el Verde',21,146,0),(1713,'San Salvador Huixcolotla',21,146,0),(1714,'San Sebastián Tlacotepec',21,146,0),(1715,'Santa Catarina Tlaltempan',21,146,0),(1716,'Santa Inés Ahuatempan',21,146,0),(1717,'Santa Isabel Cholula',21,146,0),(1718,'Santiago Miahuatlán',21,146,0),(1719,'Huehuetlán el Grande',21,146,0),(1720,'Santo Tomás Hueyotlipan',21,146,0),(1721,'Soltepec',21,146,0),(1722,'Tecali de Herrera',21,146,0),(1723,'Tecamachalco',21,146,0),(1724,'Tecomatlán',21,146,0),(1725,'Tehuacán',21,146,0),(1726,'Tehuitzingo',21,146,0),(1727,'Tenampulco',21,146,0),(1728,'Teopantlán',21,146,0),(1729,'Teotlalco',21,146,0),(1730,'Tepanco de López',21,146,0),(1731,'Tepango de Rodríguez',21,146,0),(1732,'Tepatlaxco de Hidalgo',21,146,0),(1733,'Tepeaca',21,146,0),(1734,'Tepemaxalco',21,146,0),(1735,'Tepeojuma',21,146,0),(1736,'Tepetzintla',21,146,0),(1737,'Tepexco',21,146,0),(1738,'Tepexi de Rodríguez',21,146,0),(1739,'Tepeyahualco',21,146,0),(1740,'Tepeyahualco de Cuauhtémoc',21,146,0),(1741,'Tetela de Ocampo',21,146,0),(1742,'Teteles de Avila Castillo',21,146,0),(1743,'Teziutlán',21,146,0),(1744,'Tianguismanalco',21,146,0),(1745,'Tilapa',21,146,0),(1746,'Tlacotepec de Benito Juárez',21,146,0),(1747,'Tlacuilotepec',21,146,0),(1748,'Tlachichuca',21,146,0),(1749,'Tlahuapan',21,146,0),(1750,'Tlaltenango',21,146,0),(1751,'Tlanepantla',21,146,0),(1752,'Tlaola',21,146,0),(1753,'Tlapacoya',21,146,0),(1754,'Tlapanalá',21,146,0),(1755,'Tlatlauquitepec',21,146,0),(1756,'Tlaxco',21,146,0),(1757,'Tochimilco',21,146,0),(1758,'Tochtepec',21,146,0),(1759,'Totoltepec de Guerrero',21,146,0),(1760,'Tulcingo',21,146,0),(1761,'Tuzamapan de Galeana',21,146,0),(1762,'Tzicatlacoyan',21,146,0),(1763,'Venustiano Carranza',21,146,0),(1764,'Vicente Guerrero',21,146,0),(1765,'Xayacatlán de Bravo',21,146,0),(1766,'Xicotepec',21,146,0),(1767,'Xicotlán',21,146,0),(1768,'Xiutetelco',21,146,0),(1769,'Xochiapulco',21,146,0),(1770,'Xochiltepec',21,146,0),(1771,'Xochitlán de Vicente Suárez',21,146,0),(1772,'Xochitlán Todos Santos',21,146,0),(1773,'Yaonáhuac',21,146,0),(1774,'Yehualtepec',21,146,0),(1775,'Zacapala',21,146,0),(1776,'Zacapoaxtla',21,146,0),(1777,'Zacatlán',21,146,0),(1778,'Zapotitlán',21,146,0),(1779,'Zapotitlán de Méndez',21,146,0),(1780,'Zaragoza',21,146,0),(1781,'Zautla',21,146,0),(1782,'Zihuateutla',21,146,0),(1783,'Zinacatepec',21,146,0),(1784,'Zongozotla',21,146,0),(1785,'Zoquiapan',21,146,0),(1786,'Zoquitlán',21,146,0),(1787,'Amealco de Bonfil',22,146,0),(1788,'Pinal de Amoles',22,146,0),(1789,'Arroyo Seco',22,146,0),(1790,'Cadereyta de Montes',22,146,0),(1791,'Colón',22,146,0),(1792,'Corregidora',22,146,0),(1793,'Ezequiel Montes',22,146,0),(1794,'Huimilpan',22,146,0),(1795,'Jalpan de Serra',22,146,0),(1796,'Landa de Matamoros',22,146,0),(1797,'El Marqués',22,146,0),(1798,'Pedro Escobedo',22,146,0),(1799,'Peñamiller',22,146,0),(1800,'Querétaro',22,146,0),(1801,'San Joaquín',22,146,0),(1802,'San Juan del Río',22,146,0),(1803,'Tequisquiapan',22,146,0),(1804,'Tolimán',22,146,0),(1805,'Cozumel',23,146,0),(1806,'Felipe Carrillo Puerto',23,146,0),(1807,'Isla Mujeres',23,146,0),(1808,'Othón P. Blanco',23,146,0),(1809,'Benito Juárez',23,146,0),(1810,'José María Morelos',23,146,0),(1811,'Lázaro Cárdenas',23,146,0),(1812,'Solidaridad',23,146,0),(1813,'Tulum',23,146,0),(1814,'Ahualulco',24,146,0),(1815,'Alaquines',24,146,0),(1816,'Aquismón',24,146,0),(1817,'Armadillo de los Infante',24,146,0),(1818,'Cárdenas',24,146,0),(1819,'Catorce',24,146,0),(1820,'Cedral',24,146,0),(1821,'Cerritos',24,146,0),(1822,'Cerro de San Pedro',24,146,0),(1823,'Ciudad del Maíz',24,146,0),(1824,'Ciudad Fernández',24,146,0),(1825,'Tancanhuitz',24,146,0),(1826,'Ciudad Valles',24,146,0),(1827,'Coxcatlán',24,146,0),(1828,'Charcas',24,146,0),(1829,'Ebano',24,146,0),(1830,'Guadalcázar',24,146,0),(1831,'Huehuetlán',24,146,0),(1832,'Lagunillas',24,146,0),(1833,'Matehuala',24,146,0),(1834,'Mexquitic de Carmona',24,146,0),(1835,'Moctezuma',24,146,0),(1836,'Rayón',24,146,0),(1837,'Rioverde',24,146,0),(1838,'Salinas',24,146,0),(1839,'San Antonio',24,146,0),(1840,'San Ciro de Acosta',24,146,0),(1841,'San Luis Potosí',24,146,0),(1842,'San Martín Chalchicuautla',24,146,0),(1843,'San Nicolás Tolentino',24,146,0),(1844,'Santa Catarina',24,146,0),(1845,'Santa María del Río',24,146,0),(1846,'Santo Domingo',24,146,0),(1847,'San Vicente Tancuayalab',24,146,0),(1848,'Soledad de Graciano Sánchez',24,146,0),(1849,'Tamasopo',24,146,0),(1850,'Tamazunchale',24,146,0),(1851,'Tampacán',24,146,0),(1852,'Tampamolón Corona',24,146,0),(1853,'Tamuín',24,146,0),(1854,'Tanlajás',24,146,0),(1855,'Tanquián de Escobedo',24,146,0),(1856,'Tierra Nueva',24,146,0),(1857,'Vanegas',24,146,0),(1858,'Venado',24,146,0),(1859,'Villa de Arriaga',24,146,0),(1860,'Villa de Guadalupe',24,146,0),(1861,'Villa de la Paz',24,146,0),(1862,'Villa de Ramos',24,146,0),(1863,'Villa de Reyes',24,146,0),(1864,'Villa Hidalgo',24,146,0),(1865,'Villa Juárez',24,146,0),(1866,'Axtla de Terrazas',24,146,0),(1867,'Xilitla',24,146,0),(1868,'Zaragoza',24,146,0),(1869,'Villa de Arista',24,146,0),(1870,'Matlapa',24,146,0),(1871,'El Naranjo',24,146,0),(1872,'Ahome',25,146,0),(1873,'Angostura',25,146,0),(1874,'Badiraguato',25,146,0),(1875,'Concordia',25,146,0),(1876,'Cosalá',25,146,0),(1877,'Culiacán',25,146,0),(1878,'Choix',25,146,0),(1879,'Elota',25,146,0),(1880,'Escuinapa',25,146,0),(1881,'El Fuerte',25,146,0),(1882,'Guasave',25,146,0),(1883,'Mazatlán',25,146,0),(1884,'Mocorito',25,146,0),(1885,'Rosario',25,146,0),(1886,'Salvador Alvarado',25,146,0),(1887,'San Ignacio',25,146,0),(1888,'Sinaloa',25,146,0),(1889,'Navolato',25,146,0),(1890,'Aconchi',26,146,0),(1891,'Agua Prieta',26,146,0),(1892,'Alamos',26,146,0),(1893,'Altar',26,146,0),(1894,'Arivechi',26,146,0),(1895,'Arizpe',26,146,0),(1896,'Atil',26,146,0),(1897,'Bacadéhuachi',26,146,0),(1898,'Bacanora',26,146,0),(1899,'Bacerac',26,146,0),(1900,'Bacoachi',26,146,0),(1901,'Bácum',26,146,0),(1902,'Banámichi',26,146,0),(1903,'Baviácora',26,146,0),(1904,'Bavispe',26,146,0),(1905,'Benjamín Hill',26,146,0),(1906,'Caborca',26,146,0),(1907,'Cajeme',26,146,0),(1908,'Cananea',26,146,0),(1909,'Carbó',26,146,0),(1910,'La Colorada',26,146,0),(1911,'Cucurpe',26,146,0),(1912,'Cumpas',26,146,0),(1913,'Divisaderos',26,146,0),(1914,'Empalme',26,146,0),(1915,'Etchojoa',26,146,0),(1916,'Fronteras',26,146,0),(1917,'Granados',26,146,0),(1918,'Guaymas',26,146,0),(1919,'Hermosillo',26,146,0),(1920,'Huachinera',26,146,0),(1921,'Huásabas',26,146,0),(1922,'Huatabampo',26,146,0),(1923,'Huépac',26,146,0),(1924,'Imuris',26,146,0),(1925,'Magdalena',26,146,0),(1926,'Mazatán',26,146,0),(1927,'Moctezuma',26,146,0),(1928,'Naco',26,146,0),(1929,'Nácori Chico',26,146,0),(1930,'Nacozari de García',26,146,0),(1931,'Navojoa',26,146,0),(1932,'Nogales',26,146,0),(1933,'Onavas',26,146,0),(1934,'Opodepe',26,146,0),(1935,'Oquitoa',26,146,0),(1936,'Pitiquito',26,146,0),(1937,'Puerto Peñasco',26,146,0),(1938,'Quiriego',26,146,0),(1939,'Rayón',26,146,0),(1940,'Rosario',26,146,0),(1941,'Sahuaripa',26,146,0),(1942,'San Felipe de Jesús',26,146,0),(1943,'San Javier',26,146,0),(1944,'San Luis Río Colorado',26,146,0),(1945,'San Miguel de Horcasitas',26,146,0),(1946,'San Pedro de la Cueva',26,146,0),(1947,'Santa Ana',26,146,0),(1948,'Santa Cruz',26,146,0),(1949,'Sáric',26,146,0),(1950,'Soyopa',26,146,0),(1951,'Suaqui Grande',26,146,0),(1952,'Tepache',26,146,0),(1953,'Trincheras',26,146,0),(1954,'Tubutama',26,146,0),(1955,'Ures',26,146,0),(1956,'Villa Hidalgo',26,146,0),(1957,'Villa Pesqueira',26,146,0),(1958,'Yécora',26,146,0),(1959,'General Plutarco Elías Calles',26,146,0),(1960,'Benito Juárez',26,146,0),(1961,'San Ignacio Río Muerto',26,146,0),(1962,'Balancán',27,146,0),(1963,'Cárdenas',27,146,0),(1964,'Centla',27,146,0),(1965,'Centro',27,146,0),(1966,'Comalcalco',27,146,0),(1967,'Cunduacán',27,146,0),(1968,'Emiliano Zapata',27,146,0),(1969,'Huimanguillo',27,146,0),(1970,'Jalapa',27,146,0),(1971,'Jalpa de Méndez',27,146,0),(1972,'Jonuta',27,146,0),(1973,'Macuspana',27,146,0),(1974,'Nacajuca',27,146,0),(1975,'Paraíso',27,146,0),(1976,'Tacotalpa',27,146,0),(1977,'Teapa',27,146,0),(1978,'Tenosique',27,146,0),(1979,'Abasolo',28,146,0),(1980,'Aldama',28,146,0),(1981,'Altamira',28,146,0),(1982,'Antiguo Morelos',28,146,0),(1983,'Burgos',28,146,0),(1984,'Bustamante',28,146,0),(1985,'Camargo',28,146,0),(1986,'Casas',28,146,0),(1987,'Ciudad Madero',28,146,0),(1988,'Cruillas',28,146,0),(1989,'Gómez Farías',28,146,0),(1990,'González',28,146,0),(1991,'Güémez',28,146,0),(1992,'Guerrero',28,146,0),(1993,'Gustavo Díaz Ordaz',28,146,0),(1994,'Hidalgo',28,146,0),(1995,'Jaumave',28,146,0),(1996,'Jiménez',28,146,0),(1997,'Llera',28,146,0),(1998,'Mainero',28,146,0),(1999,'El Mante',28,146,0),(2000,'Matamoros',28,146,0),(2001,'Méndez',28,146,0),(2002,'Mier',28,146,0),(2003,'Miguel Alemán',28,146,0),(2004,'Miquihuana',28,146,0),(2005,'Nuevo Laredo',28,146,0),(2006,'Nuevo Morelos',28,146,0),(2007,'Ocampo',28,146,0),(2008,'Padilla',28,146,0),(2009,'Palmillas',28,146,0),(2010,'Reynosa',28,146,0),(2011,'Río Bravo',28,146,0),(2012,'San Carlos',28,146,0),(2013,'San Fernando',28,146,0),(2014,'San Nicolás',28,146,0),(2015,'Soto la Marina',28,146,0),(2016,'Tampico',28,146,0),(2017,'Tula',28,146,0),(2018,'Valle Hermoso',28,146,0),(2019,'Victoria',28,146,0),(2020,'Villagrán',28,146,0),(2021,'Xicoténcatl',28,146,0),(2022,'Amaxac de Guerrero',29,146,0),(2023,'Apetatitlán de Antonio Carvajal',29,146,0),(2024,'Atlangatepec',29,146,0),(2025,'Atltzayanca',29,146,0),(2026,'Apizaco',29,146,0),(2027,'Calpulalpan',29,146,0),(2028,'El Carmen Tequexquitla',29,146,0),(2029,'Cuapiaxtla',29,146,0),(2030,'Cuaxomulco',29,146,0),(2031,'Chiautempan',29,146,0),(2032,'Muñoz de Domingo Arenas',29,146,0),(2033,'Españita',29,146,0),(2034,'Huamantla',29,146,0),(2035,'Hueyotlipan',29,146,0),(2036,'Ixtacuixtla de Mariano Matamoros',29,146,0),(2037,'Ixtenco',29,146,0),(2038,'Mazatecochco de José María Morelos',29,146,0),(2039,'Contla de Juan Cuamatzi',29,146,0),(2040,'Tepetitla de Lardizábal',29,146,0),(2041,'Sanctórum de Lázaro Cárdenas',29,146,0),(2042,'Nanacamilpa de Mariano Arista',29,146,0),(2043,'Acuamanala de Miguel Hidalgo',29,146,0),(2044,'Natívitas',29,146,0),(2045,'Panotla',29,146,0),(2046,'San Pablo del Monte',29,146,0),(2047,'Santa Cruz Tlaxcala',29,146,0),(2048,'Tenancingo',29,146,0),(2049,'Teolocholco',29,146,0),(2050,'Tepeyanco',29,146,0),(2051,'Terrenate',29,146,0),(2052,'Tetla de la Solidaridad',29,146,0),(2053,'Tetlatlahuca',29,146,0),(2054,'Tlaxcala',29,146,0),(2055,'Tlaxco',29,146,0),(2056,'Tocatlán',29,146,0),(2057,'Totolac',29,146,0),(2058,'Ziltlaltépec de Trinidad Sánchez Santos',29,146,0),(2059,'Tzompantepec',29,146,0),(2060,'Xaloztoc',29,146,0),(2061,'Xaltocan',29,146,0),(2062,'Papalotla de Xicohténcatl',29,146,0),(2063,'Xicohtzinco',29,146,0),(2064,'Yauhquemehcan',29,146,0),(2065,'Zacatelco',29,146,0),(2066,'Benito Juárez',29,146,0),(2067,'Emiliano Zapata',29,146,0),(2068,'Lázaro Cárdenas',29,146,0),(2069,'La Magdalena Tlaltelulco',29,146,0),(2070,'San Damián Texóloc',29,146,0),(2071,'San Francisco Tetlanohcan',29,146,0),(2072,'San Jerónimo Zacualpan',29,146,0),(2073,'San José Teacalco',29,146,0),(2074,'San Juan Huactzinco',29,146,0),(2075,'San Lorenzo Axocomanitla',29,146,0),(2076,'San Lucas Tecopilco',29,146,0),(2077,'Santa Ana Nopalucan',29,146,0),(2078,'Santa Apolonia Teacalco',29,146,0),(2079,'Santa Catarina Ayometla',29,146,0),(2080,'Santa Cruz Quilehtla',29,146,0),(2081,'Santa Isabel Xiloxoxtla',29,146,0),(2082,'Acajete',30,146,0),(2083,'Acatlán',30,146,0),(2084,'Acayucan',30,146,0),(2085,'Actopan',30,146,0),(2086,'Acula',30,146,0),(2087,'Acultzingo',30,146,0),(2088,'Camarón de Tejeda',30,146,0),(2089,'Alpatláhuac',30,146,0),(2090,'Alto Lucero de Gutiérrez Barrios',30,146,0),(2091,'Altotonga',30,146,0),(2092,'Alvarado',30,146,0),(2093,'Amatitlán',30,146,0),(2094,'Naranjos Amatlán',30,146,0),(2095,'Amatlán de los Reyes',30,146,0),(2096,'Angel R. Cabada',30,146,0),(2097,'La Antigua',30,146,0),(2098,'Apazapan',30,146,0),(2099,'Aquila',30,146,0),(2100,'Astacinga',30,146,0),(2101,'Atlahuilco',30,146,0),(2102,'Atoyac',30,146,0),(2103,'Atzacan',30,146,0),(2104,'Atzalan',30,146,0),(2105,'Tlaltetela',30,146,0),(2106,'Ayahualulco',30,146,0),(2107,'Banderilla',30,146,0),(2108,'Benito Juárez',30,146,0),(2109,'Boca del Río',30,146,0),(2110,'Calcahualco',30,146,0),(2111,'Camerino Z. Mendoza',30,146,0),(2112,'Carrillo Puerto',30,146,0),(2113,'Catemaco',30,146,0),(2114,'Cazones de Herrera',30,146,0),(2115,'Cerro Azul',30,146,0),(2116,'Citlaltépetl',30,146,0),(2117,'Coacoatzintla',30,146,0),(2118,'Coahuitlán',30,146,0),(2119,'Coatepec',30,146,0),(2120,'Coatzacoalcos',30,146,0),(2121,'Coatzintla',30,146,0),(2122,'Coetzala',30,146,0),(2123,'Colipa',30,146,0),(2124,'Comapa',30,146,0),(2125,'Córdoba',30,146,0),(2126,'Cosamaloapan de Carpio',30,146,0),(2127,'Cosautlán de Carvajal',30,146,0),(2128,'Coscomatepec',30,146,0),(2129,'Cosoleacaque',30,146,0),(2130,'Cotaxtla',30,146,0),(2131,'Coxquihui',30,146,0),(2132,'Coyutla',30,146,0),(2133,'Cuichapa',30,146,0),(2134,'Cuitláhuac',30,146,0),(2135,'Chacaltianguis',30,146,0),(2136,'Chalma',30,146,0),(4047,'SAN JOSE DEL CABO',3,146,0),(2274,'Veracruz',30,146,0);

/*Table structure for table `cat_clientes` */

CREATE TABLE `cat_clientes` (
  `id_cliente` bigint(20) NOT NULL AUTO_INCREMENT,
  `rfc_cliente` varchar(15) DEFAULT NULL,
  `nombre_comercial` varchar(255) DEFAULT NULL,
  `nombre_fiscal` varchar(255) DEFAULT NULL,
  `tipo_cliente` char(1) DEFAULT NULL,
  `calle` varchar(255) DEFAULT NULL,
  `numext` varchar(50) DEFAULT NULL,
  `numint` varchar(50) DEFAULT NULL,
  `colonia` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `localidad` varchar(200) DEFAULT NULL,
  `id_ciu` int(11) DEFAULT NULL,
  `id_est` int(11) DEFAULT NULL,
  `id_pai` int(11) DEFAULT NULL,
  `nombre_contacto` varchar(200) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `telefono_contacto` varchar(20) DEFAULT NULL,
  `estilista` tinyint(4) DEFAULT '0',
  `celular_contacto` varchar(20) DEFAULT NULL,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `foraneo` tinyint(4) DEFAULT '0',
  `id_listaprecio` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `cat_clientes` */

insert  into `cat_clientes`(`id_cliente`,`rfc_cliente`,`nombre_comercial`,`nombre_fiscal`,`tipo_cliente`,`calle`,`numext`,`numint`,`colonia`,`cp`,`localidad`,`id_ciu`,`id_est`,`id_pai`,`nombre_contacto`,`email_contacto`,`telefono_contacto`,`estilista`,`celular_contacto`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`,`foraneo`,`id_listaprecio`) values (1,'HUCR860220HL1','JOSE RAMON HUERTA CORONADO','JOSE RAMON HUERTA CORONADO','F','JUAN BALDERAS','4002','A','BENITO JUAREZ','82180','MAZATLAN',1883,25,146,'','','',0,'','A',0,'2017-12-05 01:52:40',0,'2022-01-26 21:58:23',0,1),(2,'HUEL171127HL2','LILIA NATALIA HUERTA ESTRADA','LILIA NATALIA HUERTA ESTRADA','F','JUAN BALDERAS','4002','A','BENITO JUAREZ','82180','MAZATLAN',1883,25,146,'','','',1,'','A',0,'2017-12-05 01:52:40',NULL,NULL,0,NULL),(3,'HUCR860220HH1','CLIENTE DE PRUEBA','CLIENTE DE PRUEBA','F','JUAN ESCUTIA','1001','INT B','BENITO JUAREZ','82180','MAZATLAN',1883,25,146,'CLIENTE DE PRUEBA','correo@hotmail.com','9801010',0,'6699252600','A',2,'2019-07-04 17:56:12',0,'2022-01-26 21:57:50',0,2),(4,'HUCR860220H11','RAMN','RAMON HUERTA','F','','','','','82180','',1883,25,146,'','','',0,'','A',0,'2022-01-25 23:32:38',0,'2022-01-25 23:55:52',0,NULL);

/*Table structure for table `cat_conceptos` */

CREATE TABLE `cat_conceptos` (
  `id_concepto` bigint(20) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` tinyint(1) DEFAULT NULL COMMENT '1=Ingresos,2=Gastos',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_concepto`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `cat_conceptos` */

insert  into `cat_conceptos`(`id_concepto`,`descripcion`,`tipo`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'CORTES MES DE JUNIO',1,'A',2,'2019-07-21 14:58:05',NULL,NULL),(2,'RENTA',2,'A',2,'2019-07-21 14:58:20',NULL,NULL),(3,'VENTAS DIARIAS',1,'A',2,'2019-07-21 14:59:20',NULL,NULL);

/*Table structure for table `cat_denominaciones` */

CREATE TABLE `cat_denominaciones` (
  `id_denominacion` bigint(20) NOT NULL AUTO_INCREMENT,
  `denominacion` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`id_denominacion`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Data for the table `cat_denominaciones` */

insert  into `cat_denominaciones`(`id_denominacion`,`denominacion`) values (1,0.10),(2,0.20),(3,0.50),(4,1.00),(5,2.00),(6,5.00),(7,10.00),(8,20.00),(9,50.00),(10,100.00),(11,200.00),(12,500.00),(13,100.00);

/*Table structure for table `cat_empleados` */

CREATE TABLE `cat_empleados` (
  `id_empleado` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo_empleado` varchar(20) NOT NULL,
  `nombre_empleado` varchar(200) NOT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_empleado`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `cat_empleados` */

insert  into `cat_empleados`(`id_empleado`,`codigo_empleado`,`nombre_empleado`,`celular`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'12345','EMPLEADO 1','1','A',2,'2019-07-14 00:23:59',NULL,NULL),(2,'11111','EMPLEADO 2','2','A',2,'2019-07-14 00:24:12',NULL,NULL),(3,'22222','EMPLEADO 3','3','A',2,'2019-07-14 00:24:23',NULL,NULL),(4,'111496','ESTEFANIA LINARES','6691412014','A',2,'2019-07-16 18:12:27',NULL,NULL),(5,'123456','EMPLEADO 3','66633','A',2,'2019-07-21 14:55:55',NULL,NULL);

/*Table structure for table `cat_empresas` */

CREATE TABLE `cat_empresas` (
  `id_empresa` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apepaterno` varchar(50) DEFAULT NULL,
  `apematerno` varchar(50) DEFAULT NULL,
  `nombre_comercial` varchar(250) DEFAULT NULL,
  `nombre_fiscal` varchar(250) DEFAULT NULL,
  `tipo_empresa` enum('F','M') NOT NULL DEFAULT 'M',
  `rfc` varchar(15) DEFAULT NULL,
  `maneja_inventario` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=No, 1=Si',
  `logotipo` varchar(255) DEFAULT NULL,
  `calle` varchar(200) DEFAULT NULL,
  `numext` varchar(50) DEFAULT NULL,
  `numint` varchar(50) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `colonia` varchar(200) DEFAULT NULL,
  `localidad` varchar(255) DEFAULT NULL,
  `id_ciu` bigint(20) DEFAULT NULL,
  `id_est` bigint(20) DEFAULT NULL,
  `id_pai` bigint(20) DEFAULT NULL,
  `regimen_fiscal` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `status` varchar(1) DEFAULT 'A' COMMENT 'A=Activo,I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `logotipo_sucursal` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_empresa`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `cat_empresas` */

insert  into `cat_empresas`(`id_empresa`,`nombre`,`apepaterno`,`apematerno`,`nombre_comercial`,`nombre_fiscal`,`tipo_empresa`,`rfc`,`maneja_inventario`,`logotipo`,`calle`,`numext`,`numint`,`cp`,`colonia`,`localidad`,`id_ciu`,`id_est`,`id_pai`,`regimen_fiscal`,`telefono`,`fechacreador`,`usermodif`,`fechamodif`,`email`,`status`,`usercreador`,`logotipo_sucursal`) values (1,NULL,NULL,NULL,'EMPRESA DE PRUEBA','EMPRESA DE PRUEBA SA DE CV','M','XAXX010101000',1,'logo.jpg',NULL,NULL,NULL,NULL,NULL,NULL,1883,25,146,'PERSONAS MORALES',NULL,NULL,NULL,NULL,NULL,'A',NULL,0),(2,NULL,NULL,NULL,'EMPRESAS RAMON','EMPRESAS RAMON SA DE CV','M','HUCR860220000',1,'logo.jpg',NULL,NULL,NULL,NULL,NULL,NULL,1883,25,146,'PERSONAS MORALES',NULL,NULL,NULL,NULL,NULL,'A',NULL,0);

/*Table structure for table `cat_estados` */

CREATE TABLE `cat_estados` (
  `id_est` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID del estado',
  `nom_est` varchar(60) DEFAULT NULL COMMENT 'Nombre del estado',
  `key_pai_est` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'País',
  `uso_est` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Indice de uso del estado',
  PRIMARY KEY (`id_est`,`key_pai_est`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

/*Data for the table `cat_estados` */

insert  into `cat_estados`(`id_est`,`nom_est`,`key_pai_est`,`uso_est`) values (31,'Yucatán',146,0),(30,'Veracruz',146,0),(29,'Tlaxcala',146,0),(28,'Tamaulipas',146,0),(27,'Tabasco',146,0),(26,'Sonora',146,0),(25,'Sinaloa',146,1),(24,'San Luis Potosí',146,0),(23,'Quintana Roo',146,0),(22,'Queretaro',146,0),(21,'Puebla',146,0),(20,'Oaxaca',146,0),(19,'Nuevo León',146,0),(18,'Nayarit',146,0),(17,'Morelos',146,0),(16,'Michoacán',146,0),(15,'Estado de México',146,0),(14,'Jalisco',146,0),(13,'Hidalgo',146,0),(12,'Guerrero',146,0),(11,'Guanajuato',146,0),(10,'Durango',146,0),(9,'Distrito Federal',146,0),(8,'Chihuahua',146,0),(7,'Chiapas',146,0),(6,'Colima',146,0),(5,'Coahuila',146,0),(4,'Campeche',146,0),(3,'Baja California sur',146,0),(2,'Baja California',146,0),(1,'Aguascalientes',146,0),(32,'Zacatecas',146,0),(34,'Texas',75,0);

/*Table structure for table `cat_formaspagos` */

CREATE TABLE `cat_formaspagos` (
  `id_formapago` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_formapago` varchar(100) DEFAULT NULL,
  `tipo_formapago` tinyint(4) DEFAULT NULL COMMENT '1=Efectivo,2=Tarjeta Debito,3=Tarjeta Credito,4=Transferencia,5=Cheque',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_formapago`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `cat_formaspagos` */

insert  into `cat_formaspagos`(`id_formapago`,`nombre_formapago`,`tipo_formapago`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'EFECTIVO',1,'A',NULL,NULL,NULL,NULL);

/*Table structure for table `cat_horarios` */

CREATE TABLE `cat_horarios` (
  `id_horario` bigint(20) NOT NULL AUTO_INCREMENT,
  `hora_inicio` datetime NOT NULL,
  `hora_fin` datetime NOT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_horario`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `cat_horarios` */

insert  into `cat_horarios`(`id_horario`,`hora_inicio`,`hora_fin`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'2019-07-14 08:00:00','2019-07-14 08:40:00','A',2,'2019-07-14 00:22:13',NULL,NULL),(2,'2019-07-14 08:40:00','2019-07-14 09:20:00','A',2,'2019-07-14 00:22:32',NULL,NULL),(3,'2019-07-14 09:20:00','2019-07-14 10:00:00','A',2,'2019-07-14 00:23:01',NULL,NULL),(4,'2019-07-14 10:00:00','2019-07-14 10:40:00','A',2,'2019-07-14 00:23:14',NULL,NULL),(5,'2019-07-14 10:40:00','2019-07-14 11:20:00','A',2,'2019-07-14 00:23:30',NULL,NULL);

/*Table structure for table `cat_impuestos` */

CREATE TABLE `cat_impuestos` (
  `id_impuesto` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_impuesto` varchar(200) DEFAULT NULL,
  `porcentaje` decimal(14,6) DEFAULT NULL,
  `tipo_impuesto` tinyint(4) DEFAULT NULL COMMENT '1=IVA,2=Ret IVA,3=Ret ISR',
  PRIMARY KEY (`id_impuesto`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `cat_impuestos` */

insert  into `cat_impuestos`(`id_impuesto`,`nombre_impuesto`,`porcentaje`,`tipo_impuesto`) values (1,'IVA',16.000000,1),(2,'RET IVA',10.666700,2),(3,'RET ISR',11.000000,3);

/*Table structure for table `cat_lineas` */

CREATE TABLE `cat_lineas` (
  `id_linea` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_linea` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_linea`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `cat_lineas` */

insert  into `cat_lineas`(`id_linea`,`nombre_linea`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'LINEA DE SERVICIOS','A',0,'2017-12-05 01:53:07',NULL,NULL),(2,'LINEA BARBERIA','A',2,'2019-07-04 17:59:06',NULL,NULL),(4,'LINEA DE PRUEBA','A',2,'2021-11-29 21:27:01',NULL,NULL),(5,'LINEA DE PRUEBA','A',2,'2021-11-29 21:27:12',NULL,NULL),(6,'LINEA DE PRUEBA','A',2,'2021-11-29 21:27:16',NULL,NULL);

/*Table structure for table `cat_lineas_sucursales` */

CREATE TABLE `cat_lineas_sucursales` (
  `id_linea_sucursal` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_sucursal` bigint(20) DEFAULT NULL,
  `id_linea` bigint(20) DEFAULT NULL,
  `id_sucursal_surtido` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_linea_sucursal`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `cat_lineas_sucursales` */

insert  into `cat_lineas_sucursales`(`id_linea_sucursal`,`id_sucursal`,`id_linea`,`id_sucursal_surtido`) values (1,1,2,2);

/*Table structure for table `cat_listaprecios` */

CREATE TABLE `cat_listaprecios` (
  `id_listaprecio` bigint(20) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) DEFAULT NULL,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activa, I=Inactiva',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_listaprecio`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `cat_listaprecios` */

insert  into `cat_listaprecios`(`id_listaprecio`,`descripcion`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'Lista de Prueba 1','A',NULL,NULL,NULL,NULL),(2,'Lista de Prueba 2','A',NULL,NULL,NULL,NULL),(3,'Lista de Prueba 3','I',NULL,NULL,NULL,NULL);

/*Table structure for table `cat_listaprecios_detalles` */

CREATE TABLE `cat_listaprecios_detalles` (
  `id_listaprecio_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_listaprecio` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `precio` decimal(14,6) DEFAULT NULL,
  `valor_puntos` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`id_listaprecio_detalle`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `cat_listaprecios_detalles` */

insert  into `cat_listaprecios_detalles`(`id_listaprecio_detalle`,`id_listaprecio`,`id_producto`,`precio`,`valor_puntos`) values (1,1,2,50.000000,500.00),(2,1,3,60.000000,600.00),(3,2,4,70.000000,700.00),(4,2,5,80.000000,800.00);

/*Table structure for table `cat_modulos` */

CREATE TABLE `cat_modulos` (
  `id_modulo` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT 'ID del Modulo',
  `descripcion` varchar(50) NOT NULL COMMENT 'Descripcion del Modulo',
  `id_padre` mediumint(9) DEFAULT '0',
  `orden` int(11) DEFAULT '0',
  `newWin` char(20) DEFAULT NULL,
  `newTab` char(50) DEFAULT NULL,
  `icono` char(50) DEFAULT NULL,
  `controller` char(100) DEFAULT NULL COMMENT 'Controlador',
  PRIMARY KEY (`id_modulo`)
) ENGINE=MyISAM AUTO_INCREMENT=23014 DEFAULT CHARSET=utf8 COMMENT='Catalogo de Modulos de Acceso';

/*Data for the table `cat_modulos` */

insert  into `cat_modulos`(`id_modulo`,`descripcion`,`id_padre`,`orden`,`newWin`,`newTab`,`icono`,`controller`) values (1000,'Ventas',0,1000,NULL,NULL,'facturacion',NULL),(2000,'Compras',0,2000,NULL,NULL,'facturacion',NULL),(3000,'Inventarios',0,3000,NULL,NULL,'box',NULL),(4000,'Catalogos',0,4000,NULL,NULL,'catalogos',NULL),(5000,'Reportes',0,5000,NULL,NULL,'library_folder',NULL),(6000,'Configuraciones',0,4000,NULL,NULL,'cog',NULL),(1100,'Punto de Venta',1000,1100,NULL,'formPuntoVenta','folder_billete','ventas'),(2100,'Ordenes de Compra',2000,2100,NULL,NULL,'facturacion',NULL),(3100,'Movimientos de Inventario',3000,3100,NULL,'gridMovimientosAlmacen','box',NULL),(4100,'Clientes',4000,4100,NULL,'gridClientes','clientes','clientes'),(4200,'Productos',4000,4200,NULL,'gridProductos','productos','productos'),(5100,'Ventas',5000,5100,NULL,'formReporteVentas','library_folder','ventas'),(6100,'Configuracion Empresa',6000,6100,NULL,NULL,'factory',NULL),(6110,'Configuracion de Facturacion',6100,6110,NULL,NULL,'folder_billete',NULL),(6111,'Certificados CSD',6110,6111,NULL,'gridCertificados','certificate','certificados'),(4400,'Series',4000,4400,NULL,'gridSeries','folios',NULL),(1200,'Remisiones',1000,1200,NULL,'gridRemisiones','facturas',NULL),(4300,'Agentes de Ventas',4000,4300,NULL,'gridAgentes','clientes',NULL),(4500,'Lineas',4000,4500,NULL,'gridLineas','servicios',NULL),(4600,'Unidades de Medida',4000,4600,NULL,'gridUnidadesMedidas','unidades_medida',NULL),(5200,'Existencia',5000,5200,NULL,'formReporteExistencia','box','productos'),(5300,'Ventas Productos',5000,5300,NULL,'formReporteVentasProductos','facturas','ventas'),(1300,'Facturacion',1000,1300,NULL,NULL,'facturas',NULL),(1400,'Turnos',1000,1400,NULL,'gridTurnos','ordven','turnos'),(1500,'Cortes',1000,1500,NULL,'gridCortes','cortes','cortes'),(1600,'Movimientos de Caja',1000,1600,NULL,'gridMovimientosCaja','facturas','movimientos_caja'),(5400,'Ventas Productos Excel',5000,5400,NULL,'formReporteVentasProductosExcel','facturas','ventas'),(3200,'Inventarios Fisicos',3000,3200,NULL,'gridInventarios','box','inventarios'),(1700,'Abonos',1000,1700,NULL,'gridAbonos','facturas','abonos'),(5500,'Cartera Clientes',5000,5500,NULL,'formReporteCarteraClientes','agent',NULL),(5600,'Abonos Clientes',5000,5600,NULL,'formReporteAbonosClientes','facturas','ventas'),(5700,'Ventas Clientes',5000,5700,NULL,'formReporteVentasClientes','agent','clientes'),(5800,'Pedido Sugerido',5000,5800,NULL,'formReportePedidoSugerido','box','ventas'),(7000,'Bancos',0,7000,NULL,NULL,'bank',NULL),(5900,'Movimientos Bancos',5000,5900,NULL,'formReporteMovimientosBancos','report','movimientos_banco'),(7100,'Movimientos Bancos',7000,7100,NULL,'gridMovimientosBancos','ordven','movimientos_banco'),(7200,'Gastos',7000,7200,NULL,'gridGastos','facturas','movimientos_banco'),(4700,'Conceptos',4000,4700,NULL,'gridConceptos','conceptos','conceptos'),(4800,'Chequeras',4000,4800,NULL,'gridChequeras','razon','chequeras'),(3300,'Maximos y Minimos',3000,3300,NULL,'formMaximosMinimos','estadisticas','productos'),(5910,'Ventas Productos Costos',5000,5910,NULL,'formReporteVentasProductosCostos','facturas','ventas'),(4900,'Empleados',4000,4900,NULL,'gridEmpleados','group','empleados'),(1800,'Citas',1000,1800,NULL,'gridCitas','calendar','citas'),(4910,'Horarios',4000,4910,NULL,'gridHorarios','calendar','horarios'),(8000,'Administracion',0,8000,NULL,NULL,'corporate',NULL),(8100,'Checador',8000,8100,NULL,'formChecador','clock','checadas'),(5911,'Reporte Checadas',5000,5911,NULL,'formReporteChecadas','calendar','checadas'),(23012,'Listas de Precios',4000,4920,NULL,'gridListaPrecios','conceptos','listaprecios');

/*Table structure for table `cat_paises` */

CREATE TABLE `cat_paises` (
  `id_pai` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Código',
  `id_iso_pai` smallint(6) NOT NULL,
  `id_iso2_pai` char(2) NOT NULL,
  `id_iso3_pai` char(3) NOT NULL,
  `nom_pai` varchar(80) NOT NULL COMMENT 'Nombre del país',
  `uso_pai` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Contador de uso',
  PRIMARY KEY (`id_pai`)
) ENGINE=MyISAM AUTO_INCREMENT=241 DEFAULT CHARSET=utf8;

/*Data for the table `cat_paises` */

insert  into `cat_paises`(`id_pai`,`id_iso_pai`,`id_iso2_pai`,`id_iso3_pai`,`nom_pai`,`uso_pai`) values (1,4,'AF','AFG','Afganistán',0),(2,248,'AX','ALA','Islas Aland',0),(3,8,'AL','ALB','Albania',0),(4,276,'DE','DEU','Alemania',0),(5,20,'AD','AND','Andorra',0),(6,24,'AO','AGO','Angola',0),(7,660,'AI','AIA','Anguilla',0),(8,10,'AQ','ATA','Antártida',0),(9,28,'AG','ATG','Antigua y Barbuda',0),(10,530,'AN','ANT','Antillas Holandesas',0),(11,682,'SA','SAU','Arabia Saudí',0),(12,12,'DZ','DZA','Argelia',0),(13,32,'AR','ARG','Argentina',0),(14,51,'AM','ARM','Armenia',0),(15,533,'AW','ABW','Aruba',0),(16,36,'AU','AUS','Australia',0),(17,40,'AT','AUT','Austria',0),(18,31,'AZ','AZE','Azerbaiyán',0),(19,44,'BS','BHS','Bahamas',0),(20,48,'BH','BHR','Bahréin',0),(21,50,'BD','BGD','Bangladesh',0),(22,52,'BB','BRB','Barbados',0),(23,112,'BY','BLR','Bielorrusia',0),(24,56,'BE','BEL','Bélgica',0),(25,84,'BZ','BLZ','Belice',0),(26,204,'BJ','BEN','Benin',0),(27,60,'BM','BMU','Bermudas',0),(28,64,'BT','BTN','Bhután',0),(29,68,'BO','BOL','Bolivia',0),(30,70,'BA','BIH','Bosnia y Herzegovina',0),(31,72,'BW','BWA','Botsuana',0),(32,74,'BV','BVT','Isla Bouvet',0),(33,76,'BR','BRA','Brasil',0),(34,96,'BN','BRN','Brunéi',0),(35,100,'BG','BGR','Bulgaria',0),(36,854,'BF','BFA','Burkina Faso',0),(37,108,'BI','BDI','Burundi',0),(38,132,'CV','CPV','Cabo Verde',0),(39,136,'KY','CYM','Islas Caimán',0),(40,116,'KH','KHM','Camboya',0),(41,120,'CM','CMR','Camerún',0),(42,124,'CA','CAN','Canadá',0),(43,140,'CF','CAF','República Centroafricana',0),(44,148,'TD','TCD','Chad',0),(45,203,'CZ','CZE','República Checa',0),(46,152,'CL','CHL','Chile',0),(47,156,'CN','CHN','China',0),(48,196,'CY','CYP','Chipre',0),(49,162,'CX','CXR','Isla de Navidad',0),(50,336,'VA','VAT','Ciudad del Vaticano',0),(51,166,'CC','CCK','Islas Cocos',0),(52,170,'CO','COL','Colombia',0),(53,174,'KM','COM','Comoras',0),(54,180,'CD','COD','República Democrática del Congo',0),(55,178,'CG','COG','Congo',0),(56,184,'CK','COK','Islas Cook',0),(57,408,'KP','PRK','Corea del Norte',0),(58,410,'KR','KOR','Corea del Sur',0),(59,384,'CI','CIV','Costa de Marfil',0),(60,188,'CR','CRI','Costa Rica',0),(61,191,'HR','HRV','Croacia',0),(62,192,'CU','CUB','Cuba',0),(63,208,'DK','DNK','Dinamarca',0),(64,212,'DM','DMA','Dominica',0),(65,214,'DO','DOM','República Dominicana',0),(66,218,'EC','ECU','Ecuador',0),(67,818,'EG','EGY','Egipto',0),(68,222,'SV','SLV','El Salvador',0),(69,784,'AE','ARE','Emiratos Árabes Unidos',0),(70,232,'ER','ERI','Eritrea',0),(71,703,'SK','SVK','Eslovaquia',0),(72,705,'SI','SVN','Eslovenia',0),(73,724,'ES','ESP','España',0),(74,581,'UM','UMI','Islas ultramarinas de Estados Unidos',0),(75,840,'US','USA','Estados Unidos',0),(76,233,'EE','EST','Estonia',0),(77,231,'ET','ETH','Etiopía',0),(78,234,'FO','FRO','Islas Feroe',0),(79,608,'PH','PHL','Filipinas',0),(80,246,'FI','FIN','Finlandia',0),(81,242,'FJ','FJI','Fiyi',0),(82,250,'FR','FRA','Francia',0),(83,266,'GA','GAB','Gabón',0),(84,270,'GM','GMB','Gambia',0),(85,268,'GE','GEO','Georgia',0),(86,239,'GS','SGS','Islas Georgias del Sur y Sandwich del Sur',0),(87,288,'GH','GHA','Ghana',0),(88,292,'GI','GIB','Gibraltar',0),(89,308,'GD','GRD','Granada',0),(90,300,'GR','GRC','Grecia',0),(91,304,'GL','GRL','Groenlandia',0),(92,312,'GP','GLP','Guadalupe',0),(93,316,'GU','GUM','Guam',0),(94,320,'GT','GTM','Guatemala',0),(95,254,'GF','GUF','Guayana Francesa',0),(96,324,'GN','GIN','Guinea',0),(97,226,'GQ','GNQ','Guinea Ecuatorial',0),(98,624,'GW','GNB','Guinea Bissau',0),(99,328,'GY','GUY','Guyana',0),(100,332,'HT','HTI','Haití',0),(101,334,'HM','HMD','Islas Heard y McDonald',0),(102,340,'HN','HND','Honduras',0),(103,344,'HK','HKG','Hong Kong',0),(104,348,'HU','HUN','Hungría',0),(105,356,'IN','IND','India',0),(106,360,'ID','IDN','Indonesia',0),(107,364,'IR','IRN','Irán',0),(108,368,'IQ','IRQ','Iraq',0),(109,372,'IE','IRL','Irlanda',0),(110,352,'IS','ISL','Islandia',0),(111,376,'IL','ISR','Israel',0),(112,380,'IT','ITA','Italia',0),(113,388,'JM','JAM','Jamaica',0),(114,392,'JP','JPN','Japón',0),(115,400,'JO','JOR','Jordania',0),(116,398,'KZ','KAZ','Kazajstán',0),(117,404,'KE','KEN','Kenia',0),(118,417,'KG','KGZ','Kirguistán',0),(119,296,'KI','KIR','Kiribati',0),(120,414,'KW','KWT','Kuwait',0),(121,418,'LA','LAO','Laos',0),(122,426,'LS','LSO','Lesotho',0),(123,428,'LV','LVA','Letonia',0),(124,422,'LB','LBN','Líbano',0),(125,430,'LR','LBR','Liberia',0),(126,434,'LY','LBY','Libia',0),(127,438,'LI','LIE','Liechtenstein',0),(128,440,'LT','LTU','Lituania',0),(129,442,'LU','LUX','Luxemburgo',0),(130,446,'MO','MAC','Macao',0),(131,807,'MK','MKD','ARY Macedonia',0),(132,450,'MG','MDG','Madagascar',0),(133,458,'MY','MYS','Malasia',0),(134,454,'MW','MWI','Malawi',0),(135,462,'MV','MDV','Maldivas',0),(136,466,'ML','MLI','Malí',0),(137,470,'MT','MLT','Malta',0),(138,238,'FK','FLK','Islas Malvinas',0),(139,580,'MP','MNP','Islas Marianas del Norte',0),(140,504,'MA','MAR','Marruecos',0),(141,584,'MH','MHL','Islas Marshall',0),(142,474,'MQ','MTQ','Martinica',0),(143,480,'MU','MUS','Mauricio',0),(144,478,'MR','MRT','Mauritania',0),(145,175,'YT','MYT','Mayotte',0),(146,484,'MX','MEX','Mexico',3),(147,583,'FM','FSM','Micronesia',0),(148,498,'MD','MDA','Moldavia',0),(149,492,'MC','MCO','Mónaco',0),(150,496,'MN','MNG','Mongolia',0),(151,500,'MS','MSR','Montserrat',0),(152,508,'MZ','MOZ','Mozambique',0),(153,104,'MM','MMR','Myanmar',0),(154,516,'NA','NAM','Namibia',0),(155,520,'NR','NRU','Nauru',0),(156,524,'NP','NPL','Nepal',0),(157,558,'NI','NIC','Nicaragua',0),(158,562,'NE','NER','Níger',0),(159,566,'NG','NGA','Nigeria',0),(160,570,'NU','NIU','Niue',0),(161,574,'NF','NFK','Isla Norfolk',0),(162,578,'NO','NOR','Noruega',0),(163,540,'NC','NCL','Nueva Caledonia',0),(164,554,'NZ','NZL','Nueva Zelanda',0),(165,512,'OM','OMN','Omán',0),(166,528,'NL','NLD','Países Bajos',0),(167,586,'PK','PAK','Pakistán',0),(168,585,'PW','PLW','Palau',0),(169,275,'PS','PSE','Palestina',0),(170,591,'PA','PAN','Panamá',0),(171,598,'PG','PNG','Papúa Nueva Guinea',0),(172,600,'PY','PRY','Paraguay',0),(173,604,'PE','PER','Perú',0),(174,612,'PN','PCN','Islas Pitcairn',0),(175,258,'PF','PYF','Polinesia Francesa',0),(176,616,'PL','POL','Polonia',0),(177,620,'PT','PRT','Portugal',0),(178,630,'PR','PRI','Puerto Rico',0),(179,634,'QA','QAT','Qatar',0),(180,826,'GB','GBR','Reino Unido',0),(181,638,'RE','REU','Reunión',0),(182,646,'RW','RWA','Ruanda',0),(183,642,'RO','ROU','Rumania',0),(184,643,'RU','RUS','Rusia',0),(185,732,'EH','ESH','Sahara Occidental',0),(186,90,'SB','SLB','Islas Salomón',0),(187,882,'WS','WSM','Samoa',0),(188,16,'AS','ASM','Samoa Americana',0),(189,659,'KN','KNA','San Cristóbal y Nevis',0),(190,674,'SM','SMR','San Marino',0),(191,666,'PM','SPM','San Pedro y Miquelón',0),(192,670,'VC','VCT','San Vicente y las Granadinas',0),(193,654,'SH','SHN','Santa Helena',0),(194,662,'LC','LCA','Santa Lucía',0),(195,678,'ST','STP','Santo Tomé y Príncipe',0),(196,686,'SN','SEN','Senegal',0),(197,891,'CS','SCG','Serbia y Montenegro',0),(198,690,'SC','SYC','Seychelles',0),(199,694,'SL','SLE','Sierra Leona',0),(200,702,'SG','SGP','Singapur',0),(201,760,'SY','SYR','Siria',0),(202,706,'SO','SOM','Somalia',0),(203,144,'LK','LKA','Sri Lanka',0),(204,748,'SZ','SWZ','Suazilandia',0),(205,710,'ZA','ZAF','Sudáfrica',0),(206,736,'SD','SDN','Sudán',0),(207,752,'SE','SWE','Suecia',0),(208,756,'CH','CHE','Suiza',0),(209,740,'SR','SUR','Surinam',0),(210,744,'SJ','SJM','Svalbard y Jan Mayen',0),(211,764,'TH','THA','Tailandia',0),(212,158,'TW','TWN','Taiwán',0),(213,834,'TZ','TZA','Tanzania',0),(214,762,'TJ','TJK','Tayikistán',0),(215,86,'IO','IOT','Territorio Británico del Océano Índico',0),(216,260,'TF','ATF','Territorios Australes Franceses',0),(217,626,'TL','TLS','Timor Oriental',0),(218,768,'TG','TGO','Togo',0),(219,772,'TK','TKL','Tokelau',0),(220,776,'TO','TON','Tonga',0),(221,780,'TT','TTO','Trinidad y Tobago',0),(222,788,'TN','TUN','Túnez',0),(223,796,'TC','TCA','Islas Turcas y Caicos',0),(224,795,'TM','TKM','Turkmenistán',0),(225,792,'TR','TUR','Turquía',0),(226,798,'TV','TUV','Tuvalu',0),(227,804,'UA','UKR','Ucrania',0),(228,800,'UG','UGA','Uganda',0),(229,858,'UY','URY','Uruguay',0),(230,860,'UZ','UZB','Uzbekistán',0),(231,548,'VU','VUT','Vanuatu',0),(232,862,'VE','VEN','Venezuela',0),(233,704,'VN','VNM','Vietnam',0),(234,92,'VG','VGB','Islas Vírgenes Británicas',0),(235,850,'VI','VIR','Islas Vírgenes de los Estados Unidos',0),(236,876,'WF','WLF','Wallis y Futuna',0),(237,887,'YE','YEM','Yemen',0),(238,262,'DJ','DJI','Yibuti',0),(239,894,'ZM','ZMB','Zambia',0),(240,716,'ZW','ZWE','Zimbabue',0);

/*Table structure for table `cat_parametros` */

CREATE TABLE `cat_parametros` (
  `id_parametro` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) DEFAULT NULL,
  `ciudad_default` int(11) DEFAULT NULL,
  `estado_default` int(11) DEFAULT NULL,
  `pais_default` int(11) DEFAULT NULL,
  `decimales_moneda` tinyint(1) DEFAULT '0',
  `decimales_cantidad` tinyint(1) DEFAULT '0',
  `registros_pagina` mediumint(6) DEFAULT '0',
  `tipo_texto` char(1) DEFAULT '1',
  `metodo_costeo` tinyint(4) DEFAULT NULL,
  `smtp_servidor` varchar(100) DEFAULT NULL,
  `smtp_puerto` varchar(100) DEFAULT NULL,
  `smtp_usuario` varchar(100) DEFAULT NULL,
  `smtp_pass` varchar(100) DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'I' COMMENT 'A=Activo,I=Inactivo',
  `id_serie_entrada` bigint(20) DEFAULT NULL COMMENT 'Serie para entradas de ajuste inventario',
  `id_serie_salida` bigint(20) DEFAULT NULL COMMENT 'Serie para salidas de ajuste de inventarios',
  PRIMARY KEY (`id_parametro`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `cat_parametros` */

insert  into `cat_parametros`(`id_parametro`,`descripcion`,`ciudad_default`,`estado_default`,`pais_default`,`decimales_moneda`,`decimales_cantidad`,`registros_pagina`,`tipo_texto`,`metodo_costeo`,`smtp_servidor`,`smtp_puerto`,`smtp_usuario`,`smtp_pass`,`status`,`id_serie_entrada`,`id_serie_salida`) values (1,NULL,1883,25,146,2,2,100,'1',NULL,NULL,NULL,NULL,NULL,'A',3,2);

/*Table structure for table `cat_parametros_empresas` */

CREATE TABLE `cat_parametros_empresas` (
  `id_parametro_empresa` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `porcentaje_credito` decimal(18,6) DEFAULT '0.000000',
  `porcentaje_foraneos` decimal(18,6) DEFAULT '0.000000',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_parametro_empresa`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `cat_parametros_empresas` */

insert  into `cat_parametros_empresas`(`id_parametro_empresa`,`id_empresa`,`porcentaje_credito`,`porcentaje_foraneos`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,0.000000,0.000000,'A',NULL,NULL,NULL,NULL);

/*Table structure for table `cat_parametros_ventas` */

CREATE TABLE `cat_parametros_ventas` (
  `id_parametro_venta` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_almacen` bigint(20) DEFAULT NULL,
  `id_serie` bigint(20) DEFAULT NULL,
  `id_cliente` bigint(20) DEFAULT NULL,
  `agrega_concepto_auto` tinyint(1) DEFAULT '0',
  `impresion_ticket` tinyint(1) DEFAULT '0',
  `id_serie_eaju` bigint(20) DEFAULT NULL,
  `id_serie_saju` bigint(20) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `mostrar_agente` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_parametro_venta`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `cat_parametros_ventas` */

insert  into `cat_parametros_ventas`(`id_parametro_venta`,`id_empresa`,`id_sucursal`,`id_almacen`,`id_serie`,`id_cliente`,`agrega_concepto_auto`,`impresion_ticket`,`id_serie_eaju`,`id_serie_saju`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`,`mostrar_agente`) values (1,1,1,1,1,1,1,0,2,3,'A',NULL,NULL,NULL,NULL,1);

/*Table structure for table `cat_productos` */

CREATE TABLE `cat_productos` (
  `id_producto` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) DEFAULT NULL,
  `codigo_barras` varchar(20) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `detalles` text,
  `id_linea` bigint(20) DEFAULT NULL,
  `id_unidadmedida` bigint(20) DEFAULT NULL,
  `id_proveedor` bigint(20) DEFAULT NULL,
  `precio_compra` decimal(14,6) DEFAULT '0.000000',
  `precio_venta` decimal(14,6) DEFAULT '0.000000',
  `precio_estilista` decimal(14,6) DEFAULT '0.000000',
  `metodo_costeo` tinyint(1) DEFAULT '0',
  `ultimo_costo` decimal(14,6) DEFAULT '0.000000',
  `costo_promedio` decimal(14,6) DEFAULT '0.000000',
  `tipo_producto` char(1) DEFAULT 'P' COMMENT 'P=Producto, S=Servicio',
  `stock_minimo` decimal(14,6) DEFAULT '0.000000',
  `stock_marximo` decimal(14,6) DEFAULT '0.000000',
  `stock` decimal(14,6) DEFAULT '0.000000',
  `iva` tinyint(1) DEFAULT '0',
  `ret_isr` tinyint(1) DEFAULT '0',
  `ret_iva` tinyint(1) DEFAULT '0',
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `valor_puntos` decimal(14,2) DEFAULT '0.00',
  PRIMARY KEY (`id_producto`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `cat_productos` */

insert  into `cat_productos`(`id_producto`,`codigo`,`codigo_barras`,`descripcion`,`detalles`,`id_linea`,`id_unidadmedida`,`id_proveedor`,`precio_compra`,`precio_venta`,`precio_estilista`,`metodo_costeo`,`ultimo_costo`,`costo_promedio`,`tipo_producto`,`stock_minimo`,`stock_marximo`,`stock`,`iva`,`ret_isr`,`ret_iva`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`,`valor_puntos`) values (1,'SER1','SER0001','SERVICIO','',1,1,NULL,50.000000,100.000000,100.000000,0,50.000000,50.000000,'S',0.000000,0.000000,0.000000,1,0,0,'A',0,'2017-12-05 01:53:42',NULL,NULL,0.00),(2,'PR1','PR00001','PRODUCTO DE PRUEBA2','',1,1,NULL,50.000000,100.000000,100.000000,0,50.000000,50.000000,'P',0.000000,0.000000,0.000000,1,0,0,'A',0,'2017-12-05 02:03:03',2,'2019-07-04 18:03:58',0.00),(3,'CC28','75007614','COCA COLA 600ml','CAJA CON 24 PIEZAS',1,1,NULL,11.380000,13.000000,0.000000,0,11.380000,11.380000,'P',0.000000,0.000000,0.000000,0,0,0,'A',2,'2018-05-25 13:25:13',NULL,NULL,0.00),(4,'PRO0001','75000001','PRODUCTO DE PRUEBA','',2,2,NULL,50.000000,100.000000,0.000000,0,50.000000,50.000000,'P',0.000000,0.000000,0.000000,0,0,0,'A',2,'2019-07-04 17:59:58',NULL,NULL,0.00),(5,'CC1','1000000001','CORTE CABALLERO','',1,1,NULL,0.000000,50.000000,0.000000,0,0.000000,0.000000,'S',0.000000,0.000000,0.000000,0,0,0,'A',2,'2019-07-04 18:02:06',2,'2019-07-21 14:12:47',0.00),(6,'53272742','13740274327425','1244242471741757','',2,2,NULL,140.000000,250.000000,200.000000,0,140.000000,140.000000,'P',0.000000,0.000000,0.000000,0,0,0,'A',2,'2019-07-04 18:20:14',NULL,NULL,0.00),(7,'RRFF','1212121321454','prueba 12','',1,1,NULL,1.000000,1.000000,1.000000,0,1.000000,1.000000,'P',0.000000,0.000000,0.000000,0,0,0,'A',0,'2020-06-24 10:34:33',NULL,NULL,0.00);

/*Table structure for table `cat_productos_stocks` */

CREATE TABLE `cat_productos_stocks` (
  `id_stock` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_almacen` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `stock_min` decimal(14,6) DEFAULT '0.000000',
  `stock_max` decimal(14,6) DEFAULT '0.000000',
  `stock` decimal(14,6) DEFAULT '0.000000',
  `stock_aviso` decimal(14,6) DEFAULT '0.000000',
  `AddUsuario` bigint(20) DEFAULT NULL,
  `AddFecha` datetime DEFAULT NULL,
  `ModUsuario` bigint(20) DEFAULT NULL,
  `ModFecha` datetime DEFAULT NULL,
  PRIMARY KEY (`id_stock`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `cat_productos_stocks` */

insert  into `cat_productos_stocks`(`id_stock`,`id_almacen`,`id_producto`,`stock_min`,`stock_max`,`stock`,`stock_aviso`,`AddUsuario`,`AddFecha`,`ModUsuario`,`ModFecha`) values (1,1,2,0.000000,0.000000,4.000000,0.000000,0,'2017-12-05 02:06:51',2,'2019-07-04 18:03:58'),(2,1,0,0.000000,0.000000,24.000000,0.000000,2,'2018-05-25 13:15:12',NULL,NULL),(3,1,3,0.000000,0.000000,48.000000,0.000000,2,'2018-05-25 13:38:51',2,'2019-07-21 15:12:21'),(4,1,4,12.000000,24.000000,-4.000000,0.000000,2,'2019-07-04 17:59:58',2,'2019-07-21 15:17:00'),(5,1,5,0.000000,0.000000,0.000000,0.000000,2,'2019-07-04 18:02:06',2,'2019-07-21 14:12:47'),(6,1,6,6.000000,12.000000,-1.000000,0.000000,2,'2019-07-04 18:20:14',2,'2019-07-21 15:17:00'),(7,1,7,0.000000,0.000000,0.000000,0.000000,0,'2020-06-24 10:34:33',NULL,NULL);

/*Table structure for table `cat_proveedores` */

CREATE TABLE `cat_proveedores` (
  `id_proveedor` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_proveedor` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `cat_proveedores` */

/*Table structure for table `cat_series` */

CREATE TABLE `cat_series` (
  `id_serie` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `nombre_serie` varchar(20) NOT NULL,
  `folioinicio` int(11) NOT NULL,
  `foliofin` int(11) NOT NULL,
  `foliosig` int(11) DEFAULT NULL,
  `tipo_serie` tinyint(1) NOT NULL COMMENT '0=Factura,1=NOTAS DE CREDITO,2=COMPRAS,3=VENTAS,4=INVENTARIO,5=NOMINA,6=REMISIONES, 7=ENTRADAS, 8=SALIDAS, 9=ABONOS,10=MOVIMIENTOS BANCOS',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_serie`,`id_empresa`,`id_sucursal`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `cat_series` */

insert  into `cat_series`(`id_serie`,`id_empresa`,`id_sucursal`,`nombre_serie`,`folioinicio`,`foliofin`,`foliosig`,`tipo_serie`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,1,'VENT',1,100000,17,3,'A',0,'2017-12-05 02:00:24',0,'2017-12-05 02:00:36'),(2,1,1,'SAJU',1,1000000,7,8,'A',0,'2017-12-05 02:00:53',NULL,NULL),(3,1,1,'EAJU',1,100000,2,7,'A',0,'2017-12-05 02:01:07',NULL,NULL),(4,1,1,'INVFIS',1,10000,6,4,'A',NULL,NULL,NULL,NULL),(5,1,1,'REM',1,1000000,4,6,'A',2,'2019-04-11 15:25:57',NULL,NULL),(6,1,1,'ABO',1,1000000,4,9,'A',2,'2019-04-11 15:26:24',NULL,NULL),(7,1,1,'MOVBAN',1,1000000,4,10,'A',2,'2019-04-11 15:26:50',NULL,NULL),(8,1,1,'GAS',1,1000000,2,11,'A',2,'2019-04-11 15:27:04',NULL,NULL);

/*Table structure for table `cat_sucursales` */

CREATE TABLE `cat_sucursales` (
  `id_sucursal` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `nombre_sucursal` varchar(250) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `calle` varchar(200) DEFAULT NULL,
  `numext` varchar(50) DEFAULT NULL,
  `numint` varchar(50) DEFAULT NULL,
  `colonia` varchar(200) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `localidad` varchar(200) DEFAULT NULL,
  `ciudad` varchar(200) DEFAULT NULL,
  `estado` varchar(200) DEFAULT NULL,
  `pais` varchar(200) DEFAULT NULL,
  `logotipo` varchar(255) DEFAULT NULL,
  `orden` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`,`id_empresa`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `cat_sucursales` */

insert  into `cat_sucursales`(`id_sucursal`,`id_empresa`,`nombre_sucursal`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`,`calle`,`numext`,`numint`,`colonia`,`cp`,`localidad`,`ciudad`,`estado`,`pais`,`logotipo`,`orden`) values (1,1,'SUCURSAL 1','A',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,2,'SUCURSAL CENTRO','A',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,'SUCURSAL 2','A',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,2,'SUCURSAL JUAREZ','A',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `cat_tiposmovimientos` */

CREATE TABLE `cat_tiposmovimientos` (
  `id_tipomovimiento` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo_movimiento` varchar(10) NOT NULL,
  `nombre_movimiento` varchar(50) NOT NULL,
  `tipo_movimiento` tinyint(1) NOT NULL,
  `genera_recosteo` tinyint(1) NOT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_tipomovimiento`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `cat_tiposmovimientos` */

insert  into `cat_tiposmovimientos`(`id_tipomovimiento`,`codigo_movimiento`,`nombre_movimiento`,`tipo_movimiento`,`genera_recosteo`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'COM','COMPRAS',1,1,'A',NULL,NULL,NULL,NULL),(2,'EAJU','ENTRADA DE AJUSTE',1,0,'A',NULL,NULL,NULL,NULL),(3,'SAJU','SALIDA DE AJUSTE',2,0,'A',NULL,NULL,NULL,NULL),(4,'TRAS','TRASPASO DE ALMACEN',3,0,'A',NULL,NULL,NULL,NULL),(5,'INVI','INVENTARIO INICIAL',1,1,'A',NULL,NULL,NULL,NULL),(6,'REM','REMISIONES',4,0,'A',NULL,NULL,NULL,NULL);

/*Table structure for table `cat_unidadesdemedida` */

CREATE TABLE `cat_unidadesdemedida` (
  `id_unidadmedida` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo_unidad` varchar(20) DEFAULT NULL,
  `descripcion_unidad` varchar(100) DEFAULT NULL,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_unidadmedida`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `cat_unidadesdemedida` */

insert  into `cat_unidadesdemedida`(`id_unidadmedida`,`codigo_unidad`,`descripcion_unidad`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,'SER1','SERVICIO','A',0,'2017-12-05 01:53:20',NULL,NULL),(2,'PZA','PIEZA','A',2,'2019-07-04 17:58:40',NULL,NULL);

/*Table structure for table `cat_usuarios` */

CREATE TABLE `cat_usuarios` (
  `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(150) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `pass` blob,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo,I=Inactivo',
  `esadmin` tinyint(1) DEFAULT '0',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `puede_eliminar` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `cat_usuarios` */

insert  into `cat_usuarios`(`id_usuario`,`nombre_usuario`,`usuario`,`pass`,`status`,`esadmin`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`,`puede_eliminar`) values (2,'USUARIO PRUEBA','prueba@hotmail.com','9k����\Z�ӵ%N?�','A',0,NULL,NULL,NULL,NULL,0);

/*Table structure for table `cat_usuarios_privilegios` */

CREATE TABLE `cat_usuarios_privilegios` (
  `id_usuario_privilegio` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) NOT NULL,
  `id_privilegio` bigint(20) NOT NULL,
  `tipo_privilegio` tinyint(4) NOT NULL COMMENT '1=Empresa,2=Sucursal,3=Almacen,4=Modulo',
  PRIMARY KEY (`id_usuario_privilegio`)
) ENGINE=MyISAM AUTO_INCREMENT=201 DEFAULT CHARSET=utf8;

/*Data for the table `cat_usuarios_privilegios` */

insert  into `cat_usuarios_privilegios`(`id_usuario_privilegio`,`id_usuario`,`id_privilegio`,`tipo_privilegio`) values (1,2,1,1),(2,2,1,2),(3,2,1,3),(85,2,5400,4),(81,2,5300,4),(80,2,5200,4),(79,2,5100,4),(78,2,5000,4),(77,2,4600,4),(76,2,4500,4),(75,2,4400,4),(74,2,4300,4),(73,2,4200,4),(72,2,4100,4),(71,2,4000,4),(70,2,3100,4),(69,2,3000,4),(68,2,1600,4),(67,2,1500,4),(66,2,1400,4),(65,2,1100,4),(64,2,1000,4),(86,2,3200,4),(118,2,1200,4),(123,2,1700,4),(125,2,5500,4),(126,2,5600,4),(127,2,5700,4),(132,2,5800,4),(141,2,7000,4),(142,2,7100,4),(143,2,7200,4),(144,2,4700,4),(145,2,4800,4),(146,2,5900,4),(147,2,3300,4),(189,2,5910,4),(190,2,4900,4),(191,2,1800,4),(192,2,4910,4),(193,2,8000,4),(194,2,8100,4),(195,2,5911,4),(196,2,2,1),(197,2,2,2),(198,2,2,3),(199,2,3,2),(200,2,4,2);

/*Table structure for table `checadas` */

CREATE TABLE `checadas` (
  `id_checada` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empleado` bigint(20) NOT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_checada`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `checadas` */

insert  into `checadas`(`id_checada`,`id_empleado`,`fecha_hora`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,'2019-07-14 01:24:37','A',2,'2019-07-14 00:24:37',NULL,NULL),(2,2,'2019-07-14 01:24:44','A',2,'2019-07-14 00:24:44',NULL,NULL),(3,3,'2019-07-14 01:24:54','A',2,'2019-07-14 00:24:54',NULL,NULL),(4,1,'2019-07-15 21:23:20','A',2,'2019-07-15 20:23:20',NULL,NULL),(5,1,'2019-07-16 19:03:55','A',2,'2019-07-16 18:03:55',NULL,NULL),(6,4,'2019-07-16 19:12:40','A',2,'2019-07-16 18:12:40',NULL,NULL),(7,1,'2019-07-16 19:43:46','A',2,'2019-07-16 18:43:46',NULL,NULL),(8,5,'2019-07-21 15:56:08','A',2,'2019-07-21 14:56:08',NULL,NULL),(9,1,'2021-06-01 10:00:52','A',0,'2021-06-01 09:00:52',NULL,NULL),(10,5,'2021-06-01 10:01:01','A',0,'2021-06-01 09:01:01',NULL,NULL),(11,2,'2021-06-01 10:01:09','A',0,'2021-06-01 09:01:09',NULL,NULL),(12,1,'2021-06-01 10:01:20','A',0,'2021-06-01 09:01:20',NULL,NULL);

/*Table structure for table `citas` */

CREATE TABLE `citas` (
  `id_cita` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_cliente` bigint(20) NOT NULL,
  `id_agente` bigint(20) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_horario` bigint(20) NOT NULL,
  `observaciones` varchar(200) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cita`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `citas` */

insert  into `citas`(`id_cita`,`id_empresa`,`id_sucursal`,`id_cliente`,`id_agente`,`fecha`,`id_horario`,`observaciones`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,1,1,3,'2019-07-14',1,'1','A',2,'2019-07-14 00:25:33',NULL,NULL),(2,1,1,2,3,'2019-07-14',2,'2','A',2,'2019-07-14 00:25:52',NULL,NULL),(3,1,1,3,2,'2019-07-14',1,'1','A',2,'2019-07-14 00:26:17',NULL,NULL),(4,1,1,2,3,'2019-07-15',1,'2','A',2,'2019-07-15 20:24:57',NULL,NULL),(8,1,1,1,3,'2019-07-21',1,'1','A',2,'2019-07-21 14:53:32',NULL,NULL),(9,1,1,2,3,'2019-07-21',2,'2','A',2,'2019-07-21 14:54:25',NULL,NULL),(10,1,1,1,1,'2020-09-28',2,'','A',0,'2020-09-28 12:54:15',NULL,NULL);

/*Table structure for table `cortes` */

CREATE TABLE `cortes` (
  `id_corte` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_turno` bigint(20) NOT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `fecha_corte` datetime DEFAULT NULL,
  `total_liquidado` decimal(18,2) DEFAULT '0.00',
  `total_retenido` decimal(18,2) DEFAULT '0.00',
  `total_corte` decimal(18,2) DEFAULT '0.00',
  `total_turno` decimal(18,2) DEFAULT '0.00',
  `total_ventas` decimal(18,2) DEFAULT '0.00',
  `total_depositos` decimal(18,2) DEFAULT '0.00',
  `total_retiros` decimal(18,2) DEFAULT '0.00',
  `diferencia_corte` decimal(18,2) DEFAULT '0.00',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_corte`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `cortes` */

insert  into `cortes`(`id_corte`,`id_empresa`,`id_sucursal`,`id_turno`,`consecutivo`,`concepto`,`fecha_corte`,`total_liquidado`,`total_retenido`,`total_corte`,`total_turno`,`total_ventas`,`total_depositos`,`total_retiros`,`diferencia_corte`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (5,1,1,3,5,'cierre de turno','2019-07-04 18:30:50',10000.00,1.00,10001.00,2.00,729.00,0.00,0.00,9270.00,'A',2,'2019-07-04 18:31:26',2,'2019-07-21 14:25:06'),(4,1,1,2,4,'CORTE 01','2018-01-01 13:07:45',1.00,0.00,1.00,1.00,100.00,0.00,0.00,-100.00,'A',2,'2018-05-25 13:08:23',NULL,NULL),(3,1,1,1,3,'CORTE TURNO 1','2017-12-05 02:07:12',250.00,500.00,750.00,500.00,100.00,200.00,50.00,0.00,'A',0,'2017-12-05 02:09:45',NULL,NULL),(7,1,1,5,6,'1','2019-07-21 15:46:32',1200.00,1000.00,2200.00,100.00,219.00,500.00,50.00,1431.00,'A',2,'2019-07-21 14:47:00',NULL,NULL);

/*Table structure for table `cortes_liquidaciones` */

CREATE TABLE `cortes_liquidaciones` (
  `id_corte_liquidacion` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_corte` bigint(20) NOT NULL,
  `id_formapago` bigint(20) NOT NULL,
  `id_denominacion` bigint(20) NOT NULL,
  `cantidad` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_corte_liquidacion`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `cortes_liquidaciones` */

insert  into `cortes_liquidaciones`(`id_corte_liquidacion`,`id_corte`,`id_formapago`,`id_denominacion`,`cantidad`,`total`) values (1,3,1,9,5.00,250.00),(2,4,1,4,1.00,1.00),(6,5,1,10,100.00,10000.00),(7,7,1,4,1200.00,1200.00);

/*Table structure for table `cortes_retenciones` */

CREATE TABLE `cortes_retenciones` (
  `id_corte_retencion` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_corte` bigint(20) NOT NULL,
  `id_formapago` bigint(20) NOT NULL,
  `id_denominacion` bigint(20) NOT NULL,
  `cantidad` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_corte_retencion`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `cortes_retenciones` */

insert  into `cortes_retenciones`(`id_corte_retencion`,`id_corte`,`id_formapago`,`id_denominacion`,`cantidad`,`total`) values (1,3,1,4,500.00,500.00),(6,7,1,4,1000.00,1000.00),(5,5,1,1,10.00,1.00);

/*Table structure for table `cxc` */

CREATE TABLE `cxc` (
  `id_cxc` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_remision` bigint(20) DEFAULT NULL,
  `id_cliente` bigint(20) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `total` decimal(24,6) DEFAULT '0.000000',
  `abonos` decimal(24,6) DEFAULT '0.000000',
  `saldo` decimal(24,6) DEFAULT '0.000000',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cxc`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `cxc` */

insert  into `cxc`(`id_cxc`,`id_remision`,`id_cliente`,`fecha`,`total`,`abonos`,`saldo`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,1,NULL,1500.000000,150.000000,1350.000000,NULL,2,'2019-04-11 15:33:51',NULL,NULL),(2,3,1,NULL,150.000000,50.000000,100.000000,NULL,2,'2019-07-21 14:50:31',NULL,NULL);

/*Table structure for table `cxc_abonos` */

CREATE TABLE `cxc_abonos` (
  `id_cxc_abono` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_cxc` bigint(20) NOT NULL,
  `id_serie` bigint(20) NOT NULL,
  `serie` varchar(20) DEFAULT NULL,
  `folio` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `concepto` varchar(100) DEFAULT NULL,
  `observacion` varchar(100) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cxc_abono`,`id_cxc`,`id_serie`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `cxc_abonos` */

insert  into `cxc_abonos`(`id_cxc_abono`,`id_cxc`,`id_serie`,`serie`,`folio`,`fecha`,`concepto`,`observacion`,`importe`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,6,'ABO',1,'2019-04-11 15:33:15','ABONO CXC REM - 1','ABONO 11/04/2019',50.000000,'A',2,'2019-04-11 15:37:47',NULL,NULL),(2,1,6,'ABO',2,'2019-07-21 15:48:27','ABONO CXC REM - 1','',100.000000,'A',2,'2019-07-21 14:48:39',NULL,NULL),(3,2,6,'ABO',3,'2019-07-21 15:49:55','ABONO CXC REM - 3','',50.000000,'A',2,'2019-07-21 14:50:45',NULL,NULL);

/*Table structure for table `inventarios` */

CREATE TABLE `inventarios` (
  `id_inventario` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_almacen` bigint(20) NOT NULL,
  `id_serie` bigint(20) NOT NULL,
  `serie_inventario` varchar(20) DEFAULT NULL,
  `folio_inventario` int(11) DEFAULT NULL,
  `fecha_inventario` datetime DEFAULT NULL,
  `concepto_inventario` varchar(200) DEFAULT NULL,
  `aplicado` tinyint(1) DEFAULT '0',
  `fecha_aplica` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_inventario`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `inventarios` */

insert  into `inventarios`(`id_inventario`,`id_empresa`,`id_sucursal`,`id_almacen`,`id_serie`,`serie_inventario`,`folio_inventario`,`fecha_inventario`,`concepto_inventario`,`aplicado`,`fecha_aplica`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,1,1,4,'INVFIS',1,'2018-03-02 14:32:50','PRUEBA 1',1,'2018-03-02 14:34:25','A',0,'2018-03-02 14:33:25',0,'2018-03-02 14:33:28'),(2,1,1,1,4,'INVFIS',4,'2018-03-02 14:38:27','prueba 2',1,'2018-03-02 14:51:53','A',0,'2018-03-02 14:42:20',NULL,NULL),(3,1,1,1,4,'INVFIS',5,'2019-07-21 16:10:33','inventario fisico',1,'2019-07-21 15:12:21','A',2,'2019-07-21 15:11:40',NULL,NULL);

/*Table structure for table `inventarios_detalles` */

CREATE TABLE `inventarios_detalles` (
  `id_inventario_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_inventario` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `stock` decimal(24,6) DEFAULT '0.000000',
  `conteo` decimal(24,6) DEFAULT '0.000000',
  `diferencia` decimal(24,6) DEFAULT '0.000000',
  PRIMARY KEY (`id_inventario_detalle`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `inventarios_detalles` */

insert  into `inventarios_detalles`(`id_inventario_detalle`,`id_inventario`,`id_producto`,`stock`,`conteo`,`diferencia`) values (2,1,2,-3.000000,10.000000,13.000000),(3,2,2,10.000000,5.000000,-5.000000),(4,3,3,-25.000000,48.000000,73.000000);

/*Table structure for table `kardex` */

CREATE TABLE `kardex` (
  `id_kardex` bigint(20) NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT NULL,
  `id_movimiento` bigint(20) DEFAULT NULL,
  `tipo` int(11) DEFAULT NULL,
  `id_producto` bigint(20) DEFAULT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `inicial` decimal(18,2) DEFAULT '0.00',
  `entrada` decimal(18,2) DEFAULT '0.00',
  `salida` decimal(18,2) DEFAULT '0.00',
  `saldo` decimal(18,2) DEFAULT '0.00',
  PRIMARY KEY (`id_kardex`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `kardex` */

/*Table structure for table `movimientos_almacen` */

CREATE TABLE `movimientos_almacen` (
  `id_movimiento` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_tipomovimiento` bigint(20) DEFAULT NULL,
  `id_almacen_origen` bigint(20) DEFAULT NULL,
  `id_almacen_destino` bigint(20) DEFAULT NULL,
  `id_serie` bigint(20) DEFAULT NULL,
  `id_agente` bigint(20) DEFAULT NULL,
  `id_inventario` bigint(20) DEFAULT NULL,
  `serie_movimiento` varchar(20) DEFAULT NULL,
  `folio_movimiento` int(11) DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT NULL,
  `concepto_movimiento` varchar(100) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_movimiento`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `movimientos_almacen` */

insert  into `movimientos_almacen`(`id_movimiento`,`id_empresa`,`id_sucursal`,`id_tipomovimiento`,`id_almacen_origen`,`id_almacen_destino`,`id_serie`,`id_agente`,`id_inventario`,`serie_movimiento`,`folio_movimiento`,`fecha_movimiento`,`concepto_movimiento`,`importe`,`descuento`,`subtotal`,`impuestos`,`total`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,1,6,1,NULL,2,1,NULL,'SAJU',1,'2018-01-17 12:28:34','1',100.000000,0.000000,100.000000,0.000000,100.000000,'A',0,'2018-01-17 12:29:35',NULL,NULL),(2,1,1,3,1,NULL,2,NULL,NULL,'SAJU',2,'2018-01-15 21:30:13','pueba',100.000000,0.000000,100.000000,0.000000,100.000000,'A',2,'2018-01-29 21:30:49',NULL,NULL),(3,1,1,2,0,1,4,NULL,1,'INVFIS',3,'2018-03-02 14:34:25','PRUEBA 1',0.000000,0.000000,0.000000,0.000000,0.000000,'A',0,'2018-03-02 14:34:25',NULL,NULL),(4,1,1,3,1,0,2,NULL,2,'SAJU',4,'2018-03-02 14:51:53','prueba 2',0.000000,0.000000,0.000000,0.000000,0.000000,'A',0,'2018-03-02 14:51:53',NULL,NULL),(5,1,1,5,NULL,1,2,NULL,NULL,'SAJU',5,'2018-05-25 13:10:59','PRUEBA',273.360000,0.000000,273.360000,0.000000,273.360000,'A',2,'2018-05-25 13:15:12',NULL,NULL),(6,1,1,2,NULL,1,3,NULL,NULL,'EAJU',1,'2019-07-21 16:07:33','inventario inicial',195.000000,0.000000,195.000000,0.000000,195.000000,'A',2,'2019-07-21 15:08:32',NULL,NULL),(7,1,1,2,0,1,2,NULL,3,'SAJU',6,'2019-07-21 15:12:21','inventario fisico',0.000000,0.000000,0.000000,0.000000,0.000000,'A',2,'2019-07-21 15:12:21',NULL,NULL);

/*Table structure for table `movimientos_almacen_detalles` */

CREATE TABLE `movimientos_almacen_detalles` (
  `id_movimiento_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_movimiento` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `cantidad` decimal(24,6) DEFAULT NULL,
  `costo` decimal(24,6) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_detalle`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `movimientos_almacen_detalles` */

insert  into `movimientos_almacen_detalles`(`id_movimiento_detalle`,`id_movimiento`,`id_producto`,`cantidad`,`costo`,`importe`,`descuento`,`subtotal`,`impuestos`,`total`) values (1,1,2,1.000000,100.000000,100.000000,0.000000,100.000000,0.000000,100.000000),(2,2,2,1.000000,100.000000,100.000000,0.000000,100.000000,0.000000,100.000000),(3,3,2,13.000000,0.000000,0.000000,0.000000,0.000000,0.000000,0.000000),(4,4,2,5.000000,0.000000,0.000000,0.000000,0.000000,0.000000,0.000000),(5,5,0,24.000000,11.390000,273.360000,0.000000,273.360000,0.000000,273.360000),(6,6,3,15.000000,13.000000,195.000000,0.000000,195.000000,0.000000,195.000000),(7,7,3,73.000000,0.000000,0.000000,0.000000,0.000000,0.000000,0.000000);

/*Table structure for table `movimientos_bancos` */

CREATE TABLE `movimientos_bancos` (
  `id_movimiento_banco` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_serie` bigint(20) NOT NULL,
  `serie` varchar(20) DEFAULT NULL,
  `folio` int(11) DEFAULT NULL,
  `id_concepto` bigint(20) NOT NULL,
  `id_chequera` bigint(20) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `observaciones` varchar(200) DEFAULT NULL,
  `tipo_movimiento` tinyint(1) DEFAULT NULL COMMENT '1=Ingreso,2=egreso',
  `tipo_origen` tinyint(4) DEFAULT NULL COMMENT '1=Efectivo,2=Bancos',
  `importe` decimal(18,6) DEFAULT NULL,
  `origen` tinyint(1) DEFAULT '1' COMMENT '1=Movimientos Bancos,2=Gastos',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_banco`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `movimientos_bancos` */

insert  into `movimientos_bancos`(`id_movimiento_banco`,`id_empresa`,`id_sucursal`,`id_serie`,`serie`,`folio`,`id_concepto`,`id_chequera`,`fecha`,`observaciones`,`tipo_movimiento`,`tipo_origen`,`importe`,`origen`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,1,7,'MOVBAN',1,1,1,'2019-07-21 15:59:37','saldo inicial julio',1,2,5000.000000,1,'A',2,'2019-07-21 15:00:05',2,'2019-07-21 15:01:14'),(2,1,1,7,'MOVBAN',2,3,1,'2019-07-21 16:00:20','',1,2,2000.000000,1,'A',2,'2019-07-21 15:01:02',NULL,NULL),(3,1,1,8,'GAS',1,2,NULL,'2019-07-21 16:02:43','renta',2,1,4000.000000,2,'A',2,'2019-07-21 15:03:13',NULL,NULL),(4,1,1,7,'MOVBAN',3,3,NULL,'2019-07-21 16:05:41','',1,1,1000.000000,1,'A',2,'2019-07-21 15:06:15',NULL,NULL);

/*Table structure for table `movimientos_caja` */

CREATE TABLE `movimientos_caja` (
  `id_movimiento_caja` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_turno` bigint(20) NOT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `tipo` tinyint(1) DEFAULT NULL COMMENT '1=Deposito,2=Retiro',
  `total` decimal(18,2) DEFAULT '0.00',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_caja`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `movimientos_caja` */

insert  into `movimientos_caja`(`id_movimiento_caja`,`id_empresa`,`id_sucursal`,`id_turno`,`consecutivo`,`fecha`,`concepto`,`tipo`,`total`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,1,1,NULL,'2017-12-05 02:07:30','DEPOSITO 1',1,200.00,'A',0,'2017-12-05 02:07:38',NULL,NULL),(2,1,1,1,NULL,'2017-12-05 02:07:43','RETIRO 1',2,50.00,'A',0,'2017-12-05 02:07:50',NULL,NULL),(3,1,1,5,NULL,'2019-07-21 15:44:46','compra',2,50.00,'A',2,'2019-07-21 14:45:07',NULL,NULL),(4,1,1,5,NULL,'2019-07-21 15:45:32','deposito 500',1,500.00,'A',2,'2019-07-21 14:45:43',NULL,NULL);

/*Table structure for table `remisiones` */

CREATE TABLE `remisiones` (
  `id_remision` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_almacen` bigint(20) NOT NULL,
  `id_cliente` bigint(20) NOT NULL,
  `id_agente` bigint(20) NOT NULL,
  `id_serie` bigint(20) NOT NULL,
  `serie` varchar(20) DEFAULT NULL,
  `folio` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `condicion_pago` tinyint(1) DEFAULT NULL COMMENT '1=Contado, 2=Credito',
  `concepto` varchar(100) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `comision` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  `aplicado` tinyint(1) DEFAULT '0',
  `fecha_aplica` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_remision`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `remisiones` */

insert  into `remisiones`(`id_remision`,`id_empresa`,`id_sucursal`,`id_almacen`,`id_cliente`,`id_agente`,`id_serie`,`serie`,`folio`,`fecha`,`condicion_pago`,`concepto`,`importe`,`descuento`,`subtotal`,`comision`,`impuestos`,`total`,`aplicado`,`fecha_aplica`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`) values (1,1,1,1,1,2,5,'REM',1,'2019-04-11 15:32:09',2,'CREDITO',1500.000000,0.000000,1500.000000,0.000000,0.000000,1500.000000,1,'2019-04-11 15:33:51','A',2,'2019-04-11 15:32:51',NULL,NULL),(2,1,1,1,2,2,5,'REM',2,'2019-04-11 15:38:06',1,'CREDITO',130.000000,0.000000,130.000000,0.000000,0.000000,130.000000,0,NULL,'A',2,'2019-04-11 15:38:52',NULL,NULL),(3,1,1,1,1,1,5,'REM',3,'2019-07-21 15:48:09',2,'credito',150.000000,0.000000,150.000000,0.000000,0.000000,150.000000,1,'2019-07-21 14:50:31','A',2,'2019-07-21 14:49:22',NULL,NULL);

/*Table structure for table `remisiones_detalles` */

CREATE TABLE `remisiones_detalles` (
  `id_remision_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_remision` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `cantidad` decimal(24,6) DEFAULT NULL,
  `costo` decimal(24,6) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  PRIMARY KEY (`id_remision_detalle`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `remisiones_detalles` */

insert  into `remisiones_detalles`(`id_remision_detalle`,`id_remision`,`id_producto`,`cantidad`,`costo`,`importe`,`descuento`,`subtotal`,`impuestos`,`total`) values (1,1,3,10.000000,150.000000,1500.000000,0.000000,1500.000000,0.000000,1500.000000),(2,2,3,10.000000,13.000000,130.000000,0.000000,130.000000,0.000000,130.000000),(3,3,4,1.000000,150.000000,150.000000,0.000000,150.000000,0.000000,150.000000);

/*Table structure for table `turnos` */

CREATE TABLE `turnos` (
  `id_turno` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_caja` bigint(20) DEFAULT NULL,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_corte` bigint(20) DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `fechainicio` datetime NOT NULL,
  `fechafin` datetime DEFAULT NULL,
  `total_turno` decimal(18,2) DEFAULT '0.00',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `usercierre` bigint(20) DEFAULT NULL,
  `fechacierre` datetime DEFAULT NULL,
  PRIMARY KEY (`id_turno`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `turnos` */

insert  into `turnos`(`id_turno`,`id_caja`,`id_empresa`,`id_sucursal`,`id_corte`,`consecutivo`,`concepto`,`fechainicio`,`fechafin`,`total_turno`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`,`usercierre`,`fechacierre`) values (1,NULL,1,1,3,1,'TURNO 1','2017-12-05 02:06:28','2017-12-05 02:07:12',500.00,'A',0,'2017-12-05 02:06:39',NULL,NULL,0,'2017-12-05 02:07:12'),(2,NULL,1,1,4,2,'prueba','2018-01-01 21:17:54','2018-01-01 13:07:45',1.00,'A',2,'2018-01-29 21:18:26',NULL,NULL,2,'2018-01-01 13:07:45'),(3,NULL,1,1,5,3,'PRUEBA2','2018-05-25 13:09:17','2019-07-04 18:30:50',2.00,'A',2,'2018-05-25 13:21:04',NULL,NULL,2,'2019-07-04 18:30:50'),(5,NULL,1,1,7,4,'1','2019-07-14 00:16:22','2019-07-21 15:46:32',100.00,'A',2,'2019-07-14 00:16:33',NULL,NULL,2,'2019-07-21 15:46:32');

/*Table structure for table `turnos_detalles` */

CREATE TABLE `turnos_detalles` (
  `id_turno_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_turno` bigint(20) NOT NULL,
  `id_formapago` bigint(20) NOT NULL,
  `id_denominacion` bigint(20) NOT NULL,
  `cantidad` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_turno_detalle`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `turnos_detalles` */

insert  into `turnos_detalles`(`id_turno_detalle`,`id_turno`,`id_formapago`,`id_denominacion`,`cantidad`,`total`) values (1,1,1,4,500.00,500.00),(2,2,1,4,1.00,1.00),(3,3,1,4,2.00,2.00),(5,5,1,4,100.00,100.00);

/*Table structure for table `ventas` */

CREATE TABLE `ventas` (
  `id_venta` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_almacen` bigint(20) DEFAULT NULL,
  `id_serie` bigint(20) DEFAULT NULL,
  `id_cliente` bigint(20) DEFAULT NULL,
  `serie_venta` varchar(20) DEFAULT NULL,
  `folio_venta` int(11) DEFAULT NULL,
  `fecha_venta` datetime DEFAULT NULL,
  `concepto_venta` varchar(100) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  `pago` decimal(24,6) DEFAULT '0.000000',
  `cambio` decimal(24,6) DEFAULT '0.000000',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `id_turno` bigint(20) DEFAULT NULL,
  `id_agente` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_venta`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Data for the table `ventas` */

insert  into `ventas`(`id_venta`,`id_empresa`,`id_sucursal`,`id_almacen`,`id_serie`,`id_cliente`,`serie_venta`,`folio_venta`,`fecha_venta`,`concepto_venta`,`importe`,`descuento`,`subtotal`,`impuestos`,`total`,`pago`,`cambio`,`status`,`usercreador`,`fechacreador`,`usermodif`,`fechamodif`,`id_turno`,`id_agente`) values (1,1,1,1,1,1,'VENT',1,'2017-12-05 02:06:43','',100.000000,0.000000,100.000000,0.000000,100.000000,100.000000,0.000000,'A',0,'2017-12-05 02:06:51',NULL,NULL,1,NULL),(2,1,1,1,1,1,'VENT',2,'2018-01-29 21:18:40','',100.000000,0.000000,100.000000,0.000000,100.000000,100.000000,0.000000,'A',2,'2018-01-29 21:19:04',NULL,NULL,2,NULL),(3,1,1,1,1,1,'VENT',3,'2018-05-25 13:36:50','',39.000000,0.000000,39.000000,0.000000,39.000000,100.000000,61.000000,'A',2,'2018-05-25 13:38:51',NULL,NULL,3,NULL),(4,1,1,1,1,1,'VENT',4,'2018-05-28 14:00:21','',156.000000,0.000000,156.000000,0.000000,156.000000,156.000000,0.000000,'A',0,'2018-05-28 14:01:52',NULL,NULL,3,NULL),(5,1,1,1,1,1,'VENT',5,'2019-07-04 09:27:15','',13.000000,0.000000,13.000000,0.000000,13.000000,13.000000,0.000000,'A',2,'2019-07-04 09:28:45',NULL,NULL,3,NULL),(6,1,1,1,1,1,'VENT',6,'2019-07-04 09:31:00','',113.000000,13.000000,100.000000,0.000000,100.000000,200.000000,100.000000,'A',2,'2019-07-04 09:32:12',NULL,NULL,3,NULL),(7,1,1,1,1,3,'VENT',7,'2019-07-04 17:56:34','',100.000000,25.000000,75.000000,0.000000,75.000000,75.000000,0.000000,'A',2,'2019-07-04 18:06:52',NULL,NULL,3,NULL),(8,1,1,1,1,1,'VENT',8,'2019-07-04 18:17:02','',100.000000,20.000000,80.000000,0.000000,80.000000,80.000000,0.000000,'A',2,'2019-07-04 18:17:56',NULL,NULL,3,NULL),(9,1,1,1,1,1,'VENT',9,'2019-07-04 18:20:37','',250.000000,50.000000,200.000000,0.000000,200.000000,200.000000,0.000000,'A',2,'2019-07-04 18:21:40',NULL,NULL,3,NULL),(10,1,1,1,1,1,'VENT',10,'2019-07-04 18:27:43','',66.000000,0.000000,66.000000,0.000000,66.000000,200.000000,134.000000,'A',2,'2019-07-04 18:29:27',NULL,NULL,3,NULL),(12,1,1,1,1,1,'VENT',12,'2019-07-14 00:15:55','1111',13.000000,0.000000,13.000000,0.000000,13.000000,13.000000,0.000000,'A',2,'2019-07-14 00:16:42',NULL,NULL,5,3),(13,1,1,1,1,1,'VENT',13,'2019-07-14 00:17:47','',63.000000,0.000000,63.000000,0.000000,63.000000,63.000000,0.000000,'A',2,'2019-07-14 00:18:07',NULL,NULL,5,NULL),(14,1,1,1,1,1,'VENT',14,'2019-07-14 01:10:12','2',13.000000,0.000000,13.000000,0.000000,13.000000,13.000000,0.000000,'A',2,'2019-07-14 01:10:37',NULL,NULL,5,1),(15,1,1,1,1,1,'VENT',15,'2019-07-16 19:04:55','',100.000000,20.000000,80.000000,0.000000,80.000000,100.000000,20.000000,'A',2,'2019-07-16 18:05:55',NULL,NULL,5,3),(16,1,1,1,1,1,'VENT',16,'2019-07-21 15:46:06','',50.000000,0.000000,50.000000,0.000000,50.000000,50.000000,0.000000,'A',2,'2019-07-21 14:46:20',NULL,NULL,5,3);

/*Table structure for table `ventas_detalles` */

CREATE TABLE `ventas_detalles` (
  `id_venta_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `cantidad` decimal(24,6) DEFAULT NULL,
  `precio` decimal(24,6) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  PRIMARY KEY (`id_venta_detalle`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Data for the table `ventas_detalles` */

insert  into `ventas_detalles`(`id_venta_detalle`,`id_venta`,`id_producto`,`cantidad`,`precio`,`importe`,`descuento`,`subtotal`,`impuestos`,`total`) values (1,1,2,1.000000,100.000000,100.000000,0.000000,100.000000,0.000000,100.000000),(2,2,2,1.000000,100.000000,100.000000,0.000000,100.000000,0.000000,100.000000),(3,3,3,3.000000,13.000000,39.000000,0.000000,39.000000,0.000000,39.000000),(4,4,3,12.000000,13.000000,156.000000,0.000000,156.000000,0.000000,156.000000),(5,5,3,1.000000,13.000000,13.000000,0.000000,13.000000,0.000000,13.000000),(6,6,3,1.000000,13.000000,13.000000,3.000000,10.000000,0.000000,10.000000),(7,6,2,1.000000,100.000000,100.000000,10.000000,90.000000,0.000000,90.000000),(8,7,4,1.000000,100.000000,100.000000,25.000000,75.000000,0.000000,75.000000),(9,8,4,1.000000,100.000000,100.000000,20.000000,80.000000,0.000000,80.000000),(10,9,6,1.000000,250.000000,250.000000,50.000000,200.000000,0.000000,200.000000),(11,10,4,1.000000,66.000000,66.000000,0.000000,66.000000,0.000000,66.000000),(13,12,3,1.000000,13.000000,13.000000,0.000000,13.000000,0.000000,13.000000),(14,13,5,1.000000,50.000000,50.000000,0.000000,50.000000,0.000000,50.000000),(15,13,3,1.000000,13.000000,13.000000,0.000000,13.000000,0.000000,13.000000),(16,14,3,1.000000,13.000000,13.000000,0.000000,13.000000,0.000000,13.000000),(17,15,1,1.000000,100.000000,100.000000,20.000000,80.000000,0.000000,80.000000),(18,16,5,1.000000,50.000000,50.000000,0.000000,50.000000,0.000000,50.000000);

/*Table structure for table `ventas_formaspagos` */

CREATE TABLE `ventas_formaspagos` (
  `id_venta_formapago` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(20) NOT NULL,
  `id_formapago` bigint(20) NOT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  PRIMARY KEY (`id_venta_formapago`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Data for the table `ventas_formaspagos` */

insert  into `ventas_formaspagos`(`id_venta_formapago`,`id_venta`,`id_formapago`,`importe`) values (1,1,1,100.000000),(2,2,1,100.000000),(3,3,1,100.000000),(4,4,1,156.000000),(5,5,1,13.000000),(6,6,1,200.000000),(7,7,1,75.000000),(8,8,1,80.000000),(9,9,1,200.000000),(10,10,1,200.000000),(11,11,1,100.000000),(12,12,1,13.000000),(13,13,1,63.000000),(14,14,1,13.000000),(15,15,1,100.000000),(16,16,1,50.000000);

/* Function  structure for function  `getPrecioProducto` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` FUNCTION `getPrecioProducto`(V_id_producto INT, V_id_cliente INT) RETURNS decimal(14,6)
BEGIN
	DECLARE V_precio_lista DECIMAL;
	DECLARE V_id_listaprecio_cliente BIGINT;
	
	SELECT id_listaprecio INTO V_id_listaprecio_cliente  FROM cat_clientes WHERE id_cliente = V_id_cliente LIMIT 1;
	
	IF(V_id_listaprecio_cliente IS NULL) THEN
		SELECT precio_venta INTO V_precio_lista  FROM cat_productos WHERE id_producto = V_id_producto LIMIT 1;
	ELSE
		SELECT lpd.precio INTO V_precio_lista  FROM cat_listaprecios_detalles lpd 
		INNER JOIN cat_listaprecios lp ON lp.id_listaprecio = lpd.id_listaprecio
		WHERE lpd.id_producto = V_id_producto AND lp.id_listaprecio = V_id_listaprecio_cliente AND lp.status = 'A'
		LIMIT 1;
		
		IF(V_precio_lista IS NULL) THEN
			SELECT precio_venta INTO V_precio_lista  FROM cat_productos WHERE id_producto = V_id_producto LIMIT 1;	
		END IF;	
	
	
	END IF;	
    
	RETURN 	V_precio_lista;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `get_menus_del_usuario` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `get_menus_del_usuario`(V_User INTEGER(11) , V_nodo INTEGER(11),V_todos  BOOLEAN)
BEGIN
IF V_todos=FALSE THEN
	/*
	SELECT id_modulo as id, descripcion as text,newWin,newTab,icono,
            if ((select count(id_modulo) as numero from cat_modulos where id_padre=id)=0, true, '') as leaf,concat(concat('images/iconos/',icono),'.png') as icon,icono 
            FROM
            cat_usuarios_privilegios priv
            LEFT JOIN cat_modulos mods ON priv.Origen='MOD' AND priv.KEYId=mods.IDMod
            WHERE KEYUsuPriv=V_User AND KEYPadMod=V_nodo ORDER by orden;  
            */  
            SELECT id_modulo AS id, descripcion AS TEXT, newWin, newTab,CONCAT(CONCAT('images/iconos/',icono),'.png') AS icon,icono AS iconMaster,
                IF ((SELECT COUNT(id_modulo) AS numero FROM cat_modulos WHERE id_padre=id)=0, TRUE, '') AS leaf,icono 
                FROM cat_usuarios_privilegios priv
                LEFT JOIN cat_modulos mods ON priv.tipo_privilegio=4 AND priv.id_privilegio=mods.id_modulo
                WHERE id_usuario=V_User AND id_padre=V_nodo ORDER BY orden;
ELSE
	SELECT id_modulo AS id, descripcion AS TEXT, newWin, newTab,CONCAT(CONCAT('images/iconos/',icono),'.png') AS icon,icono AS iconMaster,
                IF ((SELECT COUNT(id_modulo) AS numero FROM cat_modulos WHERE id_padre=id)=0, TRUE, '') AS leaf,icono FROM cat_modulos
                WHERE id_padre=V_nodo ORDER BY orden;
END IF;
END */$$
DELIMITER ;

/* Procedure structure for procedure `loginGetEmpresas` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `loginGetEmpresas`(V_usuario INTEGER(11),v_esAdmin BOOLEAN)
BEGIN
IF v_esAdmin=FALSE THEN
SELECT e.id_empresa,e.nombre_fiscal,e.maneja_inventario
FROM cat_usuarios_privilegios up
INNER JOIN cat_empresas  e ON (up.id_privilegio = e.id_empresa AND up.tipo_privilegio = 1)
WHERE up.id_usuario = V_usuario AND up.tipo_privilegio = 1
AND e.STATUS='A'
ORDER BY e.id_empresa;
ELSE
	SELECT id_empresa,nombre_fiscal,maneja_inventario
	FROM cat_empresas;
	
END IF;
END */$$
DELIMITER ;

/* Procedure structure for procedure `loginGetSucursales` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `loginGetSucursales`(V_usuario INTEGER(11),v_esAdmin BOOLEAN,V_id_empresa INTEGER(11))
BEGIN
IF v_esAdmin=FALSE THEN
SELECT s.id_sucursal,s.nombre_sucursal
FROM cat_usuarios_privilegios up
INNER JOIN cat_sucursales  s ON (up.id_privilegio =s.id_sucursal AND up.tipo_privilegio = 2)
inner join cat_empresas e ON (e.id_empresa = s.id_empresa)
WHERE up.id_usuario = V_usuario AND up.tipo_privilegio = 2 and s.id_empresa = V_id_empresa
AND s.STATUS='A'
ORDER BY s.id_sucursal;
ELSE
	SELECT id_sucursal,nombre_sucursal
	fROM cat_sucursales s
	INNER JOIN catempresas e on (e.id_empresa = s.id_empresa)
	where s.id_empresa = V_id_empresa;
END IF;
END */$$
DELIMITER ;

/* Procedure structure for procedure `loginGetUserId` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `loginGetUserId`(V_User VARCHAR(80))
BEGIN
		SELECT id_usuario IDUsu,esadmin AdminUsu FROM cat_usuarios WHERE usuario=V_User;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `spConsultaTodasEmpresas` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spConsultaTodasEmpresas`()
BEGIN
		SELECT id_empresa,nombre_fiscal,maneja_inventario
		FROM cat_empresas ORDER BY id_empresa;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `spConsultaTodasEmpresasSucursales` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spConsultaTodasEmpresasSucursales`()
BEGIN
		DROP TABLE IF EXISTS todas_empresas_tmp;
		
		CREATE TEMPORARY TABLE todas_empresas_tmp(
			Origen VARCHAR(10),
			CFDiEMP TINYINT(1),
			IDConcat VARCHAR(10),
			Nombre VARCHAR(250),
			IDEmpresa INT(11),
			ComEmp VARCHAR(250),
			IDSucursal INT(11),
			NombreSucursal VARCHAR(250),
			bandera TINYINT(1),
			RFCEmp VARCHAR(15)			
		);
		INSERT INTO todas_empresas_tmp SELECT 'EMP' AS Origen,1,
			CONCAT('EMP-', e.id_empresa) AS IDConcat,
			UPPER(e.nombre_comercial) AS Nombre, 
			e.id_empresa AS IDEmpresa, 
			'MATRIZ' AS ComEmp, 	
			0 AS IDSucursal, 
			IF(ISNULL(s.id_sucursal), 0, 'MATRIZ') AS NombreSucursal,			
			IF(ISNULL(e.id_empresa), 0, 1) AS bandera,
			rfc RFCEmp
		FROM cat_empresas e 
		LEFT JOIN cat_sucursales s ON s.id_empresa=e.id_empresa 
		GROUP BY e.id_empresa HAVING bandera!=0;
		
		INSERT INTO todas_empresas_tmp SELECT IF(ISNULL(id_sucursal),'EMP','SUC') AS Origen,1 as CFDiEmp,
			IF(ISNULL(id_sucursal),CONCAT('EMP-', s.id_empresa),CONCAT('SUC-', id_sucursal)) AS IDConcat,
			IF(ISNULL(id_sucursal), UPPER(nombre_comercial), UPPER(nombre_sucursal)) AS Nombre, 
			s.id_empresa AS IDEmpresa, 
			IF(ISNULL(id_sucursal), 'MATRIZ', (SELECT UPPER(nombre_comercial) FROM cat_empresas e2 WHERE e2.id_empresa = s.id_empresa)) AS ComEmp, 	
			IF(ISNULL(id_sucursal), 0, id_sucursal) AS IDSucursal, 
			IF(ISNULL(id_sucursal), 'MATRIZ', UPPER(nombre_sucursal)) AS NombreSucursal,
			0 AS bandera,
			rfc RFCEmp
		FROM cat_empresas e 
		LEFT JOIN cat_sucursales s ON s.id_empresa=e.id_empresa;
    
		
    
    
		SELECT Origen,CFDiEmp,IDConcat,Nombre ,IDEmpresa,ComEmp,IDSucursal,NombreSucursal,RFCEmp
		FROM todas_empresas_tmp ORDER BY IDEmpresa,IDSucursal;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `spConsultaTodasSucursales` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spConsultaTodasSucursales`(V_id_empresa INTEGER(11))
BEGIN
		SELECT id_sucursal,nombre_sucursal
		FROM cat_sucursales 
		WHERE id_empresa = V_id_empresa		
		ORDER BY id_sucursal;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `spMaximosMinimosProductos` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spMaximosMinimosProductos`(V_id_almacen BIGINT,V_id_linea BIGINT,V_id_producto BIGINT)
BEGIN
		
	DROP TEMPORARY TABLE IF EXISTS TempProductos;
	
	CREATE TEMPORARY TABLE TempProductos( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT, 
		id_linea BIGINT,
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),
		stock DECIMAL(24,6)
	);
	
	
	/*
	INSERT INTO TempProductos(id_producto ,id_almacen,id_linea,stock_min,stock_max,stock)	
	SELECT pp.id_producto,s.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) 
	FROM cat_productos pp 
	LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND s.id_almacen = V_id_almacen
	where pp.tipo_producto = 'P';
	*/
	
	IF V_id_producto > 0 THEN
		INSERT INTO TempProductos(id_producto ,id_almacen,id_linea,stock_min,stock_max,stock)	
		SELECT pp.id_producto,s.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) 
		FROM cat_productos pp 
		LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND s.id_almacen = V_id_almacen
		WHERE pp.tipo_producto = 'P' and pp.id_producto = V_id_producto;
	END if;
	
	
	IF V_id_linea > 0 and V_id_producto = 0 THEN
		INSERT INTO TempProductos(id_producto ,id_almacen,id_linea,stock_min,stock_max,stock)	
		SELECT pp.id_producto,s.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) 
		FROM cat_productos pp 
		LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND s.id_almacen = V_id_almacen
		WHERE pp.tipo_producto = 'P' AND pp.id_linea = V_id_linea;
	END if;
	
	IF V_id_linea = 0 AND V_id_producto = 0 THEN
		INSERT INTO TempProductos(id_producto ,id_almacen,id_linea,stock_min,stock_max,stock)	
		SELECT pp.id_producto,s.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) 
		FROM cat_productos pp 
		LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND s.id_almacen = V_id_almacen
		WHERE pp.tipo_producto = 'P';
		
	end if;
	
	
	
	SELECT ps.id_producto ,p.codigo,p.descripcion,V_id_almacen AS id_almacen,ps.id_linea,ps.stock_min,ps.stock_max,ps.stock,0 modificado FROM TempProductos ps
	INNER JOIN cat_productos p ON p.id_producto = ps.id_producto;
	
	
END */$$
DELIMITER ;

/* Procedure structure for procedure `spPedidoSugerido` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spPedidoSugerido`(V_id_sucursal_origen BIGINT,V_id_sucursal BIGINT,V_FechaInicio DATETIME, V_FechaFin DATETIME, V_id_linea BIGINT,V_id_producto BIGINT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS TempVentas;
	
	CREATE TEMPORARY TABLE TempVentas( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT,
		id_linea BIGINT,
		cantidad DECIMAL(24,6)
		
	); 	
	
	DROP TEMPORARY TABLE IF EXISTS TempProductos;
	
	CREATE TEMPORARY TABLE TempProductos( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT, 
		id_linea BIGINT,
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),
		stock DECIMAL(24,6)
	);
	
	DROP TEMPORARY TABLE IF EXISTS Resultado;
	
	CREATE TEMPORARY TABLE Resultado( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT,
		id_linea BIGINT,
		/*nombre_sucursal VARCHAR(255),
		descripcion VARCHAR(255),
		codigo_barras VARCHAR(255),
		codigo VARCHAR(255),
		cantidad DECIMAL(24,6),*/
		/*nombre_linea VARCHAR(255),*/
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),
		stock_min_nvo DECIMAL(24,6),
		stock_max_nvo DECIMAL(24,6),
		stock DECIMAL(24,6),
		ventas DECIMAL(24,6),
		pedido_sugerido DECIMAL(24,6)
	);
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen,p.id_linea,SUM(vd.cantidad)
	FROM ventas_detalles vd
	INNER JOIN ventas v ON v.id_venta = vd.id_venta
	INNER JOIN cat_productos p ON p.id_producto = vd.id_producto
	WHERE v.fecha_venta BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A'
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen;	
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen_origen,p.id_linea,SUM(vd.cantidad)
	FROM movimientos_almacen_detalles vd
	INNER JOIN movimientos_almacen v ON v.id_movimiento = vd.id_movimiento
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	INNER JOIN cat_tiposmovimientos tm ON tm.id_tipomovimiento = v.id_tipomovimiento
	WHERE v.fecha_movimiento BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A' AND tm.tipo_movimiento = 4	
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen_origen;
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen,p.id_linea,SUM(vd.cantidad)
	FROM remisiones_detalles vd
	INNER JOIN remisiones v ON v.id_remision = vd.id_remision
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	WHERE v.fecha BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A'
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen;	
	
	/*IF V_id_empresa > 0 THEN
		DELETE FROM TempVentas WHERE id_empresa <> V_id_empresa;
	END IF;*/
		
	IF V_id_sucursal > 0 THEN
		DELETE FROM TempVentas WHERE id_sucursal <> V_id_sucursal;
	END IF;
	
	IF V_id_linea > 0 THEN
		DELETE FROM TempVentas WHERE id_linea <> V_id_linea;
	END IF;
	
	IF V_id_producto > 0 THEN
		DELETE FROM TempVentas WHERE id_producto <> V_id_producto;
	END IF;
	
	IF V_id_sucursal_origen = 2 then 
		DELETE FROM TempVentas WHERE id_linea in (51/*ADARA*/,163/*ALBELLA*/,45/*APPLE*/,30/*ARI*/,50/*BISSU*/,161/*CANAY COSMETICOS*/,
		150/*COLORE*/,146/*COLORI*/,164/*COLORTONE*/,29/*CRISTALEX*/,44/*CURTIS*/,145/*DE LA FUENTE*/,14/*DEVIBELL*/,
		120/*DISAVA*/,63/*DUO*/,26/*EROS NAILS*/,20/*ESTEFANIA*/,114/*G&K*/,59/*IMPORTACIONES GUADALAJARA*/,
		88/*INTMEX*/,37/*JAVIER MINA*/,40/*JDENIS*/,46/*JLASH*/,72/*JORDANA*/,52/*JULIA SANCHEZ*/,167/*L.A. COLORS*/,
		53/*LAKREEM*/,76/*MAXHI*/,95/*NAIL PRO*/,165/*NIN ACCESORIOS*/,47/*PACHS*/,
		112/*NIYA NAIL*/,136/*NUNN CARE*/,9/*ORGANIC NAIL*/,28/*OUTLET NAILS*/,60/*PERFECT NAIL*/,93/*PINK UP*/,153/*SANTILLAN*/,
		122/*SECRET COSMETICS*/,158/*STAR ONE*/,42 /*Caroline*/, 31 /*globusman*/);
	end if;
	
	IF V_id_sucursal_origen = 3 THEN 
		DELETE FROM TempVentas WHERE id_linea not IN (51/*ADARA*/,163/*ALBELLA*/,45/*APPLE*/,30/*ARI*/,50/*BISSU*/,161/*CANAY COSMETICOS*/,
		150/*COLORE*/,146/*COLORI*/,164/*COLORTONE*/,29/*CRISTALEX*/,44/*CURTIS*/,145/*DE LA FUENTE*/,14/*DEVIBELL*/,
		120/*DISAVA*/,63/*DUO*/,26/*EROS NAILS*/,20/*ESTEFANIA*/,114/*G&K*/,59/*IMPORTACIONES GUADALAJARA*/,
		88/*INTMEX*/,37/*JAVIER MINA*/,40/*JDENIS*/,46/*JLASH*/,72/*JORDANA*/,52/*JULIA SANCHEZ*/,167/*L.A. COLORS*/,
		53/*LAKREEM*/,76/*MAXHI*/,95/*NAIL PRO*/,165/*NIN ACCESORIOS*/,47/*PACHS*/,
		112/*NIYA NAIL*/,136/*NUNN CARE*/,9/*ORGANIC NAIL*/,28/*OUTLET NAILS*/,60/*PERFECT NAIL*/,93/*PINK UP*/,153/*SANTILLAN*/,
		122/*SECRET COSMETICS*/,158/*STAR ONE*/,42 /*Caroline*/, 31 /*globusman*/);
	END IF;
	
	
	INSERT INTO TempProductos(id_producto ,id_empresa,id_sucursal,id_almacen,id_linea,stock_min,stock_max,stock)	
	SELECT pp.id_producto,pp.id_empresa,pp.id_sucursal,pp.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) FROM(
	SELECT p.id_producto,a.id_empresa,a.id_sucursal,a.id_almacen,p.id_linea
	FROM cat_productos p, cat_almacenes a) pp 
	LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND pp.id_almacen = s.id_almacen;
	
	/*IF V_id_empresa > 0 THEN
		DELETE FROM TempProductos WHERE id_empresa <> V_id_empresa;
	END IF;*/
		
	IF V_id_sucursal > 0 THEN
		DELETE FROM TempProductos WHERE id_sucursal <> V_id_sucursal;
	END IF;
	
	IF V_id_linea > 0 THEN
		DELETE FROM TempProductos WHERE id_linea <> V_id_linea;
	END IF;
	
	IF V_id_producto > 0 THEN
		DELETE FROM TempProductos WHERE id_producto <> V_id_producto;
	END IF;
	
	
	IF V_id_sucursal_origen = 2 THEN 
		DELETE FROM TempProductos WHERE id_linea IN (51/*ADARA*/,163/*ALBELLA*/,45/*APPLE*/,30/*ARI*/,50/*BISSU*/,161/*CANAY COSMETICOS*/,
		150/*COLORE*/,146/*COLORI*/,164/*COLORTONE*/,29/*CRISTALEX*/,44/*CURTIS*/,145/*DE LA FUENTE*/,14/*DEVIBELL*/,
		120/*DISAVA*/,63/*DUO*/,26/*EROS NAILS*/,20/*ESTEFANIA*/,114/*G&K*/,59/*IMPORTACIONES GUADALAJARA*/,
		88/*INTMEX*/,37/*JAVIER MINA*/,40/*JDENIS*/,46/*JLASH*/,72/*JORDANA*/,52/*JULIA SANCHEZ*/,167/*L.A. COLORS*/,
		53/*LAKREEM*/,76/*MAXHI*/,95/*NAIL PRO*/,165/*NIN ACCESORIOS*/,47/*PACHS*/,
		112/*NIYA NAIL*/,136/*NUNN CARE*/,9/*ORGANIC NAIL*/,28/*OUTLET NAILS*/,60/*PERFECT NAIL*/,93/*PINK UP*/,153/*SANTILLAN*/,
		122/*SECRET COSMETICS*/,158/*STAR ONE*/,42 /*Caroline*/, 31 /*globusman*/);
	END IF;
	
	IF V_id_sucursal_origen = 3 THEN 
		DELETE FROM TempProductos WHERE id_linea NOT IN (51/*ADARA*/,163/*ALBELLA*/,45/*APPLE*/,30/*ARI*/,50/*BISSU*/,161/*CANAY COSMETICOS*/,
		150/*COLORE*/,146/*COLORI*/,164/*COLORTONE*/,29/*CRISTALEX*/,44/*CURTIS*/,145/*DE LA FUENTE*/,14/*DEVIBELL*/,
		120/*DISAVA*/,63/*DUO*/,26/*EROS NAILS*/,20/*ESTEFANIA*/,114/*G&K*/,59/*IMPORTACIONES GUADALAJARA*/,
		88/*INTMEX*/,37/*JAVIER MINA*/,40/*JDENIS*/,46/*JLASH*/,72/*JORDANA*/,52/*JULIA SANCHEZ*/,167/*L.A. COLORS*/,
		53/*LAKREEM*/,76/*MAXHI*/,95/*NAIL PRO*/,165/*NIN ACCESORIOS*/,47/*PACHS*/,
		112/*NIYA NAIL*/,136/*NUNN CARE*/,9/*ORGANIC NAIL*/,28/*OUTLET NAILS*/,60/*PERFECT NAIL*/,93/*PINK UP*/,153/*SANTILLAN*/,
		122/*SECRET COSMETICS*/,158/*STAR ONE*/,42 /*Caroline*/, 31 /*globusman*/);
	END IF;	
	
	INSERT INTO Resultado(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,stock_min,stock_max,stock,ventas)
	SELECT t1.id_producto,t1.id_empresa,t1.id_sucursal,t1.id_almacen,t1.id_linea,MAX(stock_min),MAX(stock_max),MAX(stock),SUM(t1.cantidad)
	FROM(	
		SELECT p.id_producto,p.id_empresa,p.id_sucursal,p.id_almacen,p.id_linea,stock_min,stock_max,stock,IFNULL(cantidad,0) AS cantidad FROM TempProductos p
		LEFT JOIN TempVentas v ON v.id_producto = p.id_producto AND v.id_empresa = p.id_empresa AND v.id_sucursal = p.id_sucursal
		AND v.id_almacen = p.id_almacen
	) t1		
	GROUP BY t1.id_producto,t1.id_empresa,t1.id_sucursal,t1.id_almacen,t1.id_linea;	
	
	
	UPDATE Resultado SET stock_min_nvo = ventas WHERE ventas > 0;
	
	UPDATE Resultado SET stock_max_nvo = ventas * 2 WHERE ventas > 0;
	
	UPDATE Resultado SET pedido_sugerido = stock_max - stock
	WHERE stock >= 0 AND stock <= stock_min;
		
	DELETE FROM Resultado WHERE pedido_sugerido < 1;
	
	/*DELETE FROM Resultado WHERE stock <= 0;*/
		
	SELECT t1.id_producto,t1.nombre_fiscal,t1.nombre_sucursal,t1.nombre_almacen,t1.descripcion,t1.codigo_barras,t1.codigo,t1.nombre_linea,MAX(t1.stock_min) AS stock_min,MAX(t1.stock_max) AS stock_max,MAX(t1.stock_min_nvo) AS stock_min_nvo,MAX(t1.stock_max_nvo) AS stock_max_nvo,MAX(t1.stock) AS stock,SUM(t1.ventas) AS ventas,MAX(t1.pedido_sugerido) AS pedido_sugerido
	FROM (	
	SELECT r.id_producto,r.id_empresa,r.id_sucursal,r.id_almacen,r.id_linea,e.nombre_fiscal,su.nombre_sucursal,al.nombre_almacen,p.descripcion,p.codigo_barras,p.codigo,l.nombre_linea,IFNULL(r.stock_min,0) AS stock_min,IFNULL(r.stock_max,0) AS stock_max,IFNULL(r.stock_min_nvo,0) AS stock_min_nvo,IFNULL(r.stock_max_nvo,0) AS stock_max_nvo,IFNULL(r.stock,0) AS stock,IFNULL(r.ventas,0) AS ventas,IFNULL(r.pedido_sugerido,0) AS pedido_sugerido FROM Resultado r
	INNER JOIN cat_productos p ON p.id_producto = r.id_producto
	INNER JOIN cat_lineas l ON l.id_linea = r.id_linea
	INNER JOIN cat_empresas e ON e.id_empresa = r.id_empresa
	INNER JOIN cat_sucursales su ON su.id_sucursal = r.id_sucursal
	INNER JOIN cat_almacenes al ON al.id_almacen = r.id_almacen
	) t1
	Where t1.pedido_sugerido > 0
	GROUP BY t1.id_producto,t1.id_empresa,t1.id_sucursal,t1.id_almacen,t1.id_linea,t1.nombre_fiscal,t1.nombre_sucursal,t1.nombre_almacen,t1.descripcion,t1.codigo_barras,t1.codigo,t1.nombre_linea
	ORDER BY t1.id_empresa,t1.id_sucursal,t1.id_almacen,t1.codigo;
	
	
	
END */$$
DELIMITER ;

/* Procedure structure for procedure `spReporteMovimientosBancos` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spReporteMovimientosBancos`(V_id_empresa BIGINT,V_id_sucursal BIGINT,V_FechaInicio DATETIME, V_FechaFin DATETIME, V_id_concepto BIGINT,V_id_producto BIGINT)
BEGIN
	Declare  V_Ingresos DECIMAL(24,6);
	DECLARE V_Egresos DECIMAL(24,6);
	DECLARE V_SaldoAnterior DECIMAL(24,6);
	
	DROP TEMPORARY TABLE IF EXISTS Movimientos;
	
	CREATE TEMPORARY TABLE Movimientos( 
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_concepto BIGINT,
		id_chequera BIGINT,
		fecha DATETIME,
		serie VARCHAR(20),
		folio int(20),
		observaciones VARCHAR(200),
		tipo_movimiento TINYINT,
		tipo_origen TINYINT,
		importe DECIMAL(24,6)
		
	);
	
	DROP TEMPORARY TABLE IF EXISTS Resultado;
	
	CREATE TEMPORARY TABLE Resultado( 
		id_movimiento_banco BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_concepto BIGINT,
		id_chequera BIGINT,
		fecha datetime,
		serie VARCHAR(20),
		folio INT(20),
		observaciones VARCHAR(200),
		nombre_concepto VARCHAR(255),
		tipo_movimiento tinyint,
		tipo_origen TINYINT,
		saldoanterior DECIMAL(24,6),
		ingresos DECIMAL(24,6),
		egresos DECIMAL(24,6),
		saldo DECIMAL(24,6),
		tipo TINYINT
	);
	
	
	/*
	IF V_id_producto > 0 THEN
		DELETE FROM TempProductos WHERE id_producto <> V_id_producto;
	END IF;
	*/
	
	insert into Movimientos(serie,folio,id_empresa,id_sucursal,id_concepto,id_chequera,fecha,observaciones,tipo_movimiento,tipo_origen,importe)
	Select m.serie,m.folio,m.id_empresa,m.id_sucursal,m.id_concepto,m.id_chequera,m.fecha,m.observaciones,m.tipo_movimiento,m.tipo_origen,m.importe
	FROM movimientos_bancos	m	
	WHERE m.fecha BETWEEN V_FechaInicio AND V_FechaFin AND m.status = 'A';
	
	IF V_id_sucursal > 0 THEN
		DELETE FROM Movimientos WHERE id_sucursal <> V_id_sucursal;
	END IF;	
	
	IF V_id_concepto > 0 THEN
		DELETE FROM Movimientos WHERE id_concepto <> V_id_concepto;
	END IF;	
	
	set V_Ingresos = (
	SELECT IFNULL(SUM(importe),0)
	FROM movimientos_bancos WHERE tipo_movimiento = 1 AND fecha < V_FechaInicio);
	
	SET V_Egresos = (
	SELECT ifnull(SUM(importe),0)
	FROM movimientos_bancos WHERE tipo_movimiento = 2 AND fecha < V_FechaInicio);
	
	set V_SaldoAnterior = V_Ingresos - V_Egresos;
	
	insert into Resultado(fecha,nombre_concepto,saldoanterior,ingresos,egresos,saldo,tipo)
	values (V_FechaInicio,'SALDO ANTERIOR',V_SaldoAnterior,0,0,V_SaldoAnterior,1);
		
	INSERT INTO Resultado(serie,folio,id_empresa,id_sucursal,id_concepto,nombre_concepto,id_chequera,fecha,observaciones,tipo_movimiento,tipo_origen,saldoanterior,ingresos,egresos,saldo,tipo)
	SELECT serie,folio,id_empresa,id_sucursal,m.id_concepto,c.descripcion,id_chequera,fecha,observaciones,tipo_movimiento,tipo_origen,V_SaldoAnterior,CASE tipo_movimiento WHEN 1 THEN importe ELSE 0 END AS ingresos,
	CASE tipo_movimiento WHEN 2 THEN importe ELSE 0 END AS egresos,0,2 
	FROM Movimientos m
	LEFT JOIN cat_conceptos c ON c.id_concepto = m.id_concepto
	GROUP BY id_empresa,id_sucursal,id_concepto,id_chequera,fecha,tipo_movimiento,tipo_origen
	ORDER BY fecha;	
	
	
	
	/*
	update usuario nu
	set nu.bicikilometros=(select sum(be.km)
	from biker_rutasusuario nra, biker_etapa be
	where be.codigo = nra.codigoruta and
	      nra.codigousuario = nu.codigo) 
	
*/
	
	Select DATE_FORMAT(r.fecha,'%d/%m/%Y') as fecha,nombre_concepto,ifnull(r.observaciones,'') as observaciones,IFNULL(s.nombre_sucursal,'') as nombre_sucursal,ifnull(ch.descripcion,'') as nombre_chequera,case r.tipo_origen when 1 then 'EFECTIVO' WHEN 2 THEN 'BANCOS' else '' END AS tipo_origen,r.saldoanterior,r.ingresos,r.egresos,r.saldo,r.tipo 
	FROM Resultado r		
	LEFT join cat_sucursales s on s.id_sucursal = r.id_sucursal
	left join cat_chequeras ch on ch.id_chequera = r.id_chequera	
	ORDER BY r.fecha ASC;	
	
	
	
	
END */$$
DELIMITER ;

/* Procedure structure for procedure `spReporteVentas` */

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spReporteVentas`(V_id_empresa BIGINT,V_id_sucursal BIGINT,V_FechaInicio DATETIME, V_FechaFin DATETIME, V_id_linea BIGINT,V_id_producto BIGINT, V_Agrupado TINYINT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS TempVentas;
	
	CREATE TEMPORARY TABLE TempVentas( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_linea BIGINT,
		nombre_sucursal VARCHAR(255),
		descripcion VARCHAR(255),
		codigo_barras VARCHAR(255),
		codigo VARCHAR(255),
		cantidad DECIMAL(24,6),
		nombre_linea VARCHAR(255),
		stock DECIMAL(24,6),
		precio DECIMAL(24,6),
		subtotal DECIMAL(24,6)
	); 	
	DROP TEMPORARY TABLE IF EXISTS TempResultado;	
	
	CREATE TEMPORARY TABLE TempResultado( 
		id_producto BIGINT,
		id_linea BIGINT,
		nombre_sucursal VARCHAR(255),
		descripcion VARCHAR(255),
		codigo_barras VARCHAR(255),
		codigo VARCHAR(255),
		ventas DECIMAL(24,6),
		nombre_linea VARCHAR(255),
		stock DECIMAL(24,6),
		precio DECIMAL(24,6),
		subtotal DECIMAL(24,6)
	); 	
	
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_linea,nombre_sucursal,descripcion,codigo_barras,codigo,cantidad,nombre_linea,stock,precio,subtotal)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,p.id_linea,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS 
	stock, MAX(vd.precio) AS precio, SUM(vd.total) AS subtotal
	FROM ventas_detalles vd
	INNER JOIN ventas v ON v.id_venta = vd.id_venta
	INNER JOIN cat_productos p ON p.id_producto = vd.id_producto
	INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
	INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
	LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.id_almacen
	WHERE v.fecha_venta BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A'
	GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_sucursal,v.id_almacen,su.nombre_sucursal;
	/*
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_linea,nombre_sucursal,descripcion,codigo_barras,codigo,cantidad,nombre_linea,stock,precio,subtotal)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,p.id_linea,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS stock,
	MAX(vd.costo) AS precio, SUM(vd.total) AS subtotal
	FROM movimientos_almacen_detalles vd
	INNER JOIN movimientos_almacen v ON v.id_movimiento = vd.id_movimiento
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
	INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
	INNER JOIN cat_tiposmovimientos tm ON tm.id_tipomovimiento = v.id_tipomovimiento
	LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.`id_almacen_origen`
	WHERE v.fecha_movimiento BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A' AND tm.tipo_movimiento = 4
	GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_sucursal,v.id_almacen_origen,su.nombre_sucursal;
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_linea,nombre_sucursal,descripcion,codigo_barras,codigo,cantidad,nombre_linea,stock,precio,subtotal)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,p.id_linea,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS stock,
	MAX(vd.costo) AS precio, SUM(vd.total) AS subtotal
	FROM remisiones_detalles vd
	INNER JOIN remisiones v ON v.id_remision = vd.id_remision
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
	INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
	LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.`id_almacen`
	WHERE v.fecha BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A'
	GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_sucursal,v.id_almacen,su.nombre_sucursal;			
	*/
	IF V_id_empresa > 0 THEN
		DELETE FROM TempVentas WHERE id_empresa <> V_id_empresa;
	END IF;
		
	IF V_id_sucursal > 0 THEN
		DELETE FROM TempVentas WHERE id_sucursal <> V_id_sucursal;
	END IF;
	
	IF V_id_linea > 0 THEN
		DELETE FROM TempVentas WHERE id_linea <> V_id_linea;
	END IF;
	
	IF V_id_producto > 0 THEN
		DELETE FROM TempVentas WHERE id_producto <> V_id_producto;
	END IF;
	
	INSERT INTO TempResultado(id_producto,id_linea,nombre_sucursal,descripcion,codigo_barras,codigo,ventas,nombre_linea,stock,precio,subtotal)
	SELECT t1.id_producto,t1.id_sucursal,t1.nombre_sucursal,t1.descripcion,t1.codigo_barras,t1.codigo,SUM(t1.cantidad) AS ventas,t1.nombre_linea,SUM(DISTINCT t1.stock) AS stock,MAX(t1.precio) AS precio, SUM(t1.subtotal) AS subtotal
	FROM  (
		SELECT id_producto,id_sucursal,nombre_sucursal,descripcion,codigo_barras,codigo,cantidad,nombre_linea,stock,precio,subtotal FROM TempVentas
		) t1
		GROUP BY t1.id_producto,t1.codigo,t1.descripcion,t1.id_sucursal,t1.nombre_sucursal     
		ORDER BY t1.descripcion,t1.nombre_sucursal;
	
	
		
	IF V_Agrupado = 1 THEN
		SET @PivotQuery = NULL;
		SELECT
		  GROUP_CONCAT( DISTINCT
		    CONCAT(
		      ' sum(IF(nombre_sucursal = ''',
		      nombre_sucursal,
		      ''', stock, 0)) AS ''',
		      t.nombre_sucursal,''''
		    )
		  ) INTO @PivotQuery
		FROM
		  (SELECT
		     nombre_sucursal
		   FROM     
		     TempResultado
		) t;
		SET @PivotQuery = CONCAT('SELECT t1.codigo as CODIGO,t1.descripcion as DESCRIPCION,t1.nombre_linea as NOMBRE_LINEA,t1.codigo_barras AS CODIGO_BARRAS,t1.codigo AS CODIGO,SUM(t1.ventas) as VENTAS,', @PivotQuery, 
			' FROM TempResultado t1 group by t1.descripcion,t1.codigo_barras,t1.codigo,t1.nombre_linea');
		PREPARE statement FROM @PivotQuery;
		EXECUTE statement;
		DEALLOCATE PREPARE statement;
	END IF;
	
	IF V_Agrupado = 0 THEN
		SELECT t1.id_producto,t1.nombre_sucursal,t1.descripcion,t1.codigo_barras,t1.codigo,t1.ventas,t1.nombre_linea,
		t1.stock, t1.precio, t1.subtotal 
		FROM TempResultado t1
		ORDER BY t1.descripcion,t1.nombre_sucursal;
	END IF;	
	
	
END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
