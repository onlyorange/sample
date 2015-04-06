# Server-side code

Backbone = require 'backbone'
Collection = Backbone.Collection
Model = Backbone.Model
_ = require('underscore')._
$ = require 'jquery'

Models = require '../shared/models'
Utils = require '../shared/utils'
Config = require '../shared/config'
db = require('./db').db
log = Config.log 'app'


stringify = (s) -> JSON.stringify s


Config.sync = Backbone.sync
Models.serverInit()
Config.user = new Models.Person {id:1, roles:'admin' }  # system

Config.timebase = new Models.Timebase
Config.app = new Models.Application

db.init()
  
log 'Configurations:'
$.each Config,(key)-> log key + ':' + stringify @

_.extend Config, Backbone.Events  # global events
if Config.deployment is 'development' then Config.bind 'all',(name)-> log 'event:'+name

log 'appUniverse: '+ stringify Config.app


Config.app.loadApp
  error:-> log 'dying, probably won\'t happen'
  success:-> 
    log 'app loaded on server side'


exports.actions = # Actions are available from client
  
  
  init: (cb) ->
    #console.log(@session)
    if @session.user_id
      R.get "user:#{@session.user_id}", (err, data) =>
        if data then cb data else cb false
    else
      cb false

  sendMessage: (opts, cb) ->
    
    # log 'current game on server '+ Config.cGame.get('id')        
    # console.log('roomid '+opts.roomid)
    if opts.roomid 
      # console.log(@session)
      if opts.message_type == 'start_new_game'
        
        
        Models.Game::lockGame {}
        log 'on server side game is lock'
        gameObj = new Models.Game id: opts.gameid
        gameObj.fetch success:(gameObj)->

          log 'on server side we have the game'

          if +gameObj.get('game_started') == 1
            log 'game already started'
            Models.Game::unlockGame {}
            cb true

          else

            log 'we try to save the game'

            gameObj.set 'game_started':1
            
            StartDateMysql = Config.timebase.set_mysql_date_from_server(gameObj.get('StartDate'))           
            gameObj.set 'StartDate':StartDateMysql
            
            gameObj.save {}, success: (gameObj)->

              Models.Game::unlockGame {}
              SS.publish.channel 100, 'newMessage', opts
              cb true
        
        
        ###
        gameObj = new Models.Game id: opts.gameid
        gameObj.lockFetchSaveUnlockGame opts, err:-> cb true ,  success:->
          SS.publish.channel 100, 'newMessage', opts
          cb true
        ###
        
        
      
        
      else
        if opts.message_type == 'start_new_round'
        
          ###
          gameObj = new Models.Game id: opts.gameid
          gameObj.lockFetchSaveUnlockGame opts, err:-> cb true ,  success:->
            SS.publish.channel 100, 'newMessage', opts
            cb true
          
          ###
          
          Models.Game::lockGame {}
          
          gameObj = new Models.Game id: opts.gameid
          gameObj.fetch success:(gameObj)->
            log  'db ROUND'+gameObj.get('round')+' opts round'+opts.round
            if +gameObj.get('round') == +opts.round
              log 'start_round = round already started'
              Models.Game::lockGame {}
              cb true
            else
              
              gameObj.set 'round':opts.round
              
              StartDateMysql = Config.timebase.set_mysql_date_from_server(gameObj.get('StartDate'))
              gameObj.set 'StartDate':StartDateMysql
              
              gameObj.save {}, success: (gameObj)->
                
                Models.Game::unlockGame {}
                SS.publish.channel 100, 'newMessage', opts
                cb true  

                  
                  
          
        else 
          if opts.message_type == 'end_round'
          
            Models.Game::lockGame {}
            gameObj = new Models.Game id: opts.gameid
            gameObj.fetch success:(gameObj)->
              if +gameObj.get('round') == +opts.round
                log 'end_round = end round '+ opts.round
                Models.Game::unlockGame {} 
                SS.publish.channel 100, 'newMessage', opts
                cb true
              else
                Models.Game::unlockGame {}
                log 'end_round = round does not need to be started'
                cb true
              
          else  
          
            if opts.message_type == 'finish_game'
              
              Models.Game::lockGame {}
              gameObj = new Models.Game id: opts.gameid
              gameObj.fetch success:(gameObj)->
                if +gameObj.get('game_started') == 1
                  
                  gameObj.set 'game_started':'2'
                  StartDateMysql = Config.timebase.set_mysql_date_from_server(gameObj.get('StartDate'))
                  gameObj.set 'StartDate':StartDateMysql
                  gameObj.save {}, success: (gameObj)->
                
                    Models.Game::unlockGame {}
                    SS.publish.channel 100, 'newMessage', opts
                    cb true    
                 
                else
                  Models.Game::unlockGame {}
                  log 'end_round = round does not need to be started'
                  cb true
            else
              SS.publish.channel 100, 'newMessage', opts
              cb true
      
    else
      SS.publish.broadcast 'newMessage', opts
      cb true
  
  
  subscribeToRoom: (room, cb) ->
    @session.channel.subscribe(room)
    cb true
  
  signIn: (user, cb) ->
    @session.setUserId(user)
    cb user
    
  
  authenticate: (params,cb)-> # called after user authenticates so the session can be linked properly
    session = @session
    session.setUserId(params.pid)
    cb 'ready'

  logout: (params,cb)->
    cb 'logged out'

  time: (cb) -> cb { timestamp: new Date().getTime(), version: Config.version }

  ###
  # give me commander Data
  appsync: (method,model,cb)->
    log 'method: '+method
    switch method
      when 'read'
        # userApp = appUniverse.each collection collection.filter (collection) -> if collection typeof collection collection.filter -> return true # auth filter
        cb Config.app.xport(),'read successful'  #for now just blast it, later tune this up to filter 
      when 'update'
        Config.app.mport model
        cb model,'update successful'
  ###
  
  

