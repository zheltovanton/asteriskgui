/*

 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.
 */

 
$.datepicker._get_original = $.datepicker._get;
$.datepicker._get = function(inst, name){
	var func = $.datepicker._get_original(inst, name);

	var range = 'period';
	if(!range) return func;

	var that = this;

	switch(range){
		case 'period':
		case 'multiple':
			var datepickerExtension = $(this.dpDiv).data('datepickerExtensionRange');
			if(!datepickerExtension){
				datepickerExtension = new _datepickerExtension();
				$(this.dpDiv).data('datepickerExtensionRange', datepickerExtension);
			}
			datepickerExtension.range = range;
			datepickerExtension.range_multiple_max = inst.settings['range_multiple_max'] || 0;

			switch(name){
				case 'onSelect':
					var func_original = func;
					if(!func_original) func_original = function(){};

					func = function(dateText, inst){
						datepickerExtension.onSelect(dateText, inst);
						func_original(dateText, inst, datepickerExtension);

						 // hide fix
						that._datepickerShowing = false;
						setTimeout(function(){
							that._updateDatepicker(inst);
							that._datepickerShowing = true;
						});

						datepickerExtension.setClassActive(inst);
					        
					};

					break;
				case 'beforeShowDay':
					var func_original = func;
					if(!func_original) func_original = function(){ return [true, '']; };

					func = function(date){
						var state = func_original(date);
						state = datepickerExtension.fillDay(date, state);

						return state;
					};

					break;
				case 'beforeShow':
					var func_original = func;
					if(!func_original) func_original = function(){};

					func = function(input, inst){
						func_original(input, inst);

						datepickerExtension.setClassActive(inst);
					};

					break;
				case 'onChangeMonthYear':
					var func_original = func;
					if(!func_original) func_original = function(){};

					func = function(year, month, inst){
						func_original(year, month, inst);

						datepickerExtension.setClassActive(inst);
					};

					break;
			}
			break;
	}

	return func;
};

$.datepicker._setDate_original = $.datepicker._setDate;
$.datepicker._setDate = function(inst, date, noChange){
	var range = inst.settings['range'];
	if(!range) return $.datepicker._setDate_original(inst, date, noChange);

	var datepickerExtension = this.dpDiv.data('datepickerExtensionRange');
	if(!datepickerExtension) return $.datepicker._setDate_original(inst, date, noChange);

	switch(range){
		case 'period':
			if(!(typeof(date) == 'object' && date.length != undefined)){ date = [date, date]; }

			datepickerExtension.step = 0;

			$.datepicker._setDate_original(inst, date[0], noChange);
			datepickerExtension.startDate = this._getDate(inst);
			datepickerExtension.startDateText = this._formatDate(inst);

			$.datepicker._setDate_original(inst, date[1], noChange);
			datepickerExtension.endDate = this._getDate(inst);
			datepickerExtension.endDateText = this._formatDate(inst);

			datepickerExtension.setClassActive(inst);

			break;
		case 'multiple':
			if(!(typeof(date) == 'object' && date.length != undefined)){ date = [date]; }

			datepickerExtension.dates = [];
			datepickerExtension.datesText = [];

			var that = this;
			$.map(date, function(date_i){
				$.datepicker._setDate_original(inst, date_i, noChange);
				datepickerExtension.dates.push(that._getDate(inst));
				datepickerExtension.datesText.push(that._formatDate(inst));
			});

			datepickerExtension.setClassActive(inst);

			break;
	}
};

