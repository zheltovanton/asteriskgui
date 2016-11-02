
$(function() {
    RefreshData();

    setTimeout(RefreshData(), 5000);

    function RefreshData (){
            $.ajax({
                type: "GET",
                url: "../db/queue/status",
                //data: filter,
                success:function(data) {
		json_data = data;  //store to global var for exporting
      	   	var counter = data.length;
		//$("#content").text("Queues: " + counter);
		$("#content").append('<div class="row" id="row"></div>');
		$("#row").append('<div class="col-sm-6" id="colleft"></div>');
		$("#row").append('<div class="col-sm-6" id="colright"></div>');
		var sw = false;
		var na = true; //$("#na").prop("checked");
		// add queue panels from json response
		for (var i = 0; i < counter; i++) {
			var q = data[i].queue;
			var m = data[i].members;
			if  (m.length == 0) continue;
			if ($('[id^=b'+q+']').length == 0) {
				sw = !sw;
				sw ? $("#colleft").append('<div class="panel panel-default" id="b'+q+'"></div>') :
				     $("#colright").append('<div class="panel panel-default" id="b'+q+'"></div>');
        	    		$('[id^=b'+q+']').append('<div class="panel-heading">' + q + ' ' + m.length+'</div>');
	            		$('[id^=b'+q+']').append('<div class="queue_members" id="div'+q+'"></ul>');
            			$('[id^=div'+q+']').append('<ul class="nav nav-pills" id="ul'+q+'"></ul>');
			}
			//add queue members
			for (var n = 0; n < m.length; n++) { 
				//check if member label exist, skip it 
				if ($('[id^=li'+q+m[n].member+']').length == 0) {
					if ((m[n].state=='na')) {
						$('[id^=ul'+q+']').append('<li id="li'+q+m[n].member+'"><span id="s'+q+m[n].member+'" class="label label-default">'+m[n].member+'</span></li>');
					}
					if (m[n].state=='aviable') {
						$('[id^=ul'+q+']').append('<li id="li'+q+m[n].member+'"><span id="s'+q+m[n].member+'" class="label label-success">'+m[n].member+'</span></li>');
					}
					if (m[n].state=='busy') {
						$('[id^=ul'+q+']').append('<li id="li'+q+m[n].member+'"><span id="s'+q+m[n].member+'" class="label label-danger">'+m[n].member+'</span></li>');
					}
				}
				// change class if need
				if ((m[n].state=='na')) {
					$('[id^=s'+q+m[n].member+']').addClass("label label-default");
				}
				if (m[n].state=='aviable') {
					$('[id^=s'+q+m[n].member+']').addClass("label label label-success");
				}
				if (m[n].state=='busy') {
					$('[id^=s'+q+m[n].member+']').addClass("label label-danger");
				}
					
            			//$('[id^=ul'+q+']').append('<li ><a href="#">Messages</a></li>');
			}
		}
            	}
            });
   }

    $("#config_panel input[type=checkbox]").on("click", function() {
        var $cb = $(this);
        RefreshData();
    }); 

  
  
});