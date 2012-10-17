#!/usr/bin/php
<?php

echo ("\nqdabuild 1.0 (c) 2003 Derrick Sobodash\n");
set_time_limit(6000000);

if ($argc < 2) { DisplayOptions(); die; }
else { $path = $argv[1]; }

// Simple routine to read in a directory listing and split it to an array
$mydir="";
$orgfile = "$path";
if ($handle = opendir($orgfile)) {
	while (false !== ($file = readdir($handle))) { 
		$mydir .= $orgfile . "/$file\n";
	}
	closedir($handle);
}
$filelist = split("\n", $mydir);

print "Staring new header... ";
$header = str_pad((pack("V*", 0) . "QDA0" . pack("V*", (count($filelist) - 3))) , 0x100, chr(0), STR_PAD_RIGHT);
print "done!\n";
$binary = "";

for ($i=2; $i < (count($filelist)-1); $i++) {
	print "Adding " . $filelist[$i] . "... ";
	$fd = fopen($filelist[$i], "rb");
	$file = fread($fd, filesize($filelist[$i]));
	fclose($fd);
	$header .= pack("V*", (0x100 + (268 * (count($filelist) - 3)) + strlen($binary))) .
	           pack("V*", strlen($file)) . pack("V*", strlen($file)) .
	           str_pad($filelist[$i], 256, chr(0), STR_PAD_RIGHT);
	$binary .= $file;
	print "done!\n";
}

print "Writing new resource file... ";
$fo = fopen("$path-a.qda", "w");
fputs($fo, $header . $binary);
fclose($fo);
print "done!\n";

echo ("All done!...\n\n");

function DisplayOptions() {
	echo ("Builds a QDA resource for Akuji the Demon\n  usage: qdabuild [input path]\n\n");
}

?>
