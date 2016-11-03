
$(function() {
    RefreshData();
    
    json_data = '';  //store to global var for exporting
    
    function CheckItems() {
	$("#content").empty();
	RefreshData ();
	
    }
 
    for (var i = 1; i < 100000; i++)
    	setTimeout(function () { RefreshData();  }, 10000 * i);


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
			var chk_na = $("#na").prop("checked");
			var chk_empty = $("#empty").prop("checked");
			// add queue panels from json response
			for (var i = 0; i < counter; i++) {
				var q = data[i].queue;
				var m = data[i].members;
				var c = data[i].callers;

				if  ((!chk_empty)&&(m.length == 0)) continue; // need show empty queue or not

				if ($('[id^=b'+q+']').length == 0) { // check this block exist or not
					sw = !sw;
					sw ? $("#colleft").append('<div class="panel panel-default" id="b'+q+'"></div>') :
					     $("#colright").append('<div class="panel panel-default" id="b'+q+'"></div>');
                	    		$('[id^=b'+q+']').append('<div class="panel-heading">Queue: ' + q +'</div>');
		            		$('[id^=b'+q+']').append('<div class="queue_members" id="div'+q+'"></ul>');
                    			$('[id^=div'+q+']').append('<ul class="nav nav-pills" id="ul'+q+'"></ul>');
				}

		 		//add callers 'from' 'to' 'queue' 'time'
		 		if ((c.length>0)&& ($('[id^=b'+q+']').length == 0))
                    			$('[id^=div'+q+']').append('<ul class="nav nav-pills" id="ul_c'+q+'"></ul>');
		 		for (var n = 0; n < c.length; n++) { 
					if ($('[id^=c_li'+q+c[n].to+']').length == 0) {
						$('[id^=ul_ñ'+q+']').append('<li id="ñ_li'+q+c[n].to+
							'"><span id="c_s'+c[n].from + ' -> ' + c[n].to + ' : ' + c[n].time+'" class="label label-default">'+
							li_text+'</span></li>');
					} else {  // if caller already exist - change text 
						$('[id^=c_s' + q + c[n].number+']').text(q+c[n].number + ' ' + c[n].time);
					}
				}


				//add queue members
				if ($('[id^=ul'+q+']').length == 0)
                    			$('[id^=div'+q+']').append('<ul class="nav nav-pills" id="ul'+q+'"></ul>');
				for (var n = 0; n < m.length; n++) { 
					//check if member label exist, skip it 
					if ($('[id^=li'+q+m[n].number+']').length == 0) {
						var li_text='';

						if (m[n].member == m[n].number) { li_text = m[n].member; }
					 		else { li_text = m[n].member+' / '+m[n].number; }

						if ((chk_na) && (m[n].state=='na')) {
							$('[id^=ul'+q+']').append('<li id="li'+q+m[n].number+
								'"><span id="s'+q+m[n].number+'" class="label label-default">'+
								li_text+'</span></li>');
						}
						if (m[n].state=='aviable') {
							$('[id^=ul'+q+']').append('<li id="li'+q+m[n].number+
								'"><span id="s'+q+m[n].number+'" class="label label-success">'+
								li_text+'</span></li>');
						}
						if (m[n].state=='busy') {
							$('[id^=ul'+q+']').append('<li id="li'+q+m[n].number+
								'"><span id="s'+q+m[n].number+'" class="label label-danger">'+
								li_text+'</span></li>');
						}
					}
					// change class if need
					if ($('[id^=s'+q+m[n].number+']').length > 0) {
						if (m[n].state=='na') {
							$('[id^=s'+q+m[n].number+']').addClass("label label-default");
						}
						if (m[n].state=='aviable') {
							$('[id^=s'+q+m[n].number+']').addClass("label label label-success");
						}
						if (m[n].state=='busy') {
							$('[id^=s'+q+m[n].number+']').addClass("label label-danger");
						}
					}
                    			//$('[id^=ul'+q+']').append('<li ><a href="#">Messages</a></li>');
				}
			}
                    	}
                    });
   }
   
    $("#config_panel input[type=checkbox]").on("click", function() {
        var $cb = $(this);
        CheckItems();
    }); 
    
  
  
});