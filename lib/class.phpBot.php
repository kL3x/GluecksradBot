<?php

class phpBot {

        /**
         * Version & Useragent des Bots festlegen
         */
        protected  $version    = '1.1.0';

        /**
         * $this->mysql = Beinhaltet alle wichtigen Daten für die MySQL Datenbank...
         * MySQLi ist vielleicht schneller?
         */
        protected $mysql                = array('host' => 'localhost',
                                                'user' => 'admin',
                                                'pass' => 'blubbablubb',
                                                'database' => 'grx');

        /**
         * $this->bot: Beinhaltet alle wichtigen Daten
         * die zum Bot gehören
         */
        protected  $bot    = array('nick' => 'oRo',
                                   'pass' => '6Gz3ccB4hzRsN',
                                   'nh' => 'default',
                                   'channel' => 'Treffpunkt',
                                   'sid' => 0,
                                   'loggedin' => false,
                                   'logout' => false,
                                   'superuser' => false);
        /**
         * $this->server: Beinhaltet alle Daten rund um
         * die Community
         */
        protected   $server     = array('host' => 'squizzl.de',
                                       'login_port' => 1977,
                                       'login_path' => 'POST /cassiopeia/NetCommunity?',
                                       'login_string' => 'username=$this->bot[nick]&password=$this->bot[pass]&group=squizzl&templateset=default',
                                       'chat_port' => 1977,
                                       'chat_path' => 'GET /NEW?',
                                       'chat_string' => 'nick=%nick%&sessionid=%sid%&nh=%nh%',
                                       'send_port' => 1977,
                                       'send_path' => 'GET /SEND?',
                                       'send_string' => 'message=%input%',
                                       'mail_port' => 80,
                                   'mail_path' => 'POST /cassiopeia/NetCommunityMailcenter?',
                                   'mail_string' => 'service=send&nick=%nick%&sessionid=%sid%&newFolder=outbox&recipient=%host%&subject=%subject%&content=%input%',
                                   'gbook_port' => 80,
                                   'gbook_path' => 'POST /cassiopeia/NetCommunityMemberguestbook?',
                                   'gbook_string' => 'service=addEntry&nick=%nick%&sessionid=%sid%&host=%host%&entry=%input%');
    public      $timers     = array();

    protected   $month      = array(1 => 'Januar', 2 => 'Februar', 3 => 'M&auml;rz', 4 => 'April', 5 => 'Mai',
                                    6 => 'Juni', 7 => 'Juli', 8 => 'August', 9 => 'September', 10 => 'Oktober',
                                    11 => 'November', 12 => 'Dezember');
    protected   $days       = array('Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag');

        protected        $users_in_room = 0;


    protected  $db, $timer, $user, $grX;
    protected  $logfile;

        public function __construct() {
        $this->db = new db;
        $this->db->connect($this->mysql['host'], $this->mysql['user'], $this->mysql['pass'], $this->mysql['database'], 0);
        $this->timer = new timer();

        $this->logfile = BASE_DIR . 'logs/'.date('Y').'/'.date('m').'-'.$this->month[date('n')].'/'.iif(date('j') < 10, '0'.date('j'), date('j')).'-'.$this->days[date('w')].'.html';
        if (!file_exists(BASE_DIR . 'logs/'.date('Y').'/'.date('m').'-'.$this->month[date('n')].'/')) {
            rmkdir(BASE_DIR . 'logs/'.date('Y').'/'.date('m').'-'.$this->month[date('n')].'/');
            $this->log("[".date("H:i:s")."] Es wurde ein neues Verzeichnis angelegt:<br />\n" . BASE_DIR . "logs/" . date('Y') . "/" . date('m') . "-" . $this->month[date('n')] . "/<br />\n");
        }

        // Den Timer für den Login starten
        $this->timer->start('login');
        }

        public function check_login() {
        if ($this->bot['loggedin'] == false) {
            $fp = fsockopen($this->server['host'], $this->server['login_port'], $errno, $errstr, 10);
            if (!$fp) {
                fclose($fp);
                $this->log("[".date("H:i:s")."] Es konnte keine Verbindung zum Server hergestellt werden.<br />\nFehler [$errno]: $errstr<br />\n\n");
            } else {
                fclose($fp);
                $this->log("[".date("H:i:s")."] Verbindung zu " . $this->server['host'] . " erfolgreich hergestellt...<br />\n");
                $this->login();
                      }
        }
        }

