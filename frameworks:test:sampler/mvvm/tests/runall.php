<?php

if ($handle = opendir(__DIR__))
{
    while (($entry = readdir($handle)) !== false )
    {        
        if( strpos($entry, 'Test') !== false ) {
        	echo "<h2>$entry</h2>";
        	include "$entry";
       	}
    }
    closedir($handle);
}
?>
