#!/usr/bin/php
<?php

echo ("\nqdadump 1.0 - (c) 2003 Derrick Sobodash\n");
set_time_limit(6000000);

if ($argc < 2) { DisplayOptions(); die; }
else { $file = $argv[1]; }

$fd = fopen($file, "rb");
fseek($fd, 0x4, SEEK_SET);
$id = fread($fd, 4);
if($id != "QDA0") die(print "Not a valid QDA file!");

print "Reading header... ";
fseek($fd, 0x8, SEEK_SET);
$count = hexdec(bin2hex(strrev(fread ($fd, 1))));
fseek($fd, 0x100, SEEK_SET);

for($i=0; $i<$count; $i++) {
	$off[$i]  = hexdec(bin2hex(strrev(fread ($fd, 4))));
	$size[$i] = hexdec(bin2hex(strrev(fread ($fd, 4))));
	$unk[$i]  = hexdec(bin2hex(strrev(fread ($fd, 4))));
	$name[$i] = rtrim(fread($fd, 256));
}
print "done!\n";

$newdir = substr($file, 0, strlen($file)-4);
mkdir($newdir);
for($i=0; $i<$count; $i++) {
	print "Writing " . $name[$i] . "... ";
	fseek($fd, $off[$i], SEEK_SET);
	$file = fread($fd, $size[$i]);
	$fo = fopen("./$newdir/" . $name[$i], "wb");
	fputs($fo, $file);
	print "done!\n";
}

echo ("All done!...\n\n");

function DisplayOptions() {
	echo ("Rips a QDA resource from Akuji the Demon\n  usage: qdadump [input file]\n\n");
}

?>
