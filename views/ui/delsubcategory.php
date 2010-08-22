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
<?php if (in_array('propose', $_SESSION['privs']) && $is_category) { ?>
 <form class="newreferendum" method="post" enctype="multipart/form-data">
  <input type="hidden" name='new_referendum_add' value='false'>
  <?php
   $referendum_type_ids = array_values($referendum_types);
   $referendum_random_type_id = $referendum_type_ids[0];
  ?>
  <input type="hidden" name='new_referendum_type' value='<?php echo $referendum_random_type_id; ?>'>
  <input type="hidden" name='new_referendum_breakpoint' value='1 second'>

  <h2><?php E_("Delete category"); ?></h2>
  <table>
   <?php
    $referendum_subcategory_options = array();
    foreach ($subcategories as $category) {
     $referendum_subcategory_options[] = "<option value='{$category}'>{$category}</option>";
    }
    echo drawInputRow(T_("Category title"),
		       "<select name='new_referendum_path'>" .
		       implode('\n', $referendum_subcategory_options) .
		       "</select>",
		       'referendum-type');

    echo drawInputRow('', "<input name='new_referendum' type='submit' value='" . T_("Delete category") . "'>");
   ?>
  </table>
 </form>

<?php } ?>