    protected function login() {
        $fp = fsockopen($this->server['host'], $this->server['login_port'], $errno, $errstr);
                if (!$fp) {
                        $this->log("[".date("H:i:s")."] Es konnte keine Verbindung zum Server hergestellt werden.<br />\nFehler [$errno]: $errstr<br />\n\n");
                } else {
                        $request = "GET / HTTP/1.1\r\n";
                        $request.= "Host: ".$this->server['host']."\r\n";
                        $request.= "User-Agent: Mozilla/1.3.3.7 (ChaosBot - Squizzl Mod)\r\n";
                        $request.= "Connection: Keep-Alive\r\n\r\n";
                        fwrite($fp, $request);
                        $content = '';
                        while(!feof($fp)) {
                                $content.= fgets($fp, 512);
                                $this->bot['sid'] = substr(strstr($content, 'Set-Cookie: FreeCSSession='),26,32);
                        }
                        fclose($fp);

                            $fp2 = fsockopen ($this->server['host'], $this->server['login_port'], $errno, $errstr);
                            fputs($fp2, "POST /LOGIN HTTP/1.1\r\n");
                            fputs($fp2, "Host: ".$this->server['host']."\r\n");
                            fputs($fp2, "User-Agent: Mozilla/1.3.3.7 (ChaosBot - Squizzl Mod)\r\n");
                            fputs($fp2, "Referer: http://squizzl.de\r\n");
                            fputs($fp2, "Cookie: FreeCSSession=".$this->bot['sid']."\r\n");
                            fputs($fp2, "Content-Length: ".strlen("username=".$this->bot[nick]."&password=".$this->bot[pass]."&group=NoMoreDrama&templateset=default")."\r\n");
                            fputs($fp2, "Connection: Keep-Alive\r\n\r\n");
                            fputs($fp2, "username=".$this->bot[nick]."&password=".$this->bot[pass]."&group=NoMoreDrama&templateset=default");
                            fclose($fp2);

                            $this->send("/j ".$this->bot['channel']);

                        if (!$this->bot['sid'] || $this->bot['sid'] == "lurker") {
                                $this->log("[".date("H:i:s")."] Falsches Passwort. Bitte Einstellungen überprüfen.<br />\n");
                                exit;
                        } else {
                            $this->log("[".date("H:i:s")."] Erfolgreich in der Community angemeldet. CookieID: ".$this->bot['sid']."<br />\n");
                                $this->readChat();
                        }
                }
    }

    private function readChat() {
        $fp = fsockopen($this->server['host'], $this->server['chat_port'], $errno, $errstr);
                if (!$fp) {
                        $this->log("[".date("H:i:s")."] Es konnte keine Verbindung zum Server hergestellt werden.<br />\nFehler [$errno]: $errstr<br />\n\n");
                } else {
                        $this->log("[".date("H:i:s")."] Verbindung zum Chat hergestellt...");
                        // Für den Timeout-Killer extra nochmals die Zeit aktualisieren
                        $this->timers['tok'] = time(); // timeout killer
                        $this->timers['grx_idle'] = time()+4999494994949494;

                        $request = "GET /MESSAGES  HTTP/1.1\r\n";
                        $request.= "Host: ".$this->server['host']."\r\n";
                        $request.= "User-Agent: Mozilla/1.3.3.7 (ChaosBot - Squizzl Mod)\r\n";
                        $request.= "Referer: $ref/LOGIN\r\n";
                        $request.= "Cookie: FreeCSSession=".$this->bot['sid']."\r\n";
                        $request.= "Conntection: Keep-Alive\r\n\r\n";

                        fputs($fp, $request);
                        while (!feof($fp)) {
                                $line_raw = fgets($fp);
                                $quit_status = socket_get_status($fp);
                                $line = strip_tags($line_raw);

                                if (preg_match("/Herzlich Willkommen im Squizzl-Chat!/isU", $line_raw) && $this->bot['loggedin'] == false) {
                                    if (!isset($this->bot['logintime']) || $this->bot['logintime'] < 0) {
                        $this->bot['logintime'] = time();
                    }
                    $this->timer->stop('login');
                                $this->log("Benštigte Zeit fŸr den Login: ".$this->timer->gettime('login')."</b></font><br />\n");
                                        $this->wait();
                                        $this->send("/j ".$this->bot['channel']);
                                        $this->wait();
                                $this->clean_up_log($this->logfile);
                                $this->bot['loggedin'] = true;

                                $this->user = new user($this->bot, $this->server, $this->db, $this->timer);
                                $this->grX = new grX($this->bot, $this->server, $this->db, $this->user, $this->timer);

                                } elseif ($this->bot['loggedin'] == true) {
                    include(BASE_DIR . "lib/timers.php");
                                        include(BASE_DIR . "lib/chatparser.php");
                                }

                                if (strtolower($nick) != "oRo") {
                                        file_put_contents($this->logfile, $line_raw, FILE_APPEND);
                                }

                                unset($line, $line_raw);
                        }
                        fclose($fp);
                        if ($this->bot['loggedin'] == false) {
                $this->bot['loggedin'] = false;
                                   $this->log("[".date("H:i:s")."] Verbindung zum Chat verloren...");
                                if (date('G') < 5 && date('G') > 2) sleep(10);
                                   $this->check_login();
                           }
                }
    }

