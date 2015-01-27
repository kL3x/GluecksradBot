<?php

/**
 * Das Copyright muss drin bleiben. Der Befehl darf nicht
 * auskommentiert werden. Bei nicht Beachtung erfolgen
 * rechtliche Schritte.
 */
if (preg_match("/^cmd info$/", $msg, $r)) {
	$this->send("/m $nick :-)");
	$this->send("/");

}

/**
 * Admins, Helfer, Superuser anzeigen
 */
if (preg_match('/^show (admins|helfer|superuser|su|ban)$/iU', $msg, $r) && $msg_type == "msg") {
	$type = trim($r[1]);
	if ($type == "superuser") $type = "su";
	
	$users = $this->user->get_users($type);
	/**
	 * Banned Users
	 */
	if ($type == "ban" && $this->user->get_level($nick) >= 3 && $users['users'] != "") {
		$this->send("/m $nick Autokick (".$users['count']."): ".$users['users']);		
	} elseif ($type == "ban") {
		$this->send("/m $nick Es befinden sich keine User auf der Autokick-Liste");
	}
	/**
	 * Superuser
	 */
	if ($type == "su" && $users['users'] != "") {
		$this->send("/m $nick Permanente SuperUser (".$users['count']."): ".$users['users']);
	} elseif ($type == "su") {
		$this->send("/m $nick Es befinden sich keine User auf der SU-Liste");
	}
	/**
	 * Helfer
	 */	
	if ($type == "helfer" && $users['users'] != "") {
		$this->send("/m $nick Bothelfer (".$users['count']."): ".$users['users']);
	} elseif ($type == "helfer") {
		$this->send("/m $nick Es befinden sich keine User auf der Bothelfer-Liste");
	}
	/**
	 * Administratoren
	 */	
	if ($type == "admins" && $users['users'] != "") {
		$this->send("/m $nick Administratoren (".$users['count']."): ".$users['users']);
	} elseif ($type == "admins") {
		$this->send("/m $nick EEs befinden sich keine User auf der Admin-Liste");
	}
}


