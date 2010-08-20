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
 if (   in_array('propose', $_SESSION['privs'])
     && $is_category
     && isset($_POST['new_referendum'])) {
  $sql = "begin;
	  insert into referendum (title, category) values ('{$_POST['new_referendum_title']}', '{$category_id}');
	 ";
  if ($category_type == 'category') {
   $add = pg_escape_string($_POST['new_referendum_add']);
   $path = pg_escape_string($category_path_connect . $_POST['new_referendum_path']);
   $breakpoint = pg_escape_string($_POST['new_referendum_breakpoint']);
   $type = pg_escape_string($_POST['new_referendum_type']);
   $text = pg_escape_string($_POST['new_referendum_text']);
   $sql .= "insert into referendum_type_category (referendum, add, path, breakpoint, type, text)
	     select
	      currval('referendum_id_seq'),
	      '{$add}',
	      '{$path}',
	      '{$breakpoint}',
	      '{$type}',
	      '{$text}';";
  } else if ($category_type == 'text') {
   $text = pg_escape_string($_POST['new_referendum_text']);
   $sql .= "insert into referendum_type_text (referendum, text)
	     select
	      currval('referendum_id_seq'),
	      '{$text}';";
  } else if ($category_type == 'law') {
   $para_nr = (int) $_POST['new_referendum_paragraphs'];

   $para_ids = array();
   $para_root = array();
   for ($para = 0; $para < $para_nr; $para++)
    if ($_POST["new_referendum_{$para}_path"] != '') {
     $path = $_POST["new_referendum_{$para}_path"];
     $relative = false;
     if (beginsWith($path, 'n')) {
      list($relative, $path) = explode('.', $path, 2);
      sscanf($relative, "n%d", &$relative);
     }
     $para_ids[$para] = array('relative' => $relative,
			      'path' => $path,
			      'id' => $para,
			      'type' => $_POST["new_referendum_{$para}_type"],
			      'title' => $_POST["new_referendum_{$para}_title"],
			      'text' => $_POST["new_referendum_{$para}_text"],
			      'sub' => array(),
			      'next' => array());
     $para_root[$para] = &$para_ids[$para];
    }
   for ($para = 0; $para < $para_nr; $para++)
    if (isset($para_ids[$para]))
     if ($para_ids[$para]['relative'] !== false) {
      if ($para_ids[$para]['path'] == '')
       $para_ids[$para_root[$para]['relative']]['next'][] = &$para_ids[$para];
      else
       $para_ids[$para_root[$para]['relative']]['sub'][] = &$para_ids[$para];
      unset($para_root[$para]);
     }

   if (isset($_FILES["new_referendum_import"])) {
    $file = $_FILES["new_referendum_import"];
    if (!is_uploaded_file($file['tmp_name']))
     die(T_('Please enter a valid path for the file to upload'));
    $doc = new DOMDocument();
    $doc->load($file['tmp_name']);
    $doc->xinclude();
    $doc->validate()
     or die('Malformed XML file');
    if ($doc->doctype->publicId != 'DemoWave//law//0.1.0')
     die('Not a DemoWave Law file');

    function domNodeIsEmpty($node) {
     return (   $node->nodeType == XML_TEXT_NODE
	     && trim($node->data) == '');
    }

    function insertNode($rel, $path, $node) {
     global $para_root, $para_ids, $para_nr;

     $children = $node->childNodes;
     $para = false;
     $title = '';
     $text = '';
     $pos = 0;
     while ($pos < $children->length && domNodeIsEmpty($children->item($pos))) $pos++;
     if ($pos < $children->length) {
      $item = $children->item($pos);
      if (   $item->nodeType == XML_ELEMENT_NODE
	  && $item->tagName == 'h') {
       $title = trim($item->firstChild->data);
       $pos++;
      }
     }
     while ($pos < $children->length && domNodeIsEmpty($children->item($pos))) $pos++;
     if ($pos < $children->length) {
      $item = $children->item($pos);
      if ($item->nodeType == XML_TEXT_NODE) {
       $text = trim($item->data);
       $pos++;
      }
     }

     if ($text != '' || $title != '') {
      $para = $para_nr++;
      $para_ids[$para] = array('relative' => $rel,
			       'id' => $para,
			       'type' => 'add',
			       'title' => $title,
			       'text' => $text,
			       'sub' => array(),
			       'next' => array());
      if ($path != '') {
       $para_ids[$para]['path'] = $path;
       if ($rel !== false)
        $para_ids[$rel]['sub'][] = &$para_ids[$para];
      } else {
       $para_ids[$para]['path'] = '';
       if ($rel !== false)
        $para_ids[$rel]['next'][] = &$para_ids[$para];
      }
      if ($rel === false)
       $para_root[$para] = &$para_ids[$para];

      $subRel = $para;
      $subPath = '0';
     } else {
      $subRel = $rel;
      $subPath = pathCombine($path, 0, '0');
     }

     $prevChild = insertNodes($children, $pos, $subRel, $subPath);

     return $para;
    }

    function insertNodes($nodes, $pos, $subRel = false, $subPath = '') {
     $prevChild = false;
     while ($pos < $nodes->length && domNodeIsEmpty($nodes->item($pos))) $pos++;
     while ($pos < $nodes->length) {
      if ($prevChild === false) 
       $prevChild = insertNode($subRel, $subPath, $nodes->item($pos++));
      else
       $prevChild = insertNode($prevChild, '', $nodes->item($pos++));
      while ($pos < $nodes->length && domNodeIsEmpty($nodes->item($pos))) $pos++;
     }
     return $prevChild;
    }

    insertNodes($doc->documentElement->childNodes, 0, false, $_POST["new_referendum_import_path"]);
   }

   function paraToSql($para) {
    global $sql, $category_id;

    if ($para['type'] == 'add') {
     if ($para['relative'] === false)
      $sql .= "insert into referendum_type_law (referendum, id, add, path, title, text)
		select
		 currval('referendum_id_seq'),
		 '{$para['id']}',
		 true,
		 law_next_paragraph({$category_id}, '{$para['path']}'),
		 '{$para['title']}',
		 '{$para['text']}';
	      ";
     else
      $sql .= "insert into referendum_type_law (referendum, id, add, path, title, text)
		select
		 currval('referendum_id_seq'),
		 '{$para['id']}',
		 true,
		 law_next_paragraph({$category_id},
				    law_paragraph_combine(path, 0, '{$para['path']}')),
		 '{$para['title']}',
		 '{$para['text']}'
		from
		 referendum_type_law
		where     referendum = currval('referendum_id_seq')
		      and id = '{$para['relative']}';
	      ";
    } else if ($para['type'] == 'change') {
     $sql .= "insert into referendum_type_law (referendum, id, add, path, title, text)
	       select
		currval('referendum_id_seq'),
		'{$para['id']}',
		true,
		'{$para['path']}',
		'{$para['title']}',
		'{$para['text']}';
	     ";
    } else if ($para['type'] == 'delete') {
     $sql .= "insert into referendum_type_law (referendum, id, add, path)
	       select
		currval('referendum_id_seq'),
		'{$para['id']}',
		false,
		'{$para['path']}';
	     ";
    }

    foreach (array_reverse($para['sub']) as $sub)
     paraToSql($sub);

    foreach (array_reverse($para['next']) as $next)
     paraToSql($next);

   }

   foreach (array_reverse($para_root) as $root)
    paraToSql($root);


  }
  $sql .= "end;";

  pg_query($dbconn, $sql)
   or die('Unable to add referendum: ' . pg_last_error());
  $row = pg_fetch_row(pg_query($dbconn, "select currval('referendum_id_seq');"))
   or die('Unable to fetch id of new referendum: '. pg_last_error());
  $referendum = $row[0];


  $referendumurl = $_SERVER["SCRIPT_NAME"] . '?' .
		   queryString(queryConstruct(array('view' => 'category',
						    'categoryview' => 'referendums',
						    'referendum_search_status__1_list' => 'on',
						    'referendum_search_status__0_list' => 'on',
						    'referendum_search_status__-1_list' => 'on',
						    'referendum_search_referendum_lower' => $referendum,
						    'referendum_search_referendum_upper' => $referendum,
						    'expanded_referendums' => $referendum),
					      array(),
					      array('referendum_search_')));

  if (isset($fudinc)) {
   $fudid = fud_new_topic(sprintf(T_("%s:%s: %s"),
				  $category_path, $referendum, $_POST['new_referendum_title']),
			  sprintf("<h1><a href='%s' target='_blank'>" . T_("%s:%s: %s") . "</a></h1>" .
				  T_("This thread is for discussions on the above DemoWave administered referendum."),
				  $referendumurl, $category_path, $referendum, $_POST['new_referendum_title']),
			  3, $fuduser, $fudforum);
   pg_query($dbconn, "insert into referendum_fud (referendum, fudid) values('{$referendum}', '{$fudid}')")
    or die('Unable to add referendum FUDfudforum topic: ' . pg_last_error());
  }

  header("Status: 303 See Other");
  header("Location: " . $referendumurl);
  exit(1);
 }
?>
