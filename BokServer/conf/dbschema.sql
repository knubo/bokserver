create table IF NOT EXISTS bok_person(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  firstname varchar(40),
  lastname varchar(40),
  search1 varchar(80),
  search2 varchar(80),
  illustrator tinyint,
  translator tinyint,
  author tinyint,
  editor tinyint
);


CREATE INDEX search1index USING RTREE ON bok_person (search1);
CREATE INDEX search2index USING RTREE ON bok_person (search2);

create table IF NOT EXISTS book_publisher(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name varchar(40)
);

create table IF NOT EXISTS book_subtitle(
  book_id INTEGER(8) UNSIGNED,
  number INTEGER(2) UNSIGNED,
  title varchar(40)
);

create table IF NOT EXISTS bok_book(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usernumber INTEGER(8),
  title varchar(40),
  org_title varchar(40),
  ISBN varchar(40),
  author_id INTEGER(8) UNSIGNED,
  coauthor_id INTEGER(8) UNSIGNED,
  illustrator_id INTEGER(8) UNSIGNED,
  translator_id INTEGER(8) UNSIGNED,
  editor_id INTEGER(8) UNSIGNED,
  publisher_id INTEGER(8) UNSIGNED,
  price NUMERIC(8,2) UNSIGNED,
  published_year INTEGER(4) UNSIGNED,
  written_year INTEGER(4) UNSIGNED,
  category_id INTEGER(4) UNSIGNED,
  placement_id INTEGER(8) UNSIGNED,
  edition INTEGER(4) UNSIGNED,
  impression INTEGER(4) UNSIGNED,
  series varchar(40),
  number_in_series INTEGER(4) UNSIGNED
);

create table IF NOT EXISTS book_placement(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  placement varchar(40) UNIQUE,
  text varchar(40)
); 

create table IF NOT EXISTS book_category(
  id INTEGER(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name varchar(25) UNIQUE
);

create table if not exists bok_log(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  occured TIMESTAMP,
  username varchar(25),
  category varchar(10),
  action varchar(10),
  message TEXT
);

create table if not exists bok_user(
  username varchar(25) UNIQUE,
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pass varchar(15),
  person varchar(40),
  readonly tinyint,
  reducedwrite tinyint

);

CREATE TABLE IF NOT EXISTS bok_standard (
   id VARCHAR(20) NOT NULL PRIMARY KEY,
   value VARCHAR(100)
);
