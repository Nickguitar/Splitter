# Splitter
Splits a file into subfiles of defined size

## Wtf is this sh!t

This is just a PHP version of an old method (dSplit) used to bypass anti virus signature based scans.
The main idea is to detect which offsets of the file are recognized by the AV software as malicious and patch them so that the AV cannot detect it anymore.

For example, if you use a 100Kb executable, you'll generate subfiles with 10kb, 20kb, 30kb, etc., until 100kb, e.g, which is the original one.
With this files in hands, you can scan all of them with the AV and see which are detected.
If your AV detects every file from 40kb, e.g., that means that the malicious signature is somewhere between 30kb and 40kb.
Now you can split the original file, but only within region between 30kb and 40kb, and you can split with an interval of 1kb.
You'll now generate 10 files that you can scan and see which are detected.
Doing this recursivelly will lead you to the offsets that are recognized by the AV software as malicious.

## Usage

`foo@bar:~$ php splitter.php -i interval [-b] [-e] [-o] [-v] -f input_file`

```
	-i: size in bytes of the interval of the split
	-b: the byte in which the split will begin (default = 0)
	-e: the byte in which the split will end (default = input_file's length)
	-f: the file that will be splitted
	-o: the output path (default = current dir)
	-v: verbose mode
```

Example:

`foo@bar:~$ php splitter.php -i 1000 -f malware.exe -o path`

If 'malware.exe' is 70Kb, this will generate about 70 files in path/ with ~1Kb each. The first will contain the firsts 1000 bytes, the second the firsts 2000 bytes, etc.

`foo@bar:~$ php splitter.php -i 100 -f malware.exe -o path -e 4000`

Remember that malware.exe is 70Kb, but this time it will generate about 40 files, since we specified the end byte (4000) and the interval changed to 100.

`foo@bar:~$ php splitter.php -i 1 -f malware.exe -o path -b 6700 -e 6800`

This will generate 100 files with 1 byte each, from offsets 6700 to 6800. 

## Ok, and then?

At this point, you'll scan all those 100 files and see which are detected by the AV, so that you'll know which offsets you need to patch in malware.exe.

And you do this simply editing malware.exe with a hex editor and changing the corresponding byte to something else, like 0x00 or 0x90.

Oh, and sometimes you'll break the executable if you patch some important byte. In this cases, you can try patching the previous or the next byte, or changing the byte value you replaced (e.g., 0xFF instead of 0x00).

Since it is an old method and I haven't used it in a while, I don't know if it works anymore. But if it still works, obviously this was made only for educational purposes and I'll not be responsible for you getting caught by your girlfriend while trying to infect her PC to see her messages with your friend Carlos. =)
