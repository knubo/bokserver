create table if not exists bok_log(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  occured TIMESTAMP,
  username varchar(25),
  category varchar(10),
  action varchar(10),
  message TEXT
);


create table if not exists bok_user(
  username varchar(25) PRIMARY KEY,
  pass varchar(15),
  person varchar(40),
  readonly tinyint,
  reducedwrite tinyint

);

CREATE TABLE IF NOT EXISTS bok_standard (
   id VARCHAR(20) NOT NULL PRIMARY KEY,
   value VARCHAR(100)
);
