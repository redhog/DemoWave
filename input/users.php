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
 if (isset($_GET['users_page_start']))
  $users_page_start = (int) $_GET['users_page_start'];
 else
  $users_page_start = 0;
 if (isset($_GET['users_page_len']))
  $users_page_len = (int) $_GET['users_page_len'];
 else
  $users_page_len = 10;
 if (isset($_GET['users_sort']))
  $users_sort = strToSort($_GET['users_sort']);
 else
  $users_sort = strToSort('usermanagement_enabled:desc,vote_enabled:desc');

 $users_search = varsToSearch('users_search_', $_GET);
?>
