<?
require('chart.php');
require('data.php');

$chart = new chart(300, 200, "example36");
$chart->plot($data, false, "red", "gradient", "black", 8);
$chart->stroke();
?>

