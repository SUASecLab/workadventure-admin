<?php
header("Content-Type:application/json");
// we implement nothing
echo json_encode(array(
    "api/woka/list" => "not-implemented", // anything != "v1" will enable local woka service
));
?>
