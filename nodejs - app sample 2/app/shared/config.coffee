# Shared globals in here

if typeof module != 'undefined' && module.exports
  Config = exports
  config = require('../../config/app.coffee').config
  $ = require 'jquery'
  uuid = require 'node-uuid'  
  $.extend(Config,config) # join all configs into single global, this
  server = true
else
  $ = @$
  uuid = {}
  Config = @Config = {}
  server = false
  
Config.version = 0.01

stringify = (s) -> JSON.stringify s

Config.ajaxRoot = '/api/ajax/'

Config.modelTransport = 'socketstream' # or 'ajax'


Config.transition = 250   # transition time for animate

minute = 60*1000
hour = 60*minute
day = 24*hour
week = 7*day
year = 52*week
month = year / 12

Config.siteurl = -> 
  if server 
    if @https.enabled then return 'https://' + @https.domain
    if not @http.port or @http.port is 80 then return 'http://'+ @https.domain
    'http://'+ @https.domain + ':'+ @http.port
  else
    document.location.protocol+'//'+document.location.host+'/'
    
Config.log = (name,verbose)->
  if Config.logging  # make sure ok even when not set
    subsyslevel = Config.logging[name]
    if subsyslevel and subsyslevel > Config.loglevel then return (s)-> # off
  if not server and not window.testMode and window.appMode is 'deployment' then return (s)-> # off 
  return (s,verbose)->
    if verbose then console.log '['+new Date().toString()+'|'+name+']'+s
    else console.log '['+name+']'+s
    s # handy to send back the string

Config.uuid =-> uuid.v1()  # currently server only


log = Config.log 'config'
stringify = (s) -> JSON.stringify s    

Config.tracking = {}

Config.DSTOffset = 1 # 0,1 offset for Daylight Savings Time
Config.serviceTimeOffset = 0*hour # pacific time offset from GMT (PSTTime)
Config.heartbeat = 600 # seconds, ten minutes
Config.expireCookies = 365  # days

log stringify(Config)

Config.jsonparse = (data) ->
  try 
    return JSON.parse data
  catch ex
    return null
    

Config.implementing = (mixins..., classReference) ->
  for mixin in mixins
    for key, value of mixin::
      classReference::[key] = value
  classReference  
    
String.prototype.trim = -> @replace(/^\s\s*/, '').replace(/\s\s*$/, '') 

Config.views = 
  getVersion: (id) -> @versions[@path][id] or '0'
  path: 'A' # 'A' by default
  versions: # this vector overrides the default set of templates (version 0)
    A:      
      'footer_public': '0'
    B:      
      'footer_public': '0'

Object.filter = (obj,predicate) ->
  result =  {}
  for key of obj
    if not obj.hasOwnProperty(key) then continue
    if typeof obj[key] is 'object' then result[key] = Object.filter(obj[key],predicate)
    else if predicate(key,obj[key]) then result[key] = obj[key]
  return result

$.wait = (time)->
  $.Deferred (dfd) -> setTimeout dfd.resolve, time
  

Config.maxRounds = 3
Config.timeRound = "00:01:00"

# Dynamically figure out facebook ids
#Config.app.fb_app_id = Config.facebook.app_id
#Config.app.fb_app_secret = Config.facebook.app_secret
Config.fb_app_id = "273188282803852"
Config.fb_app_secret = "c48a92d9c829191b85a27dda98efb9dc"