    protected function send($string) {
        // Einige Sachen im $string ersetzen
        $string = str_replace("<news>", "blubb", $string);
                $string = str_replace("<", "&#60;", $string);
                $string = str_replace("(", "&#40;", $string);
                $string = str_replace(")", "&#41;", $string);

        $fp = fsockopen($this->server['host'], $this->server['send_port'], $errno, $errstr, 10);
		if ($fp) {
			$request = $this->server['send_path'].$this->replacevars($this->server['send_string'], $string)."  HTTP/1.1\r\n";
                        $request.= "Host: ".$this->server['host']."\r\n";
                        $request.= "User-Agent: Mozilla/1.3.3.7 (ChaosBot - Squizzl Mod)\r\n";
                        $request.= "Referer: $ref/LOGIN\r\n";
                        $request.= "Cookie: FreeCSSession=".$this->bot['sid']."\r\n";
                        $request.= "Conntection: Keep-Alive\r\n\r\n";
                        fputs($fp, $request);
                        fclose($fp);
                        #if ($this->bot['su'] == false) {
                        #        $this->wait();
                        #}
                } else {
            $this->log("[".date("H:i:s")."] Fehler: Die Nachricht '$string' konnte nicht gesendet werden.<br />\nFehler [$errno]: $errstr<br />\n\n");
                }
    }

    protected function sendMail($nick, $subject, $message) {
                $fp = fsockopen($this->server['host'], $this->server['mail_port'], $errno, $errstr);
                if ($fp) {
                        $requestHeader = $this->server['mail_path']."  HTTP/1.1\r\n";
                        $requestHeader.= "Host: ".$this->server['host']."\r\n";
                        $requestHeader.= "User-Agent: Mozilla/1.3.3.7 (ChaosBot - Squizzl Mod)\r\n";
                        $requestHeader.= "Content-Type: application/x-www-form-urlencoded\r\n";
                        $requestHeader.= "Content-Length: ".strlen($this->replacevars($this->server['mail_string'], $message, $nick, $subject))."\r\n";
                        $requestHeader.= "Connection: close\r\n\r\n";
                        $requestHeader.= $this->replacevars($this->server['mail_string'], $message, $nick, $subject);
                        fwrite($fp, $requestHeader);
                        fclose($fp);
                        $this->log("[".date("H:i:s")."] Community-Mail wurde an $nick verschickt.");
                        return true;
                } else {
                        $this->log("[".date("H:i:s")."] Fehler: Die Community-Mail konnte nicht an $nick versendet werden.");
                        return false;
                }
        }

    private function replacevars($url, $message = '', $host = '', $subject = '') {
                $url = str_replace("%nick%", $this->bot['nick'], $url);
                $url = str_replace("%pass%", $this->bot['pass'], $url);
                $url = str_replace("%sid%", $this->bot['sid'], $url);
                $url = str_replace("%nh%", $this->bot['nh'], $url);
                $url = str_replace("%host%", $host, $url);
                $url = str_replace("%subject%", urlencode($subject), $url);
                $url = str_replace("%input%", urlencode($message), $url);
                return $url;
        }

        protected function wait() { usleep(499000); }

        private function clean_up_log($logfile) {
        $string = file_get_contents($logfile);
        $string = preg_replace("~HTTP/1.0 200 OK~isU", "", $string);
        $string = preg_replace("~Content-Type: text/html~isU", "", $string);
        $string = preg_replace("~Content-Type: multipart/mixed;boundary=ThisRandomString~isU", "", $string);
        $string = preg_replace("~--ThisRandomString~isU", "", $string);
        $string = preg_replace("~<BODY(.*)>~isU", '<BODY onFocus="scrolling = false" onBlur="scrolling = true" onMouseOver="scrolling = false" onMouseOut="scrolling = true" onLoad = "rel();">', $string);
        file_put_contents($logfile, $string);
    }

    private function log($string) {
        file_put_contents($this->logfile, "<font face=Arial size=2 color=#990000><b>".$string."</b></font><br />\n", FILE_APPEND);
        $string = strip_tags($string, "<br />\n");
        print $string;
    }

    private function getlogSize() {
                $filelines = "";
                $filesize = "";
                $return = array();
                $data = read_recursiv(BASE_DIR ."/logs");
                foreach ($data as $value) {
                        $file = file($value);
                        $filelines += count($file);
                        $filesize += filesize($value);
                }
                $return['filecount'] = count($data);
                $return['filesize'] = formatsize($filesize);
                $return['filelines'] = formatnumber($filelines);
                return $return;
        }

        function getfileSize() {
                $filelines = "";
                $filesize = "";
                $data = read_recursiv(BASE_DIR);
                foreach ($data as $value) {
                        $file = file($value);
                        $filelines += count($file);
                        $filesize += filesize($value);
                }
                $return['filecount'] = count($data);
                $return['filesize'] = formatsize($filesize);
                $return['filelines'] = formatnumber($filelines);
                return $return;
        }

    public function get_status() {
                $return = array();

                $return['logintime'] = $this->bot['logintime'];
                $return['logfile'] = "unkown";
                $return['log'] = $this->getlogSize();
                $return['bot'] = $this->getfileSize();

                return $return;
        }

}

?>