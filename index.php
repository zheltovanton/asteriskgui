<?php

		
echo '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8" />
    <title>Asterisk Report</title>
    <link rel="stylesheet" type="text/css" href="public/css/index.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,600,400" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="navigation">
        <h1>Asterisk</h1>
        <ul>
            <li>Report 
		<ul>
		<li><a href="view/rep.cdr.php" target="index">Call records</a></li>
		</ul>
	   </li>
            <li>Settings 
		<ul>
		<li><a href="view/sip.users.php" target="index">Sip users</a></li>
		</ul>
	    </li>
            <li>Diagnostics 
		<ul>
		<li><a href="view/diag.total.php" target="index">Total</a></li>
		<li><a href="view/sip.registry.php" target="index">Sip Registry</a></li>
		<li><a href="view/sip.peers.php" target="index">Sip Peers</a></li>
		<li><a href="view/sip.channelstats.php" target="index">Channel Stats</a></li>
		</ul>
	    </li>

        </ul>
    </div>
    <div class="index-frame">
        <iframe name="index" src="view/rep.cdr.php"></iframe>
    </div>
</body>
</html>';

