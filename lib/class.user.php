<?php

class user extends phpBot {
    
    protected $bot, $server, $db, $timer;
    
    public function __construct($bot, $server, $db, $timer) {
        $this->bot = $bot;
        $this->server = $server;
        $this->db = $db;
        $this->timer = $timer;
    }
    
    public function add($nick, $target, $level) {			
		$result = $this->db->query_first("SELECT `nick`, `level` FROM `users` WHERE nick = '$target'");
		if (!$result) {
			$this->db->query("INSERT INTO `users` (id, nick, addedby, level, regdate) VALUES ('', '$target', '".iif($nick != null, $nick, 'System')."', '$level', '".time()."')");
			return true;
		} else {
			$this->db->query("UPDATE `users` SET level = '$level', addedby = '".iif($nick != null, $nick, 'System')."' WHERE nick = '$target'");
			return true;
		}		
	}
	
    public function del($nick, $target) {
		$result = $this->db->query_first("SELECT `nick` FROM `users` WHERE nick = '$target'");
		if ($result) {
			$this->db->query("UPDATE `users` SET level = '1', addedby = '$nick' WHERE nick = '$target'");
			return true;
		} else {
			return false;
		}
	}
	
    public function add_ban($nick, $target, $bantime = 0) {
    	if ($bantime != 0) {
			$bantime = time() + $bantime;
		}
		
		$result = $this->db->query_first("SELECT `nick`, `level` FROM `users` WHERE nick = '$target'");
		if (!$result) {
			$this->db->query("INSERT INTO `users` (nick, level, addedby, banned, bantime) VALUES ('$target', '0', '".iif($nick != null, $nick, 'System')."', 1, '".$bantime."')");
			return 1;
		} else {
			$this->db->query("UPDATE `users` SET level = '0', addedby = '".iif($nick != null, $nick, 'System')."', banned = 1, bantime = '$bantime' WHERE nick = '$target'");
			return 2;
		}	
	}
	
	public function del_ban($nick, $target = 0) {		
		$result = $this->db->query_first("SELECT `nick` FROM `users` WHERE nick = '$target' AND banned = 1");
		if ($result) {
			$this->db->query("UPDATE `users` SET level = '1', addedby = '".iif($nick != null, $nick, 'System')."', banned = 0, bantime = 0 WHERE nick = '$target'");
			return true;
		} else {
			return false;
		}		
	}
    
    public function update($nick, $field, $value) {
		$result = $this->db->query_first("SELECT `nick` FROM `users` WHERE nick = '$nick'");
		if ($result) {
			if ((is_array($field) && is_array($value)) && (count($field) == count($value))) {

			} elseif (!is_array($field) && !is_array($value)) {
				$this->db->query("UPDATE `users` SET $field = '$value' WHERE nick = '$nick'");
				return true;
			} else {
				$this->log("Error: \$user->update_user(); Wrong paramters given.");
				return false;
			}
		} else {
			return false;
		}		
	}
	
	public function add_points($target, $points) {
		$result = $this->db->query_first("SELECT `nick` FROM `users` WHERE nick = '$target'");
		if ($result) {
			$this->db->query("UPDATE `users` SET points = points + $points WHERE nick = '$target'");
			return true;
		} else {
			return false;
		}		
	}
	
	public function del_points($target, $points) {
		$result = $this->db->query_first("SELECT `nick` FROM `users` WHERE nick = '$target'");
		if ($result) {
			$this->db->query("UPDATE `users` SET points = points - $points WHERE nick = '$target'");
			return true;
		} else {
			return false;
		}		
	}
    
    protected function get_userid($nick) {
		if (is_numeric($id)) {
			$result = $this->db->query_first("SELECT `id` FROM `users` WHERE nick = '$nick'");
			return $result['id'];
		} else {
			return 0;
		}
	}
    
    protected function get_nick($id) {
		if (is_numeric($id)) {
			$result = $this->db->query_first("SELECT `nick` FROM `users` WHERE id = '$id'");
			return $result['nick'];
		} else {
			return 0;
		}
	}
    
    protected function get_level($param) {
		if (is_string($param)) {
			$return = $this->db->query_first("SELECT `level` FROM `users` WHERE nick = '$param'");
			return $return['level'];
		} elseif (is_numeric($param)) {
			$return = $this->db->query_first("SELECT `level` FROM `users` WHERE id = '$param'");
			return $return['level'];
		} else {
			return 0;
		}
	}
	
