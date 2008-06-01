CREATE TABLE usuarios (
  id int(5) NOT NULL auto_increment,
  login varchar(10) NOT NULL,
  pass varchar(20) NOT NULL,
  op char(2) NOT NULL default 'U',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE trabajos(
  id int(5) NOT NULL auto_increment,
  login varchar(10) NOT NULL,
  tserver varchar(25) NOT NULL,
  tuser varchar(25) NOT NULL,
  tpass varchar(25) NOT NULL,
  parcela int(2),
  edificio int(2),
  tipo char(2),
  terminado char(2),
  ip varchar(20),
  fechaq datetime,
  fechat datetime,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
  
 