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
 function drawSelectView($view, $title) {
  $url = $_SERVER["SCRIPT_NAME"] . "?" . queryString(queryConstruct(array('view' => $view)));
  $cls = 'menu_non_current';
  if ($_GET["view"] == $view)
   $cls = 'menu_current';
  return "<a class='menu {$cls}' href='{$url}'>{$title}</a>";
 }

 $menu = array();

 if (isset($_SESSION['user']))
  $menu[] = drawSelectView('user', T_('My settings'));

 if (in_array('usermanagement', $_SESSION['privs']))
  $menu[] = drawSelectView('users', T_('Account management'));

 if (in_array('view', $_SESSION['privs']))
  $menu[] = drawSelectView('categories', T_('Referendums'));

 if (in_array('view', $_SESSION['privs']))
  $menu[] = drawSelectView('searchreferendumsglobal', T_('Search'));

 if (isset($_SESSION['user']))
  $menu[] = drawSelectView('logout', T_('Log out'));

 if (in_array('register', $_SESSION['privs']))
  $menu[] = drawSelectView('register', T_('Register'));

 if (!isset($_SESSION['user']))
  $menu[] = drawSelectView('login', T_('Log in'));

 $menu[] = drawHelp('help', T_("Help"));

 echo implode(' ', $menu);
?>
