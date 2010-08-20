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

 if (isset($_POST['save_login'])) {
  $username = pg_escape_string($_POST['login_user']);

  $row = pg_fetch_row(pg_query($dbconn, "select u.id, u.password from \"user\" as u where u.username='{$username}'"))
   or die("You don't exist, go away.");
  if ($row[1] != md5($_POST['login_password']))
   die("You don't exist, go away.");

  $_SESSION['user'] = $row[0];
  header("Status: 303 See Other");
  header("Location: " .
          $_SERVER["SCRIPT_NAME"] . '?' .
          queryString(queryConstruct(array('view' => 'categories'))));
  exit(1);
 }

 if (isset($_POST['save_logout'])) {
  session_start();
  session_unset();
  header("Status: 303 See Other");
  header("Location: " .
          $_SERVER["SCRIPT_NAME"] . '?' .
          queryString(queryConstruct(array('view' => 'login'))));
  exit(1);
 }

?>
