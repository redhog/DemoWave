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
 function drawUserOption($default, $value, $comment) {
  if ($default)
   return "<option value='unchanged' selected='1'>{$comment}</option>";
  else
   return "<option value='{$value}'>{$comment}</option>";
 }
?>

<h1><?php E_("Search for users"); ?></h1>
<form action="<?php echo $_SERVER["SCRIPT_NAME"] . '?' . queryString(queryConstruct()); ?>" method="get" enctype="multipart/form-data">
 <?php

  foreach ($_GET as $key => $value)
   if (!beginsWith($key, 'users_search_'))
    echo "<input name='{$key}' value='{$value}' type='hidden'>\n";

 ?>
 <table>
  <tr> 
   <?php echo drawSearchField(T_('Id'), 'users_search_id', false, 'bound', 10); ?>
   <?php echo drawSearchField(T_('User name'), 'users_search_username', false, 'match', 10); ?>
  </tr>
  <tr> 
   <?php echo drawSearchField(T_('Given name'), 'users_search_givenname', false, 'match', 10); ?>
   <?php echo drawSearchField(T_('Surname'), 'users_search_surname', false, 'match', 10); ?>
  </tr>
  <tr>
   <?php echo drawSearchField(T_('Vote'), 'users_search_vote_enabled',
                              array(T_('Enabled') => 'true', T_('Disabled') => 'false'),
                             'list', 10); ?>
   <?php echo drawSearchField(T_('Manage'), 'users_search_usermanagement_enabled',
                              array(T_('Enabled') => 'true', T_('Disabled') => 'false'),
                              'list', 10); ?>
  </tr>
  </tr>
 </table>

 <input type="submit" name="search_users" value="Search" />
</form>

<h1><?php printf(T_("%s: Users"), $users_len); ?></h1>

<form action="<?php 
 echo $_SERVER["SCRIPT_NAME"] . '?' . queryString(queryConstruct()); ?>" method="post" enctype="multipart/form-data">
 <table class='list'>
  <?php

   echo "<tr>\n";
   echo drawSortHeader($users_sort, 'users_sort', 'id', T_('Id'));
   echo drawSortHeader($users_sort, 'users_sort', 'username', T_('Username'));
   echo drawSortHeader($users_sort, 'users_sort', 'givenname', T_('Given name'));
   echo drawSortHeader($users_sort, 'users_sort', 'surname', T_('Surname'));
   echo drawSortHeader($users_sort, 'users_sort', 'vote_enabled', T_('Vote'));
   echo drawSortHeader($users_sort, 'users_sort', 'usermanagement_enabled', ('Manage'));
   echo "</tr>\n";

   foreach ($users as $attr)
    {
     echo "<tr class='list_item list_item_collapsed list_item_users'>\n" .
	  " <td>{$attr['id']}</td>\n" .
	  " <td>{$attr['username']}</td>\n" .
	  " <td>{$attr['givenname']}</td>\n" .
	  " <td>{$attr['surname']}</td>\n";

     echo " <td><select name='userenable_vote_{$attr['id']}'>\n";
     echo drawOption($attr['vote_enabled'] == 't', 'true', T_("Enabled"));
     echo drawOption($attr['vote_enabled'] == 'f', 'false', T_("Disabled"));
     echo " </select></td>";

     echo " <td><select name='userenable_usermanagement_{$attr['id']}'>\n";
     echo drawOption($attr['usermanagement_enabled'] == 't', 'true', T_("Enabled"));
     echo drawOption($attr['usermanagement_enabled'] == 'f', 'false', T_("Disabled"));
     echo " </select></td>";

     echo "</tr>";
    }
  ?>
  <tr><td></td><td></td><td></td><td></td><td></td><td><input type="submit" name="save_userenable" value="Save"></td></tr>
 </table>
 <?php
  echo drawPrevNextPage($users_len,
			$users_page_start,
			$users_page_len,
			'users_page_start',
			'users_page_len');
 ?>
</form>
