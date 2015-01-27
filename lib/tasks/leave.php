<?php

if ($this->grX->is_playing($nick)) {
	$this->grX->cmd_quit($nick, true);
} else {
	$this->send("/$nick Schönen Tag noch! *ww*");
}

if ($nick_level < 3) {
	$this->users_in_room = $this->users_in_room-1;
	if ($this->users_in_room >= 5) {
		$this->grX->max_wait = 30;
	} else {
		$this->grX->max_wait = 5;
	}
}

?>