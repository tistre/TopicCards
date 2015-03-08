# TopicBank database schema for MySQL
#
# Run the install/db_schema_mysql.php file through PHP (with your preferred 
# table prefix as the only argument) for a valid SQL schema:
#
# $ php install/db_schema_mysql.php topicbank_ > topicbank_schema.sql
#
# Make sure to use utf8mb4 for the character set:
#
# mysql> create database topicbank_test character set utf8mb4 collate utf8mb4_bin;
#
# This requires MySQL >= 5.5 and these my.cnf settings:
#
# [mysqld]
# innodb_file_per_table = 1
# innodb_file_format = barracuda
# innodb_large_prefix = 1
#
# When connecting to the database, also make sure to use charset=utf8mb4
# and run this init command:
#
# SET SESSION sql_mode='STRICT_ALL_TABLES';
#

<?php $prefix = strtolower($argv[ 1 ]); ?>


create table <?=$prefix?>topic
(
    topic_created timestamp default CURRENT_TIMESTAMP not null, 
    topic_id varchar(64) not null, 
    topic_version integer default 1 not null, 
    topic_updated timestamp not null,
    primary key <?=$prefix?>topic_id (topic_id)
)
engine = InnoDB character set utf8mb4 collate utf8mb4_bin row_format DYNAMIC;


create table <?=$prefix?>subject
(
    subject_id bigint not null AUTO_INCREMENT,
    subject_topic varchar(64) not null, 
    subject_value varchar(1000) not null,
    subject_islocator tinyint not null, 
    primary key <?=$prefix?>subject_id (subject_id),
    foreign key <?=$prefix?>subject_topic (subject_topic) references <?=$prefix?>topic (topic_id) on delete cascade
)
engine = InnoDB character set utf8mb4 collate utf8mb4_bin row_format DYNAMIC;

create index <?=$prefix?>subject_topic_idx on <?=$prefix?>subject (subject_topic);
create index <?=$prefix?>subject_islocator_idx on <?=$prefix?>subject (subject_islocator);
create index <?=$prefix?>subject_value_idx on <?=$prefix?>subject (subject_value);


create table <?=$prefix?>type
(
    type_id bigint not null AUTO_INCREMENT,
    type_topic varchar(64) not null, 
    type_type varchar(64) not null,
    primary key <?=$prefix?>type_id (type_id),
    foreign key <?=$prefix?>type_topic (type_topic) references <?=$prefix?>topic (topic_id) on delete cascade,
    foreign key <?=$prefix?>type_type (type_type) references <?=$prefix?>topic (topic_id)
)
engine = InnoDB character set utf8mb4 collate utf8mb4_bin row_format DYNAMIC;

create index <?=$prefix?>type_topic_idx on <?=$prefix?>type (type_topic);


create table <?=$prefix?>name
(
    name_id bigint not null AUTO_INCREMENT,
    name_topic varchar(64) not null, 
    name_type varchar(64) not null, 
    name_value varchar(1000) not null,
    name_reifier varchar(64) null, 
    primary key <?=$prefix?>name_id (name_id),
    foreign key <?=$prefix?>name_topic (name_topic) references <?=$prefix?>topic (topic_id) on delete cascade,
    foreign key <?=$prefix?>name_type (name_type) references <?=$prefix?>topic (topic_id),
    foreign key <?=$prefix?>name_reifier (name_reifier) references <?=$prefix?>topic (topic_id)
)
engine = InnoDB character set utf8mb4 collate utf8mb4_bin row_format DYNAMIC;

create index <?=$prefix?>name_topic_idx on <?=$prefix?>name (name_topic);


create table <?=$prefix?>occurrence
(
    occurrence_id bigint not null AUTO_INCREMENT,
    occurrence_topic varchar(64) not null, 
    occurrence_type varchar(64) not null, 
    occurrence_datatype varchar(64) not null, 
    occurrence_value mediumtext not null,
    occurrence_reifier varchar(64) null, 
    primary key <?=$prefix?>occurrence_id (occurrence_id),
    foreign key <?=$prefix?>occurrence_topic (occurrence_topic) references <?=$prefix?>topic (topic_id) on delete cascade,
    foreign key <?=$prefix?>occurrence_type (occurrence_type) references <?=$prefix?>topic (topic_id),
    foreign key <?=$prefix?>occurrence_datatype (occurrence_datatype) references <?=$prefix?>topic (topic_id),
    foreign key <?=$prefix?>occurrence_reifier (occurrence_reifier) references <?=$prefix?>topic (topic_id)
)
engine = InnoDB character set utf8mb4 collate utf8mb4_bin row_format DYNAMIC;

