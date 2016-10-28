
$(function() {

    var json_data;
	
    $("#but_excel a").click(function() {
    	JSONToCSVConvertor(json_data, "CDR Report", true);
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
                        url: "../db/diag/sip_peers",
                        data: filter,
			success:function(data) {
			    	json_data = data;  //store to global var for exporting

 				var counter = data.length;

				$("#total").text("Peers: " + counter);
      			}
                    });
                }
            },
            fields: [
                { name: "name", title: "Name", type: "text", width: 100  },
                { name: "ip", title: "IP", type: "text", width: 100  },
                { name: "port", title: "Port", type: "text", width: 100  },
                { name: "state", title: "State", type: "text", width: 100  },
                { name: "desc", title: "Description", type: "text", width: 100  }

            ],
	    onDataLoaded: function(data) {
	    },
	    onError: function() {
		console.log( "Ошибка: " + this.src );
	    }

        });

});