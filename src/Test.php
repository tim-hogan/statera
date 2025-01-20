<?php
$target_dir = dirname(__FILE__) . "/attachments/";
error_log("About to test writing to directory");
file_put_contents($target_dir . "test", "test");
?>