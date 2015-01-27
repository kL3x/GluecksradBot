<?php

class db {

	/******************************************

	Verbindungsdaten einbinden, z.B. in einer config.php

	define('DB_USER', 'root');
	define('DB_KENNWORT', '');
	define('DB_SERVER', 'localhost');
	define('DB_NAME', 'armin');
	// Bitte geben Sie an ob das Script bei einen DB- und Queryfehler abbrechen soll
	// Bei 0 erfolgt kein Abbruch, bei 1 erfolgt ein Abbruch
	define('DB_ERROR', '1');

	Verbindung zur DB:
	$db->db_connect(DB_BENUTZER, DB_KENNWORT, DB_SERVER, DB_NAME);

	*********************************************/
	var $query = 0;
	var $query_result = 0;
	var $result = 0;
	var $connect = array();
	var $db_time = 0;

	var $servern;
	var $usern; 
	var $passwordn;
	var $dbn; 

	// Zugriffszeit auf DB erfassen
	function db_time($starttime,$endtime) {

		$this->db_time = $endtime - $starttime + $this->db_time;

	}

	// Rückgabe Anzahl der DB-Zugriffe
	function getQuerys() {

		return $this->query;

	}

	// Rückgabe Zugriffszeit DB
	function getQueryTime() {

		return $this->db_time;

	}

	// Query ausführen
	function query($sql = 0, $limit = 0, $offset = 0, $connection = 0) {

		$this->query++;
		$starttime = microtime();
		if ($limit != 0) $sql .= " LIMIT $offset, $limit";
		$this->query_result = mysql_query($sql, $this->connect[$connection]);
		$endtime = microtime();
		$this->db_time($starttime, $endtime);
		$this->query_result = empty($this->query_result) ? $this->error('Queryfehler', $sql) : $this->query_result;

		return $this->query_result;

	}

	function unbuffered_query($sql = 0, $connection = 0) {

		$this->query++;
		$starttime = microtime();
		$this->query_result = mysql_unbuffered_query($sql, $this->connect[$connection]);
		$endtime = microtime();
		$this->db_time($starttime,$endtime);
		$this->query_result = empty($this->query_result) ? $this->error_db('Queryfehler', $sql) : $this->query_result;

		return $this->query_result;
	}
	
	function query_first($sql, $limit = 0, $offset = 0) {
  		$this->query($sql, $limit, $offset);
  		$returnarray = $this->fetch_array($this->query_result, MYSQL_ASSOC);
  		$this->free_result($this->query_result);
  		return $returnarray;
 	}

	// Zeiger auf Datensatz setztem; count der Datensatz
	// fetch = 1; Datensatz wird als Array zurückgegeben.
	function data_seek($result = 0, $count = 0, $fetch = 0) {

		$this->result = empty($result) ? $this->query_result : $result;
		empty($this->result) ? false : mysql_data_seek($this->result, $count);

		if (!empty($fetch)) {

			switch ($fetch) {

				case 'array':
					$this->result = $this->fetch_array($this->result);
					break;

			}

		}

		return $this->result;
	}

	// Rückgabe als assoziativen Array; $row['spaltenname']
	function fetch_assoc($result = 0) {

		$this->result = empty($result) ? $this->query_result : $result;
		$this->result = empty($this->result) ? false : mysql_fetch_assoc($this->result);

		return $this->result;

	}

	// Rückgabe in Form eines indizierten Arrays; $row[0] etc.
	function fetch_row($result = 0) {

		$this->result = empty($result) ? $this->query_result : $result;
		$this->result = empty($this->result) ? false : mysql_fetch_row($this->result);

		return $this->result;

	}

	// Rückgabe als assoziativen Array; $row['spaltenname']
	function fetch_array($result = 0,  $type = MYSQL_BOTH) {

		$this->result = empty($result) ? $this->query_result : $result;
		$this->result = empty($this->result) ? false : mysql_fetch_array($this->result, $type);

		return $this->result;

	}

	// Rückgabe als des Ergebnisses als Objekt; $row->spaltenname
	function fetch_object($result = 0) {

		$this->result = empty($result) ? $this->query_result : $result;
		$this->result = empty($this->result) ? false : mysql_fetch_object($this->result);

		return $this->result;

	}

	// Anzahl der Datensätze eines Ergebnisses
	function num_rows($result = 0) {

		$this->result = empty($result) ? $this->query_result : $result;
		$this->result = empty($this->result) ? false : mysql_num_rows($this->result);

		return $this->result;

	}

	// Liefert die Anzahl betroffener Datensätze einer vorhergehenden MySQL Operation (INSERT, UPDATE, DELETE)
	function affected_rows($connection = 0) {

		$this->result = mysql_affected_rows($this->connect[$connection]);

		return $this->result;

	}

	// Rückgabe letzer Wert von AUTO_INCREMENT-Feld von letzter INSERT
	function insert_id($result = 0) {

		$this->result = empty($result) ? $this->query_result : $result;
		$this->result = empty($this->result) ? false : mysql_insert_id();

		return $this->result;

	}

	// Ausführen einen Query und Rückgabe der Datensätze eines Ergebnisses
	function query_num_rows($sql, $connection = 0) {

		$this->query($sql, $connection);

		return $this->num_rows();

	}
	
	function free_result($query_id=-1) {
  		if ($query_id!=-1) $this->query_id=$query_id;
  		return @mysql_free_result($this->query_id);
 	}

	// Öffnet eine Verbindung zum Datenbank-Server
	function connect($server, $user, $password, $db, $connection = 0){
		$this->servern = $server;
		$this->usern = $user;
		$this->passwordn = $password;
		$this->dbn = $db;
		$this->connect[$connection] = @mysql_pconnect($server, $user, $password);
		
		if (!$this->connect[$connection]) {

			$this->error('Verbindungsfehler ');
			$this->result = 'false';

		} elseif(!mysql_select_db($db, $this->connect[$connection])) {

			$this->error('Datenbankfehler');
			$this->result = 'false';
			$this->reconnect();

		} else {
			$this->result = 'true';
		}

		return $this->result;
	}

	// Fehlerausgabe
	function error($text, $sql= '') {

		$no = mysql_errno();
  		$msg = mysql_error();

		echo '['.$text.'] ( '. $no .' : '. $msg .' )<BR>Querybefehl: '. $sql. '';
		$this->result = (MYSQL_DBERROR == 1) ? exit() : false;
		$this->reconnect();

		return $this->result;

	}
	
	function reconnect()
	{
		$this->connect($this->servern, $this->usern, $this->passwordn, $this->dbn);
	}

	// Beendet eine Verbindung zum Datenbank-Server
	function close($connection = 0) {

		if (!empty($connection)) {

			foreach ($this->connect AS $key => $value) {

				$db_close = mysql_close();

			}

		} else {
			$db_close = mysql_close();
		}

		return $db_close;

	}

}


?>