/**
 * Glücksrad Spielbefehle
 */ 
 
 /*
// Befehl: cmd grad - An einer neuen Runde teilnehmen
if (preg_match('/^cmd grad$/iU', $msg) && $msg_type == "msg") {
	$this->grX->cmd_grad($nick);

// Befehl: cmd start - Startet eine Runde mit nur zwei Spielern
} elseif (preg_match('/^cmd start$/iU', $msg) && $msg_type == "msg") {
	$this->grX->cmd_start($nick);

// Befehl: cmd dreh - Dreht am Glücksrad
} elseif (preg_match('/^cmd dreh$/', $msg) && $msg_type == "msg") {
	$this->grX->cmd_dreh($nick);
	
// Befehl: cmd kauf - Vokale kaufen
} elseif (preg_match('/^cmd kauf$/iU', $msg) && $msg_type == "msg") {
	$this->grX->cmd_kauf($nick);
	
// Befehl: cmd Konsonant/Vokal - Konsonant oder Vokal auswählen
} elseif (preg_match('/^cmd ([a-z]{1})$/iU', $msg, $r) && $msg_type == "msg") {
	$this->grX->cmd_buchstabe($nick, $r[1]);
	
// Befehl: 'ja' oder 'nein' - Soll ein Extradreh benutzt werden?
} elseif (preg_match('/^ja$/iU', $msg) && $msg_type == "msg" && $this->grX->is_playing($nick) && $this->grX->status == 101) {
    $this->grX->cmd_ja($nick); 
} elseif (preg_match('/^nein$/iU', $msg) && $msg_type == "msg" && $this->grX->is_playing($nick) && $this->grX->status == 101) {
    $this->grX->cmd_nein($nick);
    
// Befehl: cmd loes - Bereit zum lösen vor
} elseif (preg_match('/^cmd loes$/iU', $msg) && $msg_type == "msg" && $this->grX->is_playing($nick) && $this->grX->current['nick'] == $nick) {
    $this->grX->cmd_loes($nick, null);
    
// Befehl: cmd LOESUNGSWORT - Löst den gesuchten Begriff
} elseif (preg_match('/^cmd (.*?)/iU', $msg, $r) && $msg_type == "msg" && $this->grX->status == 7 && $this->grX->current['nick'] == $nick) {
	$begriff = str_replace("ü", "ue", $r[1]);
	$begriff = str_replace("ä", "ae", $begriff);
	$begriff = str_replace("ö", "oe", $begriff);
	$this->grX->cmd_loes($nick, $begriff);

// Befehl: cmd thema - Zeigt das aktuelle Thema an
} elseif (preg_match('/^cmd thema$/iU', $msg) && $msg_type == "msg") {
	$this->grX->cmd_thema($nick);

// Befehl: cmd weiter - Gibt an den nächsten Spieler weiter
} elseif (preg_match('/^cmd weiter$/iU', $msg) && $msg_type == "msg") {
	$this->grX->cmd_weiter($nick);

// Befehl: cmd quit - Aus der aktuellen Runde aussteigen (deaktiviert)
} elseif (preg_match('/^cmd quit$/iU', $msg) && $msg_type == "msg") {
	
// Befehl: cmd me - Informationen über sich selber
} elseif (preg_match('/^cmd me$/', $msg)) {
	$nick_info = $this->user->get_info($nick);
	
	if (is_array($nick_info)) {
		if ($nick_info['rank']['points'] == 0) {
			$this->send("/m $nick Du bist noch nicht in der Toplist vertreten.");
		} else {
			$this->send("/m $nick Du bist mit ".$nick_info['rank']['points']." Punkten auf Platz ".$nick_info['rank']['rank']);
		}		
	} else {
		$this->send("/m $nick Es gibt noch keine Informationen zu dir ; )");
	}

// Befehl: cmd show <nick>
} elseif (preg_match('/^cmd show ([a-zA-Z0-9]+)$/i', $msg, $r)) {
	$nick_info = $this->user->get_info($r[1]);
	
	if (is_array($nick_info) && $nick_info['nick'] == $nick) {
		if ($nick_info['rank']['points'] == 0) {
			$this->send("/m $nick Du bist noch nicht in der Toplist vertreten.");
		} else {
			$this->send("/m $nick Du bist mit ".$nick_info['rank']['points']." Punkten auf Platz ".$nick_info['rank']['rank']);
		}		
	} elseif (is_array($nick_info)) {
		$date = formatdate('d.m.Y', $nick_info['lastplayed'], 1);
		$time = formatdate('H:i', $nick_info['lastplayed'], 0);
		
		$this->send('/m '.$nick.' '.$nick_info['nick'].' hat das letzte Mal '.iif(($date == "heute" || $date == "gestern"), $date, 'am '.$date).' um '.$time.' gespielt.');
		if ($nick_info['rank']['points'] == 0) {
			$this->send("/m $nick '".$nick_info['nick']."' ist noch nicht in der Toplist vertreten.");
		} else {
			$this->send("/m $nick '".$nick_info['nick']."' ist mit ".$nick_info['rank']['points']." Punkten auf Platz ".$nick_info['rank']['rank']);
		}
		// Informationen über <nick> ausgeben Vielleicht noch ein paar sonstige informationen
		
	} else {
		$this->send("/m $nick Der Benutzer ist nicht vorhanden");
	}
// Befehl: cmd toplist <today,week,year>
} elseif (preg_match('/^cmd toplist$/i', $msg)) {
	$this->send("/m $nick Die Toplist kannst du dir nur noch auf unserer Webseite ansehen.");
	$this->send("/m $nick Hier der direkte Link: ");

// Befehl: cmd top<anz>
} elseif (preg_match('/^cmd top ([0-9]{1,2})$/i', $msg, $matches)) {
	if ($matches[1] > 10) {
		$this->send("/m $nick Sorry, aber für mehr als 10 Benutzer schau dir bitte die Toplist auf der Homepage an:");
		$this->send("/m $nick http://gluecksrad.squizzl.de/");
	} elseif ($matches[1] < 5) {
		$this->send("/m $nick  Sorry, aber du musst dir schon mindestens 5 Plätze ansehen");
		$this->send("/".time());
	} else {
		$this->user->get_toplist($matches[1], $nick);
	}
}
*/	

