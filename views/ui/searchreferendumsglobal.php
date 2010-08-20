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
<h2><?php E_("Search for referendums"); ?></h2>
<form action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" method="get" enctype="multipart/url-encoded">
 <?php

  foreach ($_GET as $key => $value)
   if (!beginsWith($key, 'globalreferendum_search_'))
    echo "<input name='{$key}' value='{$value}' type='hidden'>\n";

 ?>
 <table>
  <tr> 
   <?php echo drawSearchField(T_('Id'), 'globalreferendum_search_referendum', false, 'bound', 10); ?>
   <?php echo drawSearchField(T_('Total'), 'globalreferendum_search_area', false, 'bound', 10, 'vote-sum'); ?>
  </tr>
  <tr>
   <?php echo drawSearchField(T_('Title'), 'globalreferendum_search_title', false, 'match', 10); ?>
   <?php echo drawSearchField(T_('Current'), 'globalreferendum_search_sum', false, 'bound', 10, 'current-vote-sum'); ?>
  </tr>
  <tr>
   <?php echo drawSearchField(T_('Started'), 'globalreferendum_search_start', false, 'bound', 10, 'start'); ?>
   <?php echo drawSearchField(T_('Vote'), 'globalreferendum_search_last', false, 'bound', 10, 'current-vote'); ?>
  </tr>
  <tr>
   <?php echo drawSearchField(T_('Estimate'), 'globalreferendum_search_completed', false, 'bound', 10, 'completed'); ?>
   <?php echo drawSearchField(T_('Status'), 'globalreferendum_search_status',
                              array(T_('Passed') => 1,
				    T_('Active') => 0,
				    T_('Rejected') => -1),
			      'list', 10, 'referendum-status'); ?>
  </tr>
 </table>
 <input type="submit" name="search_referendumsglobal" value="<?php E_("Search"); ?>">
</form>

<?php
 printf("<h2>" . T_("Referendums found in %s categories") . "</h2>\n",
        $globalreferendums_len);
 echo "<table class='list'>\n";

 echo "<tr>\n";
 echo drawSortHeader($globalreferendums_sort, 'globalreferendums_sort', 'path', T_('Category'));
 echo drawSortHeader($globalreferendums_sort, 'globalreferendums_sort', 'referendums', T_('Matches'));
 echo "</tr>\n";

 foreach ($globalreferendums as $attr) {
  $args = array('view' => 'category', 'category' => $attr['path']);
  foreach ($_GET as $key => $value)
   if (beginsWith($key, 'globalreferendum_'))
    $args['referendum_' . substr($key, strlen('globalreferendum_'))] = $value;
  $url = $_SERVER["SCRIPT_NAME"] . "?" . queryString(queryConstruct($args, array(), array('globalreferendum_', 'referendum_')));
  $title = $attr['path'];
  if ($title)
   $title = 'Top.' . $title;
  else
   $title = 'Top';
  echo "<tr class='list_item list_item_collapsed list_item_globalreferendums'>\n" .
       " <td><a href='{$url}'>{$title}</a></td>\n" .
       " <td><a href='{$url}'>{$attr['referendums']}</a></td>\n" .
       "</tr>\n";
 }
echo "</table>\n";
 echo drawPrevNextPage($globalreferendums_len,
		       $globalreferendum_page_start,
		       $globalreferendum_page_len,
		       'globalreferendum_page_start',
		       'globalreferendum_page_len');
?>
