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
<h1><?php E_("Log in"); ?></h1>

<form action="<?php 
 echo $_SERVER["SCRIPT_NAME"] . '?' . queryString(queryConstruct());
?>" method="post" enctype="multipart/form-data">
 <table>
  <?php
   printf(T_("<tr><td>Username:</td><td>%s</td></tr>"),
          "<input name='login_user' type='text' />");
   printf(T_("<tr><td>Password:</td><td>%s</td></tr>"),
          "<input name='login_password' type='password' />");
  ?>
 </table>
 <input type="submit" name="save_login" value="<?php E_("Log in"); ?>" />
</form>
