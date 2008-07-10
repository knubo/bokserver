create table bok_author(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
); 

create table bok_translator(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
); 

create table bok_illustrator(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
); 

create table bok_book(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
);

create table book_owner(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  book_id INTEGER(8) UNSIGNED,
  username 
)

create table book_placement(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
); 

create table book_category(
  id INTEGER(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
);

create table book_owner(
  book_id INTEGER(8) UNSIGNED,
  user_id INTEGER(8) UNSIGNED
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