create index <?=$prefix?>occurrence_topic_idx on <?=$prefix?>occurrence (occurrence_topic);


create table <?=$prefix?>association
(
    association_created timestamp default CURRENT_TIMESTAMP not null, 
    association_id varchar(64) not null, 
    association_version integer default 1 not null, 
    association_updated timestamp not null,
    association_type varchar(64) not null, 
    association_reifier varchar(64) null, 
    primary key <?=$prefix?>association_id (association_id),
    foreign key <?=$prefix?>association_type (association_type) references <?=$prefix?>topic (topic_id),
    foreign key <?=$prefix?>association_reifier (association_reifier) references <?=$prefix?>topic (topic_id)
)
engine = InnoDB character set utf8mb4 collate utf8mb4_bin row_format DYNAMIC;

create index <?=$prefix?>association_reifier_idx on <?=$prefix?>association (association_reifier);


create table <?=$prefix?>role
(
    role_id bigint not null AUTO_INCREMENT,
    role_association varchar(64) not null, 
    role_player varchar(64) not null, 
    role_type varchar(64) not null, 
    role_reifier varchar(64) null, 
    primary key <?=$prefix?>role_id (role_id),
    foreign key <?=$prefix?>role_association (role_association) references <?=$prefix?>association (association_id) on delete cascade,
    foreign key <?=$prefix?>role_player (role_player) references <?=$prefix?>topic (topic_id),
    foreign key <?=$prefix?>role_type (role_type) references <?=$prefix?>topic (topic_id),
    foreign key <?=$prefix?>role_reifier (role_reifier) references <?=$prefix?>topic (topic_id)
)
engine = InnoDB character set utf8mb4 collate utf8mb4_bin row_format DYNAMIC;

create index <?=$prefix?>role_association_idx on <?=$prefix?>role (role_association);
create index <?=$prefix?>role_player_idx on <?=$prefix?>role (role_player);


create table <?=$prefix?>scope
(
    scope_id bigint not null AUTO_INCREMENT,
    scope_association varchar(64) null, 
    scope_name bigint null, 
    scope_occurrence bigint null, 
    scope_scope varchar(64) not null, 
    primary key <?=$prefix?>scope_id (scope_id),
    foreign key <?=$prefix?>scope_association (scope_association) references <?=$prefix?>association (association_id) on delete cascade,
    foreign key <?=$prefix?>scope_name (scope_name) references <?=$prefix?>name (name_id) on delete cascade,
    foreign key <?=$prefix?>scope_occurrence (scope_occurrence) references <?=$prefix?>occurrence (occurrence_id) on delete cascade,
    foreign key <?=$prefix?>scope_scope (scope_scope) references <?=$prefix?>topic (topic_id)
)
engine = InnoDB character set utf8mb4 collate utf8mb4_bin row_format DYNAMIC;

create index <?=$prefix?>scope_name_idx on <?=$prefix?>scope (scope_name);


insert into <?=$prefix?>topic (topic_id) values ('a8ddd773-7ad2-4b44-908c-e0dc7d9d9802');
insert into <?=$prefix?>name (name_topic, name_type, name_value) values ('a8ddd773-7ad2-4b44-908c-e0dc7d9d9802', 'a8ddd773-7ad2-4b44-908c-e0dc7d9d9802', 'Name');
insert into <?=$prefix?>subject (subject_topic, subject_value) values ('a8ddd773-7ad2-4b44-908c-e0dc7d9d9802', 'http://schema.org/name');

insert into <?=$prefix?>topic (topic_id) values ('722ac838-4534-4a46-82d1-a60365e37985');
insert into <?=$prefix?>name (name_topic, name_type, name_value) values ('722ac838-4534-4a46-82d1-a60365e37985', 'a8ddd773-7ad2-4b44-908c-e0dc7d9d9802', 'Concept');
insert into <?=$prefix?>subject (subject_topic, subject_value) values ('722ac838-4534-4a46-82d1-a60365e37985', 'http://www.w3.org/2004/02/skos/core#Concept');

insert into <?=$prefix?>type (type_topic, type_type) values ('a8ddd773-7ad2-4b44-908c-e0dc7d9d9802', '722ac838-4534-4a46-82d1-a60365e37985');
