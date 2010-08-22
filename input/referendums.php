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
 $expanded_referendums = split(',', $_GET['expanded_referendums']);

 if (!isset($_GET['referendum_page_start']))
   $_GET['referendum_page_start'] = 0;
 $referendum_page_start = (int) $_GET['referendum_page_start'];

 if (!isset($_GET['referendum_page_len']))
  $_GET['referendum_page_len'] = 10;
 $referendum_page_len = (int) $_GET['referendum_page_len'];

 if (!isset($_GET['referendums_sort']))
  $_GET['referendums_sort'] = 'area:desc';
 $referendums_sort = strToSort($_GET['referendums_sort']);

 if (   !isset($_GET['referendum_search_status__1_list'])
     && !isset($_GET['referendum_search_status__0_list'])
     && !isset($_GET['referendum_search_status__-1_list']))
  {
   if ($_GET['categoryview'] == 'referendums')
    $_GET['referendum_search_status__0_list'] = 'on';
   else if ($_GET['categoryview'] == 'decisions')
    $_GET['referendum_search_status__1_list'] = 'on';
   else if ($_GET['categoryview'] == 'rejections')
    $_GET['referendum_search_status__-1_list'] = 'on';
  }

 $referendum_search = varsToSearch('referendum_search_', $_GET);

?>
