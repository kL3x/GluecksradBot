<?php

// Hübscherer Timeout-Killer ;-)
if ($this->timers['tok'] < time()) {
    $rand = mt_rand(0, 10);
                        
    if ($rand == 2) {
        $this->send('/'.time());
    } elseif ($rand == 5) {
        $this->send('*idl0r*');
    } else {
        $this->send('/'.time());
    }
                        
    if ($this->grX->status > 1) {
        $this->timers['tok'] = time()+30;
    } else {
        $this->timers['tok'] = time()+300;
    }
    $this->user->clean_bans();
}

if ($this->grX->timers['grx_idle'] < time() && $this->grX->status >= 4) {	
	$this->grX->idle_nextpalyer();
}