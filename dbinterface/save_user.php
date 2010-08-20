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
 if (isset($_SESSION['user']) && isset($_POST['save_user'])) {

  $user = pg_escape_string($_SESSION['user']);
  $givenname = pg_escape_string($_POST['user_givenname']);
  $surname = pg_escape_string($_POST['user_surname']);
  if ($_POST['user_password1'] != $_POST['user_password2'])
   die("Passwords doesn't match");
  $password = $_POST['user_password1'];
  if ($password != '') $password = md5($password);

  $update = array();
  if ($givenname != '') $update[] = "givenname='{$givenname}'";
  if ($surname != '') $update[] = "surname='{$surname}'";
  if ($password != '') $update[] = "password='{$password}'";

  if (count($update) > 0) {
   $update = implode(', ', $update);

   pg_query($dbconn, "update \"user\" set {$update} where id='{$user}'")
    or die('Unable to update user account: ' . pg_last_error());
   $messages .= "<div>Account successfully updated.</div>";
  }
 }
?>
