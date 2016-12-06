
$(function() {


    var json_data;

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    var start1 = 0;
    var text1 = '';
    var end1 = 0;

    if(dd<10) {
        dd='0'+dd;
    } 

    if(mm<10) {
        mm='0'+mm;
    } 

    today = mm+'/'+dd+'/'+yyyy;
	
    function myRefresh(){
     	text1 = $("#searchtext").value;
        $("#content").jsGrid("search");

    }	

    $("#searchtext").change(function() {
       text1 = this.value;
        $("#content").jsGrid("search");
    });

    $('#daterange').daterangepicker({
      "showDropdowns": true,
      "showWeekNumbers": true,
      "startDate": today,
      "endDate": today,
      onSelect: function(startDate, endDate) {
        $("#content").jsGrid("search");
        }
     }, 
     function(start, end, label) {
      	start1 = start.format('YYYY-MM-DD');
      	end1 = end.format('YYYY-MM-DD');
      	$("#content").jsGrid("search");	
    });


    $("#but_excel a").click(function() {
    	JSONToCSVConvertor(json_data, "CDR Report", true);
    });

	DATA = null;	

        $("#content").jsGrid({
            height: "93%",
            width: "100%",
            filtering: false,
            inserting: false,
            editing: false,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 200,
            pageButtonCount: 5,
            deleteConfirm: "Do you really want to delete row?",
            controller: {
                loadData: function(filter) {
		    var start = start1;
		    var end = end1;
		    var text = text1;
		    console.log(text);

                    return $.ajax({
                        type: "GET",
                        url: "../db/report/cdr/?start="+start+"&end="+end+"&text="+text,
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
					if (data[i].recordingfile[0]!="/") {
						data[i].recordingfile = year + "/" + month + "/" + day + "/" + data[i].recordingfile;
					}
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
                { name: "calldate", title: "Date", type: "date", width: 120 ,filtering:false },
                { name: "clid", title: "CallerID", type: "text", width: 100  },
                { name: "src", title: "From", type: "text", width: 100 },
                { name: "dst", title: "To", type: "text", width: 100 },
                { name: "dcontext", title: "Context", type: "text", width: 100 },
                { name: "disposition", itemTemplate: function(value) {
                        var color = "white";
                        if ((value=='ANSWERED')) {
                            color = 'rgba(0,150,0,0.4)';
                        }
                        if (value=='NO ANSWER') {
                            color = 'yellow';
                        }
                        return $("<div>").addClass("rating").css('background-color', color).append(value);    
                        }, title: "Status", type: "text", width: 100,filtering:false  },
                { name: "duration", title: "Duration ", type: "number", width: 60,filtering:false  },
                { name: "billsec", title: "Answered", type: "number", width: 60,filtering:false  },
                { name: "recordingfile", 
                        itemTemplate: function(value) {
                            if (value) {
				return $("<a>").attr("href", "../download.php?audio="+value).attr("target", "_blank").text("Download");
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

    $("form").submit(function(){
 
        $("#content").jsGrid("refresh");
 
    });


});