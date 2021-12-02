/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 5.6.51-cll-lve : Database - erp_master
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`erp_master` /*!40100 DEFAULT CHARACTER SET latin1 */;

/*Table structure for table `cat_corporativos` */

DROP TABLE IF EXISTS `cat_corporativos`;

CREATE TABLE `cat_corporativos` (
  `id_corporativo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_corporativo` varchar(150) DEFAULT NULL,
  `bd_corporativo` varchar(50) DEFAULT NULL,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  PRIMARY KEY (`id_corporativo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `cat_corporativos` */

insert  into `cat_corporativos`(`id_corporativo`,`nombre_corporativo`,`bd_corporativo`,`status`) values 
(3,'Pruebas','erp_corporativopruebas','A'),
(2,'LA GRAN BELLEZA','erp_lagranbelleza','A'),
(4,'ANSEL','erp_ansel','A'),
(5,'BEACH BARBER','erp_beachbarber','A');

/*Table structure for table `cat_usuarios` */

DROP TABLE IF EXISTS `cat_usuarios`;

CREATE TABLE `cat_usuarios` (
  `id_usuario` varchar(80) NOT NULL,
  `nombre_usuario` varchar(200) DEFAULT NULL,
  `esadmin` tinyint(1) DEFAULT '0',
  `fecha_modif` datetime DEFAULT NULL,
  `tipo_usuario` tinyint(4) DEFAULT '0' COMMENT '0=web,1=aplicacion,2=ambos',
  PRIMARY KEY (`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `cat_usuarios` */

insert  into `cat_usuarios`(`id_usuario`,`nombre_usuario`,`esadmin`,`fecha_modif`,`tipo_usuario`) values 
('admin@mierpweb.mx','Administrador',2,'2016-02-19 19:47:50',0),
('lhuerta@hotmail.com','LUIS HUERTA',0,'2017-05-11 11:58:23',0),
('inter@hotmail.com','INTERNACIONAL',0,NULL,0),
('insur@hotmail.com','INSURGENTES',0,NULL,0),
('prueba@hotmail.com','Usuario Prueba',0,NULL,1),
('mar@hotmail.com','LEY MAR',0,NULL,0),
('ansel@hotmail.com','ANSEL',0,NULL,0),
('gua@hotmail.com','GUAMUCHIL',0,NULL,0),
('asesores@outlook.com','Asesores',0,NULL,0),
('ventas@hotmail.com','Ventas Vendedores',0,NULL,0),
('rosa@hotmail.com','ROSA MAR',0,NULL,0),
('jorge@beachbarber.com','JORGE CISNEROS',0,NULL,0),
('caja@beachbarber.com','CAJA',0,NULL,0),
('abel@hotmail.com','ABEL BORGUEZ',0,NULL,0),
('admin@erp.mx','admin prueba',2,NULL,0);

/*Table structure for table `cat_usuarios_corporativos` */

DROP TABLE IF EXISTS `cat_usuarios_corporativos`;

CREATE TABLE `cat_usuarios_corporativos` (
  `id_usuario` varchar(100) NOT NULL,
  `id_corporativo` int(11) NOT NULL,
  `pass` blob,
  `esadmin` tinyint(1) DEFAULT '0',
  `fecha_modif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`,`id_corporativo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `cat_usuarios_corporativos` */

insert  into `cat_usuarios_corporativos`(`id_usuario`,`id_corporativo`,`pass`,`esadmin`,`fecha_modif`) values 
('admin@mierpweb.mx',1,'9k‡¿ŸÑ\Z»”µ%N?π',0,'2016-02-19 19:52:39'),
('lhuerta@hotmail.com',2,'Ì¿°˛h	ìÏ&⁄4t4	ﬂ',0,'2017-05-11 12:03:01'),
('inter@hotmail.com',2,'Ò˘∞Å÷áó´◊ì¡∫o§ò)',0,NULL),
('insur@hotmail.com',2,'\"3\0°\'íÔdü˝ehê',0,NULL),
('prueba@hotmail.com',3,'9k‡¿ŸÑ\Z»”µ%N?π',0,NULL),
('mar@hotmail.com',2,'˘\"‰˜∫óc%VëaÔèÀa',0,NULL),
('ansel@hotmail.com',4,'Êhè´6◊|^¥dî08',0,NULL),
('gua@hotmail.com',2,'ûîC±É3O¨L†G†•∆',0,NULL),
('jorge@beachbarber.com',5,'ü≠πú oÔº.rc˘3',0,NULL),
('ventas@hotmail.com',2,'£òÄ~Â5ö(∫…πwç§N',0,NULL),
('rosa@hotmail.com',2,'≤4)ÁfR\"üÏd[»{‡™',0,NULL),
('caja@beachbarber.com',5,'◊éWÉ¿G¯PY≠ô3î0‘•',0,NULL),
('admin@erp.mx',1,'9k√†√Ä√ô‚Äû\Z√à√ì¬µ%N?¬π',0,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
