<?php
// if statement, if...else statement, if...elseif...else statement, switch statement

// if statement
$num = -1;

if($num<0){
    echo "$num is less than 0 <br>" ;
}

// if...else
$age = 13;
if($age > 18){
    echo "You are an adult<br>";
}else{
    echo "You are under 18<br>";
}

if(($age>12) && ($age< 20)){
    echo "Discount for you !!!";
}

//if...elseif...else
if($num<0){
    echo "The value of $num is a negative number";
}elseif($num==00){
    echo "The value of $num is 0";
}else{
    echo "The value of $num is a positive number";
}

//variable named score and assign a value
//after you will check and as a result should be displayed the grade: A:90-100, B: 80-89, C:70-79, D; 60-69, F:other

$grade="B";
switch($grade){
    case "A":
        echo"Great A";
        break;
    case "B":
        echo"Great B";
        break;
    case "C":
        echo"Great C";
        break;
    case "D":
        echo"You passed but study more";
        break;
    case "F":
        echo "Youre a failure";
        break;
    default:
        echo "Invalid Grade";
        break;
}


for ($x =0; $x <= 10; $x++){
    echo"The Number is $x<br>";
}
$x=1;
do{
    echo"number is $x<br>";
    $x++;
}while ($x <= 5);

$x=1;
while ($x <= 5){
    echo"number is $x<br>";
    $x++;
}

$car = array("BMW", "VW", "Audi", "Tesla");
foreach($car as $value){
    echo "$value <br>";
}
$age = array("John"=> 18, "Michael" => 23, "Joe" => 10);
foreach($age as $x => $val){
    echo "$x=$val<br>";
}
?> 