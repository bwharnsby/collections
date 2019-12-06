<?php


namespace App;

use Illuminate\Support\Collection;

class CollectionExample
{
    public function example() {
//        $data = [
//            'Team 1', 'Team 2', 'Team 3', 'Team 4',
//            'Team 5', 'Team 6', 'Team 7'
//        ];
//        $schedule = $this->scheduler($data);
//        return $schedule;

        $home_team_games = [
            ['score' => [0,1], 'date' => "2019-08-10"],
            ['score' => [1,0], 'date' => "2019-08-16"],
            ['score' => [3,0], 'date' => "2019-08-23"],
            ['score' => [3,1], 'date' => "2019-09-09"],
            ['score' => [1,0], 'date' => "2019-09-15"],
            ['score' => [2,2], 'date' => "2019-09-23"],
            ['score' => [1,0], 'date' => "2019-09-30"],
            ['score' => [0,3], 'date' => "2019-10-05"],
        ];
        $away_team_games = [
            ['score' => [2,1], 'date' => "2019-08-09"],
            ['score' => [1,1], 'date' => "2019-08-15"],
            ['score' => [1,0], 'date' => "2019-08-21"],
            ['score' => [2,3], 'date' => "2019-09-07"],
            ['score' => [1,4], 'date' => "2019-09-16"],
            ['score' => [0,1], 'date' => "2019-09-22"],
            ['score' => [1,1], 'date' => "2019-09-30"],
            ['score' => [0,2], 'date' => "2019-10-05"],
        ];
        $arr = [
            'hs' => [],
            'hc' => [],
            'as' => [],
            'ac' => []
        ];

        $date_of_fixture = "2019-10-11";

        for($i = 0; $i < count($home_team_games); $i++) {
            //distance from fixture date to first game date
            $d = $this->distanceInDays($home_team_games[0]['date'], $date_of_fixture);
            $x = $d - $this->distanceInDays($home_team_games[$i]['date'], $date_of_fixture);
            
            $weight = $this->calcDistance($x,$d);
            $arr['hs'][] = $home_team_games[$i]['score'][0] * $weight;
            $arr['hc'][] = $home_team_games[$i]['score'][1] * $weight;
        }
        for($i = 0; $i < count($away_team_games); $i++) {
            //distance from current date to first date...
            $d = $this->distanceInDays($away_team_games[0]['date'], $date_of_fixture);
            $x = $d - $this->distanceInDays($away_team_games[$i]['date'], $date_of_fixture);
            $weight = $this->calcDistance($x,$d);
            $arr['as'][] = $away_team_games[$i]['score'][1] * $weight;
            $arr['ac'][] = $away_team_games[$i]['score'][0] * $weight;
        }

        $h_score = array_sum($arr['hs'])/count($arr['hs']) + array_sum($arr['ac'])/count($arr['ac']);
        $a_score = array_sum($arr['as'])/count($arr['as']) + array_sum($arr['hc'])/count($arr['hc']);

        $new_arr = [
            'total' => $h_score + $a_score,
            'h_score' => $h_score,
            'a_score' => $a_score,
            'avg' => 0,
            'h_games' => 0,
            'a_games' => 0
        ];

        //calculate how many games each team has been involved in of the total score..
        for($i = 0; $i < count($home_team_games); $i++) {
            if(array_sum($home_team_games[$i]['score']) >= floor($new_arr['total'])) {
                $new_arr['h_games']++;
            }
        }
        for($i = 0; $i < count($away_team_games); $i++) {
            if(array_sum($away_team_games[$i]['score']) >= floor($new_arr['total'])) {
                $new_arr['a_games']++;
            }
        }
        $new_arr['h_games'] = ($new_arr['h_games']) / count($home_team_games);
        $new_arr['a_games'] = ($new_arr['a_games']) / count($away_team_games);
        $new_arr['avg'] = ($new_arr['h_games'] + $new_arr['a_games']) / 2;

        return $new_arr;
    }

    /**
     * @param $x (distance between game to predict and nth game
     * @param $d (distance between game to predict and first game
     * @return float
     */
    private function calcDistance($x, $d) {
        if(!$x) {
            return (1/$d);
        }
        return ($x/$d);
    }

    private function distanceInDays($date1, $date2) {
        return (strtotime($date2) - strtotime($date1))/60/60/24;
    }

    private function generateRandomNumberArray($numCount, $length, $offset = 0) {
        if($numCount <= 0 || $offset < 0 || $length <= 0) {
            return [];
        }
        $rnd_arr = range(0 + $offset, $numCount + $offset);
        shuffle($rnd_arr);
        $rnd_arr = array_slice($rnd_arr, 0, $length);
        return $rnd_arr;
    }

    private function generateRandomStringArray($strCount, $length) {
        $arr = [];
        for($i = 0; $i < $strCount; $i++) {
            $arr[] = $this->generateRandomString($length);
        }
        return $arr;
    }

    private function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function mergeArray(...$arrays) {
        return collect($arrays)->flatMap(function($item) {
            return array_wrap(Collection::unwrap($item));
        })->all();
    }

    private function everyThree(...$collections) {
        return collect($collections)->flatMap(function($item) {
           return Collection::wrap($item)->nth(3);
        });
    }

    private function printer($data) {
        $str = "";
        foreach($data as $k => $v) {
            $str .= 'Week ' . $k . '<br>';
        }
        return $str;
    }

    public function scheduler($teams, $rounds = 1) {
        //make the team count an even number...
        if(count($teams) % 2 != 0) {
            array_push($teams, 'bye');
        }

        //split the teams in half...
        $away = array_splice($teams, count($teams)/2);
        $home = $teams;

        //store results here...
        $round = [];

        //Number of weeks is calculated as:
        //If N is the team count and is even, then N-1 weeks.
        //If N is the team count and is odd, then N weeks.
        $count = $rounds * ((count($home) + count($away)));
        for($i = 0; $i < $count - $rounds; $i++) {
            //iterate over the 'home teams'
            for($j = 0; $j < count($home); $j++) {

                //skip match if team is a 'bye' team.
                if(in_array('bye', [$home[$j], $away[$j]])) {continue;}

                //flip the teams if on a week with an odd number.
                if($i % 2 == 0) {
                    $round[$i][$j]["Home"] = $home[$j];
                    $round[$i][$j]["Away"] = $away[$j];
                    $round[$i][$j]["HomeScore"] = random_int(0,3);
                    $round[$i][$j]["AwayScore"] = random_int(0,3);
                }
                else {
                    $round[$i][$j]["Home"] = $away[$j];
                    $round[$i][$j]["Away"] = $home[$j];
                    $round[$i][$j]["HomeScore"] = random_int(0,3);
                    $round[$i][$j]["AwayScore"] = random_int(0,3);
                }
            }
            //
            if(count($home)+count($away)-1 > 2) {
                //remove the team in the 2nd position of home array, and place at beginning of away array.
                $splice = array_splice($home,1,1);
                array_unshift($away, array_shift($splice));

                //take team in last position of away and place at end of home array
                array_push($home, array_pop($away));
            }
        }

        //return
        return $round;
    }
}