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
<?php if ($is_category) { ?>

<?php
 if (array_get($_GET, 'search_referendums_show', '')) {
  $status = "heading_expanded";
  $args = queryString(queryConstruct(array(), array('search_referendums_show')));
 } else {
  $status = "heading_collapsed";
  $args = queryString(queryConstruct(array('search_referendums_show' => '1'), array()));
 }
 $title = T_("Search for referendums");
 echo "<h2 class='{$status}'><a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$title}</a></h2>";
?>
<?php
 if (array_get($_GET, 'search_referendums_show', '')) {
  ?>
   <form action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" method="get" enctype="multipart/url-encoded">
    <?php

     foreach ($_GET as $key => $value)
      if (!beginsWith($key, 'referendum_search_'))
       echo "<input name='{$key}' value='{$value}' type='hidden'>\n";

    ?>
    <table>
     <tr> 
      <?php
       echo drawSearchField(T_('Id'), 'referendum_search_referendum', false, 'bound', 10);
       echo drawSearchField(T_('Total'), 'referendum_search_area', false, 'bound', 10, 'vote-sum');
      ?>
     </tr>
     <tr>
      <?php
       echo drawSearchField(T_('Title'), 'referendum_search_title', false, 'match', 10);
       echo drawSearchField(T_('Current'), 'referendum_search_sum', false, 'bound', 10, 'current-vote-sum');
      ?>
     </tr>
     <tr>
      <?php
       echo drawSearchField(T_('Started'), 'referendum_search_start', false, 'bound', 10, 'start');
       echo drawSearchField(T_('Vote'), 'referendum_search_last', false, 'bound', 10, 'current-vote');
      ?>
     </tr>
     <tr>
      <?php
       echo drawSearchField(T_('Estimate'), 'referendum_search_completed', false, 'bound', 10, 'completed');
       if ($category_type == 'law') {
        echo drawSearchField(T_('Paragraphs'), 'referendum_search_path', false, 'multimatch', 10, 'law-paragraph');
       }
      ?>
     </tr>
     <tr>
      <?php
       echo drawSearchField(T_('Status'), 'referendum_search_status',
			    array(T_('Passed') => 1,
				  T_('Active') => 0,
				  T_('Rejected') => -1),
			    'list', 10, 'referendum-status');
      ?>
     </tr>
    </table>
    <input type="submit" name="search_referendums" value="<?php E_("Search"); ?>">
   </form>
  <?php
 }
?>
<?php } ?>
