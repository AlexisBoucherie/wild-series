<?php

namespace App\services;

use App\Entity\Program;

class ProgramDuration
{
    public function calculate(Program $program): string
    {
        $seasons = $program->getSeasons();
        $episodes = [];
        $durations = [];
        foreach ($seasons as $season) {
            $episodes[] = $season->getEpisodes();
        }
        foreach ($episodes as $episode) {
            $durations[] = $episode->getDuration();
        }
        return $durations; // à tester dans un var_dump
    }
}