<?php

if (php_sapi_name() !== 'cli') {
    header('HTTP/1.0 403 Forbidden', true, 403);
    exit;
}
// else
