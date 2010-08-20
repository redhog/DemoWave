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
<?php
 if (in_array('view', $_SESSION['privs']) && $_GET['view'] == 'searchreferendumsglobal') {
  $sql_names = array('path', 'referendums');

  $user = 1;
  if (isset($_SESSION['user']))
   $user = $_SESSION['user'];

  $sql_fetch = " from" .
	       "  (select path, count(*) as referendums " .
	       "    from referendum_info" .
	       "    where \"user\" = '{$user}' and " . searchToSql($globalreferendum_search) .
               "    group by path) as v";

  $sql = "select count(*)" . $sql_fetch;
  $row = pg_fetch_row(pg_query($dbconn, $sql))
   or die('Could query: ' . pg_last_error());
  $globalreferendums_len = (int) $row[0];

  $sql = "select * " .
	 $sql_fetch .
	 " order by " . sortToSql($globalreferendums_sort);
	 " limit " . ($globalreferendum_page_start + $globalreferendum_page_len);
  $rows = pg_query($dbconn, $sql)
   or die('Could query: ' . pg_last_error());
  pg_result_seek($rows, $globalreferendum_page_start);

  $globalreferendums = array();
  while ($row = pg_fetch_row($rows))
   $globalreferendums[] = array_combine($sql_names, $row);
 }
?>
