<?php

class grX extends phpBot {
 
    protected   $bot, $server, $db, $user;
	
	protected	$grx_version = '1.3.3.7';
	protected	$grx_website = 'http://push.it';
	
	$tage = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
	$tag = date("w");
	
	
	private     $room_topic  = 'Wir wŸnschen euch einen schšnen $tage[$tag] !';
	
	private		$channel = 'Treffpunkt';
	private		$colors = array("9999ff", "ff8000", "dd0000", "008000",	"000070", "999999", "3399ff", "0000dd",	"00b0c5", "9900ff", "66bb00");
	private		$vowels = array("a", "e", "i", "o", "u");
	private		$konsonants = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x","y", "z");
	private     $guessed = array();
	protected	$status = 0;
	
	protected	$max_idle = 45;
	protected	$max_wait = 5;
	
	protected	$rounds = array('started' => 0, 'aborted' => 0, 'finished' => 0);	
	protected	$players = array();
	protected	$players_before = array();
	protected   $couch_users = array();
	
	protected	$current = array();
	protected	$buy, $points, $topic, $term, $spielzeit;
	
	public function __construct($bot, $server, $db, $user, $timer) {
        $this->bot = $bot;
        $this->server = $server;
        $this->db = $db;
        $this->user = $user;
        $this->timer = $timer;
        $this->start();
    }
	
