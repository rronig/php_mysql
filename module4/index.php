<?php
phpinfo();
$a="Hello";
print_r($a);
$x=5;
echo gettype($x) ."<br>";
$y=10.3;
echo gettype($y) ."<br>";
$z="hello";
echo gettype($z) ."<br>";
function displayPHPVersion() {
    echo"This is PHP version " . phpversion() . "<br>"; 
}
displayPHPVersion();
function helloWorld(){
    echo "Hello World";
}
helloWorld();
function foo($arg_1, $arg_2, $arg_n){
    echo "Example function. \n";
    $return="Some Value";
    return $return;
}

function sum(){
    $value = 120+20;
    echo $value;
}
sum();

function maximale($x, $y){
    if ($x > $y){
        return $x;
    }else{
        return $y;
    }
}
$a=20;
$b=30;
$test=maximale($a,$b);
echo "The max of $a and $b is $test \n";

function fully_divisible($n)
{if(($n%2) == 0) {
    return "$n is fully divisible by 2";
}else{
    return "$n is not fully divisible by 2";
}
}
print_r(fully_divisible(4) . "<br>");
print_r(fully_divisible(36) . "<br>");
print_r(fully_divisible(16) . "<br>");
print_r(fully_divisible(5) . "<br>");

$x = 5; //global variable
/*function localVariable(){
    global $x;
    $y=10;//local variable
    echo $x;
    echo $y;
}
localVariable();*/

$x=5;
function localVariable(){
    $y= 10;
    echo"$y" . "<br>";
}
localVariable();
echo"$x" . "<br>";

?>