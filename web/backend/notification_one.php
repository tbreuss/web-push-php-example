<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$auth = array(
    'VAPID' => array(
        'subject' => 'https://github.com/Minishlink/web-push-php-example/',
        'publicKey' => file_get_contents(dirname(__DIR__, 2) . '/keys/public_key.txt'), // don't forget that your public key also lives in app.js
        'privateKey' => file_get_contents(dirname(__DIR__, 2) . '/keys/private_key.txt'), // in the real world, this would be in a secret file
    ),
);

$subscription = json_decode(file_get_contents('php://input'), true);

if (empty($subscription['endpoint'])) {
    http_response_code(404);
    exit;
}

$webPush = new WebPush($auth);

$jsonStorage = new SubscriptionJsonPersistence(dirname(__DIR__, 2) . '/storage/subscriptions.json');

$subscription = $jsonStorage->findByEndpoint($subscription['endpoint']);

if (empty($subscription)) {
    http_response_code(404);
    exit;    
}

$subscriptionObject = Subscription::create($subscription);
$res = $webPush->sendNotification(
    $subscriptionObject,
    "Hi, how are you?"
);

$reports = [];
// handle eventual errors here, and remove the subscription from your server if it is expired
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();
    if ($report->isSuccess()) {
        $reports[] = [true, date('Y-m-d H:i:s'), "Message sent successfully for subscription {$endpoint}."];
    } else {
        $reports[] = [false, date('Y-m-d H:i:s'), "Message failed to send for subscription {$endpoint}: {$report->getReason()}"];
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($reports);
