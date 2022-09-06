<?php

require __DIR__ . '/../vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$auth = array(
    'VAPID' => array(
        'subject' => 'https://github.com/Minishlink/web-push-php-example/',
        'publicKey' => file_get_contents(__DIR__ . '/../keys/public_key.txt'), // don't forget that your public key also lives in app.js
        'privateKey' => file_get_contents(__DIR__ . '/../keys/private_key.txt'), // in the real world, this would be in a secret file
    ),
);

$webPush = new WebPush($auth);

$jsonStorage = new SubscriptionJsonPersistence(dirname(__DIR__) . '/storage/subscriptions.json');

$subscriptions = $jsonStorage->fetchAll();

foreach ($subscriptions as $subscription) {
    $subscriptionObject = Subscription::create($subscription);
    $res = $webPush->sendNotification(
        $subscriptionObject,
        "Hello subscribers, how are you?"
    );
}

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
