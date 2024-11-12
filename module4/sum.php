<?php
$x=5;
$y=10;
function sum(){
    global $x, $y;
    $y = $x + $y;
}
sum();
echo"$y";
?>