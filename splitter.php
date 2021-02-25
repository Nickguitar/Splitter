<?php

// Just checking if everything is ok  =)
function banner(){
  echo "Splits a file into subfiles of defined size.

  Usage: php splitter.php -i interval [-b] [-e] [-o] [-v] -f input_file
  -i: size in bytes of the interval of the split
  -b: the byte in which the split will beginning (default = 0)
  -e: the byte in which the split will end (default = input_file's length)
  -f: the file that will be splitted
  -o: the output path (default = current dir)
  -v: verbose mode";
  die();
}

function folder_exist($folder){
    $path = realpath($folder);
    return ($path !== false AND is_dir($path)) ? $path : false;
}

if($argc < 5){ //at least script name, "-i", interval, "-f", input_file
  banner();
}

$args = getopt("i:b:e:f:o:v");
//var_dump($args);

if(!isset($args['f'])){ //check filename
  die("You must specify an input file (-f) to split.");
}else{
  if(!is_file($args['f'])){
    die("You must specify an input FILE to split.\nDoes this file really exists? Is it really a file?");
  }else{
    $input_file = $args['f'];
  }
}

if(!isset($args["i"])){ //check size of each chunk
  die("You must specify an integer interval in bytes (-i) to split the file.");
}else{
  if(!preg_match("/^[0-9]+$/", $args['i'])){
    die("You must specify an INTEGER size of bytes to split the file.");
  }
  $chunk_size = $args["i"];
}

if(isset($args["b"])){ //check beginning byte
  if(!preg_match("/^[0-9]+$/", $args['b']) || $args['b'] == "0"){
    die("The value for the beginning (-b) of the splitting must be integer > 0");
  }
  $beginning = $args['b'];
}else{
  $beginning = 0;
}

if(isset($args["e"])){ //check ending byte
  if(!preg_match("/^[0-9]+$/", $args['e']) || $args['e'] == "0"){
    die("The value for the end (-e) of the splitting must be integer > 0");
  }
  $end = $args['e'];
}else{
  $end = filesize($input_file);
}

if(isset($args["o"])){ //check output folder
  if(is_file($args["o"]) || is_link($args["o"])){
    die("The value for the output path (-o) must be a folder");
  }else{
    if(!folder_exist($args["o"])){
      mkdir($args["o"], 0777, true);
    }
    $output = $args["o"];
  }
}else{
  $output = dirname(__FILE__);
}

isset($args["v"]) ? $verbose = 1 : $verbose = 0;

// Ok, done with checking.



function write($file, $data){
  global $verbose;
  $fp = fopen($file, "wb");
  if(fwrite($fp, $data)){
    if($verbose){
      echo "Written ".strlen($data)." bytes in $file.\n";
    }
  }
  fclose($fp);
}

function split_bytes($file,$size){
  global $beginning;
  global $end;
  global $output;

  if(file_exists($file)){ //guess what
    if($size <= filesize($file)){
      $filename = explode(".", basename($file));
      $fp = fopen($file, "rb"); //read bytes
      $bytes = fread($fp, filesize($file));
      fclose($fp);

      try{
        for($i=$beginning;$i<=$end;$i+=$size){
          $chunked = $output."/".$filename[0]."_".$i.".".$filename[1]; //name of file to be saved
          $last_chunked = $output."/".$filename[0]."_".($i-$size).".".$filename[1]; //last file created
          $chunk = substr($bytes, $beginning, $i-$beginning); //bytes from beginning to $size
          if(file_exists($last_chunked)){
            if(strlen($chunk) >= filesize($last_chunked)){ //if bytes to be written are lesser than the length of the last written file, then it's the end
              write($chunked, $chunk);
            }else{
              break;
            }
          }else{
            write($chunked, $chunk);
          }
        }
        echo "Done!\n";
      }catch(Exception $e){
        die("Error: ".$e->getMessage()); //does this shit even work?
      }

    }else{
      die("The specified size cannot be greater than input_file size.");
    }
  }else{
    die("Error opening file ".$file);
  }
}

split_bytes($input_file, $chunk_size);
?>
