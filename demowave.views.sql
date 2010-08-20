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

create or replace function intervaleabs(interval) returns interval as $$
 select case when $1 > '0' then $1 else -$1 end as i;
 $$ language sql;

create or replace function current_area(interval, real, bigint, timestamp, timestamp) returns interval as $$
 declare
  p_area alias for $1;
  p_sum alias for $2;
  p_users alias for $3;
  p_time alias for $4;
  p_now alias for $5;
 begin
  if p_users = 0 then
   return p_area;
  else
   return p_area + (p_sum / p_users) * (p_now - p_time);
  end if;
 end
 $$ language plpgsql;

create or replace function insert_vote(bigint, bigint, timestamp, real, boolean) returns boolean as $$
 declare
  p_referendum alias for $1;
  p_user alias for $2;
  p_time alias for $3;
  p_vote alias for $4;
  p_system alias for $5;
  r_time timestamp;
  r_users bigint;
  last_id bigint;
  r_sum real;
  r_area interval;
  last_row record;
  last_user_row record;
 begin
  select into last_id last
   from referendum
   where id = p_referendum
   for update of referendum;

  r_time := now();
  if p_time is not null then
   r_time := p_time;
  end if;

  select into last_row
   '-infinity'::timestamp as time,
   '0'::real as sum,
   '0 hours'::interval as area,
   '0'::bigint as users;
  if last_id is not null then
   select into last_row * from vote where id = last_id;
   if (select intervaleabs(area) >= breakpoint from referendum_status where referendum = p_referendum) then
    return false;
   end if;
  end if;

  if last_row.time > r_time then
   raise notice 'Can not vote back in time';
  end if;

  select into last_user_row *
   from vote_user_last
   where referendum = p_referendum and "user" = p_user;

  r_users := (select users from users where typename = 'vote' and time <= r_time order by time desc limit 1);

  r_sum := last_row.sum + (p_vote - last_user_row.vote);

  if (r_sum <= 0.0 and last_row.sum >= 0.0) or (r_sum >= 0.0 and last_row.sum <= 0.0) then
   r_area := '0 hours'::interval;
  else
   r_area := current_area(last_row.area, last_row.sum, last_row.users, last_row.time, r_time);
  end if;

  insert
   into vote (referendum, "user", time, vote, sum, area, users, system)
   select p_referendum, p_user, r_time, p_vote, r_sum, r_area, r_users, p_system;

  update referendum set last = currval('vote_id_seq') where id = p_referendum;
  return true;
 end;
$$ language plpgsql;

create or replace function cast_vote(bigint, bigint, real) returns boolean as $$
 declare
  p_referendum alias for $1;
  p_user alias for $2;
  p_vote alias for $3;
 begin
  return insert_vote(p_referendum, p_user, null, p_vote, false);
 end;
$$ language plpgsql;

create or replace function enable_user(bigint, bigint, timestamp) returns void as $$
 declare
  p_user alias for $1;
  p_type alias for $2;
  p_time alias for $3;
  r_time timestamp;
  r_type record;
  r_last record;
 begin
  select into r_type *
   from user_enabled_type
   where id = p_type
   for update of user_enabled_type;

  if r_type.last is not null then
   select into r_last * from user_enabled where id = r_type.last;
  else
   select into r_last 0::bigint as users;
  end if;

  if r_type.name = 'vote' then
   perform last
    from referendum
    for update of referendum;
  end if;

  r_time := now();
  if p_time is not null then
   r_time := p_time;
  end if;

  insert
   into user_enabled ("user", type, enabled, time, users)
   select
     p_user,
     p_type,
     true,
     r_time,
     r_last.users + 1;

  if r_type.name = 'vote' then
   perform
     insert_vote(v.referendum, p_user, r_time, v.vote, true)
    from vote_user_real_last as v
    where v.user = p_user;
  end if;

  update user_enabled_type set last = currval('user_enabled_id_seq') where id = p_type;
 end;
$$ language plpgsql;

create or replace function disable_user(bigint, bigint, timestamp) returns void as $$
 declare
  p_user alias for $1;
  p_type alias for $2;
  p_time alias for $3;
  r_time timestamp;
  r_type record;
  r_last record;
 begin
  select into r_type *
   from user_enabled_type
   where id = p_type
   for update of user_enabled_type;

  if r_type.last is not null then
   select into r_last * from user_enabled where id = r_type.last;
  else
   select into r_last 0::bigint as users;
  end if;

  if (select name = 'vote' from user_enabled_type where id = p_type) then
   perform last
    from referendum
    for update of referendum;
  end if;

  r_time := now();
  if p_time is not null then
   r_time := p_time;
  end if;

  insert
   into user_enabled ("user", type, enabled, time, users)
   select
     p_user,
     p_type,
     false,
     r_time,
     r_last.users - 1;

  if r_type.name = 'vote' then
   perform
     insert_vote(referendum.id, p_user, r_time, 0, true)
    from
     referendum;
  end if;

  update user_enabled_type set last = currval('user_enabled_id_seq') where id = p_type;
 end;
$$ language plpgsql;


-- Paragraphs are ordered into a tree according to user needs - any
-- paragraph might have any number of sub-paragraphs.

-- A paragraph is designated by a path in this tree where the
-- node-names in the path are numbers, separated by dots, e.g.
-- 47.359.27.11

-- At each level in this tree paragraphs are (theoretically) ordered
-- into a binary tree according to their node number at that level in
-- the first tree. The node numbers are interpreted as decimal numbers
-- in the range [0,1], e.g. a node-name abcd is interpreted as the
-- decimal number 0.abcd.

