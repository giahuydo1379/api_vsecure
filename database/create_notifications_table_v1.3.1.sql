-- auto-generated definition
create table notifications
(
  id              int auto_increment
    primary key,
  device_token_id int                                    not null,
  action          varchar(255)                           null,
  model_device    varchar(255)                           null,
  push_time       datetime default '0000-00-00 00:00:00' not null on update CURRENT_TIMESTAMP,
  constraint notifications_ibfk_1
    foreign key (device_token_id) references device_token (id)
)
  collate = utf8mb4_unicode_ci;

create index device_token_id
  on notifications (device_token_id);
