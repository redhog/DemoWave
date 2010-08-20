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

insert into user_enabled_type (name) values ('vote');
insert into user_enabled_type (name) values ('usermanagement');

insert into "user" (username) values ('sys');
select enable_user(currval('user_id_seq'), id, '1900-01-01') from user_enabled_type where name = 'vote';
select disable_user(currval('user_id_seq'), id, '1900-01-02') from user_enabled_type where name = 'vote';

-- password is md5('admin')
insert into "user" (username, password) values ('admin', '21232f297a57a5a743894a0e4a801fc3');
select enable_user(currval('user_id_seq'), id, null) from user_enabled_type where name = 'usermanagement';

insert into referendum_type (name) values ('category');
insert into referendum_type (name) values ('text');
insert into referendum_type (name) values ('law');

select * from nextval('referendum_id_seq');
insert into referendum (id, title, category, time)
 select
  currval('referendum_id_seq'),
  'Top category',
  currval('referendum_id_seq'),
  '1900-01-01';
insert into referendum_type_category (referendum, add, path, breakpoint, type, text)
 select
  currval('referendum_id_seq'),
  true,
  '',
  '5 minutes',
  id as type,
  'This is the top category. Propose a new referendum to add a sub-category.' as text
 from referendum_type
 where name = 'category';
insert into referendum_fud (referendum, fudid)
 select
  currval('referendum_id_seq'),
  0;

select
 insert_vote(currval('referendum_id_seq'), id, '1900-01-01', 1, false)
from "user"
where username = 'sys';

alter table referendum add foreign key (category) references referendum_type_category(referendum);
