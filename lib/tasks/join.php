<?php

$this->user->clean_bans();

if ($nick_level == "") {
	$this->user->add("System", $nick, $nick_level);
}
	
$nick_info = $this->user->get_info($nick);

// Gesperrter Benutzer betritt den Raum	
if ($nick_info['banned'] == 1) {		
	$this->send('/k '.$nick);		
	if ($nick_info['bantime'] != 0) {
		$date = formatdate('d.m.Y', $nick_info['bantime'], 1);
		$time = formatdate('H:i', $nick_info['bantime'], 0);
		$this->send("/m $nick Du bist bis ".iif($date == "heute", $date, "zum ".$date)." um $time Uhr aus dem Raum ausgeschlossen.");
	} else {
		$this->send("/m $nick Du wurdest permanet aus dem Raum gebannt.");
	}
		
	
// Administrator betritt den Raum
} elseif ($nick_level >= 5) {
	$this->send("/su $nick");
	$this->send("/m $nick Du bist Administrator");
	$rank = $this->user->get_rank($nick);
	$date = formatdate('d.m.Y', $nick_info['lastplayed'], 1);
	$time = formatdate('H:i', $nick_info['lastplayed'], 0);
	$this->send("/m $nick Das letzte Mal warst du ".iif($date == "heute" || $date == "gestern", $date, 'am '.$date)." um $time Uhr hier im Raum.");
	
// Helfer betritt den Raum
} elseif ($nick_level == 4) {
	$this->send("/su $nick");
	$this->send("/m $nick Du bist Helfer!");
	$rank = $this->user->get_rank($nick);
	$date = formatdate('d.m.Y', $nick_info['lastplayed'], 1);
	$time = formatdate('H:i', $nick_info['lastplayed'], 0);
	$this->send("/m $nick Das letzte Mal warst du ".iif($date == "heute" || $date == "gestern", $date, 'am '.$date)." um $time Uhr hier im Raum.");
	
// Superuser betritt den Raum
} elseif ($nick_level == 3) {
	$this->send("/su $nick");

	$rank = $this->user->get_rank($nick);
	$date = formatdate('d.m.Y', $nick_info['lastplayed'], 1);
	$time = formatdate('H:i', $nick_info['lastplayed'], 0);
	$this->send("/m $nick Das letzte Mal warst du ".iif($date == "heute" || $date == "gestern", $date, 'am '.$date)." um $time Uhr hier im Raum.");
	
	
// Normaler Chatter betritt den Raum, der vermutlich bisher noch nicht gespielt hat
// zumindest nicht mit diesem Nick!
} elseif ($nick_info['lastplayed'] == 0 && $nick_info['points'] == 0) {		
	$this->send("/m $nick Willkommen im Treffpunkt!");
	$this->send("/m $nick Frag einen der User hier im Raum, wie du mich steuern kannst.");
		
/*	if ($this->grX->status == 1) $this->send("/m $nick Gib 'cmd grad' ein, um mitzuspielen!");
	elseif ($this->grX->status == 2) $this->send("/m $nick Wenn du 'cmd grad' tippst, dann kannst mitzocken!");
	elseif ($this->grX->status == 3) $this->send("/m $nick Also ein Platz ist noch frei! Mit 'cmd grad' kannste mitspielen!");
	elseif ($this->grX->status >= 4) $this->send("/m $nick Es wird gerade gespielt. Du kannst aber zuschauen, und bei der nächsten Runde mit 'cmd grad' mitspielen!");
*/	
	$this->users_in_room = $this->users_in_room+1;
	if ($this->users_in_room >= 5) {
		$this->grX->max_wait = 30;
	} else {
		$this->grX->max_wait = 5;
	}

// Benutzer ist bereits vorhanden
} elseif ($nick_info['lastplayed'] > 0 || $nick_info['points'] > 0) {		
	$rank = $this->user->get_rank($nick);
	$date = formatdate('d.m.Y', $nick_info['lastplayed'], 1);
	$time = formatdate('H:i', $nick_info['lastplayed'], 0);
	$this->send("/m $nick Das letzte Mal warst du ".iif($date == "heute" || $date == "gestern", $date, 'am '.$date)." um $time Uhr hier im Raum.");	
		
/*	if ($this->grX->status == 1) $this->send("/m $nick Gib 'cmd grad' ein, um mitzuspielen!");
	elseif ($this->grX->status == 2) $this->send("/m $nick Wenn du 'cmd grad' tippst, dann kannst mitzocken!");
	elseif ($this->grX->status == 3) $this->send("/m $nick Also ein Platz ist noch frei! Mit 'cmd grad' kannste mitspielen!");
	elseif ($this->grX->status >= 4) $this->send("/m $nick Es wird gerade gespielt. Du kannst aber zuschauen, und bei der nächsten Runde mit 'cmd grad' mitspielen!");
*/	
	$this->users_in_room = $this->users_in_room+1;
	if ($this->users_in_room >= 5) {
		$this->grX->max_wait = 30;
	} else {
		$this->grX->max_wait = 5;
	}
}
	
//$this->send("/m ChatQuiz cmd ip $nick");



?>