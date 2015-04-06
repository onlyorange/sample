
window.LocalStorage = 
  get: (key) ->
    cookies = document.cookie.split ";"
    for cookie in cookies
      x = cookie.substr 0,cookie.indexOf("=")
      y = cookie.substr cookie.indexOf("=")+1
      x = x.replace /^\s+|\s+$/g,""
      if x is key 
        retval = unescape y
        return if retval is 'null' or retval is 'NULL' or retval is 'undefined' then null else retval
    null
  set: (key, value, expireDays) -> 
    expireDays = expireDays or 365 # set by default so persist between sessions
    obj = {}
    if not value or value is null then value = ''
    if typeof key is 'string' then obj[key] = value else obj = key
    for k,v of obj
      str = k + "=" + escape(v)
      if expireDays
        expire = new Date()
        expire.setTime (expire.getTime() + (expireDays * 24 * 3600 * 1000))
        str += "; expires=" + expire.toGMTString()
      document.cookie = str
  unset: (key)-> @set key,null
  destroy: (key) -> if @get(key) then document.cookie = key + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT"

