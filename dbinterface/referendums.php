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
 if (in_array('view', $_SESSION['privs']) && $is_category && $_GET['view'] == 'category') {
  $rows = pg_query($dbconn, "select name, id from referendum_type")
   or die(T_('Unable to query for referendum types: ') . pg_last_error());
  $referendum_types = array();
  while ($row = pg_fetch_row($rows))
   $referendum_types[$row[0]] = $row[1];

  if ($category_type == "category") {
   $sql_type_select = ", vc.add, vc.path, vt.name, vc.text"; 
   $sql_type_from = ", referendum_type_category as vc, referendum_type as vt";
   $sql_type_where = " and vc.referendum = i.referendum and vt.id = vc.type";
   $sql_type_names = array('add', 'path', 'type', 'text');
  } else if ($category_type == "text") {
   $sql_type_select = ", text";
   $sql_type_from = ", referendum_type_text as vt";
   $sql_type_where = " and vt.referendum = i.referendum";
   $sql_type_names = array('text');
  } else if ($category_type == "law") {
   $sql_type_select = ", vt.add, vt.path, vt.title as paragraphtitle, vt.text as text";
   $sql_type_from = ", referendum_type_law as vt";
   $sql_type_where = " and vt.referendum = i.referendum";
   $sql_type_names = array('add', 'path', 'paragraphtitle', 'text');
  } else die('Unknown category type');

  if (isset($fudinc)) {
   $sql_fud_select = ", f.fudid";
   $sql_fud_from = ", referendum_fud as f";
   $sql_fud_where = " and f.referendum = i.referendum";
   $sql_fud_names = array('fudid');
  } else {
   $sql_fud_select = "";
   $sql_fud_from = "";
   $sql_fud_where = "";
   $sql_fud_names = array();
  }

  $sql_names = array('referendum', 'title', 'status', 'start', 'completed', 'area', 'sum', 'last');
  $sql_names = array_merge($sql_names, $sql_fud_names, $sql_type_names);

  $sql_select = "i.referendum, i.title, i.status, date_trunc('second', i.start) as start, date_trunc('second', i.completed) as completed, date_trunc('second', i.area) as area, trunc(cast(i.sum as numeric), 3) as sum, i.last";
  $sql_from = "referendum_info as i";
  $user = 1;
  if (isset($_SESSION['user']))
   $user = $_SESSION['user'];
  $sql_where = "i.path = '{$category_path}'" .
               " and i.user = '{$user}'";

  function fetch_referendums($status, $start, $pagelen, $search, $order) {
   global $dbconn, $expanded_referendums;
   global $sql_names;
   global $sql_select, $sql_from, $sql_where;
   global $sql_fud_names, $sql_fud_select, $sql_fud_from, $sql_fud_where;
   global $sql_type_select, $sql_type_from, $sql_type_where;

   $sql_fetch = " from" .
                "  (select " . $sql_select . $sql_fud_select . $sql_type_select .
                "    from " . $sql_from . $sql_fud_from . $sql_type_from .
		"    where " . $sql_where . $sql_fud_where . $sql_type_where .
                "    order by i.referendum) as v" .
                " where " . $search;
   $sql = "select count(*) from (select distinct on (referendum) referendum " . $sql_fetch . " group by referendum) as r";
   $row = pg_fetch_row(pg_query($dbconn, $sql))
    or die('Could query: ' . pg_last_error());
   $len = (int) $row[0];

   $sql = "select * " .
	  $sql_fetch .
	  " order by " . $order;
	  " limit " . ($start + $pagelen);
   $rows = pg_query($dbconn, $sql)
    or die('Could query: ' . pg_last_error());
   pg_result_seek($rows, $start);

   $v = array();
   while ($row = pg_fetch_row($rows)) {
    $row = array_combine($sql_names, $row);
    $row['expanded'] = in_array($row['referendum'], $expanded_referendums);
    if (!isset($v[count($v)-1]) || $v[count($v)-1]['referendum'] != $row['referendum'])
     $v[] = $row;
    else {
     if (!isset($v[count($v)-1]['subrows'])) $v[count($v)-1]['subrows'] = array($v[count($v)-1],);
     $v[count($v)-1]['subrows'][] = $row;
    }
   }
   return array('len' => $len, 'referendums' => $v);
  }

  $info = fetch_referendums(0, $referendum_page_start, $referendum_page_len, searchToSql($referendum_search), sortToSql($referendums_sort));
  $referendums_len = $info['len'];
  $referendums = $info['referendums'];
 }
?>
