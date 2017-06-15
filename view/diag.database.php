<?php

require_once 'header.php';   

echo '
<script src="../public/diag_database.js"></script>

<header>
    <h1>Show database</h1>
</header>

<div class="config_panel" id="config_panel">
    <label><input id="dnd" type="checkbox">DND</label>
    <label><input id="sip" type="checkbox">SIP</label>
</div>


<div id="content"></div>
                       ';

require 'footer.php';
?>