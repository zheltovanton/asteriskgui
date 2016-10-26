
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
            pageSize: 50,
            pageButtonCount: 5,
            deleteConfirm: "Do you really want to delete row?",
            controller: {
                loadData: function(filter) {
                    return $.ajax({
                        type: "GET",
                        url: "../db/report/cdr/",
                        data: filter,
			success:function(data) {
			    	json_data = data;  //store to global var for exporting
 				var counter = data.length;
				var sbillsec_answer = 0;
				var sbillsec_not = 0;
				var cbillsec_answer = 0;
				var cbillsec_not = 0;
				
				var date = 0; 
				var day  = 0;
				var month = 0;
				var year = 0; 

				for (var i = 0; i < counter; i++) {
  				   if (data[i].disposition == "ANSWERED") {
					sbillsec_answer += parseFloat(data[i].billsec);
					cbillsec_answer += 1;
				   }
  				   if (data[i].disposition != "ANSWERED") {
					sbillsec_not += parseFloat(data[i].billsec);
					cbillsec_not += 1;
				   }
				   data[i].clid = data[i].clid.replace(/(\<(\/?[^>]+)>)/g,'');
				   data[i].clid = data[i].clid.replace(/['"']/g,'');
				   data[i].billsec = toMMSS(data[i].billsec);
				   data[i].duration = toMMSS(data[i].duration);

				date = data[i].calldate;
				day = myDay(date); //data[i].calldate.parse(mySQLDate.replace('-','/','g')).to;
				month = myMonth(date); //data[i].calldate.dateparts[0];
				year = myYear(date); //data[i].calldate.dateparts[0];
                                if (data[i].recordingfile) {
					data[i].recordingfile = year + "/" + month + "/" + day + "/" + data[i].recordingfile;
				}else{
					
				}
				}
				
				$("#total").text("Records: "+counter+
						 ", Answered " + cbillsec_answer + " (" + toMMSS(sbillsec_answer) + ")" +  
						 ", no answer " + cbillsec_not ); 
      			}
                    });
                }
            },
            fields: [
                { name: "calldate", title: "Date", type: "date", width: 120  },
                { name: "clid", title: "CallerID", type: "text", width: 100  },
                { name: "src", title: "From", type: "text", width: 100 },
                { name: "dst", title: "To", type: "text", width: 100 },
                { name: "dcontext", title: "Context", type: "text", width: 100 },
                { name: "disposition", title: "Status", type: "text", width: 100,filtering:false  },
                { name: "duration", title: "Duration ", type: "number", width: 60,filtering:false  },
                { name: "billsec", title: "Answered sec.", type: "number", width: 60,filtering:false  },
                { name: "recordingfile", 
                        itemTemplate: function(value) {
                            if (value) {
				return $("<a>").attr("href", "download.php?audio="+value).attr("target", "_blank").text("Download");
			    } else {
				return "";
			    }
                        } , title: "Audio", type: "text", width: 100,filtering:false  }

            ],
	    onDataLoaded: function(data) {
	    },
	    onError: function() {
		console.log( "Ошибка: " + this.src );
	    }

        });

});