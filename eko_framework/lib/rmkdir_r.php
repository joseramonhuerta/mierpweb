<?php
function rmkdir_r($pathname, $mode = 0755) {

    is_dir(dirname($pathname)) || rmkdir(dirname($pathname), $mode);

    return is_dir($pathname) || @mkdir($pathname, $mode, true);
}


function rmkdir($dirName, $rights=0777) {
    $dirs = explode('/', $dirName);
    $dir = '';
    foreach ($dirs as $part) {
        $dir.=$part . '/';
        if (!is_dir($dir) && strlen($dir) > 0) {
            mkdir($dir, $rights);
            chmod($dir, $rights);
        }
    }
}

?>
