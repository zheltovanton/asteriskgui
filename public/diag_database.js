
$(function() {

    var json_data;
	
    $("#but_excel a").click(function() {
    	JSONToCSVConvertor(json_data, "Diag", true);
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
                        url: "../db/diag/database",
                        data: filter,
			success:function(data) {
			    	json_data = data;  //store to global var for exporting

      			}
                    });
                }
            },
            fields: [
                { name: "cmd", title: "Command", type: "text", width: 60  },
                { name: "str", title: "Result", type: "text", width: 600  }

            ],
	    onDataLoaded: function(data) {
	    },
	    onError: function() {
		console.log( "Ошибка: " + this.src );
	    }

        });

});