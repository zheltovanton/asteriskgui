<?php
   exec(trim("sudo /usr/sbin/asterisk -rx \"sip show peers\" -C /etc/asterisk/asterisk.conf").' 2>&1', $ret);
   for ($i = 1; $i <= count($ret); $i++) { 
   	echo $ret[$i].PHP_EOL;
   } 
?>