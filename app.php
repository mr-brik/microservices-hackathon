<?php
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/ComboClient.php';

// set up scores data
$scores = [];

// subscribe to topics
$topics = ['ArenaClock', 'playerJoin'];

$client = new ComboClient;

foreach ($topics as $topic) {
    $client->subscribe($topic);
}

// set up polling loop
while (true) {
    foreach ($topics as $topic) {
        echo $topic . "\n";
        // poll topic
        $response = $client->poll($topic);
        if (!$response) {
            continue;
        }
        var_dump($response);

        switch ($topic) {
            case 'playerJoin':
                // give them a score of 0 and send fact
                $scores[$response['id']] = 0;
                $client->send(['status' => 'playing', 'score' => 0, 'id' => $response['id']]);
                break;
            
            case 'ArenaClock':
                break;
            default:
                //nothing
        }
    }
}
