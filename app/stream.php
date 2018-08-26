<?php

$relpath = fromSmpEntitiesPath($_GET['stream']);
$fullpath = App::$config->music_dir . DIRECTORY_SEPARATOR . $relpath;

// https://stackoverflow.com/a/18978453
if (checkPath($fullpath) && file_exists($fullpath)) {
    // Avoid sending unexpected errors to the client - we should be serving a file,
    // we don't want to corrupt the data we send
    @error_reporting(0);
    
    // Get the 'Range' header if one was sent
    if (isset($_SERVER['HTTP_RANGE'])) {
        $range = $_SERVER['HTTP_RANGE']; // IIS/Some Apache versions
    } else if ($apache = apache_request_headers()) { // Try Apache again
        $headers = array();
        foreach ($apache as $header => $val) {
            $headers[strtolower($header)] = $val;
        }
        if (isset($headers['range'])) {
            $range = $headers['range'];
        } else {
            $range = false; // We can't get the header/there isn't one set
        }
    } else {
        $range = false; // We can't get the header/there isn't one set
    }
    
    // Get the data range requested (if any)
    $filesize = filesize($fullpath);
    $length = $filesize;
    if ($range) {
        $partial = true;
        list($param, $range) = explode('=', $range);
        if (strtolower(trim($param)) != 'bytes') { // Bad request - range unit is not 'bytes'
            header("HTTP/1.1 400 Invalid Request");
            exit;
        }
        $range = explode(',', $range);
        $range = explode('-', $range[0]); // We only deal with the first requested range
        if (count($range) != 2) { // Bad request - 'bytes' parameter is not valid
            header("HTTP/1.1 400 Invalid Request");
            exit;
        }
        if ($range[0] === '') { // First number missing, return last $range[1] bytes
            $end = $filesize - 1;
            $start = $end - intval($range[0]);
        } else if ($range[1] === '') { // Second number missing, return from byte $range[0] to end
            $start = intval($range[0]);
            $end = $filesize - 1;
        } else { // Both numbers present, return specific range
            $start = intval($range[0]);
            $end = intval($range[1]);
            if ($end >= $filesize || (!$start && (!$end || $end == ($filesize - 1)))) {
                $partial = false;
            } // Invalid range/whole file specified, return whole file
        }
        $length = $end - $start + 1;
    } else {
        $partial = false; // No range requested
    }

    // Determine the content type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $contenttype = finfo_file($finfo, $fullpath);
    finfo_close($finfo);

    // Send standard headers
    header("Content-Type: $contenttype");
    header("Content-Length: $length");
    header('Content-Disposition: attachment; filename="' . basename($fullpath) . '"');
    header('Accept-Ranges: bytes');

    // if requested, send extra headers and part of file...
    if ($partial) {
        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $start-$end/$filesize");
        if (!$fp = fopen($fullpath, 'r')) { // Error out if we can't read the file
            header("HTTP/1.1 500 Internal Server Error");
            exit;
        }
        if ($start) {
            fseek($fp, $start);
        }
        while ($length) { // Read in blocks of 8KB so we don't chew up memory on the server
            $read = ($length > 8192) ? 8192 : $length;
            $length -= $read;
            print(fread($fp, $read));
        }
        fclose($fp);
    } else {
        readfile($fullpath); // ...otherwise just send the whole file
    }
    
    exit;
} else {
    http_response_code(404);
    die('Error: File not found.');
}
