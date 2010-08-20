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
<h1><?php echo drawHelpAnnotation(T_("Referendum categories"), 'categories'); ?></h1>
<?php
 function draw_categories($categories)
  {
   foreach ($categories as $child)
    draw_category($child);
  }

 function draw_category_list($categories)
  {
   if ($categories)
    echo "\n<dl>\n";
   draw_categories($categories);
   if ($categories)
    echo "</dl>\n";
  }

 function draw_category($category)
  {
   $args = queryString(queryConstruct(array('view' => 'category', 'category' => $category['path']), array('categoryview'), array('referendum_search_')));
   echo "<dt><a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$category['title']}</a></dt>\n";
   echo "<dd>\n";
   echo "{$category['text']}";
   draw_category_list($category['children']);
   echo "</dd>\n";
  }

 draw_category_list(array_merge(array(array('title' => $categories['title'], 'text' => $categories['text'], 'path' => $categories['path'], 'children' => array())),
			      $categories['children']));
?>
