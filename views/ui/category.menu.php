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
 function drawSelectCategoryView($view, $title, $reset=array(), $resetpattern=array()) {
  $url = $_SERVER["SCRIPT_NAME"] . "?" . queryString(queryConstruct(array('categoryview' => $view), $reset, $resetpattern));
  $cls = 'viewmenu_non_current';
  if ($_GET["categoryview"] == $view)
   $cls = 'viewmenu_current';
  return "<a class='viewmenu {$cls}' href='{$url}'>{$title}</a>";
 }

 $menu = array();

 $menu[] = drawSelectCategoryView('referendums', T_('Referendums'), array(), array('referendum_search_'));
 $menu[] = drawSelectCategoryView('decisions', T_('Decisions'), array(), array('referendum_search_'));
 $menu[] = drawSelectCategoryView('rejections', T_('Rejections'), array(), array('referendum_search_'));

 if ($category_type == 'law') {
  $menu[] = drawSelectCategoryView('law', T_('Current laws'), array(), array('law_'));
 }

 if (in_array('propose', $_SESSION['privs']) && $is_category)
  if ($category_type == 'law')
   $menu[] = drawSelectCategoryView('laweditor', T_('Propose referendum'), array('law_proposal', 'law_date'));
  else
   $menu[] = drawSelectCategoryView('newreferendum', T_('Propose referendum'));

 echo implode(' ', $menu);
?>
