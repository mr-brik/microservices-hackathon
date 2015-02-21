<?php

class ScoreBoard
{
    private $scores;

    public function __construct()
    {
        $this->scores = [];
    }

    public function addPlayer($id)
    {
        $this->scores[$id] = 0;
        return ['id' => $id, 'score' => 0];
    }

    public function updatePlayer($id, $value)
    {
        $this->scores[$id] += $value;
        return ['id' => $id, 'score' => $this->scores[$id]];
    }

    public function getScores()
    {
        return $this->scores;
    }
}
