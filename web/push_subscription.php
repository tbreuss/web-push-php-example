<?php

require 'SubscriptionJsonPersistence.php';

$subscription = json_decode(file_get_contents('php://input'), true);

if (!isset($subscription['endpoint'])) {
    echo 'Error: not a subscription';
    return;
}

$method = $_SERVER['REQUEST_METHOD'];

$jsonStorage = new SubscriptionJsonPersistence(dirname(__DIR__) . '/storage/subscriptions.json');

switch ($method) {
    case 'POST':
        // create a new subscription entry in your database (endpoint is unique)
        $jsonStorage->add($subscription);
        break;
    case 'PUT':
        // update the key and token of subscription corresponding to the endpoint
        $jsonStorage->update($subscription);
        break;
    case 'DELETE':
        // delete the subscription corresponding to the endpoint
        $jsonStorage->remove($subscription);
        break;
    default:
        echo "Error: method not handled";
        return;
}
