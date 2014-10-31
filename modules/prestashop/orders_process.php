<?php
    $reader = new XMLReader();
    if (!$reader->open("data.xml"))
    {
        die("Failed to open 'data.xml'");
    }
    while($reader->read())
    {
        $node = $reader->expand();
        // process $node...
    }
    $reader->close();
?>
