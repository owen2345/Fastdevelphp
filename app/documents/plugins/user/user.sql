create table user
(
   id_user              int not null,
   name_user            varchar(50),
   lastname_user        varchar(100),
   email_user           text,
   pass_user            text,
   role_user            int,
   createat_user        timestamp,
   avatar_user          text,
   code_user            varchar(50),
   isverified_user      bool,
   primary key (id_user)
);