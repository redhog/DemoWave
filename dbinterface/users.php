<?php /*
DemoWave
Copyright (C) 2006 RedHog (Egil Möller) <redhog@redhog.org>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or (at
your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
USA
*/ ?>
<?

 if (in_array('usermanagement', $_SESSION['privs'])) {
  $sql_names = array('id', 'username', 'givenname', 'surname', 'vote_enabled', 'usermanagement_enabled');
  $sql_fetch = " from" .
	       "  (select" .
               "     u.id," .
               "     u.username," .
               "     u.givenname," .
               "     u.surname," .
               "     exists (select * from user_enabled_now as e, user_enabled_type as t where e.user = u.id and e.type = t.id and t.name = 'vote') as vote_enabled," .
               "     exists (select * from user_enabled_now as e, user_enabled_type as t where e.user = u.id and e.type = t.id and t.name = 'usermanagement') as usermanagement_enabled" .
	       "    from \"user\" as u" .
               "    order by u.id, u.username, u.givenname, u.surname) as u" .
	       " where " . searchToSql($users_search);

  $sql = "select count(*)" . $sql_fetch;
  $row = pg_fetch_row(pg_query($dbconn, $sql))
   or die('Could query: ' . pg_last_error());
  $users_len = (int) $row[0];

  $sql = "select * " .
	 $sql_fetch .
	 " order by " . sortToSql($users_sort);
	 " limit " . ($users_page_start + $users_page_len);
  $rows = pg_query($dbconn, $sql)
   or die('Could query: ' . pg_last_error());
  pg_result_seek($rows, $users_page_start);


  $users = array();
  while ($row = pg_fetch_row($rows)) {
   $row = array_combine($sql_names, $row);
   $users[] = $row;
  }
 }
?>
