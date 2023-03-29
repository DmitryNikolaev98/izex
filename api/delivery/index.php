<?php

require_once 'deliveryApi.php';

try {
    $api = new DeliveryApi();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}