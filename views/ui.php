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
 header("Content-Type: text/html; charset=UTF-8");
?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>DemoWave</title>
  <link href="demowave.css" rel="stylesheet" type="text/css">
 </head>
 <body>
  <div class='main'>
   <?php
    if ($messages)
     {
      echo "<div class='message'>{$messages}</div>";
     }
   ?>
   <div class="menu">
    <span class="copyright">
     <a href="https://flattr.com/thing/54921/DemoWave" target="flattr">Flattr</a>,
     ©<a href="http://redhog.org" target="redhog_org">RedHog</a>, license: <a
     href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="license">GPL</a>
    </span>
    <?php require('views/ui/menu.php'); ?>
   </div>
   <?php
    echo "<div class='view view_{$_GET['view']}'>\n";
    $viewheading = "views/ui/{$_GET['view']}.heading.php";
    if (file_exists($viewheading)) {
     echo "<h1 class='viewheading'>";
     require($viewheading);
     echo "</h1>\n";
    }

    $fullview = "views/ui/{$_GET['view']}.php";
    if (file_exists($fullview)) {
     $viewmenu = "views/ui/{$_GET['view']}.menu.php";
     if (file_exists($viewmenu)) {
      echo "<div class='viewmenu'>";
      require($viewmenu);
      echo "</div>\n";
      echo "<div class='subview'>";
     }
     require($fullview);
     if (file_exists($viewmenu)) {
      echo "</div>\n";
     }
    } else {
     ?>
      <table>
       <?php
	$viewtop = "views/ui/{$_GET['view']}.top.php";
	if (file_exists($viewtop)) {
         ?>
          <tr>
           <td colspan="2">
            <?php
	     require($viewtop);
            ?>
           </td>
          </tr>
         <?php
	}
       ?>
       <tr>
	<td class="leftbox">
	 <?php
	  $viewmenu = "views/ui/{$_GET['view']}.menu.php";
	  if (file_exists($viewmenu)) {
	   echo "<div class='viewmenu'>";
	   require($viewmenu);
	   echo "</div>\n";
	   echo "<div class='subview'>";
	  }
          require("views/ui/{$_GET['view']}.left.php");
	  if (file_exists($viewmenu)) {
	   echo "</div>\n";
	  }
         ?>
	</td>
	<td class="rightbox">
	 <?php
	  require("views/ui/{$_GET['view']}.right.php");
	 ?>
	</td>
       </tr>
      </table>
     <?php
    }
    echo "</div>\n";
   ?>
  </div>
 </body>
</html>
