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

<span class="categorypath">
 <?php
  if ($_GET["category"] == '')
   $items = array();
  else
   $items = explode('.', $_GET["category"]);

  $res = array();
  $subpath = array();

  $args = queryString(queryConstruct(array('category' => ''), array('categoryview'), array('referendum_search_')));
  $res = array("<span><a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>" . T_("Top") . "</a></span>\n");
  foreach ($items as $item)
   {
    $subpath[] = $item;
    $args = queryString(queryConstruct(array('category' => implode('.', $subpath)), array('categoryview'), array('referendum_search_')));
    $res[] = "<span><a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$item}</a></span>\n";
   }
  echo implode(' &gt;&gt; ', $res);
 ?>
</span>