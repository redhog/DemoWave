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
     && isset($_POST['law_save'])) {
  $sql = "begin;
	  insert into referendum (title, category) values ('{$_SESSION["laweditor"]["title"]}', '{$category_id}');
	 ";

  $para_nr = 0;

  function paraToSql($rel, $path, $para) {
   global $sql, $para_nr, $category_id;

   if (isset($para['edit'])) {
    $para_id = $para_nr++;
    $para_path = '';

    if (strstr($para['path'], '-') === false)
     // Change a paragraph
     $sql .= "insert into referendum_type_law (referendum, id, add, path, title, text)
	       select
		currval('referendum_id_seq'),
		'{$para_id}',
		'{$para['edit']['add']}',
		'{$para['path']}',
		'{$para['edit']['title']}',
		'{$para['edit']['text']}';
	     ";
    else if ($rel === false)
     $sql .= "insert into referendum_type_law (referendum, id, add, path, title, text)
	       select
		currval('referendum_id_seq'),
		'{$para_id}',
		'{$para['edit']['add']}',
		law_next_paragraph({$category_id}, '{$path}'),
		'{$para['edit']['title']}',
		'{$para['edit']['text']}';
	     ";
    else
     $sql .= "insert into referendum_type_law (referendum, id, add, path, title, text)
	       select
		currval('referendum_id_seq'),
		'{$para_id}',
		'{$para['edit']['add']}',
		law_next_paragraph({$category_id},
				   law_paragraph_combine(path, 0, '{$path}')),
		'{$para['edit']['title']}',
		'{$para['edit']['text']}'
	       from
		referendum_type_law
	       where     referendum = currval('referendum_id_seq')
		     and id = '{$rel}';
	     ";
   } else if (strstr($para['path'], '-') !== false) {
    if ($rel === false)
     $sql .= "insert into referendum_type_law (referendum, id, add, path, title, text)
	       select
		currval('referendum_id_seq'),
		'{$para_id}',
		'false',
		law_next_paragraph({$category_id}, '{$path}'),
		'',
		'';
	     ";
    else
     $sql .= "insert into referendum_type_law (referendum, id, add, path, title, text)
	       select
		currval('referendum_id_seq'),
		'{$para_id}',
		'false',
		law_next_paragraph({$category_id},
				   law_paragraph_combine(path, 0, '{$path}')),
		'',
		''
	       from
		referendum_type_law
	       where     referendum = currval('referendum_id_seq')
		     and id = '{$rel}';
	     ";

   } else {
    $para_id = false;
    $para_path = $para['path'];
   }

   $lastrel = $para_id;
   $lastpath = pathCombine($para_path, 0, "0");
   foreach ($para['sub'] as $key => $sub)
    if ($key != '0')
     list($lastrel, $lastpath) = paraToSql($lastrel, $lastpath, $sub);

   return array($para_id, $para_path);
  }

  paraToSql(false, '', $_SESSION["laweditor"]['laws']);

  $sql .= "end;";

  pg_query($dbconn, $sql)
   or die('Unable to add referendum: ' . pg_last_error());
  $row = pg_fetch_row(pg_query($dbconn, "select currval('referendum_id_seq');"))
   or die('Unable to fetch id of new referendum: '. pg_last_error());
  $referendum = $row[0];

  if (in_array('vote', $_SESSION['privs'])) {
   $sql = "select cast_vote('{$referendum}', '{$_SESSION['user']}', '1');";
   pg_query($dbconn, $sql)
    or die(T_('Unable to insert vote: ') . pg_last_error());
   $messages .= "<div>" . sprintf(T_("Vote registered for referendum %s"), $referendum) . "</div>";
  }

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
