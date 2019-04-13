
$(function() {
    RefreshData();

    json_data = '';  //store to global var for exporting

    function CheckItems() {
        $("#content").empty();
        RefreshData ();

    }

    for (var i = 1; i < 100000; i++)
        setTimeout(function () { RefreshData();  }, 4000 * i);


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
                        $('[id^=b'+q+']').append('<div class="panel-footer" id="div_c'+q+'"></ul>');
                    }

                    //add callers 'from' 'to' 'queue' 'time'
                    if ((c.length>0)&&($('[id^=ul_c'+q+']').length == 0))
                        $('[id^=div_c'+q+']').append('<ul class="nav nav-pills" id="ul_c'+q+'"></ul>');
                    for (var n = 0; n < c.length; n++) {
                        from = c[n].from; 			//phone number with prefix if set
                        from_c = from.replace(/[^0-9]/gim,'');  //remove any chars from phone
                        to = c[n].to;  				// internal extension

                        to_text = to;

                        if (!to) to_text = 'not answered';
                        time = c[n].time;                       // call time
                        id = q+from_c;                         // id for html elements
                        li_text = from + ' -> ' + to_text + ' : ' + time;
                        if ($('[id^=c_li'+id+']').length == 0) {
                            $('[id^=ul_c'+q+']').append('<li id="c_li'+id+
                                '"><span id="c_s'+id+'" class="label label-default">'+
                                li_text + '</span></li>');
                        } else {  // if caller already exist - change text
                            $('[id^=c_s' + id +']').text(li_text);
                        }
                        $('[id^=c_s' + id +']').attr('alt', Date.now()/1000);

                        // if call not answered yet mark it
                        if (c[n].state=="Up") {
                            to ? $('[id^=c_s' + id + ']').attr("class", "label label-success") :
                                $('[id^=c_s' + id + ']').attr("class", "label label-danger");
                        }else {
                            to ? $('[id^=c_s' + id + ']').attr("class", "label label-warning") :
                                $('[id^=c_s' + id + ']').attr("class", "label label-danger");
                        }
                    }


                    //add queue members
                    if ($('[id^=ul'+q+']').length == 0)
                        $('[id^=div'+q+']').append('<ul class="nav nav-pills" id="ul'+q+'"></ul>');
                    for (var n = 0; n < m.length; n++) {
                        //check if member label exist, skip it
                        id = q+m[n].number;
                        if ($('[id^=li'+q+m[n].number+']').length == 0) {
                            var li_text='';
                            // if we have caller id, set it, if not - show only number
                            if (m[n].member == m[n].number) { li_text = m[n].member; }
                            else { li_text = m[n].member+' / '+m[n].number; }

                            if ((chk_na) && (m[n].state=='na')) {
                                $('[id^=ul'+q+']').append('<li id="li'+id+
                                    '"><span id="s'+q+id+'" class="label label-default">'+
                                    li_text+'</span></li>');
                            }
                            if (m[n].state=='aviable') {
                                $('[id^=ul'+q+']').append('<li id="li'+ id +
                                    '"><span id="s'+ id +'" class="label label-success">'+
                                    li_text+'</span></li>');
                            }
                            if (m[n].state=='busy') {
                                $('[id^=ul'+q+']').append('<li id="li'+ id +
                                    '"><span id="s'+ id +'" class="label label-danger">'+
                                    li_text+'</span></li>');
                            }
                        }
                        // change class if need
                        if ($('[id^=s'+ id +']').length > 0) {
                            if (m[n].state == 'na') {
                                $('[id^=s'+ id +']').attr("class", "label label-default");
                            }
                            if (m[n].state == 'aviable') {
                                $('[id^=s'+ id +']').attr("class", "label label-success");
                            }
                            if (m[n].state == 'busy') {
                                $('[id^=s'+ id +']').attr("class", "label label-danger");
                            }
                            if (m[n].state == 'ring') {
                                $('[id^=s'+ id +']').attr("class", "label label-warning");
                            }
                        }
                        //$('[id^=ul'+q+']').append('<li ><a href="#">Messages</a></li>');
                    }
                }
                // check not existing calls and delete it
                $('span').each(function(i,elem){
                    if( $(this).attr('id').match(/c_s/) ) {
                        //delete html elemente if it not exist in last json response
                        if (((Date.now()/1000)-$(this).attr('alt')) > 5) {
                            $(this).parent().empty();
                        }
                    }
                })

            }

        });
    }

    $("#config_panel input[type=checkbox]").on("click", function() {
        var $cb = $(this);
        CheckItems();
    });



});