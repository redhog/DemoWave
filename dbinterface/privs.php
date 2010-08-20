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
 $_SESSION['privs'] = array('view');

 if (isset($_SESSION['user'])) {

  $rows = pg_query($dbconn, "select t.name
                              from
			       user_enabled_now e,
			       user_enabled_type as t
                              where     e.\"user\"='{$_SESSION['user']}'
                                    and e.type = t.id")
   or die('Unable to fetch privileges');
  while ($row = pg_fetch_row($rows)) {
   $_SESSION['privs'][] = $row[0];
   if ($row[0] == 'vote') $_SESSION['privs'][] = 'propose';
   if ($row[0] == 'usermanagement') $_SESSION['privs'][] = 'register';
  }
 } else {
  $_SESSION['privs'][] = 'register';
 }
 /*
  $privs = implode(', ', $_SESSION['privs']);
  $messages .= "<div>Privs: {$privs}</div>";
 */
?>