	protected function get_info($param) {
		if (is_string($param)) {			
			$result = $this->db->query_first("SELECT * FROM `users` WHERE nick = '$param'");			
		} elseif (is_numeric($param)) {
			$result = $this->db->query_first("SELECT * FROM `users` WHERE id = '$param'");	
		}
		
		if ($result) {
			$result['rank'] = $this->get_rank($param);
			return $result;
		} else {
			return false;
		}
		
	}
	
    public function get_rank($param) {
		$result = "";
		if (is_string($param)) {
			$user = $this->db->query_first("SELECT `nick`, `points` FROM `users` WHERE nick = '$param'", MYSQL_ASSOC);
		} elseif (is_numeric($param)) {
			$user = $this->db->query_first("SELECT `nick`, `points` FROM `users` WHERE id = '$param'", MYSQL_ASSOC);
		}
		
		if ($user) {
			$rank = $this->db->query_first("SELECT COUNT(id) AS rank FROM `users` WHERE points >= '".$user['points']."'", MYSQL_ASSOC);
			$result['nick'] = $user['nick'];
			$result['rank'] = $rank['rank'];
			$result['points'] = $user['points'];
			return $result;
		} else {
			return false;
		}

		return $return;
	}
	
    protected function get_users($type = null) {		
		if (is_string($type) && $type != null) {
			if ($type == "banned") {
				$sqla = ", banned, bantime";
				$sqlb = "banned = '1'";
			} elseif ($type == "admins") {
				$sqla = "";
				$sqlb = "level >= '5'";
			}  elseif ($type == "helfer") {
				$sqla = "";
				$sqlb = "level = '4'";
			}  elseif ($type == "su") {
				$sqla = "";
				$sqlb = "level = '3'";
			} elseif ($type == "all") {
				$sqla = "";
				$sqlb = "nick != ''";
			}
			$return = array();
			$result = $this->db->query("SELECT `nick`$sqla FROM `users` WHERE $sqlb ORDER BY nick ASC");
			while ($row = $this->db->fetch_array($result)) {				
				if ($type == "banned" && isset($row['nick'])) {
					$return['users'] .= $row['nick'].' (Gesperrt bis: '.iif($row['bantime'] != 0, formatdate("d.m.Y - H:i", $row['bantime'], 1), 'permanent').'), ';
				} elseif (isset($row['nick'])) {
					$return['users'] .= $row['nick'].', ';
				} else {
					$return['users'] = null;
				}
			}			
			if ($return['users'] != "") {
				$return['users'] = substr($return['users'], 0, -2);
				$return['count'] = count(explode(",", $return['users']));
			}
			return $return;			
		} else {
			return false;
		}		
	}
    
    public function count($type = null) {
		if (is_string($type) && $type != null) {
			if ($type == "banned") {
				$sql_where = "WHERE banned = '1'";
			} elseif ($type == "admin") {
				$sql_where = "WHERE level >= '5'";
			}  elseif ($type == "helfer") {
				$sql_where = "WHERE level = '4'";
			}  elseif ($type == "su") {
				$sql_where = "WHERE level = '3'";
			} elseif ($type == "toplist") {
				$sql_where = "WHERE points > 0";
			} elseif ($type == "all") {
				$sql_where = "WHERE nick != ''";
			}
			$result = $this->db->query_first("SELECT COUNT(id) AS anz FROM `users` $sql_where", MYSQL_ASSOC);
			return formatnumber($result['anz']);
		} else {
			return false;
		}
	}
	
	public function get_toplist($num, $nick = "") {
		$result = $this->db->query("SELECT `nick`, `points` FROM `users` WHERE points > 0 ORDER BY points DESC LIMIT $num");
		if ($nick == "") {
			$i = 1;
			while ($row = $this->db->fetch_assoc($result)) {
				$topline .= $i . ". Platz: ".$row['nick']." (Punkte: ".$row['points'].")";
				if ($i < $num) {
					$topline .= " - ";
				}
				$i++;
			}
			return $topline;
		} else {
			$this->send("/m $nick Hier die Top $num Spieler:");
			$i = 1;
			while ($row = $this->db->fetch_assoc($result)) {
				$this->send("/m $nick Platz $i: ".$row['nick']." mit ".$row['points']." Punkte(n).");
				$i++;
			}
		}
	}
	
	protected function clean_bans() {
		$result = $this->db->query("SELECT * FROM `users` WHERE banned = 1 AND bantime > 0 ORDER BY nick ASC");
		while ($row = $this->db->fetch_array($result)) {
			if ($row['bantime'] < time()) {
				$this->db->query("UPDATE `users` SET banned = 0, bantime = 0 WHERE nick = '".$row['nick']."'");
				$this->send("/m ".$row['nick']." Du bist jetzt nicht mehr vom Spiel ausgesperrt.");
			}
		}
	}
    
}

?>