/**
 * Couch Befehle
 */
 /*
if (preg_match('/^cmd couch$/iU', $msg) && ($msg_type == "msg" || $msg_type == "whisper")) {
	$this->grX->cmd_couch($nick);
} elseif (preg_match('/^cmd couch bye$/iU', $msg) && ($msg_type == "msg" || $msg_type == "whisper")) {
	$this->grX->cmd_couch_bye($nick);
} elseif (preg_match('/^couch (.*?)$/iU', $msg, $matches) && $msg_type == "whisper") {
    $cmsg = $matches[1];
    $this->grX->sendCouch("($nick) $cmsg");
}
*/

/**
 * Superuser Befehle
 */


/**
 * Helfer Befehle
 */
 /*
// Befehl: cmd idle / .idle
if ((preg_match("/^.idle ([0-9]+)$/i", $msg, $r) && $msg_type == "whisper") ||
    (preg_match("/^cmd idle ([0-9]+)$/i", $msg, $r) && $msg_type == "msg") && $this->user->get_level($nick) >= 4) {
	if (is_numeric($r[1]) && $r[1] <= 90) {
        $this->grX->cmd_idle($nick, $r[1]);
	}
// Befehl: cmd wait / .wait
} elseif ((preg_match("/^.wait ([0-9]+)$/i", $msg, $r) && $msg_type == "whisper") ||
    (preg_match("/^cmd wait ([0-9]+)$/i", $msg, $r) && $msg_type == "msg") && $this->user->get_level($nick) >= 4) {
	if (is_numeric($r[1]) && $r[1] <= 90) {
        $this->grX->cmd_wait($nick, $r[1]);
	}
// Befehl: cmd reset / .reset
} elseif ((preg_match("/^.reset$/i", $msg) && $msg_type == "whisper" && $this->user->get_level($nick) >= 4) ||
    (preg_match("/^cmd reset$/", $msg) && $msg_type == "msg") && $this->user->get_level($nick) >= 4) {
	$this->grX->cmd_reset($nick);
// Befehl: .getl
} elseif (preg_match("/^.getl$/i", $msg) && $msg_type == "whisper" && $this->user->get_level($nick) >= 4) {
	$this->send("/me $nick wüsste wohl gerne die Lösung von mir... Bekommt er aber nicht! : P");
} elseif (preg_match("/^.loes$/i", $msg) && $msg_type == "whisper" && $this->user->get_level($nick) >= 4) {

// Befehl: cmd add ban / .add ban <nick> <time>
} elseif ((preg_match("/^.add ban ([\w]+)( [0-9]+)?$/", $msg, $r) && $msg_type == "whisper") ||
    	  (preg_match("/^cmd add ban ([\w]+)( [0-9]+)?$/", $msg, $r) && $msg_type == "msg") && $this->user->get_level($nick) >= 4) {
    $target = $r[1];
    if (isset($r[2])) $bantime = trim($r[2]);
    else $bantime = 0;
    
    $this->send("/k $target");
	if ($bantime != 0)
		$this->send("-] '$target' wurde für $bantime Minuten vom Spiel ausgeschlossen [-");
	elseif ($bantime == 0)
		$this->send("-] '$target' wurde permanent vom Spiel ausgeschlossen [-");

	$this->user->add_ban($nick, $target, $bantime*60);
// Befehl: cmd del ban / .del ban <nick>
} elseif ((preg_match("/^.del ban ([\w]+)$/", $msg, $r) && $msg_type == "whisper") ||
    	  (preg_match("/^cmd del ban ([\w]+)$/", $msg, $r) && $msg_type == "msg") && $this->user->get_level($nick) >= 4) {
    $target = $r[1];
    if ($this->user->del_ban($nick, $target)) {
    	$this->send("-] '$target' ist nicht länger vom Spiel ausgeschlossen [-");
    	$this->send("/m $nick '$target' ist nicht länger vom Spiel ausgeschlossen");		
	} else {
		$this->send("/m $nick Der Benutzer '$target' ist nicht vom Spiel ausgeschlossen...");
	}
// Befehl: cmd add points / .add points <nick> <points>
} elseif ((preg_match("/^.add points ([\w]+) ([0-9]+)$/", $msg, $r) && $msg_type == "whisper") ||
    	  (preg_match("/^cmd add points ([\w]+) ([0-9]+)$/", $msg, $r) && $msg_type == "msg") && $this->user->get_level($nick) >= 4) {
    $target = $r[1];
    $points = $r[2];
    
    if ($this->user->add_points($target, $points)) {
		$this->send("/m $nick '$target' wurden jetzt $points Punkte gut geschrieben.");
		$this->send("/m $target Dir wurden $points von $nick Punkte gut geschrieben.");
	} else {
		$this->send("/m $nick Es ist ein Fehler aufgetreten. Die Punkte konnten '$target' nicht gut geschrieben werden.");
	}
// Befehl: cmd del points / .del points <nick> <points>
} elseif ((preg_match("/^.del points ([\w]+) ([0-9]+)$/", $msg, $r) && $msg_type == "whisper") ||
    	  (preg_match("/^cmd del points ([\w]+) ([0-9]+)$/", $msg, $r) && $msg_type == "msg") && $this->user->get_level($nick) >= 4) {
    $target = $r[1];
    $points = $r[2];
    
    if ($this->user->del_points($target, $points)) {
		$this->send("/m $nick '$target' wurden jetzt $points Punkte abgezogen.");
		$this->send("/m $target Dir wurden $points von $nick Punkte abgezogen.");
	} else {
		$this->send("/m $nick Es ist ein Fehler aufgetreten. Die Punkte konnten '$target' nicht abgezogen werden.");
	}
// Befehl: cmd status / .status
} elseif ((preg_match("/^.status$/", $msg, $r) && $msg_type == "whisper") ||
    	  (preg_match("/^cmd status$/", $msg, $r) && $msg_type == "msg") && $this->user->get_level($nick) >= 4) {
    
    $this->send("/wc Glücksrad");
    $bot_info = $this->get_status();
    $grx_info = $this->grX->get_status();
    
    $this->send("-] Einen Moment bitte, es wird ein Statusbericht für '$nick' erstellt... [-");
    $this->send("/m $nick grX v".$this->grX->grx_version." läuft seit: ".getdifftime($this->bot['logintime'])."");
    $this->send("/m $nick ".$this->user->count("all")." Benutzer & ".$grx_info['terms']." Begriffe in ".$grx_info['topics']." Themenbereichen");
    $this->send("/m $nick Administratoren: ".$this->user->count("admin")." :: Helfer: ".$this->user->count("helfer")." :: Superuser: ".$this->user->count("su")." :: Gebannt: ".$this->user->count("banned"));
    $this->send("/m $nick Der Zeit befinden sich ".$this->users_in_room." Benutzer im Raum (Wartezeit: ".$this->grX->max_wait.")");
    if ($this->grX->status >= 4) {
		$this->send("/m $nick Es läuft gerade eine Runde. Das Thema lautet: ".$this->grX->topic." :: Die aktuelle Spielzeit beträgt: ".$grx_info['spielzeit_aktuell']." Sekunden :: Die gesamte Spielzeit beträgt: ".$grx_info['spielzeit']." Sekunden");
	}
    $this->send("/m $nick Gestartete Runden: ".$this->grX->rounds['started']." :: Abgebrochen: ".$this->grX->rounds['aborted']." :: Beendet: ".$this->grX->rounds['finished']."");
    $this->send("/m $nick Die 5 best platzierten Spieler: ".$this->user->get_toplist(5));
    if ($nick_level >= 5) {
		$this->send("/m $nick Informationen zu den Logdateien :: Anzahl: ".$bot_info['log']['filecount']." :: Größe: ".$bot_info['log']['filesize']);
		
	}
    $this->send("-] ... fertig! Es kann weiter gehen : ) [-");
	
}
*/


