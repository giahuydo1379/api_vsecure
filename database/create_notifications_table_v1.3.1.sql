-- auto-generated definition
create table notifications
(
  id           int auto_increment
    primary key,
  dooralarm_id int                                not null,
  action       varchar(255)                       null,
  mac_address  varchar(100)                       not null,
  push_time    datetime default CURRENT_TIMESTAMP not null
);

