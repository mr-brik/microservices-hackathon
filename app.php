<?php
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/ComboClient.php';

// set up scores data
$scores = [];
$current_time = 0;

// subscribe to topics
$topics = ['ArenaClock', 'playerJoin', 'scoreEvent'];

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
                $client->send('PlayerScore',
                    ['status' => 'playing', 
                    'score' => 0, 
                    'id' => $response['id'],
                    'time' => $current_time
                    ]
                );
                // send score board too
                break;
            
            case 'ArenaClock':
                $current_time = $response['tick'];
                // send out leader board
                $fact = ['scores' => [], 'time' => $current_time];
                // get scores from ScoreBoard object
                foreach($scores as $id => $score){
                    $fact['scores'][] = ['id' => $id, 'score' => $score];
                }
                $client->send('LeaderBoard', $fact);
                break;

            case 'scoreEvent':
                // increase player score
                if ($response['type'] == 'collision') {
                    // set score
                }
                break;
            default:
                //nothing
        }
    }
}
