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
 if (isset($_SESSION['user'])) {

  $user = pg_escape_string($_SESSION['user']);
  $row = pg_fetch_row(pg_query($dbconn, "select username, givenname, surname from \"user\" where id='{$user}'"))
   or die('Unable to fetch user information');

  $user_username = $row[0];
  $user_givenname = $row[1];
  $user_surname = $row[2];

 }
?>
