-- DemoWave
-- Copyright (C) 2008 RedHog (Egil Möller) <redhog@redhog.org>
-- Copyright (C) 2006 RedHog (Egil Möller) <redhog@redhog.org>

-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or (at
-- your option) any later version.

-- This program is distributed in the hope that it will be useful, but
-- WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
-- General Public License for more details.

-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
-- USA

drop table referendum_type_law cascade;
drop table referendum_type_text cascade;
drop table referendum_type_category cascade;
drop table vote cascade;
drop table referendum cascade;
drop table referendum_type cascade;
drop table user_enabled cascade;
drop table user_enabled_type cascade;
drop table "user" cascade;
drop table referendum_fud cascade;

create table "user" (
 id serial unique not null,
 username varchar not null,
 givenname varchar,
 surname varchar,
 password varchar
);

create table user_enabled_type (
 id serial unique not null,
 name varchar,
 last bigint
);

create table user_enabled (
 id serial  unique not null,
 "user" bigint not null references "user"(id),
 type bigint not null references user_enabled_type(id),
 enabled boolean not null,
 time timestamp not null default now(),
 users bigint
);

create table referendum_type (
 id serial unique not null,
 name varchar
);

create table referendum (
 id serial unique not null,
 title varchar,
 category bigint,
 last bigint,
 time timestamp default now()
);

create table vote (
 id serial unique not null,
 referendum bigint references referendum(id),
 "user" bigint references "user"(id),
 time timestamp,
 vote real,
 sum real,
 area interval,
 users bigint,
 system boolean
);

alter table referendum add foreign key (last) references vote(id);

create table referendum_type_category (
 referendum bigint unique not null references referendum(id),
 add boolean,
 type bigint references referendum_type(id),
 breakpoint interval,
 path varchar,
 text varchar
);

create table referendum_type_text (
 referendum bigint unique not null references referendum(id),
 text varchar
);

create table referendum_type_law (
 referendum bigint not null references referendum(id),
 id int not null,
 add boolean,
 path varchar,
 title varchar,
 text varchar,
 unique (referendum, id)
);

create table referendum_fud (
 referendum bigint unique not null references referendum(id),
 fudid bigint unique not null
);

