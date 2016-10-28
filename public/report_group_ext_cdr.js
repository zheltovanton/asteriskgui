
$(function() {

    var json_data;
	
    $("#but_excel a").click(function() {
    	JSONToCSVConvertor(json_data, "CDR group by Ext Report", true);
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
            pageSize: 50,
            pageButtonCount: 5,
            deleteConfirm: "Do you really want to delete row?",
            controller: {
                loadData: function(filter) {
                    return $.ajax({
                        type: "GET",
                        url: "../db/report/group_ext_cdr/",
                        data: filter,
			success:function(data) {
			    	json_data = data;  //store to global var for exporting
 				var counter = data.length;
 				var scount = 0;
 				var ssum = 0;

				for (var i = 0; i < counter; i++) {
					scount += parseFloat(data[i].count);
					ssum += parseFloat(data[i].sum_duration);
					data[i].sum_duration = toMMSS(data[i].sum_duration); 
					data[i].medium = toMMSS(data[i].medium); 
				}
				
				$("#total").text("Records: "+counter+
						 ", calls " + scount + 
						 ", duration " + " (" + toMMSS(ssum) + ")");  
      			}
                    });
                }
            },
            fields: [
                { name: "calldate", title: "Date", type: "date", width: 120  },
                { name: "src", title: "Extension", type: "text", width: 100  },
                { name: "count", title: "Count", type: "number", width: 100 },
                { name: "sum_duration", title: "Summary", type: "text", width: 100 },
                { name: "medium", title: "Medium", type: "text", width: 100 },

            ],
	    onDataLoaded: function(data) {
	    },
	    onError: function() {
		console.log( "Ошибка: " + this.src );
	    }

        });

});