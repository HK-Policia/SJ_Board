create table sjb_all_forum (
  id       int not null auto_increment primary key,
  kind     char(10) not null,
  Nickname char(40) not null,
  password char(100) default null,
  date     datetime not null,
  title    char(30) not null,
  content  longtext not null,
  ip       int not null,
  hits     int not null
);


create table sjb_all_reply (
  id        int not null auto_increment primary key,
  contentid int not null,
  Nickname  char(40) not null,
  password char(100) default null,
  date      datetime not null,
  ip        int not null,
  comment   text not null,
  rp_img    char(100) not null,
  re_reply_chk int not null,
  hold      char(1) not null
);