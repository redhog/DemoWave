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
 if (in_array('view', $_SESSION['privs']) && $is_category && $_GET['view'] == 'graph') {

  function pgsql_to_unix_time ($datetime) {
   // Convert to string so we can reliably parse it
   settype($datetime, 'string');

   if ($datetime == "infinity") return "infinity";

   // Break the number up into its components (yyyymmddhhmmss)
   // storing results in the array matches
   eregi('(....)-(..)-(..) (..):(..):(..)(\..*)?', $datetime, $matches);

   // Pop the first element off the matches array.  The first
   // element is not a match, but the original string, which
   // we don't want.
   array_shift ($matches);

   // Transfer the values in $matches into labeled variables
   foreach (array('year','month','day','hour','minute','second') as $var) {
    $$var = array_shift($matches);
   }

   return mktime($hour,$minute,$second,$month,$day,$year);
  }


  $rows = pg_query($dbconn, "select time, sum / users from vote where referendum = {$_GET["referendum"]}")
   or die(T_('Unable to query for referendum data: ') . pg_last_error());

  $graph = array();
  while ($row = pg_fetch_row($rows)) {
   $date = pgsql_to_unix_time($row[0]);
   if (count($graph) > 0)
    $graph[] = array($date, $graph[count($graph)-1][1]);
   $graph[] = array($date, $row[1] + 0.0);
  }

  $rows = pg_query($dbconn, "select completed as time, sum from referendum_status where referendum = {$_GET["referendum"]}")
   or die(T_('Unable to query for referendum data: ') . pg_last_error());

  $row = pg_fetch_row($rows);
  $date = pgsql_to_unix_time($row[0]);
  if ($date == "infinity")
   $date = time();
  if (count($graph) > 0)
   $graph[] = array($date, $graph[count($graph)-1][1]);
  $graph[] = array($date, $row[1] + 0.0);

 }
