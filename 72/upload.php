<?php

$files  = $_FILES['files'];
$name   = $files['name'];
$tmp    = $files['tmp_name'];

$folder_upload = __DIR__ . '/files';
move_uploaded_file( $tmp, $folder_upload . '/' . $name ) or die('Upload failed!');