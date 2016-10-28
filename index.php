<?php

$page = '';

if ($_GET["p"]=='') {
	$page = 'rep.cdr';
} else {
  	$page = $_GET["p"];
}
		
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
		<li><a href="index.php?p=rep.cdr" >Call records</a></li>
		</ul>
	   </li>
            <li>Settings 
		<ul>
		<li><a href="index.php?p=sip.users" >Sip users</a></li>
		</ul>
	    </li>
            <li>Diagnostics 
		<ul>
		<li><a href="index.php?p=diag.total" >Total</a></li>
		<li><a href="index.php?p=sip.registry" >Sip Registry</a></li>
		<li><a href="index.php?p=sip.peers" >Sip Peers</a></li>
		<li><a href="index.php?p=sip.channels" >Channels</a></li>
		<li><a href="index.php?p=sip.channelstats" >Channel Stats</a></li>
		</ul>
	    </li>

        </ul>
    </div>
    <div class="index-frame">
        <iframe name="index" src="view/'.$page.'.php"></iframe>
    </div>
</body>
</html>';

