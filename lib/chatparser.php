<?php

if (preg_match("/<!--nick:([\w]+):msg:(.*?)--><!--/isU", $line_raw, $r)) {
    $nick = $r[1];
    $msg = $r[2];
    $msg_type = "msg";
    unset($r);


        if ($nick != $this->bot['nick']) {
                include(BASE_DIR . "lib/tasks/cmd.php");
        }
} elseif (preg_match("/<!--nick:([\w]+):whisper:(.*?)--><!--/isU", $line_raw, $r)) {
        $nick = $r[1];
        $msg = $r[2];
        $msg_type = "whisper";
        unset($r);

        if ($nick != $this->bot['nick']) {
                $nick_level = $this->user->get_level($nick);
                include(BASE_DIR . "lib/tasks/cmd.php");
        }
} elseif (preg_match("/<font color=#([0-9a-f]+)>([\w]+) schreit: (.*?)<\/font>/isU", $line_raw, $r)) {
        $col = $r[1];
        $nick = $r[2];
        $msg = $r[3];
        $msg_type = "shout";
        unset($r);

        if ($nick != $this->bot['nick']) {
                include(BASE_DIR . "lib/tasks/msg.php");
        }
} elseif (preg_match("/<font color=#([0-9a-f]+)>([\w]+)</font></a> (.*?)<\/font>/isU", $line_raw, $r)) {
        $col = $r[1];
        $nick = $r[2];
        $msg = $r[3];
        $msg_type = "action";
        unset($r);

        if ($nick != $this->bot['nick']) {
                include(BASE_DIR . "lib/tasks/msg.php");
        }
} elseif (preg_match("/\<\!--chat:new:".$this->bot['nick']."--\>(?:.*?)<!-- kicked by ([\w]+) --\>/isU", $line_raw, $r)) {
        $kick_nick = $r[1];
        $leave_type = "kicked";
        unset($r);

    $this->wait();
        $this->send("/j ".$this->bot['channel']);
} elseif (preg_match("/<font color=#([0-9a-f]+)>([\w]+)<\/font> hat von <font color=#([0-9a-f]+)>([\w]+)<\/font> Superuser-Rechte verliehen bekommen/isU", $line_raw, $r)) {
        $nick_su = $r[2];
        $nick = $r[4];
        unset($r);

        if ($nick_su == $this->bot['nick']) $this->bot['su'] = true;
        include(BASE_DIR . "lib/tasks/su.php");
} elseif (preg_match("/<font color=#([0-9a-f]+)>([\w]+)<\/font> verleiht <font color=#([0-9a-f]+)>([\w]+)<\/font> Superuser-Rechte/isU", $line_raw, $r)) {
        $nick_su = $r[4];
        $nick = $r[2];
        unset($r);

        if ($nick_su == $this->bot['nick']) $this->bot['su'] = true;
        include(BASE_DIR . "lib/tasks/su.php");

} elseif (preg_match("/<i><b>([\w]+) werden <!--von ([\w]+) -->Superuser-Rechte verliehen.</i></b><br>/isU", $line_raw, $r)) {
        $nick_su = $r[2];
        $nick = $r[3];
            unset($r);

        if ($nick_su == $this->bot['nick']) $this->bot['su'] = true;
        include(BASE_DIR . "lib/tasks/su.php");

} elseif (preg_match("/<i><b>([\w]+) hat <!-- von ([\w]+) --> Superuser-Rechte verliehen bekommen/isU", $line_raw, $r)) {
        $nick_su = $r[2];
        $nick = $r[3];
        unset($r);

        if ($nick_su == $this->bot['nick']) $this->bot['su'] = true;
        include(BASE_DIR . "lib/tasks/su.php");
}

if (preg_match("/<i><b>Raum Glücksrad:<\/b><\/i><br>\* <font color=#([0-9a-f]+)>([\w]+)<\/font> chattet seit (.*?) \((.*?)\)(.*?)?/isU", $line_raw, $matches)) {
        $str = $matches[0];

        $this->users_in_room = 0;
        $nickarray = preg_split('/<BR>/', $str, -1, PREG_SPLIT_NO_EMPTY);
        $nickcount = count($nickarray);
        for ($i=0; $i < $nickcount; $i++) {
                if (preg_match("/\* <font color=#([0-9a-f]+)>([\w]+)<\/font> chattet seit (.*?) \((.*?)\)(.*?)?/isU", $nickarray[$i], $match)) {
                        $nickname = strtolower($match[2]);
                        if ($nickname == "oRo") { continue; }
                       elseif ($nickname == "oRo") { continue; }
                        elseif ($this->user->get_level($nickname) >= 3) { continue; }
                        else { $this->users_in_room = $this->users_in_room + 1; }
                }
        }

        if ($this->users_in_room >= 5) {
                $this->grX->max_wait = 30;
        } else {
           $this->grX->max_wait = 10;
        }
}

if (preg_match("/<!--chat:new:([\w]+)-->/isU", $line_raw, $r)) {
        $nick = $r[1];
        unset($r);

        if ($nick != $this->bot['nick']) {
                $nick_level = $this->user->get_level($nick);
                include(BASE_DIR . "lib/tasks/join.php");
        }
} elseif (preg_match("/<!--chat:quit:([\w]+)-->/isU", $line_raw, $r) || preg_match("/<!--room:quit:([\w]+)-->/isU", $line_raw, $r)) {
        $nick = $r[1];
        unset($r);

        if ($nick != $this->bot['nick']) {
                $nick_level = $this->user->get_level($nick);
                include(BASE_DIR . "lib/tasks/leave.php");
        } elseif ($nick == $this->bot['nick']) {
                $this->send("/j Treffpunk");
                $this->wait();
                $this->wait();
                $this->wait();
                $this->grX->start();
        }

}




?>