/**
 * Administrations Befehle
 */
 
/*
if ((preg_match("/^.(say|speak) (.*?)$/", $msg, $r) && $msg_type == "whisper" && $nick_level >= 5) ||
    (preg_match("/^cmd (say|speak) (.*?)$/", $msg, $r) && $msg_type == "msg") && $nick_level >= 5) {
	$send = $r[2];
	if (preg_match("!^(/ju$|/j$|/sw$|/ig$|/a$|/gag$|/w$|/wc$|/mail$|/away$|/c$|/q$|/scr$|/sp$|/r [0-9]+$)!", $send) ||
		preg_match("!^(/ju(.*)$|/j(.*)$|/sw $|/sw html$|/sw java$|/ig(.*)$|/a(.*)|/gag(.*)$|/w(.*)$|/wc(.*)$|/mail(.*)$|/away (.*)$|/f \+(.*)$|/f(.*)$|/sp$|/r [0-9]+$)!", $send)) {
		$this->send("/m $nick Sorry, aber das werde ich nicht machen... auf keinen Fall!");
	} else {
		$this->send($send);
	}
	$this->send("/blubb");
// Bot herunterfahren
} elseif (preg_match("/^.quit( .*?)?$/", $msg, $r) && $msg_type == "whisper" && $nick_level >= 6) {
    if (isset($r[1])) $r[1] = trim($r[1]);
    #$this->shutdown($nick, $r[1]);
	$this->send("/q");
    exit;

// Bot neustarten
} elseif (preg_match("/^.restart( .*?)?$/", $msg, $r) && $msg_type == "whisper" && $nick_level >= 6) {
    if (isset($r[1])) $r[1] = trim($r[1]);
    #$this->restart($nick, $r[1]);
    $this->send("/q");
 
} elseif ((preg_match("/^.add( admin| helfer| su| superuser) ([\w]+)$/i", $msg, $r) && $msg_type == "whisper") ||
    (preg_match("/^cmd add( admin| helfer| su| superuser) ([\w]+)$/i", $msg, $r) && $msg_type == "msg") && $nick_level >= 5) {
    $user_status = trim($r[1]);
    if ($user_status == "superuser") $user_status = "su";
	$user_nick = $r[2];
	
	if ($user_status == "admin" && $nick_level >= 6) {
		if ($this->user->add($nick, $user_nick, 5)) {
			$this->send("-] '$user_nick' wurde zum Administrator ernannt [-");
			$this->send("/m $user_nick Du wurdest zu einem Glücksrad Administrator ernannt.");
			$this->sendMail($user_nick, 'Du bist jetzt Glücksrad Administrator', "Hallo $user_nick,\n \nab sofort bist du Glücksrad Administrator. Dir stehen jetzt neue Befehle zur Verfügung:\n \n
			- cmd add <su|helfer> <nick> - Fügt einen Benutzer als Superuser oder Helfer hinzu\n
			- cmd del <su|helfer> <nick> - Löscht einen Superuser oder Helfer\n
			- cmd status - Sendet dir einen Statusbericht\n \n
			Selbstverständlich stehen dir auch alle Helfer Befehle zur Verfügung:\n
			- cmd reset - Bricht die aktuelle Runde ab\n
			- cmd idle - Setzt die Wartezeit zwischen den Runden\n
			- cmd wait - Setzt die Wartezeit nach einer Runde (diese wird im Normalfall automatisch gesetzt!!!)\n
			- cmd add ban <nick> <zeit> - Fügt einen Spieler zur Banliste hinzu, wenn du keine Zeit angibst ist der Spieler permanent gebannt\n
			- cmd del ban <nick> - Löscht einen Spieler aus der Banliste\n
			- cmd add points <nick> <punkte> - Addiert einem Spieler Punkte\n
			- cmd del points <nick> <punkte> - Zieht einem Spieler Punkte ab\n \n
			Du kannst mir auch alle Befehle flüstern, allerdings wird aus cmd ein '.'. Beispiel: /gamemaster .add points snake 100\n \n
			Bei Fehlern im Spiel wendest du dich bitte an 'snake', bei allen anderen Fragen bitte an einen der anderen Admins (show admins) wenden.\n \n-----------------------\nDies ist eine automatisch generierte Nachricht, bitte NICHT darauf antworten!");
			$this->send("/m $user_nick Dir stehen jetzt weitere Befehle zur Verfügung. Eine Übersicht der neuen Befehle habe ich dir soeben per Community Mail geschickt...");
		}
	} elseif ($user_status == "helfer" && $nick_level >= 5) {
		if ($this->user->add($nick, $user_nick, 4)) {
			$this->send("-] '$user_nick' wurde zum Helfer ernannt [-");
			$this->send("/m $user_nick Du wurdest zu einem Glücksrad Helfer ernannt.");
			$this->sendMail($user_nick, 'Du bist jetzt Glücksrad Helfer', "Hallo $user_nick,\n
			\nab sofort bist du
			Glücksrad Helfer. Dir stehen jetzt neue Befehle zur Verfügung:\n
			\n
			- cmd reset - Bricht die aktuelle Runde ab\n
			- cmd idle - Setzt die Wartezeit zwischen den Runden\n
			- cmd wait - Setzt die Wartezeit nach einer Runde (diese wird im Normalfall automatisch gesetzt!!!)\n
			- cmd add ban <nick> <zeit> - Fügt einen Spieler zur Banliste hinzu, wenn du keine Zeit angibst ist
			der Spieler permanent gebannt\n
			- cmd del ban <nick> - Löscht einen Spieler aus der Banliste\n
			- cmd add points <nick> <punkte> - Addiert einem Spieler Punkte\n
			- cmd del points <nick> <punkte> - Zieht einem Spieler Punkte ab\n
			 \n
			Du kannst mir auch alle Befehle flüstern, allerdings wird aus cmd ein '.'. Beispiel:
			/gamemaster .add points snake 100\n
			 \n
			Bei Fehlern im Spiel wendest du dich bitte an 'snake', bei allen anderen Fragen bitte an einen
			der anderen Admins (show admins)wenden.\n
			 \n
			-----------------------\n
			Dies ist eine automatisch generierte Nachricht, bitte NICHT darauf antworten!");
			$this->send("/m $user_nick Dir stehen jetzt weitere Befehle zur Verfügung. Eine Übersicht der neuen Befehle habe ich dir soeben per Community Mail geschickt...");	
		}	
	} elseif ($user_status == "su" && $nick_level >= 5) {
		if ($this->user->add($nick, $user_nick, 3)) {
			$this->send("-] '$user_nick' wurde zum Superuser ernannt [-");
			$this->send("/m $user_nick Du wurdest zu einem Glücksrad Superuser ernannt.");
			#$this->send("/m $user_nick Dir stehen jetzt weitere Befehle zur Verfügung. Eine Übersicht der neuen Befehle habe ich dir soeben per Community Mail geschickt...");				
		}
	}
	
} elseif ((preg_match("/^.del( admin| helfer| su) ([\w]+)$/i", $msg, $r) && $msg_type == "whisper") ||
    (preg_match("/^cmd del( admin| helfer| su) ([\w]+)$/i", $msg, $r) && $msg_type == "msg") && $nick_level >= 5) {
    $user_status = trim($r[1]);
	$user_nick = $r[2];
	
	if ($user_status == "admin" && $nick_level >= 6) {
		if ($this->user->del($nick, $user_nick)) {
			$this->send("-] '$user_nick' wurden die Administrator-Rechte entzogen [-");
			$this->send("/m $user_nick Dir wurden die Rechte als Glücksrad Administrator entzogen.");
		}
	} elseif ($user_status == "helfer" && $nick_level >= 5) {
		if ($this->user->del($nick, $user_nick)) {
			$this->send("-] '$user_nick' wurden die Helfer-Rechte entzogen [-");
			$this->send("/m $user_nick Dir wurden die Rechte als Glücksrad Helfer entzogen.");	
		}	
	} elseif ($user_status == "su" && $nick_level >= 5) {
		if ($this->user->del($nick, $user_nick)) {
			$this->send("-] '$user_nick' wurden die Superuser-Rechte entzogen [-");
			$this->send("/m $user_nick Dir wurden die Rechte als Glücksrad Superuser entzogen.");				
		}
	}
}
*/
?>