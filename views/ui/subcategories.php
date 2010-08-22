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
<h2><?php E_("Sub-categories"); ?></h2>
<ul>
 <?php

  foreach ($subcategories as $category)
   {
    $args = queryString(queryConstruct(array('category' => $category_path_connect . $category), array('categoryview'), array('referendum_search_')));
    echo "<li><a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>${category}</a></li>";
   }
 ?>
</ul>
