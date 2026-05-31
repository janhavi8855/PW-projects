<?php

echo "<h2>FOR LOOP</h2>";

for($i = 1; $i <= 5; $i++)
{
    echo $i . "<br>";
}

echo "<h2>WHILE LOOP</h2>";

$j = 1;

while($j <= 5)
{
    echo $j . "<br>";
    $j++;
}

echo "<h2>DO WHILE LOOP</h2>";

$k = 1;

do
{
    echo $k . "<br>";
    $k++;
}
while($k <= 5);

echo "<h2>FOREACH LOOP</h2>";

$colors = array("Red", "Blue", "Green");

foreach($colors as $color)
{
    echo $color . "<br>";
}

?>