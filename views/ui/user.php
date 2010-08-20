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
<form action="<?php 
 echo $_SERVER["SCRIPT_NAME"] . '?' . queryString(queryConstruct());
?>" method="post" enctype="multipart/form-data">
 <?php

  echo "<h2>" . T_("Name") . "</h2>\n";
  echo "<table>\n";
  echo drawInputRow(T_("Username"), $user_username);
  echo drawInputRow(T_("Given name"), "<input name='user_givenname' type='text' value='{$user_givenname}' />");
  echo drawInputRow(T_("Surname"), "<input name='user_surname' type='text' value='{$user_surname}' />");
  echo "</table>\n";

  echo "<h2>" . T_("Password") . "</h2>\n";
  echo "<table>\n";
  echo drawInputRow(T_("New password"), "<input name='user_password1' type='password' />");
  echo drawInputRow(T_("Retype password"), "<input name='user_password2' type='password' />");
  echo "</table>\n";
 ?>
 <input type="submit" name="save_user" value="<?php E_("Save"); ?>" />
</form>
