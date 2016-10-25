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
		<li><a href="rep.total.php" target="index">Total</a></li>
		</ul>
	   </li>
            <li>Settings 
		<ul>
		<li><a href="set.trunks.php" target="index">Trunks</a></li>
		<li><a href="set.peers.php" target="index">Peers</a></li>
		</ul>
	    </li>
            <li>Diagnostics 
		<ul>
		<li><a href="diag.total.php" target="index">Total</a></li>
		</ul>
	    </li>

        </ul>
    </div>
    <div class="index-frame">
        <iframe name="index" src="rep.total.php"></iframe>
    </div>
</body>
</html>';

