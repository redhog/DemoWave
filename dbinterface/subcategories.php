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
  if ($_GET['view'] == 'categories') {

   $sql = "select path, text, type_name from referendum_category " .
	  " order by path";

   $rows = pg_query($dbconn, $sql)
    or die('Could query: ' . pg_last_error());

   $categories = array('text' => '', 'title' => 'Top', 'path' => '', 'children' => array());
   while ($row = pg_fetch_row($rows)) {
    $node = &$categories;

    if ($row[0]) {
     $path = explode('.', $row[0]);
     for ($pos = 0; $pos < count($path); $pos++) {
      if (!array_key_exists($path[$pos], $node['children']))
       $node['children'][$path[$pos]] = array('text' => '', 'title' => $path[$pos], 'path' => implode('.', array_slice($path, 0, $pos + 1)), 'children' => array());
      $node = &$node['children'][$path[$pos]];
     }
    }
    $node['text'] = $row[1];
    $node['type'] = $row[2];
   }
  } else {

   $sql = "select path from referendum_category " .
	  " where path similar to '{$category_path_connect}%' and path != ''" .
	  " order by path";

   $rows = pg_query($dbconn, $sql)
    or die('Could query: ' . pg_last_error());

   $subcategories = array();
   while ($row = pg_fetch_row($rows))
    {
     $category = $row[0];
     $category = explode('.', substr($category, strlen($category_path_connect)));
     $category = $category[0];
     if (array_search($category, $subcategories) === false)
      $subcategories[] = $category;
    }
  }

 }
?>
