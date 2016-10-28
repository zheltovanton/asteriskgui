
$(function() {

    var json_data;
	
    $("#but_excel a").click(function() {
    	JSONToCSVConvertor(json_data, "Registry", true);
    });
 	DATA = null;	

        $("#content").jsGrid({
            height: "93%",
            width: "100%",
            filtering: true,
            inserting: false,
            editing: false,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 500,
            pageButtonCount: 5,
            deleteConfirm: "Do you really want to delete row?",
            controller: {
                loadData: function(filter) {
                    return $.ajax({
                        type: "GET",
                        url: "../db/diag/sip_channels",
                        data: filter,
			success:function(data) {
			    	json_data = data;  //store to global var for exporting

 				var counter = data.length;
				for (var i = 0; i < counter; i++) {
					BridgedChannel = data[i].BridgedChannel.split('-')[0];
					BridgedChannel = BridgedChannel.split('@')[0];
					data[i].BridgedChannel = BridgedChannel;
					
				}
				$("#total").text("Channels: " + counter);
      			},
    				complete: function() {
      				// Schedule the next request when the current one's complete
				    
    				    //setTimeout($("#content").jsGrid("loadData"), 5000);   	
    			}
                    });
                }
            },

            fields: [
                { name: "CallerIDname", title: "Name", type: "text", width: 100  },
                { name: "CallerIDnum", title: "Number", type: "text", width: 60  },
                { name: "Context", title: "Context", type: "text", width: 100  },
                { name: "Extension", title: "Extension", type: "text", width: 100  },
                { name: "Duration", title: "Duration", type: "text", width: 100  },
                { name: "ChannelStateDesc", title: "State", type: "text", width: 50  },
                { name: "BridgedChannel", title: "Bridged To", type: "text", width: 100  },
		{ name: "Application", title: "App", type: "text", width: 60  },
                { name: "ApplicationData", title: "App Date", type: "text", width: 70  }
                //{ name: "Channel", title: "Channel", type: "text", width: 100  }
            ],
	    onDataLoaded: function(data) {
	    },
	    onError: function() {
		console.log( "Ошибка: " + this.src );
	    }

        });

});