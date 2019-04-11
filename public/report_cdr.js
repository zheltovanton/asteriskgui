
$(function() {

    var json_data;

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    var start1 = 0;
    var text1 = '';
    var phone1 = '';
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

    $("#phonetext").change(function() {
        phone1 = this.value;
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
        window.location.replace("../db/report/cdr/download.php?start="+start1+"&end="+end1+"&text="+text1+"&phone="+phone1);
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
                    url: "../db/report/cdr/?start="+start+"&end="+end+"&text="+text+"&phone="+phone1,
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

                            if (data[i].whois_man) {
                                data[i].whois = data[i].whois_man;
                            }
                            if (data[i].disposition == "ANSWERED") {
                                sbillsec_answer += parseFloat(data[i].billsec);
                                cbillsec_answer += 1;
                            }
                            if (data[i].disposition != "ANSWERED") {
                                sbillsec_not += parseFloat(data[i].billsec);
                                cbillsec_not += 1;
                            }
                            if (data[i].clid != null)
                            {
                                data[i].clid = data[i].clid.replace(/(\<(\/?[^>]+)>)/g, '');
                                data[i].clid = data[i].clid.replace(/['"']/g,'');
                            }
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
            { name: "calldate", title: "Дата", type: "date", width: 120 ,filtering:false },
            { name: "clid", title: "Имя", type: "text", width: 100  },
            { name: "clidrb", itemTemplate: function(value) {
                var color = "white";
                if ((value=='V')) {
                    color = 'rgba(150,0,0,0.4)';
                }
                return $("<div>").addClass("rating").css('background-color', color).append(value);
            }, title: "Возврат", type: "text", width: 40 },
            { name: "cc", title: "Оператор", type: "text", width: 40 },
            { name: "disposition", itemTemplate: function(value) {
                var color = "white";
                if ((value=='ANSWERED')) {
                    color = 'rgba(0,150,0,0.4)';
                }
                if (value=='NO ANSWER') {
                    color = 'yellow';
                }
                return $("<div>").addClass("rating").css('background-color', color).append(value);
            }, title: "Статус", type: "text", width: 60,filtering:false  },
            { name: "duration", title: "Длительность", type: "number", width: 60,filtering:false  },
            { name: "recordingfile",
                itemTemplate: function(value) {
                    if (value) {
                        return '<audio controls="controls" width="120px" preload="none" src="../download.php?audio='+encodeURIComponent(value)+'" type="audio/x-wav" /></audio>';
                    } else {
                        return "";
                    }
                } , title: "Запись", type: "text", width: 150,filtering:false  }

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