<?php

use GuzzleHttp\Client;

class ComboClient 
{
    private $host = 'http://52.16.7.112:8000';
    
    private $score_topic = 'score';
    
    private $subscribers;

    public function __construct()
    {
        $this->client = new Client;
    }

    public function subscribe($topic)
    {
        try {
            $response = $this->client->post($this->host . '/topics/' . $topic . '/subscriptions');
            $body = json_decode($response->getBody(),1);
            $this->subscribers[$topic] = $body['retrieval_url'];
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function poll($topic)
    {
        if (isset($this->subscribers[$topic])){
            $response = $this->client->get($this->subscribers[$topic]);
            if ($response->getStatusCode() == 204) {
                return false;
            }
            return json_decode($response->getBody(),1);
        } else {
            return false;
        }
    }

    public function send(array $fact)
    {
        $this->client->post($this->host . '/topics/' . $score_topic . ' /facts', ['json' => $fact]);
    }
}
