<?php

 if (   in_array('propose', $_SESSION['privs'])
     && $_GET['view'] == 'category'
     && $_GET['categoryview'] == 'laweditor'
     && isset($_POST["law_edit_continue"])) {

  function addParagraph($path, $add='t', $title ='', $text='') {
   $fullpath = explode('.', $path);
   $path = $fullpath;
   $head = $path[count($path) - 1];
   unset($path[count($path) - 1]);
   $node = &$_SESSION["laweditor"]['laws'];
   foreach ($path as $item)
    $node = &$node['sub'][$item];
   if (!isset($node['sub'][$head]))
    $node['sub'][$head] = array('path' => implode('.', $fullpath), 'sub' => array());
   $prevNode = &$node['sub'][$head];
   if (!isset($prevNode['nextId'])) $prevNode['nextId'] = 0;

   $id = $head;
   if (strstr($id, '-') === false) $id .= '-';
   $id = $id . str_pad('', $prevNode['nextId']++, "0") . '1';
   $newPath = implode('.', array_merge($path, array($id)));
   $node['sub'][$id] = array('sub' => array(),
			     'path' => $newPath,
			     'add' => 'f',
			     'title' => '',
			     'text' => '',
			     'edit' => array('add' => $add,
					     'title' => $title,
					     'text' => $text));
   ksort(&$node['sub'], SORT_STRING);
   return $newPath;
  }

  function editParagraph($path, $title=false, $text=false) {
   $apath = explode('.', $path);
   $node = &$_SESSION["laweditor"]['laws'];
   foreach ($apath as $item)
    $node = &$node['sub'][$item];
   if ($title === false) $title = $node['title'];
   if ($text === false) $text = $node['text'];
   $node['edit'] = array('add' => 't',
			 'title' => $title,
			 'text' => $text);
   return $path;
  }

  function deleteParagraph($path) {
   $apath = explode('.', $path);
   $node = &$_SESSION["laweditor"]['laws'];
   foreach ($apath as $item)
    $node = &$node['sub'][$item];
   $node['edit'] = array('add' => 'f');
   return $path;
  }

  function cancelParagraph($path) {
   $apath = explode('.', $path);
   $node = &$_SESSION["laweditor"]['laws'];
   foreach ($apath as $item)
    $node = &$node['sub'][$item];
   unset($node['edit']);
   return $path;
  }

  foreach ($_POST as $key => $value)
   if ($key == "law_title")
    $_SESSION["laweditor"]["title"] = $value;
   else if (beginsWith($key, "law_edit_insert_"))
    addParagraph(str_replace('_', '.', substr($key, strlen("law_edit_insert_"))));
   else if (beginsWith($key, "law_edit_edit_"))
    editParagraph(str_replace('_', '.', substr($key, strlen("law_edit_edit_"))));
   else if (beginsWith($key, "law_edit_delete_"))
    deleteParagraph(str_replace('_', '.', substr($key, strlen("law_edit_delete_"))));
   else if (beginsWith($key, "law_edit_cancel_")) {
    cancelParagraph(str_replace('_', '.', substr($key, strlen("law_edit_cancel_"))));
   } else if (beginsWith($key, "law_edit_title_")) {
    $path = explode('_', substr($key, strlen("law_edit_title_")));
    $node = &$_SESSION["laweditor"]['laws'];
    foreach ($path as $item)
     $node = &$node['sub'][$item];
    if (isset($node['edit'])) // Don't fill this in if the item just got cancelled
     $node['edit']['title'] = $value;
   } else if (beginsWith($key, "law_edit_text_")) {
    $path = explode('_', substr($key, strlen("law_edit_text_")));
    $node = &$_SESSION["laweditor"]['laws'];
    foreach ($path as $item)
     $node = &$node['sub'][$item];
    if (isset($node['edit'])) // Don't fill this in if the item just got cancelled
     $node['edit']['text'] = $value;
   }

  if (   isset($_POST["law_import"])
      && isset($_FILES["law_import_file"])) {
   $file = $_FILES["law_import_file"];
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

   function insertNode($path, $node) {
    $children = $node->childNodes;
    $virtual = true;
    $title = '';
    $text = '';
    $pos = 0;
    while ($pos < $children->length && domNodeIsEmpty($children->item($pos))) $pos++;
    if ($pos < $children->length) {
     $item = $children->item($pos);
     if (   $item->nodeType == XML_ELEMENT_NODE
	 && $item->tagName == 'h') {
      $title = trim($item->firstChild->data);
      $virtual = false;
      $pos++;
     }
    }
    while ($pos < $children->length && domNodeIsEmpty($children->item($pos))) $pos++;
    if ($pos < $children->length) {
     $item = $children->item($pos);
     if ($item->nodeType == XML_TEXT_NODE) {
      $text = trim($item->data);
      $virtual = false;
      $pos++;
     }
    }

    if ($path != '')
     if ($node->hasAttribute('delete')) {
      if (!$node->hasAttribute('id') || !$virtual)
       die('Malformed XML: delete tag has no id or is virtual');
      $newPath = deleteParagraph(pathCombine($path, 1, $node->getAttribute('id')));
     } else if ($node->hasAttribute('id')) {
      if (!$virtual)
       $newPath = editParagraph(pathCombine($path, 1, $node->getAttribute('id')), $title, $text);
      else
       $newPath = pathCombine($path, 1, $node->getAttribute('id'));
     } else {
      if (!$virtual)
       $newPath = addParagraph($path, 't', $title, $text);
      else
       $newPath = addParagraph($path, 'f');
     }

    $lastPath = pathCombine($newPath, 0, '0');
    while ($pos < $children->length && domNodeIsEmpty($children->item($pos))) $pos++;
    while ($pos < $children->length) {
     $lastPath = insertNode($lastPath, $children->item($pos++));
     while ($pos < $children->length && domNodeIsEmpty($children->item($pos))) $pos++;
    }

    return $newPath;
   }

   insertNode('', $doc->documentElement);
   if ($doc->documentElement->hasAttribute('title'))
    if ($_SESSION["laweditor"]["title"] == '')
     $_SESSION["laweditor"]["title"] = $doc->documentElement->getAttribute('title');
    else
     $_SESSION["laweditor"]["title"] = $doc->documentElement->getAttribute('title') . ' - ' . $_SESSION["laweditor"]["title"];
  }

 }
?>
