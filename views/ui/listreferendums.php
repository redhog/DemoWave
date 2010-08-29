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

<h2>
 <?php
  $statuses = array();
  if (!isset($referendum_search['status']['list']))
   $statuses = array(T_('Referendums'), T_('Decisions'), T_('Rejected proposals'));
  else
   foreach ($referendum_search['status']['list'] as $status)
    if ($status == '1')
     $statuses[] = T_('Decisions');
    else if ($status == '0')
     $statuses[] = T_('Active referendums');
    else if ($status == '-1')
     $statuses[] = T_('Rejected proposals');
  echo $referendums_len . " " . implode('/', $statuses);
 ?>
</h2>

<form action="<?php 
 echo $_SERVER["SCRIPT_NAME"] . '?' . queryString(queryConstruct());
?>" method="post" enctype="multipart/form-data">
 <table class='list'>
  <?php

   echo "<tr>\n";
   echo drawSortHeader($referendums_sort, 'referendums_sort', 'referendum', T_('Id'));
   echo drawSortHeader($referendums_sort, 'referendums_sort', 'title', T_('Title'));
   echo drawSortHeader($referendums_sort, 'referendums_sort', 'start', T_('Started'), 'start');
   $completed_titles = array();
   if (!isset($referendum_search['status']['list']))
    $completed_titles = array(T_('Estimate'), T_('Passed'), T_('Rejected'));
   else
    foreach ($referendum_search['status']['list'] as $status)
     if ($status == '1')
      $completed_titles[] = T_('Passed');
     else if ($status == '0')
      $completed_titles[] = T_('Estimate');
     else if ($status == '-1')
      $completed_titles[] = T_('Rejected');
   echo drawSortHeader($referendums_sort, 'referendums_sort', 'completed', implode('/', $completed_titles), 'completed');
   echo drawSortHeader($referendums_sort, 'referendums_sort', 'area', T_('Total'), 'vote-sum');
   echo drawSortHeader($referendums_sort, 'referendums_sort', 'sum', T_('Current'), 'current-vote-sum');
   echo drawSortHeader($referendums_sort, 'referendums_sort', 'last', T_('Vote'), 'current-vote');
   echo "</tr>\n";

   $pyjamas = 'odd_row';

   if (!$referendums)
    {
     echo "<tr><td class='emptytable' colspan='7'>No " . implode('/', $statuses) . " found.</td></tr>\n";
    }
   else
    {
     foreach ($referendums as $attr)
      {
       if ($pyjamas == 'odd_row')
	$pyjamas = 'even_row';
       else
	$pyjamas = 'odd_row';
       if ($attr['expanded'])
	$args = array_diff($expanded_referendums, array($attr['referendum']));
       else
	$args = array_merge($expanded_referendums, array($attr['referendum']));
       $args = queryString(queryConstruct(array('expanded_referendums' => implode(',', $args))));
       $url = $_SERVER["SCRIPT_NAME"] . "?" . $args;
       $expcls = 'list_item_collapsed';
       if ($attr['expanded']) $expcls = 'list_item_expanded';
       if ($attr['status'] == '1')
	$statcls = 'list_item_referendum_passed';
       else if ($attr['status'] == '0')
	$statcls = 'list_item_referendum_active';
       else if ($attr['status'] == '-1')
	$statcls = 'list_item_referendum_rejected';
       if ($attr['status'] == '1')
	$area = T_("Passed");
       else if ($attr['status'] == '-1')
	$area = T_("Rejected");
       else
	$area = $attr['area'];
       echo "<tr class='list_item {$expcls} list_item_referendum {$statcls} {$pyjamas}'>\n" .
	    " <td><a href='{$url}'>{$attr['referendum']}</a></td>\n" .
	    " <td><a href='{$url}'>{$attr['title']}</a></td>\n" .
	    " <td><a href='{$url}'>{$attr['start']}</a></td>\n" .
	    " <td><a href='{$url}'>{$attr['completed']}</a></td>\n" .
	    " <td><a href='{$url}'>{$area}</a></td>\n" .
	    " <td><a href='{$url}'>{$attr['sum']}</a></td>\n";
       if (!in_array('vote', $_SESSION['privs']) || $attr['status'] != '0') {
	if ($attr['last'] == '1') $comment = T_('1 Yes');
	else if ($attr['last'] == '0') $comment = T_('0 No vote');
	else $comment = T_('-1 No');
	echo " <td><a href='{$url}'>{$comment}</a></td>\n";
       } else {
	echo " <td><select name='vote_{$attr['referendum']}'>\n";
	echo drawOption($attr['last'] > 0, 1, T_("1 Yes"));
	echo drawOption($attr['last'] == 0, 0, T_("0 No vote"));
	echo drawOption($attr['last'] < 0, -1, T_("-1 No"));
	echo " </select></td>";
       }
       echo "</tr>";

       if ($attr['expanded']) {
	echo "<tr class='list_item_expansion list_item_expansion_referendum {$pyjamas}'>" .
	     " <td>&nbsp;</td>" .
	     " <td colspan='6'>";
	$links = array();
	if (isset($fudinc) && $attr['fudid'] != '0') {
	 $links[] = sprintf("<a href='{$fudurl}' target='demowave-forum'>%2\$s</a>\n",
			    $attr['fudid'],
			    T_("Discussion forum"));
	}
	if ($category_type == "law") {
	 $proposals = array();
	 if (isset($_GET["law_proposal"]) && $_GET["law_proposal"]) {
	   $proposals = explode(',', $_GET["law_proposal"]);
         }
	 $proposals[] = $attr['referendum'];
	 $proposals = implode(",", $proposals);
	 $url = '?' . queryString(queryConstruct(array('categoryview' => 'law',
						       'law_show__deleted_list' => 'on',
						       'law_date' => $attr['completed'],
						       'law_show__referendum_list' => '1',
						       'law_proposal' => $proposals)));
	 $links[] = sprintf("<a href='%s'>%s</a>\n",
			    $url,
			    T_("Law view"));

	 $url = '?' . queryString(queryConstruct(array('categoryview' => 'laweditor',
						       'law_proposal' => $attr['referendum'])));
	 $links[] = sprintf("<a href='%s'>%s</a>\n",
			    $url,
			    T_("Edit as new"));
	}
	$url = '?' . queryString(queryConstruct(array('view' => 'graph',
						      'referendum' => $attr['referendum'],
						      'format' => 'export')));
	$links[] = sprintf("<a href='%s' target='demowave-graph'>%s</a>\n",
			   $url,
			   T_("Graph"));
	if (count($links) > 0)
	 printf(T_("%s: %s<br />"),
		T_("Go to"), implode(' ', $links));
	if ($category_type == "category") {
	 if ($attr['add'] == 't')
	  printf(T_("Action: Add category %s<br />" .
		    "For referendums of type: %s<br />" .
		    "Description: %s<br />"),
		 $attr['path'],
		 T_($attr['type']),
		 $attr['text']);
	 else
	  printf(T_("Action: Delete category %s<br />"), $attr['path']);
	} else if ($category_type == "text") {
	   echo $attr['text'] . '<br />';
	} else if ($category_type == "law") {
	 if (!isset($attr['subrows'])) $attr['subrows'] = array($attr);
	 foreach ($attr['subrows'] as $subrow) {
	  echo "<div class='lawreferendumitem'>";
	  if ($subrow['add'] == 't')
	   printf(T_("<h3>%s: %s</h3>\n%s"),
		  $subrow['path'],
		  $subrow['paragraphtitle'],
		  $subrow['text']);
	  else
	   printf(T_("<h3>%s: (Removed)</h3>"), $subrow['path']);
	  echo "</div>";
	 }
	}
	echo " </td>" .
	     "</tr>";
       }
      }

     if ($category_type == "law") {
      $proposals = array();
      if (isset($_GET["law_proposal"]) && $_GET["law_proposal"]) {
	$proposals = explode(',', $_GET["law_proposal"]);
      }
      foreach ($referendums as $attr) {
       $proposals[] = $attr['referendum'];
      }
      $proposals = implode(",", $proposals);
      $url = '?' . queryString(queryConstruct(array('categoryview' => 'law',
						    'law_show__deleted_list' => 'on',
						    'law_date' => $attr['completed'],
						    'law_show__referendum_list' => '1',
						    'law_proposal' => $proposals)));
    ?>
      <tr><td></td><td></td><td></td><td></td><td></td><td></td><td><?php printf("<a href='%s'>%s</a>\n", $url, T_("Law view")); ?></td></tr>
    <?php
     }

     if ($_GET['categoryview'] == 'referendums')
      {
    ?>
    <tr><td></td><td></td><td></td><td></td><td></td><td></td><td><input type="submit" name="save_referendums_vote" value="<?php E_("Cast votes"); ?>"></td></tr>
    <?php
      }
    }
  ?>
 </table>
 <?php
  echo drawPrevNextPage($referendums_len,
			$referendum_page_start,
			$referendum_page_len,
			'referendum_page_start',
			'referendum_page_len');
 ?>
</form>

<?php } ?>
