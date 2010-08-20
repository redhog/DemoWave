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
 if (in_array('view', $_SESSION['privs'])) {
  $is_category = false;
  $sql = "select c.referendum, t.name, c.title, c.breakpoint, c.text " .
	 "from referendum_category as c, referendum_type as t " .
	 "where     c.path = '{$category_path}' " .
	 "      and c.type = t.id";
  $row = pg_fetch_row(pg_query($dbconn, $sql));
  if ($row)
   {
    $is_category = true;
    $category_id = $row[0];
    $category_type = $row[1];
    $category_title = $row[2];
    $category_breakpoint = $row[3];
    $category_text = $row[4];
   }
 }
?>