-- A new paragraph is inserted after an existing one A by finding the
-- paragraph B with the lowest number greater than that of A, and
-- calculating half the sum of these two numbers.

-- Example: A has the numer 0.543, B has the number 0.5452. The new
-- paragraph will then be numbered (0.543 + 0.5452) / 2 = 0.5441.

create or replace function law_next_paragraph(bigint, varchar) returns varchar as $$
 declare
  p_category alias for $1;
  p_paragraph alias for $2;
  tail varchar;
  head varchar;
  nexthead varchar;
  test varchar;
 begin
  head := regexp_replace(p_paragraph, E'^(.*\\.)?([0-9]*)$', E'\\2');
  tail := regexp_replace(p_paragraph, E'^(.*\\.)?[0-9]*$', E'\\1');

  test = p_paragraph;
  if head = '0' and tail != '' then
   test := substring(tail for length(tail) - 1);
  end if;

  if     test != '0'
     and not exists (select *
		      from referendum as v, referendum_type_law as l
		      where     v.category = p_category
			    and l.referendum = v.id
			    and path = test) then
   raise exception 'Paragraph does not exist: %', test;
  end if;

  nexthead := (select *
                from (select substring(l.path from '[0-9]*$') as item
                       from referendum as v, referendum_type_law as l
                       where     v.category = p_category
                             and l.referendum = v.id
                             and l.path similar to replace(tail, '.', E'\\.') || '[0-9]*') as x
                where item > head
                order by item asc
                limit 1);
  head := '0.' || head;
  if nexthead is null then
   nexthead := '1.0';
  else
   nexthead := '0.' || nexthead;
  end if;
  return tail || rtrim(substring(((head::numeric + nexthead::numeric) / 2)::varchar from 3), '0');
 end;
$$ language plpgsql;

create or replace function law_paragraph_combine(varchar, int, varchar) returns varchar as $$
 declare
  p_current alias for $1;
  p_levels alias for $2;
  p_path alias for $3;
  r_current varchar[];
  r_path varchar[];
 begin
  r_current := coalesce(string_to_array(p_current, '.'), '{}'::varchar[]);
  r_path := coalesce(string_to_array(p_path, '.'), '{}'::varchar[]);

  if p_levels != 0 then
   r_current := r_current[1:array_upper(r_current, 1) - p_levels];
  end if;
  return array_to_string(r_current || r_path, '.');
 end;
$$ language plpgsql;

create or replace view users as
  select
   e.user,
   e.type,
   t.name as typename,
   e.enabled,
   e.time,
   e.users
  from
   user_enabled as e, 
   user_enabled_type as t
  where e.type = t.id
 union
  select
   0 as "user",
   t.id as type,
   t.name as typename,
   false as enabled,
   '-infinity' as time,
   0 as users
  from
   user_enabled_type as t;

create or replace view users_now as
 select distinct on (type) *
  from users
  order by type, time desc;

create or replace view user_enabled_now as
 select *
  from
   (select distinct on (u2.user, u2.type) *
     from user_enabled as u2
     order by u2.user, u2.type, u2.time desc) as u1
  where u1.enabled;

create or replace view vote_all as
 select * from vote
 union
 select
  0 as id,
  referendum.id as referendum,
  "user".id as "user",
  '-infinity' as time,
  0 as vote,
  0 as sum,
  '0 days' as area,
  0 as users,
  false as system
 from referendum, "user";

create or replace view vote_user_last as
 select distinct on (referendum, "user") *
 from vote_all
 order by referendum, "user", time desc;

create or replace view vote_user_real_last as
 select distinct on (referendum, "user") *
 from vote_all
 where not system
 order by referendum, "user", time desc;

create or replace view referendum_status as
  select
   v.category,
   c.path,
   c.breakpoint,
   v.id as referendum,
   v.title,
   case
    when l.sum != '0' and l.users != 0 then
     l.sum / l.users
    else
     '0'
   end as sum,
   v.time as start,
   current_area(l.area, l.sum, l.users, l.time, now()::timestamp) as area,
   case
    when l.sum != '0' and l.users != 0 then
     l.time + (c.breakpoint - l.area) / @ (l.sum / l.users)
    else
     'infinity'
   end as completed
  from
   vote as l,
   referendum as v,
   referendum_type_category as c
  where     l.id = v.last
	and v.category = c.referendum
 union
  select
   v.category,
   c.path,
   c.breakpoint,
   v.id as referendum,
   v.title,
   '0' as sum,
   v.time as start,
   '0 days' as area,
   'infinity' as completed
  from
   referendum as v,
   referendum_type_category as c
  where     v.last is null
	and v.category = c.referendum;


create or replace view referendum_completed as
 select *
 from referendum_status as s
 where intervaleabs(s.area) >= s.breakpoint;

create or replace view referendum_passed as
 select *
 from referendum_completed as c
 where c.area > '0 second';

create or replace view referendum_category as
 select *
  from
   (select distinct on (t.path)
     t.*,
     tt.name as type_name,
     c.title,
     c.completed
    from
     referendum_status as c,
     referendum_type_category as t,
     referendum_type as tt
    where     c.area >= c.breakpoint
          and c.referendum = t.referendum
	  and t.type = tt.id
    order by t.path, c.completed) as c
 where c.add;

create or replace view referendum_info
as select
 v.*,
 case when v.area >= v.breakpoint then 1
      when v.area <= -v.breakpoint then -1
      else 0
 end as status,
 l.user,
 l.vote as last
from
 referendum_status as v,
 vote_user_last as l
where v.referendum = l.referendum;
