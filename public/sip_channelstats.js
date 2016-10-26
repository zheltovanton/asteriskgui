
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
                        url: "../db/diag/sip_channelstat",
                        data: filter,
			success:function(data) {
			    	json_data = data;  //store to global var for exporting

 				var counter = data.length;

				$("#total").text("Channels: " + counter);
      			}
                    });
                }
            },
            fields: [
                { name: "peer", title: "Peer", type: "text", width: 100  },
                { name: "callid", title: "CallerID", type: "text", width: 100  },
                { name: "duration", title: "Duration", type: "text", width: 100  },
                { name: "receive", title: "Receive (packet)", type: "text", width: 100  },
                { name: "lostp", title: "Lost", type: "text", width: 100  },
                { name: "procentp", title: "%", type: "text", width: 60  },
                { name: "jitterp", title: "Jitter", type: "text", width: 70  },
                { name: "send", title: "Send (packet)", type: "text", width: 100  },
                { name: "losts", title: "Lost", type: "text", width: 100  },
                { name: "procents", title: "%", type: "text", width: 60  },
                { name: "jitters", title: "Jitter", type: "text", width: 70  }
            ],
	    onDataLoaded: function(data) {
	    },
	    onError: function() {
		console.log( "Ошибка: " + this.src );
	    }

        });

});