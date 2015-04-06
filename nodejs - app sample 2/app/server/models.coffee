Backbone = require 'backbone'
_ = require('underscore')._
$ = require 'jquery'
jsdom = require 'jsdom'
request = require 'request'
querystring = require 'querystring'
fs = require 'fs'
exec = require('child_process').exec


Models = exports
db = require('./db').db

$.extend Models,require('../shared/models')
Config = require '../shared/config'


Collection = Backbone.Collection
Model = Backbone.Model

log = Config.log 'server.models'
stringify = (s) -> JSON.stringify s

tb = day = dayAfter = null



jquerySrc = fs.readFileSync("./lib/client/1.jquery.min.js").toString()



#--------------------Web Service side-------------------------------------------------- 
class Models.WebService
  constructor:(o)->        
    @status = 'ready'
    @master  = Config.services.master       
    @master.protocol = @master.protocol or 'http'
    @master.method = 'serviceResponse'       
    @hostname = o.hostname
    @port = o.port
    @url = _.template '<%=protocol%>://<%=hostname%>:<%=port%>/<%=path%>/price/<%=method%>',@master        
    log 'web service loaded for source at ' + @url
    @response { ping: @hostname+'$'+@port } 
    
  request:(o)->  
    log 'service:--request from master ' + stringify o
    self = @              
    reqId = o.reqId                  
    if @status is 'master offline' then @status = 'ready' 
    if o.action is 'status'  
      @response reqId:reqId,status:@status
    else 
      log 'in ws ' +   @status + ', ' + reqId
      f = @[o.action]
      if not f
        log 'web service request, unknown action: ' + o.action
        return
      @status = 'busy'   
      f $.extend {},o,
        success:(reply)->   
          self.status = 'ready'
          self.response $.extend reply,{status:self.status,reqId:reqId}
        error:(reply)->                                                
          log 'error, calling back to master ' + stringify reply
          self.status = 'ready'
          self.response $.extend reply,{status:self.status,reqId:reqId}
  response:(o)-> 
    self = @                         
    data = $.extend { status:self.status }, o
    log 'response going to '+self.url + ' is ' + stringify data     
    Utils.asyncReq 
      url: self.url 
      data: data
      success: (reply)->
        #
      error: ->                 
        
        self.status = 'master offline'

