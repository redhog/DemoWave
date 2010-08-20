<?php /*
DemoWave
Copyright (C) 2006-2009 RedHog (Egil Möller) <redhog@redhog.org>

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
 require_once('php-gettext/gettext.inc');

 // This is a rather naivistic implementation that doesn't care about
 // qualities or advanced stuff like that, but it will at least get
 // you an acceptable language!
 $language = false;
 $helplanguage = false;
 foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang) {
  $lang = explode(';', $lang); $lang = $lang[0];
  if (   $language === false
      && (   $lang == 'en'
          || file_exists("locale/{$lang}/LC_MESSAGES/demowave.mo")))
   $language = $lang;
  if (   $helplanguage === false
      && file_exists("help/index.{$lang}.html"))
   $helplanguage = $lang;
 }
 foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang) {
  $lang = explode(';', $lang); $lang = $lang[0];
  $lang = explode('-', $lang); $lang = $lang[0];
  if (   $language === false
      && (   $lang == 'en'
          || file_exists("locale/{$lang}/LC_MESSAGES/demowave.mo")))
   $language = $lang;
  if (   $helplanguage === false
      && file_exists("help/index.{$lang}.html"))
   $helplanguage = $lang;
 }
 if ($language === false) $language = 'en';
 if ($helplanguage === false) $helplanguage = 'en';

 T_setlocale(LC_MESSAGES, $language);

 T_bindtextdomain('demowave', 'locale');
 T_bind_textdomain_codeset('demowave', 'UTF-8');
 T_textdomain('demowave');

 session_start();

 $messages = '';
 
 if (!isset($_GET['view'])) $_GET['view'] = 'login';

 require("utils.php");

 if (!isset($_SESSION['privs']))
  $_SESSION['privs'] = array();

 require("input/category.php");
 require("input/users.php");
 require("input/referendums.php");
 require("input/globalreferendums.php");
 require("input/laweditor.php");

 require("dbinterface/db.php");
 if (isset($fudinc)) {
  require($fudiglobalnc);
  require($fudinc);
  $GLOBALS['usr'] = fud_fetch_user($fuduser);
  $GLOBALS['usr']->lang = $language;
 }
 require("dbinterface/login.php");
 require("dbinterface/privs.php");
 require("dbinterface/category.php");
 require("dbinterface/save_referendums_vote.php");
 require("dbinterface/save_new_referendum.php");
 require("dbinterface/save_law_editor.php");
 require("dbinterface/save_user.php");
 require("dbinterface/save_users.php");
 require("dbinterface/save_register.php");
 require("dbinterface/user.php");
 require("dbinterface/users.php");
 require("dbinterface/subcategories.php");
 require("dbinterface/referendums.php");
 require("dbinterface/globalreferendums.php");
 require("dbinterface/law.php");
 require("dbinterface/graph.php");
?>
<?php
 $format = 'ui';
 if (isset($_GET['format'])) $format = $_GET['format'];
 require("views/{$format}.php");
?>
