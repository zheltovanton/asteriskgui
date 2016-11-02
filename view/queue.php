<?php

require_once 'header.php';   

echo '
<script src="../public/queue.js"></script>

<header>
    <h1>Show queues</h1>
</header>

<div class="config_panel" id="config_panel">
    <label><input id="na" type="checkbox" checked />Not active</label>
    <label><input id="empty" type="checkbox">Empty</label>
</div>


<div id="content"></div>
                       ';

require 'footer.php';
?>