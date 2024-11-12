<?php
$dogs=[["Chihuahua", "Mexico", 20], ["Husky", "Siberia", 15], ["Bulldog", "England", 10]];
foreach($dogs as $a){
    echo $a[0]." Origin: ".$a[1]." Lifespan: ".$a[2]."<br>";
}

for($row=0;$row<3;$row++){
    echo"<p><b>Row Number $row</b></p>";
    echo"<ul>";
    for($col= 0;$col< 3;$col++){
        echo"<li>".$dogs[$row][$col]."</li>";
    }
    echo"</ul>";
}

$phones=[["14", 20, 10], ["13", 20, 20], ["12", 20, 25]];
echo"<table><thead><tr><th>Phones</th><th>In stock</th><th>Sold</th></thead><tbody>";
for($row= 0;$row< 3;$row++){
    echo"<tr><td>";
}
?>