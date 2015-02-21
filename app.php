<?php
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/ComboClient.php';
require __DIR__.'/ScoreBoard.php';

// set up scores data
$scores = [];
$score_board = new ScoreBoard;

$current_time = 0;

// subscribe to topics
$topics = ['ArenaClock', 'playerJoin', 'scoreEvent'];

$client = new ComboClient;

foreach ($topics as $topic) {
    $client->subscribe($topic);
}

function updateScore($client, $topic, $score, $time) {
    $client->send('PlayerScore',
        ['status' => 'playing', 
        'score' => $score['score'],
        'id' => $score['id'],
        'time' => $time
        ]
    );
}

// set up polling loop
while (true) {
    foreach ($topics as $topic) {
        echo "Polling for $topic\n";

        // poll topic
        $response = $client->poll($topic);
        if (!$response) {
            continue;
        }
        var_dump($response);

        switch ($topic) {
            case 'playerJoin':
                // add player to score board
                $score = $score_board->addPlayer($response['id']);
                updateScore($client, 'PlayerScore', $score, $current_time);
                break;
            
            case 'ArenaClock':
                $current_time = $response['tick'];
                // send out leader board each tick
                var_dump($score_board->getScores());
                $fact = ['scores' => $score_board->getScores(), 'time' => $current_time];
                $client->send('LeaderBoard', $fact);
                break;

            case 'scoreEvent':
                // increase player score
                if ($response['type'] == 'collision') {
                    // set score
                    $score = $score_board->updatePlayer($response['id'], -10);
                    updateScore($client, 'PlayerScore', $score, $current_time);
                }
                break;
            default:
                //nothing
        }
    }
}
