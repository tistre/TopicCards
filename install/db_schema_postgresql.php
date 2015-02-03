-- TopicBank database schema for Postgresql
--
-- Run the install/db_schema_postgresql.php file through PHP (with your preferred 
-- table prefix as the only argument) for a valid SQL schema:
--
-- $ php install/db_schema_postgresql.php topicbank > topicbank_schema.sql
--
-- Make sure to use UNICODE for the character set:
--
-- createdb -E UNICODE topicbank_test
--

<?php $prefix = strtolower($argv[ 1 ]); ?>


create table <?=$prefix?>topic
(
    topic_created timestamp default 'now' not null, 
    topic_id varchar(64) not null primary key, 
    topic_version integer default 1 not null, 
    topic_updated timestamp not null,
    topic_isreifier smallint default 0 not null
);


create table <?=$prefix?>subject
(
    subject_id bigserial not null primary key,
    subject_topic varchar(64) not null references <?=$prefix?>topic (topic_id) on delete cascade, 
    subject_value varchar(1000) not null,
    subject_islocator smallint not null
);

create index on <?=$prefix?>subject (subject_topic);
create index on <?=$prefix?>subject (subject_islocator);


create table <?=$prefix?>type
(
    type_id bigserial not null primary key,
    type_topic varchar(64) not null references <?=$prefix?>topic (topic_id) on delete cascade, 
    type_type varchar(64) not null references <?=$prefix?>topic (topic_id)
);

create index on <?=$prefix?>type (type_topic);


create table <?=$prefix?>name
(
    name_id bigserial not null primary key,
    name_topic varchar(64) not null references <?=$prefix?>topic (topic_id) on delete cascade, 
    name_type varchar(64) not null references <?=$prefix?>topic (topic_id), 
    name_value varchar(1000) not null,
    name_reifier varchar(64) null references <?=$prefix?>topic (topic_id)
);

create index on <?=$prefix?>name (name_topic);


create table <?=$prefix?>occurrence
(
    occurrence_id bigserial not null primary key,
    occurrence_topic varchar(64) not null references <?=$prefix?>topic (topic_id) on delete cascade, 
    occurrence_type varchar(64) not null references <?=$prefix?>topic (topic_id), 
    occurrence_datatype varchar(64) not null references <?=$prefix?>topic (topic_id), 
    occurrence_value text not null,
    occurrence_reifier varchar(64) null references <?=$prefix?>topic (topic_id)
);

create index on <?=$prefix?>occurrence (occurrence_topic);


create table <?=$prefix?>association
(
    association_created timestamp default 'now' not null, 
    association_id varchar(64) not null primary key, 
    association_version integer default 1 not null, 
    association_updated timestamp not null,
    association_type varchar(64) not null references <?=$prefix?>topic (topic_id), 
    association_reifier varchar(64) null references <?=$prefix?>topic (topic_id)
);


create table <?=$prefix?>role
(
    role_id bigserial not null primary key,
    role_association varchar(64) not null references <?=$prefix?>association (association_id) on delete cascade, 
    role_player varchar(64) not null references <?=$prefix?>topic (topic_id), 
    role_type varchar(64) not null references <?=$prefix?>topic (topic_id), 
    role_reifier varchar(64) null references <?=$prefix?>topic (topic_id)
);

create index on <?=$prefix?>role (role_association);
create index on <?=$prefix?>role (role_player);


create table <?=$prefix?>scope
(
    scope_id bigserial not null primary key,
    scope_association varchar(64) null references <?=$prefix?>association (association_id) on delete cascade, 
    scope_name bigint null references <?=$prefix?>name (name_id) on delete cascade, 
    scope_occurrence bigint null references <?=$prefix?>occurrence (occurrence_id) on delete cascade, 
    scope_scope varchar(64) not null references <?=$prefix?>topic (topic_id)
);
