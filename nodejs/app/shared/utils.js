
if (typeof module != 'undefined' && module.exports) {
  Utils = exports
  var $ = require('jquery')
} else{
  var $ = jQuery
  Utils = {}
}
Utils.jsonparse = function(str){var json;try{json=JSON.parse(str);}catch (err){json={};}return json;}

Utils.wait = function(time) {
  var dfd = $.Deferred();
  setTimeout(function() {
    dfd.resolve();
  }, time);
  return dfd;
}

Utils.oneWeek = 7*24*3600000

if (typeof(exports) !== 'undefined'){ // nodejs
  var request = require('request');   
  var url = require('url');
  var querystring = require('querystring');
  Utils.asyncReq = function(args){
    var cookies = 'has_js=1';
    var fullurl = args.url;
    var urlreq = url.parse(fullurl), data,
      headers = {'host':urlreq.hostname,'user-agent':'NodeJS HTTP Client','Cookie':cookies};
    var type = args.type ? args.type.toUpperCase(): 'GET';
    
    var replyfn = function(error,response,body){
      if (response && response.headers['set-cookie']){
        cookies = 'has_js=1';
        var cookhash = {};
        $.each(response.headers['set-cookie'],function(i,c){ cookhash[c.split('=')[0]] = c; });
        $.each(cookhash,function(i,c){ cookies += '; ' + c.split(';').shift(); });
      }
      if (error){ if (args.error) args.error(null,null,error); 
      } else if (args.success) args.success(
        args.dataType === 'json' ? Utils.jsonparse(body) : (args.dataType === 'xml' ? $.parseXML(body) : body));
    };
    if (type === 'POST') {
      data = querystring.stringify(args.data);
      headers['content-type'] = 'application/x-www-form-urlencoded';
      headers['connection'] = 'keep-alive';
      headers['content-length'] = data.length;
     // console.log('argsurl:'+args.url);
      request( {uri:fullurl,method:'POST',headers:headers,body:data},replyfn);
    } else {  // assume GET
      headers['content-type'] = 'application/json'; 
      if (args.data) {
        data = querystring.stringify(args.data);
        fullurl+='?'+data
      }
      request({uri:fullurl,headers:headers},replyfn);
    }
  }

} else {  // ajax client
  Utils.asyncReq = $.ajax;  
}

dateFormat = function () {
  var  token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
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

    var  _ = utc ? "getUTC" : "get",
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

dateStrFormat = function() {
    var  token = /d{1,2}|m{1,4}|yy(?:yy)?|[S]|"[^"]*"|'[^']*'/g,
    pad = function (val, len) {
      val = String(val);
      len = len || 2;
      while (val.length < len) val = "0" + val;
      return val;
    };

    return function(dateStr, mask) {
        var dsF = dateStrFormat;
        
        if (Object.prototype.toString.call(dateStr) != "[object String]")  throw SyntaxError("Invalid datestr");

        mask = String(dsF.masks[mask] || mask || dsF.masks["default"]);
        
        var d = +dateStr.substr(8,2),
        m = +dateStr.substr(5,2),
        y = +dateStr.substr(0,4),
        flags = {
            d:    d,
            dd:   pad(d),
            m:    m,
            mm:   pad(m),
            mmm:  dateFormat.i18n.monthNames[m-1],
            mmmm: dateFormat.i18n.monthNames[m+11],
            yy:   String(y).slice(2),
            yyyy: y,
            S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
        };

        
        return mask.replace(token, function ($0) {
          return $0 in flags ? flags[$0] : $0.slice(1, $0.length -1);
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


dateStrFormat.masks = {
  "default":      "mmm d, yyyy",
  "shortDate":    "m/d/yy",
  "slashFullDate":"m/d/yyyy",
  "longDate":     "mmmm d, yyyy",
  "isoDate":      "yyyy-mm-dd"
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
  return Utils.dateFormat(this, mask, utc);
};
Utils.dateFormat = dateFormat

String.prototype.dateStrFormat = function (mask) {
    return Utils.dateStrFormat(this, mask);
};

Utils.dateStrFormat = dateStrFormat