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

$now = time();

require("chart/chart.php");

$chart = new chart(640, 480);
$chart->set_margins (60, 10, 10, 46);

$chart->set_expired(true);
$chart->set_x_ticks(1, "ctime");
$chart->set_labels("Time", "Vote sum");
$chart->set_extrema(-1, 1);

$start = $graph[0][0];
$end = $graph[count($graph) - 1][0];

function separate_xy($points) {
 $xs = array();
 $ys = array();
 foreach ($points as $point) {
  $xs[] = $point[0];
  $ys[] = $point[1];
 }
 return array($xs, $ys);
}

function shade($color1, $color2, $proportion) {
 $color1 = rgb_color($color1);
 $color2 = rgb_color($color2);

 return array(
  (int) ($color1[0] * $proportion + $color2[0] * (1.0 - $proportion)),
  (int) ($color1[1] * $proportion + $color2[1] * (1.0 - $proportion)),
  (int) ($color1[2] * $proportion + $color2[2] * (1.0 - $proportion)));
}

// Background
$chart->plot(array(1, 1),
	     array($start, $end),
	     "white", "gradient", "white", 0);

// The current area
$current = array();
$future = array();
$last = array(0, 0.0);
foreach($graph as $point) {
 if ($point[0] >= $now) {
  if ($last[0] < $now) {
   $current[] = array($now, $last[1]);
   $future = array(array($now, $last[1]));
  }
  $future[] = $point;
 } else {
  if (   ($point[1] >= 0.0 && $last[1] <= 0.0)
      || ($point[1] <= 0.0 && $last[1] >= 0.0)) {
   $current = array($point);
  } else {
   $current[] = $point;
  }
 }
 $last = $point;
}
list($future_xs, $future_ys) = separate_xy($future);
list($current_xs, $current_ys) = separate_xy($current);
if ($last[1] >= 0.0) {
 $overunder = 0;
 $color = "green";
 $endtype = "acceptance";
} else {
 $overunder = 2;
 $color = "red";
 $endtype = "rejection";
}
$future_color = shade($color, "white", 0.2);
$chart->add_legend ($color . " = current area", $color);
$chart->add_legend ("light " . $color . " = estimated area for " . $endtype, $future_color);

/*echo strftime("%Y-%m-%d %H:%M:%S", $x) . "<br>"; */
$chart->plot($future_ys, $future_xs, $future_color, "gradient", $future_color, 0 + $overunder);
$chart->plot($current_ys, $current_xs, $color, "gradient", $color, 0 + $overunder);
$chart->plot(array(0, 0),
	     array($current_xs[0], $end + 1), // + 1 to fix some rounding error in the graph lib.
	     "white", "gradient", "white", 0 + $overunder);

// The actual curve
$xs = array();
$ys = array();
foreach($graph as $point) {
 $xs[] = $point[0];
 $ys[] = $point[1];
}
$chart->plot($ys, $xs);

// The zero-line
$chart->plot(array(0, 0),
	     array($start, $end),
	     "blue");

$chart->stroke();
