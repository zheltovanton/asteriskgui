
$(function() {
 

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
		for (var i = 0; i < counter; i++) {
			var q = data[i].queue;
			var m = data[i].members;
			sw = !sw;
			sw ? $("#colleft").append('<div class="panel panel-default" id="b'+q+'"></div>') :
			     $("#colright").append('<div class="panel panel-default" id="b'+q+'"></div>');
  			$('[id^=b'+q+']').append('<div class="panel-heading">' + q + ' ' + m.length+'</div>');
  			$('[id^=b'+q+']').append('<ul class="nav nav-pills" id="ul'+q+'"></ul>');
			for (var n = 0; n < m.length; n++) { 
  			  	$('[id^=ul'+q+']').append('<li class="active"><a href="#">'+m[n].member+'</a></li>');
  			//$('[id^=ul'+q+']').append('<li ><a href="#">Messages</a></li>');
			}
		}
  	}
  });
  
  
});