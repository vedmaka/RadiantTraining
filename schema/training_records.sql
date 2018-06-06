CREATE TABLE /*_*/training_records
(
  id       int not null auto_increment,
  page_id  int           not null,
  user_id  int           not null,
  block_id int,
  block_text_id  varchar(512)  not null,
  created_at int not null,
  updated_at int not null,
  PRIMARY KEY (id),
  INDEX (id)
) /* $wgDbTableOptions */;
