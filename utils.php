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

if (!function_exists('array_combine')) {
 require_once 'PHP/Compat/Function/array_combine.php';
}

if (!function_exists('array_diff_key')) {
 require_once 'PHP/Compat/Function/array_diff_key.php';
}

function beginsWith($str, $sub)
 {
  return (substr($str, 0, strlen($sub)) === $sub);
 }

function endsWith($str, $sub)
 {
  return (substr($str, strlen($str) - strlen($sub)) === $sub);
 }

function E_($str) { echo T_($str); }

function queryConstruct($setargs = array(), $unsetargs = array(), $unsetpatterns = array())
 {
  $res = $_GET;
  if (count($unsetpatterns) > 0) {
   $newres = array();
   foreach ($res as $key => $value) {
    $unset = false;
    foreach ($unsetpatterns as $pattern)
     $unset = $unset || beginsWith($key, $pattern);
    if (!$unset)
     $newres[$key] = $value;
   }
   $res = $newres;
  }
  return array_diff_key(array_merge($res, $setargs), array_flip($unsetargs));
 }

function queryString($args = NULL)
 {
  if ($args === NULL) $args = $_GET;
  $res = array();
  foreach($args as $name => $value)
   $res[] = urlencode($name) . '=' . urlencode($value);
  $res = implode('&', $res);
  return $res;
 }

function addSort($oldsort, $key, $order) {
 $newsort = array(array($key, $order),);
 foreach ($oldsort as $sortkey)
  if ($sortkey[0] != $key)
   $newsort[] = $sortkey;
 return $newsort;
}

function flipSort($oldsort, $key) {
 if ($oldsort[0][0] == $key && $oldsort[0][1] == 'desc')
  return addSort($oldsort, $key, 'asc');
 else
  return addSort($oldsort, $key, 'desc');
}

function sortToStr($sort) {
 $str = array();
 foreach ($sort as $item)
  $str[] = $item[0] . ':' . $item[1];
 return implode(',', $str);
}

function strToSort($str) {
 $str = explode(',', $str);
 $res = array();
 foreach ($str as $item) {
  $item = explode(':', $item);
  if (   ($item[1] != 'asc' && $item[1] != 'desc')
      || strspn($item[0], 'abcdefghijklmnopqrstuvwxyz_') != strlen($item[0]))
   die(T_('Unknown sorting key'));
  $res[] = $item; 
 }
 return $res;
}

function pathCombine($path1, $levels, $path2) {
 if ($path1 != '')
  $path1 = explode('.', $path1);
 else
  $path1 = array();
 if ($path2 != '')
  $path2 = explode('.', $path2);
 else
  $path2 = array();
 if ($levels != 0)
  $path1 = array_slice($path1, 0, -$levels);
 return implode('.', array_merge($path1, $path2));
}

function drawOption($default, $value, $comment) {
 if ($default)
  return "<option value='unchanged' selected='1'>{$comment}</option>";
 else
  return "<option value='{$value}'>{$comment}</option>";
}

function drawInputPair($label, $field, $help = '') {
 if ($help != '')
  $label = drawHelpAnnotation($label, $help);
 if ($label == '')
  return sprintf(T_("<td></td><td>%s</td>\n"), $field);
 else
  return sprintf(T_("<td>%s:</td><td>%s</td>\n"), $label, $field);
}

function drawInputRow($label, $field, $help = '') {
 return "<tr>\n" . drawInputPair($label, $field, $help) . "</tr>\n";
}

function drawHelp($help, $label = '?') {
 global $helplanguage;

 if (strlen($help) > 0)
  # We specify language here explicitly, in case the webserver isn't
  # smart enought to do this automagically from headers...
  $help = "<a class='help' href='help/index.{$helplanguage}.html#${help}' target='demowave-help'>{$label}</a>";
 return $help;
}

function drawHelpAnnotation($content, $help, $label = '?') {
 $help = drawHelp($help, $label);
 return "<span class='help_annotation'>{$content}{$help}</span>";
}

function drawSortHeader($sort, $sort_var, $key, $title, $help = '') {
 $args = queryString(queryConstruct(array($sort_var => sortToStr(flipSort($sort, $key)))));
 $class = 'sort sort_not_selected';
 if ($key == $sort[0][0]) $class = 'sort sort_selected sort_' . $sort[0][1];
 $header = drawHelpAnnotation("<a class='{$class}' href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$title}</a>", $help);
 return "<th>{$header}</a></th>\n";
};

