<?php

class timer {
	
    private $starts = array();
    private $stops  = array();
    private $times  = array();

    //start timing
    public function start($eventname = "default") {
        $this->starts[$eventname] = explode(" ", microtime());    
    }

    //stop timing
    public function stop($eventname = "default") {
        $this->stops[$eventname] = explode(" ", microtime());
    }

    //calculate required time
    public function gettime($eventname = "default") {
        if(!isset($this->starts[$eventname])) return 0;
        if(!isset($this->stops[$eventname])) $this->stop($eventname);
        
        $this->times[$eventname] = $this->stops[$eventname][0] - $this->starts[$eventname][0] + $this->stops[$eventname][1] - $this->starts[$eventname][1];

        return $this->times[$eventname];
    }

    public function clear($eventname = "default") {
        unset($this->starts[$eventname], $this->stops[$eventname], $this->times[$eventname]);
    }
}

?>