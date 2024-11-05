<?php
 echo "Script is running!!";

// 1. Write to a file using `fopen`, `fwrite`, and `fclose`
function writeToFile($message) {
    // Open the file in 'a' mode (append mode)
    $file = fopen("example.txt", "a");
 
    // Check if the file was successfully opened
    if ($file == true) {
        // Write the message to the file
        fwrite($file, $message . PHP_EOL);
 
        // Close the file
        fclose($file);
 
        echo "Message written to file successfully!<br>";
    } else {
        echo "Failed to open the file for writing.<br>";
    }
}



//2. Read from a file using 'fopen', 'fread', 'feof', 'fclose'

function readFromFile(){
    $file = fopen("example.txt", 'r');

    //Check if the file was opened succesfully
    if($file){
        echo "Content of example.txt";

        //Read the file until the end (eof)
        while(!feof($file)){
            $line = fgets($file);
            echo htmlspecialchars(($line)."<br>");
        }
        fclose($file);
    } else{
        echo "Failed to open the file for reading!!";
    }
}

//3. Write a single line to the file using file_put_contents
function quickWriteToTheFile($message){
    file_put_contents("example.txt", $message.PHP_EOL);
    echo "Message written to file using file_put_contents!<br>";
}

writeToFile("This is a sample log message!!");
quickWriteToTheFile("This will overwrite everything with a new message!!");
readFromFile();



?> 