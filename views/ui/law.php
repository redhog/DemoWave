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
 function drawLawContentList($node) {
  if (isset($node['edit']))
   return drawLawContentList($node['edit']);
  $res = '';
  if (isset($node['add']) && $node['title'] != '')
   $res .= sprintf("<a href='#law-%s'>%s: %s</a>\n",
	           $node['path'], $node['path'], $node['title']);
  $childres = '';
  foreach ($node['sub'] as $subNode) {
   $subres = drawLawContentList($subNode);
   if ($subres != '')
    $childres .= "<li>\n{$subres}</li>\n";
  }
  if ($childres != '')
   $res .= "<ul>\n{$childres}</ul>\n";
  return $res;
 }

 function drawLawParagraph($level, $node) {
  if (isset($node['add']) && ($node['add'] == 't' || $_GET["law_show__deleted_list"])) {
   if (isset($_GET["law_show__referendum_list"])) {
    $dateurl = '?' . queryString(queryConstruct(array('law_date' => $node['changed'],
                                                      'law_proposal' => $node['referendum'])));
    $refurl = '?' . queryString(queryConstruct(array('view' => 'category',
						     'categoryview' => 'referendums',
						     'referendum_search_status__1_list' => 'on',
						     'referendum_search_status__0_list' => 'on',
						     'referendum_search_status__-1_list' => 'on',
						     'referendum_search_referendum_lower' => $node['referendum'],
						     'referendum_search_referendum_upper' => $node['referendum'],
						     'expanded_referendums' => $node['referendum']),
					       array(),
					       array('referendum_search_')));
    printf("<div class='law_info'>
	     Last changed: <a href='%s'>%s</a><br />
	     By: <a href='%s'>%s: %s</a><br />
	    </div>
	   ",
	   $dateurl, $node['changed'],
	   $refurl, $node['referendum'], $node['reftitle']); 
   }

   if ($node['add'] != 't' || $node['title'] != '' || isset($_GET["law_show__referendum_list"])) {
    if ($node['add'] == 't') {
     $title = sprintf(T_("%s: %s"), $node['path'], $node['title']);
     $cls = 'law_head_added';
    } else {
     $title = sprintf(T_("Paragraph deleted: %s"), $node['path']);
     $cls = 'law_head_deleted';
    }
    printf(T_("<h%s class='law_head law_head_%s %s'>%s</h%s>\n"),
	   $level, $level, $cls, $title, $level);
   }

   $node_text = $node['text'];
   // regexp change §para.para.para to a intra-document-link
   // regexp change §document.document/para.para.para to an inter-document-link

   if ($node['add'] == 't')
    if ($node['title'] != '' || isset($_GET["law_show__referendum_list"]))
     printf("<div class='law_text'>%s</div>", $node_text);
    else
     printf("<div class='law_text'><em>%s:</em> %s</div>", $node['path'], $node_text);
  }
 }

 function drawLaw($level, $node) {
  if (isset($node['path'])) {
   printf("<div class='law law_%s' id='law-%s'>\n", $level, $node['path']);

   if (isset($node['edit'])) {
    printf("<div class='law_new'>\n");
    drawLawParagraph($level, $node['edit']);
    printf("</div>
	    <div class='law_orig'>
	   ");
    drawLawParagraph($level, $node);
    printf("</div>\n");
   } else
    drawLawParagraph($level, $node);
  }

  foreach ($node['sub'] as $subNode)
   drawLaw($level + 1, $subNode);

  if (isset($node['path'])) {
   echo "</div>\n";
  }
 }
?>


<?php
 $lawexporturl = '?' . queryString(queryConstruct(array('categoryview' => 'law', 'format' => 'export'), array(), array('law_')));
 echo "<h2 class='heading_collapsed'><a href='{$lawexporturl}'>" .  T_("Export to file") . "</a></h2>";

 if (isset($_GET['select_law_view']) && $_GET['select_law_view']) {
  $status = "heading_expanded";
  $args = queryString(queryConstruct(array(), array('select_law_view')));
 } else {
  $status = "heading_collapsed";
  $args = queryString(queryConstruct(array('select_law_view' => '1'), array()));
 }
 $title = T_("Select what to show");
 echo "<h2 class='{$status}'><a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$title}</a></h2>";
?>
<?php
 if (isset($_GET['select_law_view']) && $_GET['select_law_view']) {
  echo "<form method='get' enctype='multipart/url-encoded'>\n";
  foreach ($_GET as $key => $value)
   if (!beginsWith($key, 'law_'))
    echo "<input name='{$key}' value='{$value}' type='hidden'>\n";
  echo "<table>\n";
  echo "<tr>" . drawSearchField(T_("Show paragraphs as of"), 'law_date', false, 'match') . "</tr>\n";
  echo "<tr>" . drawSearchField(T_("Show differences introduced by"), 'law_proposal', false, 'match') . "</tr>\n";
  echo "<tr>" . drawSearchField(T_("Show"), 'law_show',
				array(T_("Deleted paragraphs") => 'deleted',
				      T_("Referendum") => 'referendum'
				     ),
				'list') . "</tr>\n";
  echo drawInputRow("",
		     "<input type='submit' name='selectdate_law' value='" . T_("Show") . "' />");
  echo "</table>\n";
  echo "</form>\n";
 }
?>

<h2><?php E_("Contents"); ?></h2>
<?php echo drawLawContentList($laws); ?>

<?php drawLaw(0, $laws); ?>
