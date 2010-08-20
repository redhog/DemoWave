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
 if (in_array('usermanagement', $_SESSION['privs']) && isset($_POST['save_userenable']))
  {
   foreach ($_POST as $name => $value)
    if (beginsWith($name, 'userenable_vote_') && $value != 'unchanged') {
     $user = substr($name, strlen('userenable_vote_'));

     if ($value == 'true')
      $sql = "select enable_user({$user}::bigint, id, null) from user_enabled_type where name ='vote'";
     else
      $sql = "select disable_user({$user}::bigint, id, null) from user_enabled_type where name ='vote'";
     pg_query($dbconn, $sql)
      or die('Unable to update user: ' . pg_last_error());
     $messages .= "<div>Referendum status updated for {$user}</div>";
    } else if (beginsWith($name, 'userenable_usermanagement_') && $value != 'unchanged') {
     $user = substr($name, strlen('userenable_usermanagement_'));

     if ($value == 'true')
      $sql = "select enable_user({$user}::bigint, id, null) from user_enabled_type where name ='usermanagement'";
     else
      $sql = "select disable_user({$user}::bigint, id, null) from user_enabled_type where name ='usermanagement'";
     pg_query($dbconn, $sql)
      or die('Unable to update user: ' . pg_last_error());
     $messages .= "<div>User management status updated for {$user}</div>";
    }

  }
?>
