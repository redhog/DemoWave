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
  $res = '';
  $children = $node['sub'];
  $path = $node['path'];
  if (isset($node['edit'])) $node = $node['edit'];
  if (isset($node['add']) && $node['title'] != '') {
   if (strstr($path, '-') === false)
    $id = $path;
   else
    $id = "New";
   $res .= sprintf("<a href='#law-%s'>%s: %s</a>\n",
	           $path, $id, $node['title']);
  }
  $childres = '';
  foreach ($children as $subNode) {
   $subres = drawLawContentList($subNode);
   if ($subres != '')
    $childres .= "<li>\n{$subres}</li>\n";
  }
  if ($childres != '')
   $res .= "<ul>\n{$childres}</ul>\n";
  return $res;
 }

 function drawLaw($level, $node) {
  printf("<div class='law law_%s' id='law-%s'>\n", $level, $node['path']);

  if ($level > 0) {
   if (strstr($node['path'], '-') === false)
    $id = $node['path'];
   else
    $id = "New";

   if (isset($node['edit'])) {
    if ($node['edit']['add'] == 't') {
     printf("<div class='law_head law_edit_head law_head_%s law_head_added'>
              %s:
	      <input
               class='head_input'
	       name='law_edit_title_%s'
	       type='text'
	       value='%s'
	      />
	      <input
               class='clickable_head law_edit_cancel'
	       name='law_edit_cancel_%s'
	       type='submit'
	       value='<-'
	      />
	      <input
               class='clickable_head law_edit_delete'
	       name='law_edit_delete_%s'
	       type='submit'
	       value='X'
	      />
	     </div>
             <div class='law_text'>
              <textarea name='law_edit_text_%s'>%s</textarea>
             </div>
	    ",
	    $level,
            $id,
            $node['path'], $node['edit']['title'],
            $node['path'],
            $node['path'],
            $node['path'], $node['edit']['text']);
    } else {
     $title = sprintf(T_("Paragraph deleted: %s"), $id);
     printf("<div class='law_head law_edit_head law_head_%s law_head_deleted'>
              <input
               class='clickable_head law_edit_edit'
	       name='law_edit_edit_%s'
	       type='submit'
	       value='%s'
	      />
	      <input
               class='clickable_head law_edit_cancel'
	       name='law_edit_cancel_%s'
	       type='submit'
	       value='<-'
	      />
	     </div>
	    ",
	    $level,
	    $node['path'], $title,
	    $node['path']);
    }
   } else {
    if (!isset($node['add'])) {
     $title = sprintf(T_("Paragraph not created: %s"), $id);
     $cls = "law_head_virtual";
    } else if ($node['add'] == 't') {
     $title = sprintf(T_("%s: %s"), $id, $node['title']);
     $cls = "law_head_added";
    } else {
     $title = sprintf(T_("Paragraph deleted: %s"), $id);
     $cls = "law_head_deleted";
    }
    printf("<div class='law_head law_edit_head law_head_%s %s'>
             <input
              class='clickable_head law_edit_edit'
	      name='law_edit_edit_%s'
	      type='submit'
	      value='%s'
	     />
	     <input
               class='clickable_head law_edit_delete'
	      name='law_edit_delete_%s'
	      type='submit'
	      value='X'
	     />
	    </div>
	   ",
	   $level, $cls,
	   $node['path'], $title,
	   $node['path']);

    if ($node['add'] == 't')
     printf("<div class='law_text'>%s</div>", $node['text']);
   }
  }

  printf("<div class='law law_%s'>
	   <input
	    class='law_edit_insert'
	    name='law_edit_insert_%s'
	    type='submit'
	    value='Insert new paragraph after %s' />
	  </div>
	 ", $level + 1, pathCombine($node['path'], 0, '0'), pathCombine($node['path'], 0, '0'));

  foreach ($node['sub'] as $key => $subNode)
   if ($key != '0')
    drawLaw($level + 1, $subNode);

  printf("<input
	   class='law_edit_insert'
	   name='law_edit_insert_%s'
	   type='submit'
	   value='Insert new paragraph after %s' />
	 ",
	 $node['path'], $node['path']);

  echo "</div>\n";
 }
?>

<form method="post" enctype="multipart/form-data">
 <input type='hidden' name='law_edit_continue' value='yes' />

 <table class="law_importexport">
  <?php
   echo drawInputRow(T_("Import from file"),
		      "<input name='law_import_file' type='file'>");
   echo drawInputRow('', "<input name='law_import' type='submit' value='" . T_("Import") . "'>");

   $lawexporturl = '?' . queryString(queryConstruct(array('categoryview' => 'laweditor', 'format' => 'export', 'law_edit_continue' => 'yes')));
   echo drawInputRow(T_("Export to file"), "<a href='{$lawexporturl}' target='demowave-export'>" . T_("Export") . "</a>");

  ?>
 </table>

 <table>
  <?php
   echo drawInputRow(T_("Change summary"), "<input type='text' name='law_title' value='{$_SESSION["laweditor"]["title"]}' />");
   echo drawInputRow('', "<input name='law_save' type='submit' value='" . T_("Save changes") . "'>");
  ?>
 </table>

 <h3><?php E_("Contents"); ?></h3>
 <?php echo drawLawContentList($_SESSION["laweditor"]['laws']); ?>

 <?php drawLaw(0, $_SESSION["laweditor"]['laws']); ?>
</form>
