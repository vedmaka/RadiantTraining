CREATE TABLE /*_*/training_blocks
(
  id       int not null auto_increment,
  page_id  int           not null,
  block_id varchar(512)  not null,
  title    varchar(1024) not null,
  created_at int not null,
  updated_at int not null,
  PRIMARY KEY (id),
  INDEX (id)
) /* $wgDbTableOptions */;
