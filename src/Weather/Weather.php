<?php
namespace idla\Weather;

class Weather
{
    private $raw = null;
    private $forcast = null;
    private $history = null;

    public function init(string $lon, string $lat)
    {
        $url = "http://www.student.bth.se/~idla18/dbwebb-kurser/ramverk1/me/redovisa/htdocs/weatherAPI";

        $data = array("lat" => $lat, "lon" => $lon);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );
        $context  = stream_context_create($options);
        $this->raw = file_get_contents($url, false, $context);
        
        $this->forcast = json_decode($this->raw)->forcast;
        $this->history = json_decode($this->raw)->history;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getWeek()
    {
        if ($this->forcast != null) {
            $week = $this->forcast->daily;

            foreach ($week as $day) {
                $currentWeek[] = [
                    "day" => date('D', $day->dt),
                    "date" => date('d/m', $day->dt),
                    "minTemp" => $day->temp->min,
                    "maxTemp" => $day->temp->max,
                    "weather" => $day->weather[0]->description,
                ];
            }

        }
        return $currentWeek ?? null;
    }

    public function getHistory()
    {
        if ($this->history != null) {
            $history = $this->history;

            foreach ($history as $day) {
                $maxTemp = 0;
                $minTemp = 0;
                $description = null;
                $dayDate = null;
                $date = null;

                foreach ($day as $hour) {
                    $dayDate = date('D', $hour->dt);
                    $date = date('d/m', $hour->dt);

                    if ($maxTemp < $hour->temp) {
                        $maxTemp = $hour->temp;
                    }
    
                    if ($minTemp == null) {
                        $minTemp = $hour->temp;
                    } elseif ($minTemp > $hour->temp) {
                        $minTemp = $hour->temp;
                    }
                    $description = $hour->weather[0]->description;
                }

                $historydays[] = [
                    "day" => $dayDate,
                    "date" => $date,
                    "maxTemp" => $maxTemp,
                    "minTemp" => $minTemp,
                    "description" => $description,
                ];
            }

        }
        return $historydays ?? null;
    }
}
