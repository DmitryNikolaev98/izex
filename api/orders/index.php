<?php

require_once 'ordersApi.php';

try {
    $api = new ordersApi();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}