	protected function start() {		
		$this->wait();
		$this->send("ChaosBot v".$this->version." Squizzl-Version (c) 2010 'Treffpunkt'");
		$this->wait();
        $this->send("/t ".$this->room_topic."");
	// $this->change_color();	
	 $this->status = 1;
	}
	
/*	protected function cmd_grad($nick) {	 
        if ($this->status == 1 && !$this->is_playing($nick) && !$this->has_played($nick)) {
			$this->players[1] = array('nick' => $nick, 'points' => 0, 'extradreh' => 0);
			$this->send($nick.', du bist der/die Erste in dieser Runde, bleiben noch zwei andere Willige.');
			$this->send("/t Es werden noch 2 Spieler gesucht! Hilfe & Toplist auf: ".$this->grx_website." ::");
			$this->status = 2;
		} elseif ($this->status == 2 && !$this->is_playing($nick) && !$this->has_played($nick)) {
			$this->players[2] = array('nick' => $nick, 'points' => 0, 'extradreh' => 0);
			$this->send('*woow*, Eine(n) Zweite(n) haben wir schon. Welcome, '.$nick.'! Noch eine(r) - wer will?');
			$this->send("/t Es wird noch 1 Spieler gesucht! Hilfe & Toplist auf: ".$this->grx_website." :: ");
			$this->status = 3;
		} elseif ($this->status == 3  && !$this->is_playing($nick) && !$this->has_played($nick)) {
			$this->players[3] = array('nick' => $nick, 'points' => 0, 'extradreh' => 0);
			$this->send('Na endlich, der/die Dritte in der Runde ist: '.$nick.'!');
			$this->start_round();
		} elseif ($this->status < 4 && $this->is_playing($nick)) {
			$this->send("/m $nick Du nimmst bereits am nächsten Spiel teil!");
		} elseif ($this->status >= 4) {
			$this->send('/m '.$nick.' Es wird gerade gespielt. Du musst bis zur neuen Runde warten.');
		}       
    }
    
	protected function cmd_start($nick) {
        if ($this->status < 4 && $this->is_playing($nick) && count($this->players) == 2) {
			$players[3] = array('nick' => '-', 'punkte' => 0, 'extradreh' => 0);
			$this->send('Ok, also ihr wollt zu zweit anfangen? Kein Problem!');
			$this->start_round();
		} elseif (count($this->players) < 2 && $this->status < 4) {
			$this->send('Ha, mit dir alleine spielen... Aber sonst gehts dir gut?');
		}
    }
    
	protected function cmd_thema($nick) {
		if ($this->status >= 4) {
			$this->send("/m $nick Das aktuelle Thema lautet: ".$this->topic);
			$this->send("/".time());
		}
    }
	protected function cmd_weiter($nick) {
        if ($this->current['nick'] == $nick && $this->status <= 4) {
            $this->get_nextplayer();
            // einbauen random spruch
            $this->send('/me '.$this->current['nick'].', Du bist dran!');
            $this->showterm();
        }
    }
	
	protected function cmd_dreh($nick, $extradreh = false) {        
        if ($extradreh == true) {
			$this->players[$this->current['id']]['extradreh'] = $this->players[$this->current['id']]['extradreh']-1;
			if ($this->players[$this->current['id']]['extradreh'] == 0) {
				$this->send('Du hast jetzt keinen Extradreh mehr!');
			}
			$this->status = 4;
		}

		if ($this->status == 4 && $nick == $this->current['nick']) {
			$this->buy = false;

			mt_rand(0,(double)microtime()*1000000);
			$grdt = mt_rand(1, 20);
			$this->send('/me *ratter*');

			if ($grdt == 3) {
				$this->get_nextplayer();
				$this->send('/me och, leider aussetzen *hihi*');
				$this->send('/me '.$this->current['nick'].', Du bist dran!');
				$this->status = 4;
			} elseif ($grdt == 6) {
				$this->players[$this->current['id']]['points'] = 0;
				$this->get_nextplayer();
				$this->send('/me Bankrott! *auslach*');
				$this->send('/me '.$this->current['nick'].', Du bist dran!');
				$this->status = 4;
			} elseif ($grdt == 12) {
				if ($this->players[$this->current['id']]['extradreh'] >= 3) {
				} else {
					$this->players[$this->current['id']]['extradreh'] = $this->players[$this->current['id']]['extradreh'] + 1;
					if ($this->players[$this->current['id']]['nick'] == "ayana") {
						$this->send('/me Extradreh!!! *froi*');
						} else {
						$this->send('/me Extradreh!!! *freu*');
					}
					$this->send('/me *ratter*');
				}
				mt_rand(0,(double)microtime()*1000);
				$this->points = mt_rand(1, 22)*100;

				$this->send('/me Du spielst um '.$this->points.' Punkte!');
				$this->send('/me Nun ja, welchen Konsonant (Mitlaut) willst du? Z. B. \'cmd f\'');
				$this->status = 5;
			} else {
				mt_rand(0,(double)microtime()*1000);
				$this->points = mt_rand(1, 22)*100;
				$this->send('/me Du spielst um '.$this->points.' Punkte!');
				$this->send('/me Nun ja, welchen Konsonant (Mitlaut) willst du? Z. B. \'cmd f\'');
				$this->status = 5;
			}
			$this->showterm();
			$this->timers['grx_idle'] = time()+$this->max_idle;

		}        
    }
	
	protected function cmd_kauf($nick) {
		if ($this->status == 4 && $this->current['nick'] == $nick) {
			if ($this->players[$this->current['id']]['points'] >= 300) {
				$this->send('/me Na gut, welchen Vokal (Selbstlaut) willst du denn kaufen? Z. B. \'cmd e\'');
				$this->buy = true;
				$this->status = 6;
			} else {
				$this->send('/me Du hast leider zu wenige Punkte, '.$nick.'! *ohhh*');
				$this->status = 4;
			}
			$this->showterm();
			$this->timers['grx_idle'] = time()+$this->max_idle;
		}
	}
	
	protected function cmd_buchstabe($nick, $letter) {
        if (($this->status == 5 || $this->status == 6) && $this->current['nick'] == $nick) {
			$letter = strtolower($letter);

			if ($this->status == 5 && !in_array($letter, $this->konsonants)) {
				$this->get_nextplayer();
				$extradrehfrage = false;
				$this->send('/me neeeeeeee, oder? Ist das etwa ein Konsonant?');
				$this->send('/me '.$this->current['nick'].', Du bist dran!');
				$this->status = 4;
			} elseif ($this->status == 6 && !in_array($letter, $this->vowels)) {
				$this->get_nextplayer();
				$extradrehfrage = false;
				$this->send('/me *lachweg* - och nööö, weisst du noch nich mal, dass das kein Vokal ist???');
				$this->send('/me '.$this->current['nick'].', Du bist dran!');
				$this->status = 4;
			} else {
				
				if (in_string($letter, strtolower($this->term), 1) && !in_array($letter, $this->guessed)) {
					$this->guessed[] = $letter;

					if ($this->buy == true) {
						$this->players[$this->current['id']]['points'] = $this->players[$this->current['id']]['points'] - 300;
					} else {
						$this->points = $this->points * substr_count(strtolower($this->term), $letter);
						$this->players[$this->current['id']]['points'] = $this->players[$this->current['id']]['points'] + $this->points;
					}
					$this->status = 4;
					$this->send('/me *blink*');
					$this->send('/me Deine Punkte, '.$this->players[$this->current['id']]['nick'].': '.$this->players[$this->current['id']]['points']);
				} else {
					if ($this->players[$this->current['id']]['extradreh'] >= 1) {
						$this->send('Du hast einen Extra Dreh! Willst du ihn einsetzen? (\'ja\' oder \'nein\')');
						$this->status = 101;
					} else {
						$this->get_nextplayer();
						$this->send('/me '.$this->current['nick'].', Du bist dran!');
						$this->send('/me Deine Punkte, '.$this->players[$this->current['id']]['nick'].': '.$this->players[$this->current['id']]['points']);
						$this->status = 4;
					}
				}
			}
			$this->showterm();
			$this->timers['grx_idle'] = time()+$this->max_idle;
		}        
    }
    
    protected function cmd_loes($nick, $begriff) {
        // Auf das lösen des gesuchten Begriffs vorbereiten
        if ($begriff == null) {
        	$this->send('/me Jetzt bin ich aber neugierig. Sag mir deine Lösung, z. B. \'cmd haus\'');
			$this->status = 7;
			$this->timers['grx_idle'] = time()+$this->max_idle;
        
        // Versuch den gesuchten Begriff zu lösen
        } elseif ($this->status == 7 && $this->current['nick'] == $nick && $begriff != "loes") {
            if (strtolower($begriff) == strtolower($this->term)) {
                $bonus = mt_rand(1, 10)*100;
				$total = $this->players[$this->current['id']]['points'] + $bonus;
				
				// Update der Punkte hinzufügen
                $this->send('/me '.strtoupper($this->players[$this->current['id']]['nick']).', DU HAST GEWONNEN!!!');
                $this->user->add_points($nick, $total);
                $this->send('/me Die Top 3 Spieler: '.$this->user->get_toplist(3));
                $last_time = time()+$this->max_wait;
				for ($i=1; $i < count($this->players)+1; $i++) {
				    // Den Spieler mit Wartzeit zur gerade gespielt Liste hinzufügen
					$this->roundbefore[] = $this->players[$i]['nick'];
					$this->timers[$this->players[$i]['nick']] = $last_time;
					
					// Dem Spieler sagen welcher Rang er ist und festhalten wann er das
					// letzte mal gespielt hat
					$this->user->update($this->players[$i]['nick'], 'lastplayed', time());
					$user = $this->user->get_rank($this->players[$i]['nick']);
					$this->send("/m ".$this->players[$i]['nick']." Du bist mit ".$user['points']." Punkten auf Platz ".$user['rank'].".");
				}
                $this->reset();
                $this->rounds['finished'] = $this->rounds['finished'] + 1;   
            } else {
                // Randomspruch einbauen
                $this->send("/me öhmmm... FALSCH!");
                $this->get_nextplayer();
                
                $this->send('/me '.$this->current['nick'].', Du bist dran!');
				$this->send('/me Deine Punkte, '.$this->players[$this->current['id']]['nick'].': '.$this->players[$this->current['id']]['points']);
			    $this->showterm();

				$this->status = 4;                
            }
            
        }
    }
    
	protected function cmd_quit($nick, $room = false) {
        if ($this->is_playing($nick)) {
            $num_players = count($this->players);
            
            // Ein Spieler, Runde noch nicht gestartet
            if ($num_players == 1 && $this->status < 4) {
            	if ($room) {
            		$this->send("/$nick Schönen Tag noch! *ww*");					
				} else {
					$this->send("/me Du möchtest echt nicht mehr spielen $nick? Okay...");
				}
				unset($this->players[1]);
				$this->send("/t Es werden noch 3 Spieler gesucht! :: Hilfe & Toplist auf: ".$this->grx_website." :: <news>");
				$this->status = 1;
			// Zwei Spieler, Runde noch nicht gestartet
			} elseif ($num_players == 2 && $this->status < 4) {				
				if ($room) {
            		$this->send("/$nick Schönen Tag noch! *ww*");					
				} else {
					$this->send("/me Du möchtest echt nicht mehr spielen $nick? Okay...");
				}
				$this->send("/t Es werden noch 2 Spieler gesucht! :: Hilfe & Toplist auf: ".$this->grx_website." :: <news>");
				
				foreach ($this->players as $id => $nickname) {
					if ($this->players[$id]['nick'] == $nick) {
						if ($id == 1) {
							$this->players[1] = array('nick' => $this->players[2]['nick'], 'points' => 0, 'extradreh' => 0);
							unset($this->players[2]);
						} elseif ($id == 2) {
							unset($this->players[2]);
						}
					}
				}
				$this->status = 2;
			// Zwei Spieler Runde gestartet => Abbrechen
			} elseif ($num_players == 2 && $this->status >= 4) {
				if ($room) {
            		$this->send("/m $nick Öhhhh... Du warst noch am spielen oder so...");
					$this->send("/me Leider hat uns $nick während des Spiels verlassen, darum wird es abgebrochen!");				
				} else {
						$this->send("/me $nick hat wohl keine Lust mehr zu spielen, darum wird es abgebrochen!");
				}
				$this->rounds['aborted'] = $this->rounds['aborted'] + 1;
				$this->reset();
            // Drei Spieler, Runde gestartet => Die Zwei anderen weiterspielen lassen
			} elseif ($num_players == 3 && $this->status >= 4) {
				if ($room) {
            		$this->send("/m $nick Öhhhh... Du warst noch am spielen oder so...");
					$this->send("/me Leider hat uns $nick während des Spiels verlassen. Jetzt geht es zu zweit weiter!");				
				} else {
					$this->send("/me $nick hat wohl keine Lust mehr zu spielen... na gut, dann geht es zu zweit weiter!");
				}							
				foreach ($this->players as $id => $nickname) {					
					if ($this->players[$id]['nick'] == $nick) {						
						if ($id == 1) {
							$this->players[1] = array('nick' => $this->players[2]['nick'], 'points' => $this->players[2]['points'], 'extradreh' => $this->players[2]['extradreh']);
							$this->players[2] = array('nick' => $this->players[3]['nick'], 'points' => $this->players[3]['points'], 'extradreh' => $this->players[3]['extradreh']);
							unset($this->players[3]);
						} elseif ($id == 2) {
							$this->players[2] = array('nick' => $this->players[3]['nick'], 'points' => $this->players[3]['points'], 'extradreh' => $this->players[3]['extradreh']);
							unset($this->players[3]);
						} elseif ($id == 3) {
							unset($this->players[3]);
						}
						
						if ($this->current['id'] == 1)			
						
						if ($this->current['nick'] == $nick) {
							unset($this->players[3]);
							$this->get_nextplayer();
							$this->send('/me '.$this->current['nick'].', Du bist dran!');
							$this->send('/me Deine Punkte, '.$this->players[$this->current['id']]['nick'].': '.$this->players[$this->current['id']]['points']);
							$this->showterm();
							$this->status = 4;
						}						
					}				
				}		
			}                   
        }
    }
    
	protected function cmd_ja($nick) {
        if ($this->status == 101 && $this->current['nick'] == $nick && $this->players[$this->current['id']]['extradreh'] >= 1) {
            $this->cmd_dreh($nick, true);
        }
    }
    
	protected function cmd_nein($nick) {
        if ($this->status == 101 && $this->current['nick'] == $nick && $this->players[$this->current['id']]['extradreh'] >= 1) {
            $this->send('Okay... dann vielleicht beim nächsten mal *gg*');
            $this->get_nextplayer();
            $this->send('/me '.$this->current['nick'].', Du bist dran!');
            $this->showterm();
            $this->status = 4;
            $this->timers['grx_idle'] = time()+$this->max_idle;
        }        
    }
    
    protected function cmd_getl($nick) {
		$this->send("/m $nick Die Lösung lautet:");
		$this->send("/m $nick ".$this->term);
	}
	
	private function start_round() {
        mt_rand(0,(double)microtime()*1000000);

		if (count($this->players) == 3) $rand = mt_rand(1, 3);
		elseif (count($this->players) == 2) $rand = mt_rand(1, 2);

		$this->current['id'] = $rand;
		$this->current['nick'] = $this->players[$rand]['nick'];
		
		$this->select_term();
		
		$this->send("/t Es wird gerade gespielt - zuschauen erwünscht! Aktuelles Thema: ".$this->topic." :: Hilfe & Toplist auf: ".$this->grx_website);
		// Random Startspruch
		$this->send("/me Lasst uns beginnen! ".$this->current['nick']." fängt an!");
		$this->send("/me Die Hilfe ist auf: ".$this->grx_website);
		$this->send("/me Thema: ".$this->topic);
		$this->showterm();
		$this->timers['grx_idle'] = time()+$this->max_idle;
		$this->status = 4;
		unset($this->roundbefore);       
        $this->rounds['started'] = $this->rounds['started'] + 1;
        $this->timer->start('spielzeit');
    }

	private function select_term() {
		$result = $this->db->query_first("SELECT id AS topicid, topic FROM topics WHERE active = 1 ORDER BY RAND() LIMIT 1");
		$this->topic = $result['topic'];
		
		$result = $this->db->query_first("SELECT term FROM terms WHERE active = 1 AND topicid = ".$result['topicid']." ORDER BY RAND() LIMIT 1");
		$this->term = trim($result['term']);		
	}
	
	protected function is_playing($nick) {
        if ($this->players[1]['nick'] == $nick) return true;
		elseif ($this->players[2]['nick'] == $nick) return true;
		elseif ($this->players[3]['nick'] == $nick) return true;
		else return false;
    }
	
	private function has_played($nick) {
        if ($this->timers[$nick] < time()) {
			return false;
		} else {
			$this->send("/m $nick Du musst noch ".($this->timers[$nick] - time())." Sekunden warten bevor du wieder spielen kannst...");
			$this->send("/".time());
			return true;
		}
    }
    
	private function get_nextplayer() {
        $num_players = count($this->players);
		if ($num_players == 3 && $this->current['id'] == 3) {
			$this->current['id'] = 1;
			$this->current['nick'] = $this->players[1]['nick'];

		} elseif ($num_players == 2 && $this->current['id'] == 2) {
			$this->current['id'] = 1;
			$this->current['nick'] = $this->players[1]['nick'];

		} elseif ($num_players == 3 && $this->current['id'] == 2) {
			$this->current['id'] = 3;
			$this->current['nick'] = $this->players[3]['nick'];
		} elseif ($this->current['id'] == 1) {
			$this->current['id'] = 2;
			$this->current['nick'] = $this->players[2]['nick'];
		}
    }
    
    protected function idle_nextpalyer() {
		
		$msg = array('/me ² '.$this->current['nick'].' ... Du hast wohl keine Lust, hä?!',
				'/me haste Angst, '.$this->current['nick'].' oder was is mit dir?',
			  	'/me naja.. '.$this->current['nick'].' wer nich will, der hat. Gell?',
			  	'/me beweg deinen Arsch sofort her, '.$this->current['nick'].'!!!',
				'/me naaaaaaa los '.$this->current['nick'].', einschlafen ist nicht!!!',
				'/me HALLO? '.$this->current['nick'].' willste nich mehr???');

		$count = count($msg)-1;
		$rand = mt_rand(1, $count);
		$this->send($msg[$rand]);

		$this->get_nextplayer();
		$this->send('/me Ok, '.$this->current['nick'].'. Du bist dran!');
		$this->showterm();
		$this->timers['grx_idle'] = time()+$this->max_idle;
		$this->status = 4;
	}
	
	private function showterm() {
		$replace = strtolower($this->term);
		$return = "";
		for ($i=0; $i < strlen($replace); $i++) {
			$letter = $replace{$i};
			if (in_array($letter, $this->guessed)) {
				$return .= strtoupper($letter) . ' ';
			} else {
				if ($letter == " ")
					$return .= ' &nbsp;';
				else
					$return .= ' _';
			}
		}
		$this->send('/me [ '.$return.' ] ');
	}
	
	
	protected function cmd_reset($nick) {
        $this->send("/me Das aktuelle Spiel wurde von $nick abgebrochen!");
        $this->reset();
        $this->rounds['aborted'] = $this->rounds['aborted'] + 1;
    }
    
	protected function cmd_idle($nick, $idle) {
        // Das ganze in die Logfiles schreiben
        $this->send(":: Die Idle-Zeit wurde von $nick auf $idle Sekunden gesetzt ::");
        $this->max_idle = $idle;
    }
	protected function cmd_wait($nick, $idle) {
        // Das ganze in die Logfiles schreiben
        $this->send(":: Die Wartezeit wurde von $nick auf $idle Sekunden gesetzt ::");
        $this->max_wait = $idle;
    }
	
	private function reset() {
	    $this->change_color();
        $this->send("/t Es werden noch 3 Spieler gesucht! :: ".$this->grx_website." ::");
	    $this->send("/aq Neues Spiel? Neues Glück?");
        $this->send("/me Mit 'cmd grad' kannst du mitspielen!");
        
        // Wichtig Statistiken Hochzählen
                     
        unset($this->players);
        unset($this->current);
        unset($this->guessed);
        unset($this->topic);
        unset($this->term);
               
        $this->guessed = array();
        $this->players = array();
		$this->status = 1;
		$this->timer->stop('spielzeit');
		$this->spielzeit = $this->spielzeit + $this->timer->gettime('spielzeit');
    }
	
	
	protected function cmd_couch($nick) {
        if (!$this->on_couch($nick)) {
            $users_on_couch = count($this->couch_users);
		    $users = $this->get_couchusers();
		    $this->couch_users[] = $nick;
		    
		    $this->send("/me $nick... ich werde wegen dir die Couch nicht sauber machen!!!");
		    $this->sendCouch(formatdate('H:i', time()) . ' '.$nick.' betritt die Glücksrad Couch');
            if ($users_on_couch >= 1) {
            	$this->send("/m $nick Du kannst mit den anderen auf der Couch sprechen in dem du mir 'couch dein Text' zuflüsterst.");
                $this->send("/m $nick Folgende Benutzer sind schon auf der Glücksrad Couch:");
			    $this->send("/m $nick $users");
            } else {
            	$this->send("/m $nick Du kannst mit den anderen auf der Couch sprechen in dem du mir 'couch dein Text' zuflüsterst.");
                $this->send("/m $nick Du bist der erste auf der Glücksrad Couch...");
            }	
        } else {
            $this->send("/m $nick Du bist doch schon auf der Couch...");
        }
    }
	
	protected function cmd_couch_bye($nick) {
        foreach ($this->couch_users as $id => $nickname) {
            if ($nick == $nickname) unset($this->couch_users[$id]);
        }        
        for ($i = 0; $i <= count($this->couch_users); $i++) {
            if ($this->couch_users[$i]== "") {
                unset($this->couch_users[$i]);
            }
        }        
	   $this->couch_users = array_values($this->couch_users);			
	   $this->send("/m $nick Du hast die Glücksrad Couch verlassen");
	   $this->sendCouch(formatdate('H:i', time()).' '.$nick.' verlässt die Glücksrad Couch');
    }
	
	protected function sendCouch($string) {
        for ($i=0; $i < count($this->couch_users); $i++) {
	       $this->send("/m ".$this->couch_users[$i]." $string");
	   }	
    }	
	
	private function get_couchusers() {
        $users = "";
        for ($i = 0; $i < count($this->couch_users); $i++) {
            $users .= $this->couch_users[$i] . ', ';
        }        
        return substr($users, 0, strlen($users)-2);
    }
    
    private function on_couch($nick) {
        if (in_array($nick, $this->couch_users)) {
            return true;
        }
    }
    
    private function count_couch() {
    	return count($this->couch_users);
    }
	
	
	private function count_topics() {
		$result = $this->db->query_first("SELECT COUNT(id) AS anz FROM `topics`", MYSQL_ASSOC);
		return formatnumber($result['anz']);
	}
	
	private function count_terms() {
		$result = $this->db->query_first("SELECT COUNT(id) AS anz FROM `terms`", MYSQL_ASSOC);
		return formatnumber($result['anz']);
	}
	
	private function change_color() {
		$colorcount = count($this->colors);
		mt_rand(0,(double)microtime()*100000);
		$colornum = mt_rand(1, $colorcount);
		$this->send('/me wechselt mal die Farbe ; )');
		$this->send('/col ' . trim($this->colors[$colornum-1]));
	}
	
	
	public function get_status() {
		$return = array();
		
		$return['spielzeit'] = $this->spielzeit;
		$return['spielzeit_aktuell'] = $this->timer->gettime('spielzeit');
		$return['status'] = $this->status;
		$return['num_couch_users'] = $this->count_couch();
		$return['rounds'] = $this->rounds;
		$return['terms'] = $this->count_terms();
		$return['topics'] = $this->count_topics();
		
		return $return;
	}
	*/
	
}

?>