function drawSearchField($title, $name, $values, $type, $size = 10, $help = '') {

 if ($type == 'bound')
  $field = "<input name='{$name}_lower' value='{$_GET[$name . '_lower']}' type='text' size='{$size}' />-
	    <input name='{$name}_upper' value='{$_GET[$name . '_upper']}' type='text' size='{$size}' />";
 else if ($type == 'match')
  $field = "<input name='{$name}' value='{$_GET[$name]}' type='text' size='{$size}' />";
 else if ($type == 'list') {
  $field = "\n";
  foreach($values as $label => $value) {
   $checked = '';
   if (isset($_GET[$name . '__' . $value . '_list']))
    $checked = 'checked';
   $field .= "<input name='{$name}__{$value}_list' {$checked} type='checkbox' />
	      <label for='{$name}__{$value}_list'>{$label}</label><br />\n";
  }
 } else
  die(T_('Unknown type for drawSearchField'));

 return drawInputPair($title, $field, $help);
}

function drawPrevNextPage($len, $page_start, $page_len, $page_start_name, $page_len_name) {
 $first = T_("First");
 $prev = T_("Previous");
 $next = T_("Next");
 $last = T_("Last");

 if ($page_start > 0) {
  $args = queryString(queryConstruct(array($page_start_name => 0)));
  $first = "<a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$first}</a>";
  $newstart = $page_start - $page_len;
  if ($newstart <= 0) $newstart = 0;
  $args = queryString(queryConstruct(array($page_start_name => $newstart)));
  $prev = "<a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$prev}</a>";
 }

 if ($page_start + $page_len < $len) {
  $args = queryString(queryConstruct(array($page_start_name => $page_start + $page_len)));
  $next = "<a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$next}</a>";
  $args = queryString(queryConstruct(array($page_start_name => $len - $page_len)));
  $last = "<a href='{$_SERVER["SCRIPT_NAME"]}?{$args}'>{$last}</a>";
 }

 return sprintf(T_("<div class='nextprevpage'>" .
                  " <span class='prevpage'>" .
                  "  %s %s" .
		  " </span>" .
		  " <span class='nextpage'>" .
                  "  %s %s" .
		  " </span>" .
		  "</div>"),
                  $first, $prev, $next, $last);
}

function varsToSearch($prefix, $vars) {
 $search = array() ;
 foreach($vars as $key => $value) {
  if (beginsWith($key, $prefix) && $value != "")
   {
    $key = substr($key, strlen($prefix));
    $subkeyarray = false;
    if (endsWith($key, '_lower')) {
     $key = substr($key, 0, strlen($key) - strlen('_lower'));
     $subkey = 'lower';
    } else if (endsWith($key, '_upper')) {
     $key = substr($key, 0, strlen($key) - strlen('_upper'));
     $subkey = 'upper';
    } else if (endsWith($key, '_list')) {
     $keyvalue = explode('__', substr($key, 0, strlen($key) - strlen('_list')));
     $key = $keyvalue[0];
     $value = $keyvalue[1];
     $subkey = 'list';
     $subkeyarray = true;
    } else
     $subkey = 'value';
    if (!isset($search[$key])) $search[$key] = array();
    if ($subkeyarray) {
     if (!isset($search[$key][$subkey])) $search[$key][$subkey] = array();
     $search[$key][$subkey][] = $value;
    } else
     $search[$key][$subkey] = $value;
   }
 }
 return $search;
}

function searchToSql($search) {
 $res = array();
 foreach($search as $key => $value) {
  if (isset($value['value']))
   $res[] = $key . " LIKE '%" .  $value['value'] . "%'";
  if (isset($value['lower']))
   $res[] = $key . " >= '" .  $value['lower'] . "'";
  if (isset($value['upper']))
   $res[] = $key . " <= '" .  $value['upper'] . "'";
  if (isset($value['list']))
   $res[] = $key . " in ('" .  implode("','", $value['list']) . "')";
 }
 if (count($res) == 0)
  return 'true';
 return implode(' and ', $res);
}

function sortToSql($sort) {
 $str = array();
 foreach ($sort as $item)
  $str[] = $item[0] . ' ' . $item[1];
 return implode(', ', $str);
}


?>