var _datepickerExtension = function(){
	this.range = "period",
	this.range_multiple_max = 2,
	this.step = 0,
	this.dates = [],
	this.datesText = [],
	this.startDate = null,
	this.endDate = null,
	this.startDateText = '',
	this.endDateText = '',
	this.onSelect = function(dateText, inst){
		switch(this.range){
			case 'period': return this.onSelectPeriod(dateText, inst); break;
			case 'multiple': return this.onSelectMultiple(dateText, inst); break;
		}
	},
	this.onSelectPeriod = function(dateText, inst){
		this.step++;
		this.step %= 2;

		if(this.step){
			// выбирается первая дата
			this.startDate = this.getSelectedDate(inst);
			this.endDate = this.startDate;

			this.startDateText = dateText;
			this.endDateText = this.startDateText;
		}else{
			// выбирается вторая дата
			this.endDate = this.getSelectedDate(inst);
			this.endDateText = dateText;

			if(this.startDate.getTime() > this.endDate.getTime()){
				this.endDate = this.startDate;
				this.startDate = this.getSelectedDate(inst);

				this.endDateText = this.startDateText;
				this.startDateText = dateText;
			}
		}
	},
	this.onSelectMultiple = function(dateText, inst){
		var date = this.getSelectedDate(inst);

		var index = -1;
		$.map(this.dates, function(date_i, index_date){
			if(date_i.getTime() == date.getTime()) index = index_date;
		});
		var indexText = $.inArray(dateText, this.datesText);

		if(index != -1) this.dates.splice(index, 1);
		else this.dates.push(date);

		if(indexText != -1) this.datesText.splice(indexText, 1);
		else this.datesText.push(dateText);

		if(this.range_multiple_max && this.dates.length > this.range_multiple_max){
			this.dates.splice(0, 1);
			this.datesText.splice(0, 1);
		}
	},
	this.fillDay = function(date, state){
		var _class = state[1];

		if(date.getDate() == 1) _class += ' first-of-month';
		if(date.getDate() == new Date(date.getFullYear(), date.getMonth()+1, 0).getDate()) _class += ' last-of-month';

		state[1] = _class.trim();

		switch(this.range){
			case 'period': return this.fillDayPeriod(date, state); break;
			case 'multiple': return this.fillDayMultiple(date, state); break;
		}
	},
	this.fillDayPeriod = function(date, state){
		if(!this.startDate || !this.endDate) return state;

		var _class = state[1];

		if(date >= this.startDate && date <= this.endDate) _class += ' selected';
		if(date.getTime() == this.startDate.getTime()) _class += ' selected-start';
		if(date.getTime() == this.endDate.getTime()) _class += ' selected-end';

		state[1] = _class.trim();

		return state;
	},
	this.fillDayMultiple = function(date, state){
		var _class = state[1];

		var date_is_selected = false;
		$.map(this.dates, function(date_i){
			if(date_i.getTime() == date.getTime()) date_is_selected = true;
		});
		if(date_is_selected) _class += ' selected selected-start selected-end';

		state[1] = _class.trim();

		return state;
	},
	this.getSelectedDate = function(inst){
		return new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay);
	};
	this.setClassActive = function(inst){
		var that = this;
		setTimeout(function(){
			$('td.selected > *', inst.dpDiv).addClass('ui-state-active');
			if(that.range == 'multiple') $('td:not(.selected)', inst.dpDiv).removeClass('ui-datepicker-current-day').children().removeClass('ui-state-active');
		});
	};
};

String.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return hours+':'+minutes+':'+seconds;
}


function toMMSS(str) {
    var sec_num = parseInt(str, 10); // don't forget the second param
    var minutes = Math.floor(sec_num / 60);
    var seconds = sec_num - (minutes * 60);

    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return minutes+':'+seconds;
}


function mysqlTimeStampToDate(timestamp) {
   //function parses mysql datetime string and returns javascript Date object
   //input has to be in this format: 2007-06-05 15:26:02
   var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
   var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
   return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
}

function myDay(timestamp) {
   //function parses mysql datetime string and returns javascript Date object
   //input has to be in this format: 2007-06-05 15:26:02
   var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
   var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
   return parts[2];
}
function myMonth(timestamp) {
   //function parses mysql datetime string and returns javascript Date object
   //input has to be in this format: 2007-06-05 15:26:02
   var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
   var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
   return parts[1];
}
function myYear(timestamp) {
   //function parses mysql datetime string and returns javascript Date object
   //input has to be in this format: 2007-06-05 15:26:02
   var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
   var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
   return parts[0];
}

function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}

var DateField = function(config) {
    jsGrid.Field.call(this, config);
};

DateField.prototype = new jsGrid.Field({
    filterTemplate: function() {
    	var grid = this._grid;
    
        var now = new Date();
        this._fromPicker = $("<input>").datepicker({ 
		defaultDate: now.setFullYear(now.getFullYear()), 
	    	dateFormat: "dd.mm.yy",
    		onSelect: function (date) {
                 	console.log(this.startDate);
			grid.search();
  	  	}

	});
        
        return $("<div>").append(this._fromPicker).append(this._toPicker);
    },
    
    insertTemplate: function(value) {
        return this._insertPicker = $("<input>").datepicker({ defaultDate: new Date() });
    },
    
    editTemplate: function(value) {
        return this._editPicker = $("<input>").datepicker().datepicker("setDate", new Date(value));
    },
    
    insertValue: function() {
        return this._insertPicker.datepicker("getDate").format("dd/mm/yyyy");
    },
    
    editValue: function() {
        return this._editPicker.datepicker("getDate").format("dd/mm/yyyy");
    },
    
    filterValue: function() {
        return {
            from: this._fromPicker.datepicker("getDate")
        };
    }
});

jsGrid.fields.date = DateField;

var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};

