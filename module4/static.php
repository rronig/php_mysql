<?php
function callCounter(){
    static $count = 0;
    $count++;
    echo"The value of count variable is $count <br>";
}
callCounter();
callCounter();
?>