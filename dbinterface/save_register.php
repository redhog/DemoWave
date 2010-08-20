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
 if (in_array('register', $_SESSION['privs']) && isset($_POST['save_register'])) {

  $username = pg_escape_string($_POST['register_username']);
  $givenname = pg_escape_string($_POST['register_givenname']);
  $surname = pg_escape_string($_POST['register_surname']);
  if ($_POST['register_password1'] != $_POST['register_password2'])
   die("Passwords doesn't match");
  $password = $_POST['register_password1'];
  if ($password != '') $password = md5($password);

  if ($username == '' or $password == '')
   die('Please supply both a username and password');


  pg_query($dbconn, "insert into \"user\" (username, givenname, surname, password)" .
                    " values ('{$username}', '{$givenname}', '{$surname}', '{$password}')") 
   or die('Unable to update register account: ' . pg_last_error());
  $messages .= "<div>Account successfully add.</div>";
  if (!isset($_SESSION['user'])) {
   header("Status: 303 See Other");
   header("Location: " .
	   $_SERVER["SCRIPT_NAME"] . '?' .
	   queryString(queryConstruct(array('view' => 'login'))));
   exit(1);
  }
 }
?>
