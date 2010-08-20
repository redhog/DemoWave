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
 if (   in_array('view', $_SESSION['privs'])
     && $is_category
     && $_GET['view'] == 'category'
     && (   $_GET['categoryview'] == 'law'
         ||(   $_GET['categoryview'] == 'laweditor'
            && (   !isset($_GET["law_edit_continue"])
                && !isset($_POST["law_edit_continue"]))))) {

  $law_date_filter = '';
  if ($_GET['law_date'] != '')
   $law_date_filter = " and completed < '{$_GET['law_date']}'";

  $new_referendums_sql = "false";
  if ($_GET["law_proposal"]) {
    $proposal = pg_escape_string($_GET["law_proposal"]);
    $new_referendums_sql = "v.referendum = {$proposal}";
  }
  $sql = "select -- distinct on (path)
	   l.path, l.add, v.referendum, v.title as reftitle, v.completed as changed, l.title, l.text
 	  from
	   referendum_status as v,
	   referendum_type_law as l
	  where     v.path = '{$category_path}'
	        and l.referendum = v.referendum
		and (   (    intervaleabs(v.area) >= v.breakpoint
		         and v.area > '0 second'
			 {$law_date_filter})
                     or {$new_referendums_sql})
	  order by path, completed desc";
  $rows = pg_query($dbconn, $sql)
   or die('Uanble to query for paragraphs');

  $sql_names = array('path', 'add', 'referendum', 'reftitle', 'changed', 'title', 'text');

  $laws = array('sub' => array());
  while ($row = pg_fetch_row($rows)) {
   $law = array_combine($sql_names, $row);
   $law['sub'] = array();
   $node = &$laws;
   $path = explode('.', $law['path']);
   $head = $path[count($path) - 1];
   unset($path[count($path) - 1]);
   foreach ($path as $item) {
    if (!isset($node['sub'][$item]))
     $node['sub'][$item] = array('sub' => array());
    $node = &$node['sub'][$item];
   }

   if ($law['referendum'] == $_GET["law_proposal"]) {
    if (!isset($node['sub'][$head]))
     $node['sub'][$head] = array('sub' => array());
    $node['sub'][$head]['edit'] = $law;
   } else {
    $node['sub'][$head] = $law;
   }
  }
  

/*
  if ($_GET["law_proposal"]) {
   $proposal = pg_escape_string($_GET["law_proposal"]);
   $sql = "select
            l.path, l.add, l.referendum, v.title as reftitle, now() as changed, l.title, l.text
           from
            referendum as v,
            referendum_type_law as l
           where     v.id = '{$proposal}'
                 and l.referendum = v.id
          ";

   $rows = pg_query($dbconn, $sql)
    or die('Uanble to query for proposal');
   while ($row = pg_fetch_row($rows)) {
    $change = array_combine($sql_names, $row);
    $path = explode('.', $change['path']);

    $node = &$laws;
    foreach ($path as $item) {
     if (!isset($node['sub'][$item]))
      $node['sub'][$item] = array('sub' => array());
     $node = &$node['sub'][$item];
    }
    $node['edit'] = $change;
   }
  }
*/
  if (   $_GET['categoryview'] == 'laweditor'
      && !isset($_POST["law_edit_continue"])) {
   $_SESSION["laweditor"] = array('laws' => $laws, 'current' => 0);
  }
 }
?>
