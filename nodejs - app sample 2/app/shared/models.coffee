
if typeof module != 'undefined' && module.exports
  Models = exports
  Backbone = require 'backbone'
  _ = require('underscore')._
  $ = require 'jquery'
  db = require('../server/db').db
  Config = require '../shared/config'  
  server = true
  util = require 'util'
  
  
else
  Backbone = @Backbone
  _ = @_  
  $ = @$ 
  Models = @Models = {}
  Config = @Config
  Views = @Views
  server = false


Collection = Backbone.Collection
Model = Backbone.Model
Events = Backbone.Events

log = Config.log 'models'

stringify = (s) -> JSON.stringify s

jsonparse = (s) -> Config.jsonparse(s)

String.prototype.trim = -> @replace(/^\s\s*/, '').replace(/\s\s*$/, '')
disptime = (t,fancy)-> 
  d = new Date(t)
  if fancy then t + '--' + d.format("yyyy-mm-dd'T'HH:MM:ss") + ', utc:'+ d.format("UTC:yyyy-mm-dd'T'HH:MM:ss")
  else t + '--' + d.toString()
tb = null

getUrl = (object) ->
  if not (object and object.url) then return null
  if _.isFunction(object.url) then object.url() else object.url
urlError = -> throw 'A "url" property or function must be specified'


#log stringify SS
Models.init = ->  # client init (not sure how to control include order right now)
  Config = window.Config
  Views = window.Views
  Utils = window.Utils

  log 'initing client models'
  # Models.FacebookUser::clientInit()

  Backbone.sync = (method,model,options) -> # for socketstream, everything goes as a 'get'
    params = { dataType:'json', data: {} }
    if not options.url then params.url = getUrl(model) or urlError()
    if (not options.data and model and (method is 'create' or method is 'update')) then params.data.model = stringify model.toJSON()
    params.data._method = method;
    c = $.extend(true, params, options)
    if c.data and c.data.id and $.isArray(c.data.id) then c.data.id = stringify c.data.id 
    if not c.data.id then c.data.id = model.id
    descriptor = if model.model then model.model::descriptor else model.getDescriptor() 
    if method is 'create' or method is 'read' 
      success = c.success
      error = c.error
      descriptorHash = {}
      if descriptor.columns then $.each descriptor.columns,(i,col)-> descriptorHash[col.field] = col
      fixdata = (item)->
        $.each item,(k,v)->
          if not v then return true
          if not descriptorHash[k] 
            log 'no descriptor for '+ descriptor.name + ':'+ k
          else if descriptorHash[k].type is 'date'
            item[k] = new Date(v)
      c.success = (reply, textStatus, jqXHR)->
        if not reply.success
          error(jqXHR,textStatus,reply) if error
          return
        data = reply.results
        if data # might not have results?
          if $.isArray(data) then $.each data,(index,item)-> fixdata item  # collection
          else fixdata data # model
        success(data,textStatus,jqXHR) if success
    if Config.modelTransport is 'socketstream'
      #console.log(SS.server)
      SS.server.ajax[descriptor.name] c.data, (reply)-> 
        reply.responseText = stringify { info: reply.info }
        c.success(reply,reply.responseText,reply) if c.success
    else # ajax
      Utils.asyncReq c
    
      
Models.serverInit = ->
  Backbone.sync = (method,model,options) ->
    if (model instanceof Collection)
      collection = model
      # should be read only, id should be same as last part of url
      db.dispatch $.extend({_method:method}, options.data),collection.model::descriptor,(reply)->
        if options.success and reply.success then options.success(reply.results,0) # do error case too
        else if options.error and not reply.success
          # log 'error in sync, '+ stringify reply
          options.error {responseText:stringify(reply)}
    else
      if method isnt 'create' and not model.id then log '!!!! programmer error, no id for model, with method=' + method
      db.dispatch {_method:method,model:model.attributes,id:model.id},model.descriptor,(reply)->
        switch method
          when 'read'
            model.set reply.results, {silent:true}
          when 'create'
            model.set reply.results, {silent:true}
          # when 'update'
            # log 'update'
          when 'delete'
            model.clear {silent:true}
        if reply.success and options.success then options.success(model,0)
        else if not reply.success and options.error then options.error(model,reply.info)

class Models.Base extends Model
  rights: ''
  initialize: ->
    @urlRoot = Config.ajaxRoot + @descriptor.name
  setName:(name)-> 
    @descriptor.name = name
    @urlRoot = Config.ajaxRoot + name
    @
  url: -> @urlRoot+'?id='+@get('id')
  ctx: Config
  context:(newcontext)-> # get or set global context
    if newcontext then @ctx = newcontext
    @ctx

  xport: (opt) -> 
    result = {}
    settings = _({recurse: true}).extend(opt || {})
    process = (targetObj = {}, source) ->
      targetObj.id = source.id || null
      targetObj.cid = source.cid || null
      targetObj.attrs = source.toJSON()
      _.each source, (value, key) ->
        # since models store a reference to their collection
        # we need to make sure we don't create a circular refrence
        if (settings.recurse)
          if (key isnt 'collection') and (source[key] instanceof Backbone.Collection)
            log 'collection!!'
            targetObj.collections = targetObj.collections || {}
            targetObj.collections[key] = {}
            targetObj.collections[key].models = []
            targetObj.collections[key].id = source[key].id || null
            _.each source[key].models, (value, index) ->
              process(targetObj.collections[key].models[index], value)
          else if (source[key] instanceof Backbone.Model)
            targetObj.models = targetObj.models || {}
            process targetObj.models[key], value
    process result, @
    return result

   
  mport: (data, silent) ->
    process = (targetObj, data) ->
      targetObj.id = data.id || null
      targetObj.set data.attrs, {silent: silent}
      # loop through each collection
      if data.collections
        _.each data.collections, (collection, name) ->
          targetObj[name].id = collection.id
          Skeleton.models[collection.id] = targetObj[name]  
          _.each collection.models, (modelData, index) ->
            newObj = targetObj[name]._add {}, {silent: silent}
            process newObj, modelData
      if data.models
        _.each data.models, (modelData, name) ->
          process targetObj[name], modelData
    process @, data
    return @

  template:-> 
    @toJSON()

  getClass: -> 
    return Models.Base

  getDescriptor: ->
    return @descriptor
    
  filterFields: (o)-> 
    out = {}
    self = @
    descrkeys = {}
    $.each @descriptor.columns,-> descrkeys[@field] = {}
    $.each o,(k,v)->
      if descrkeys[k] then out[k] = v
    out
    
  fetchById: (o,id='all') -> 
    collection = new Collection
    modelClass = @getClass()
    collection.model = modelClass
    collection.url = Config.ajaxRoot + @descriptor.name
    collection.fetch _.extend { data: {id:id} }, #options
      success: (collection,response) ->
        collection.model = modelClass  
        o.success(collection,response) if o.success

  fetchAll: (o) -> @fetchById o,'all'

  countAll: (o) -> @fetchAction $.extend action:'countall',o

  fetchTimeSeries:(o)-> # o: {type:<action>,provider_id:<id>}, until:timestamp, since:timestamp, start:e.g. 0, max:e.g. 30 (limit), can also use with 'search' parameters, e.g. pid
    collection = new Collection
    modelClass = @getClass()
    collection.model = modelClass
    collection.url = Config.ajaxRoot + @descriptor.name
    params = {}
    $.each o,(k,v)-> if v isnt null and not $.isFunction(v) then params[k] = v
    collection.fetch
      data: $.extend({id:'all',action:'searchTimeSeries' }, params)
      success: (collection,response) ->
        collection.model = modelClass
        if (o.success)
          o.success(collection,response)
      error: (collection,response) ->
        if (o.error) then o.error(collection,jsonparse(response.responseText))

  fetchAction:(o)-> # options: { action:,+other parameters }, can probably consolidate fetchTimeSeries here too
    collection = new Collection
    modelClass = @getClass()
    collection.model = modelClass
    collection.url = Config.ajaxRoot + @descriptor.name
    params = {}
    $.each o,(k,v)-> if v isnt null and not $.isFunction(v) then params[k] = v
    collection.fetch
      data: $.extend({id:'all'}, params)  # don't need id either probably
      success: (collection,response) ->
        collection.model = modelClass
        if (o.success) then o.success(collection,response)
      error: (collection,response) ->        
        if (o.error) then o.error(collection,jsonparse(response.responseText))  
        
  fetchSearch:(o)-> @fetchAction $.extend action:'search',o

  fetchSearchD:(o)->
    dfr = $.Deferred()
    @fetchSearch $.extend {},o, success:dfr.resolve, error:dfr.fail
    return dfr.promise()

  fetchMostRecent:(o)-> # returns model for most recent
    optObj =
      start:0, max:1,orderby:'timestamp',order:'DESC',success:(collection,response)-> 
        model = if collection.length > 0 then collection.first() else null
        if (o.success) then o.success model,0
    @fetchTimeSeries $.extend({},o,optObj)

  input: (inputClass) ->
    self = @
    if not inputClass then inputClass = 'INPUT'

    $.each @descriptor.columns,->
      selector = inputClass+'.'+@field
      value = $.trim($(selector).val())

      if value and self.get(@field) isnt value
        log "field:"+@field+" value:"+value
        obj = {}
        obj[@field] = value
        self.set obj



class Models.Application extends Models.Base
  descriptor:
    name: 'applications'
  localStores: [ # always start with 'user'
    'email'    
  ]
  initialize: ->
    @clustermap = {}
    super   
    _.extend @,Events    # app events
    log 'Application instance inited'
  getClass: -> 
    return Models.Application
  clearStored:-> 
    $.each @localStores,-> LocalStorage.unset('user.' + @)
  getStored:->
    stored = {}
    $.each @localStores,-> stored[@] = LocalStorage.get('user.' + @)
    stored
  loadApp:(o)->
    self = @
    defer = 0
    maybeComplete = ->
      if --defer then return         
      
      if Config.schedule then $.each Config.schedule,(name,event)->  
        if event.repeat then Config.cron event.repeat, -> 
          log 'app event ' + name + ' triggering at ' + tb.date().toString() 
          self.trigger name,event   
        else
          log 'no repeat options for event ' + name
        return true
        
        
      if self.error 
        o.error {info:self.error} if o.error
      else
        o.success() if o.success
    err = -> 
      self.error = 'app load error'
      maybeComplete()
      
            
    startGameRound = ->
      
      
      # log Config.cGame.get('round')+' '+Config.maxRounds
      current_round = Config.cGame.get('round')*1+1      
      
      log 'try start round '+current_round

      if current_round <= Config.maxRounds      
          
          if current_round*1 > 1            
            showOverlay('#round_reminder_'+current_round, '350px', '130px')
            $("#round_ok_"+current_round).unbind('click')
            $("#round_ok_"+current_round).click ->
              removeOverlay('#new_round')
              return false
              
          
          log 'Config Round '+Config.cRound+ 'current_round '+ current_round

          if Config.cUserGame.get('user_type') == 'user'
          
            optsMsg = 
              message_type:'start_new_round'
              round: current_round          
              gameid:Config.cGameId
              roomid:Config.cRoomId

              user:''          
              message:'New round started ' + current_round

            log 'new round'  
            console.log(optsMsg)
            SS.server.app.sendMessage optsMsg, (response) ->
              if response.error 
                alert(response.error)            
              else 
                log 'message sent ok'  


          
            # update user game  specific fields

           
            swaps_available = Config.cUserGame.get('swaps_available')*1            
            swaps = Config.cUserGame.get('swaps')*1 
            
            log 'START NEW ROUND '+swaps_available+' '+swaps
            
            
            ###
            log 'SWAPS AVAILALBE '+swaps_available
            
            if swaps_available == 1 
              swaps_available = 1
            else
              if swaps_available == 2 # he used share
                swaps_available = swaps_available - swaps
                
                
                
            
                                 
                        
            #check if share was pressed
            if swaps_available > 2 
              swaps_available = 2 
            else 
              swaps_available = 1
                    
            log 'SWAPS AVAILALBE 2'+swaps_available
            ###
            
            swaps_available = 1
            
            Config.cUserGame.set 'swaps_available':swaps_available
            Config.cUserGame.set 'swaps':0            
            Config.cUserGame.save {}
                        
            # adding new verification - this should not happen
            if !Config.cUserGame.get('blocks_available') then Config.cUserGame.set 'blocks_available':1
            
            # swaps_now = Config.cUserGame.get('swaps_available')*1 - Config.cUserGame.get('swaps')*1            
            swaps_now = 1
            
            
            blocks_now = Config.cUserGame.get('blocks_available')*1 - Config.cUserGame.get('blocks')*1
            
            # adding new verification - this should not happen
            # if swaps_now > 2 then swaps_now = 2
            # if swaps_now < 0 then swaps_now = 0
            
            if blocks_now > 1 then blocks_now = 1
            if blocks_now < 0 then blocks_now = 0
            
            log 'we are updating blocks '+blocks_now + ' '+Config.cUserGame.get('blocks_available')*1+' '+Config.cUserGame.get('blocks')*1 

            $("#swaps_available").html(swaps_available)
            $("#blocks_available").html(blocks_now)
            
            
            # unblock all the prizes            
            
            prizesObj = $(".prizes")
            $.each prizesObj, (index, value) ->
              pos_string = $(value).attr('id').split('_')
              pos = pos_string[1]
              $("blocks_"+pos).val(0)
              $("swaps_"+pos).val(0)
              
            # end unblock all the prizes  
            
          
          else
            #spectator view
            if current_round*1 == 2
              $('#round2_coupchance #ok').click (event) ->
                 event.preventDefault() 
                 removeOverlay('#round2_coupchance')
                 return false
              showOverlay('#round2_coupchance', '350px', '130px')
                
            else
              if current_round*1 == 3
                $('#round3_coupchance #ok').click (event) ->
                  event.preventDefault() 
                  removeOverlay('#round3_coupchance')
                  return false
                showOverlay('#round3_coupchance', '350px', '130px')
		
			
			
              
              
            
            
          # create new count down for the new round
          if Config.cUserGame.get 'interval_start_round'          
            log 'we have already an interval started'
            interval = Config.cUserGame.get 'interval_start_round'          
            window.clearInterval(interval)

          countDownRound = new Models.Countdown()
          optsStartGameRound = {'target_id':"#timergame", 'start_time':Config.timeRound, 'action_on_finish':endGameRound}
          countDownRound.init optsStartGameRound
          Config.cUserGame.set 'interval_start_round':countDownRound.interval
          
       else        
                    
        if Config.cUserGame.get('user_type') == 'user'            
          
          log 'HERE WE HAVE NOW FINISH GAME'
          optsMsg = 
              message_type:'finish_game'            
              gameid:Config.cGameId
              roomid:Config.cRoomId
              round:Config.cGame.get('round')
              message:'Finish Game'


          SS.server.app.sendMessage optsMsg, (response) ->
            if response.error 
              alert(response.error)            
            else 
              log 'message sent ok'  
        
        


    endGameRound = ->      
        
        log 'try to end round '+ Config.cGame.get('round')
        
        if Config.cUserGame.get('user_type') == 'user'            
        
          optsMsg = 
              message_type:'end_round'            
              gameid:Config.cGameId
              roomid:Config.cRoomId
              round:Config.cGame.get('round')
              message:'End round '+Config.cGame.get('round')



          log 'end round'  
          console.log(optsMsg)
          SS.server.app.sendMessage optsMsg, (response) ->
            if response.error 
              alert(response.error)            
            else 
              log 'message sent ok'  
        
        
        
        
    #here all the game logic should happen with the server messages    
    renderMessage = (params, channelName) -> 
    
      # console.log('channel name' + channelName)
      
      ###
      # console.log(Config.cRoomId) 
      log 'render params'
      console.log(params)
      log Config.cRoomId
      log Config.cGameId 
      ###
      
      # if +Config.cRoomId == +params.roomid and +Config.cGameId == +params.gameid
      
      switch params.message_type
        
        when 'start_new_game'

          log 'BROADCAST START GAME ONLY ONCE '


          # update things in the current screen
          $("#text_time").html('Time left in this round:')


          if Config.cUserGame.get('user_type') == 'user'      
            # Models.Game::unlockGame {}
            blocks_available = Config.cUserGame.get('blocks_available')*1            
            Config.cUserGame.set 'blocks_available':blocks_available*1+1            
            Config.cUserGame.save {}

            blocks_now = Config.cUserGame.get('blocks_available')*1 - Config.cUserGame.get('blocks')*1
            $("#blocks_available").html(blocks_now)
            # end updating all the screens


          Config.cRound = 0
          Config.cEndRound = 0

          Config.cGame.set 'game_started':1
          Config.cGame.set 'round':0     

          # Config.cGame.save {}    


          if Config.cUserGame.get('user_type') == 'user' 
            # reattached onclick function
            log 'we shoudl reatched click function'
            Views.PlayGamePageContent::attach_click_function {only_attach:true}


          params.time = tb.getMinSec()

          # announce the game has started            
          $('#templates-message').tmpl(params).appendTo('#feed')
          # SS.client.scroll.down('#feed', 450)

          if Config.cUserGame.get 'start_game_intval'               
            log 'we have already an interval started'
            interval = Config.cUserGame.get 'start_game_intval'
            window.clearInterval(interval)                   

          startGameRound {}



        when 'start_new_round'

          if Config.cRound*1 == params.round

            log 'round already started: '+params.round

          else


            log 'user game before starting new round'
            console.log(Config.cUserGame)

            # do here all the neccesaries 
            $("#round").html(params.round)
            Config.cRound = params.round

            log 'GLOBAL CONFIG ROUND '+Config.cRound

            Config.cGame.set 'round':params.round

            # Config.cGame.save {}

            # log 'Swaps available per total per user game '+Config.cUserGame.get('swaps_available')

            params.time = tb.getMinSec()
            # announce new round
            $('#templates-message').tmpl(params).appendTo('#feed')
            # SS.client.scroll.down('#feed', 450)

        when 'end_round'  

          if Config.cGame.get('round') <= Config.maxRounds

              if Config.cEndRound*1 != params.round*1

                Config.cEndRound = params.round
                log 'config '+Config.cRound + ' opts '+params.round

                params.time = tb.getMinSec()               
                # announce end round
                $('#templates-message').tmpl(params).appendTo('#feed') 

                startGameRound {}
              else
                log 'round '+params.round*1+' already finished'
          else
            log 'GAME SHOULD BE FINISHED'



        when 'finish_game'
          log 'BROADCAST THE GAME HAS FINISH'


          Config.cGame.set 'game_started':2

          if Config.cUserGame.get('user_type') == 'user' 

            pos = Config.cUserGame.get('pos')                        
            win_prizeid = $("#prizeid_"+pos).val()            
            prize_name = $("#prize_content_"+pos).children(".overlay").children(".info").children(".title").val()                        
            $("#game_over_product").html(prize_name) 

            $("#claim_prize").click ->
              Config.appRouter.navigate '#claimprize', trigger:true
              return false

            showOverlay('#game_over', '450px', '140px');

          else

            log 'spectator finis game'
            showOverlay('#game_over', '450px', '140px');


                 


        #show message in the chat room box
        when 'messages_box'
          if +Config.cRoomId == +params.roomid and +Config.cGameId == +params.gameid
            params.time = tb.getMinSec()
            $('#templates-message').tmpl(params).appendTo('#feed')
            SS.client.scroll.down('#feed', 450)

        when 'new_user'

          if +Config.cRoomId == +params.roomid and +Config.cGameId == +params.gameid
            prizeContainerObj = $("#prize_"+params.pos)



            if Config.cPrizeId != params.prizeid

              # prizeContainerObj.css('border', '2px green solid')
              prizeContainerObj.find('.user').html(params.user)

              $("#user_"+params.pos).val(params.user) 

              #set prize id and ugid in page
              $("#prizeid_"+params.pos).val(params.prizeid)
              $("#ugid_"+params.pos).val(params.ugid)

              if $("#actions_"+params.prizeid).length
                $("#actions_"+params.prizeid).css('display', 'block') 
                $("#swap_actions_"+params.prizeid).css('display', 'block')                

              if $("#no_user_"+params.prizeid).length
                $("#no_user_"+params.prizeid).css('display', 'none')





            # push a message into chatroom also regardin new user arrived
            log 'we should push for spectator also'
            paramsMsg = {user:'', message:params.message}
            paramsMsg.time = tb.getMinSec()
            $('#templates-message').tmpl(paramsMsg).appendTo('#feed')
            SS.client.scroll.down('#feed', 450)


        when 'block_gift'

          if +Config.cRoomId == +params.roomid and +Config.cGameId == +params.gameid
            #update in all screens number of blocks for the pos
            if Config.cUserGame.get('user_type') == 'user'               

              $("#blocks_"+params.pos).val(params.blocks)

            # push the message            
            paramsMsg = {user:'', message:params.message}
            paramsMsg.time = tb.getMinSec()
            $('#templates-message').tmpl(paramsMsg).appendTo('#feed')
            SS.client.scroll.down('#feed', 450)

            log 'how user looks after block'
            console.log(Config.cUserGame)


        when 'swap_gift_unblock'

          if +Config.cRoomId == +params.roomid and +Config.cGameId == +params.gameid
            
            if Config.cUserGame.get('user_type') == 'user'                             
              $("#blocks_"+params.posSwap).val(0)
              $("#swaps_"+params.posUser).val(params.swapsUser)

            # push the message            
            paramsMsg = {user:'', message:params.message}
            paramsMsg.time = tb.getMinSec()
            $('#templates-message').tmpl(paramsMsg).appendTo('#feed')
            SS.client.scroll.down('#feed', 450)

        when 'swap_gift'

          #update hidden values in all screens
          # log 'swapping'
          # console.log(params) 
          
          if +Config.cRoomId == +params.roomid and +Config.cGameId == +params.gameid

            $("#prizeid_"+params.posUser).val(params.prizeidSwap) 
            $("#prizeid_"+params.posSwap).val(params.prizeidUser) 

            $("#swaps_"+params.posUser).val(params.swaps)

            prizeContentUser = $("#prize_content_"+params.posUser).html()
            prizeContentSwap = $("#prize_content_"+params.posSwap).html()                                  


            $("#prize_content_"+params.posUser).html(prizeContentSwap)
            $("#prize_content_"+params.posSwap).html(prizeContentUser)


            # updating the config for general prize set for this user game
            if Config.cUserGame.get('user_type') == 'user'


              if Config.cUserGame.get('pos')*1 == params.posUser*1                

                $("#swap_actions_"+params.prizeidUser).css('display', 'block')
                $("#block_actions_"+params.prizeidUser).css('display', 'none')  

                $("#swap_actions_"+params.prizeidSwap).css('display', 'none')
                $("#block_actions_"+params.prizeidSwap).css('display', 'block')

                Config.cPrizeId = params.prizeidSwap
                Config.cUserGame.set 'prizeid':params.prizeidSwap

              else 
                if Config.cUserGame.get('pos')*1 == params.posSwap*1            

                  $("#swap_actions_"+params.prizeidSwap).css('display', 'block')
                  $("#block_actions_"+params.prizeidSwap).css('display', 'none')

                  $("#swap_actions_"+params.prizeidUser).css('display', 'none')
                  $("#block_actions_"+params.prizeidUser).css('display', 'block') 

                  Config.cPrizeId = params.prizeidUser
                  Config.cUserGame.set 'prizeid':params.prizeidUser

                else

                  $("#swap_actions_"+params.prizeidSwap).css('display', 'block')
                  $("#block_actions_"+params.prizeidSwap).css('display', 'none')

                  $("#swap_actions_"+params.prizeidUser).css('display', 'block')
                  $("#block_actions_"+params.prizeidUser).css('display', 'none') 

              ###
              if Config.cPrizeId == params.prizeidUser
                log 'updategin for '+Config.cPrizeId 

                $("#swap_actions_"+params.prizeidSwap).css('display', 'none')
                $("#block_actions_"+params.prizeidSwap).css('display', 'block')

                $("#swap_actions_"+params.prizeidUser).css('display', 'block')
                $("#block_actions_"+params.prizeidUser).css('display', 'none')  

              if Config.cPrizeId == params.prizeidSwap
                log 'updategin for '+Config.cPrizeId                            

                $("#swap_actions_"+params.prizeidSwap).css('display', 'block')
                $("#block_actions_"+params.prizeidSwap).css('display', 'none')

                $("#swap_actions_"+params.prizeidUser).css('display', 'none')
                $("#block_actions_"+params.prizeidUser).css('display', 'block') 




              if Config.cPrizeId == params.prizeidUser  
                log 'updategin for '+Config.cPrizeId              
                Config.cPrizeId = params.prizeidSwap
                Config.cUserGame.set 'prizeid':params.prizeidUser


              if Config.cPrizeId == params.prizeidSwap
                log 'updategin for '+Config.cPrizeId              
                Config.cPrizeId = params.prizeidUser
                Config.cUserGame.set 'prizeid':params.prizeidUser
              ###

              $("#overlay_"+params.prizeidSwap).css("display", "none") 

              log 'how user looks after swap'
              console.log(Config.cUserGame)


              log 'we shoudl reatched click function'
              Views.PlayGamePageContent::attach_click_function {only_attach:true}


            # push the message
            paramsMsg = {user:'', message:params.message}
            paramsMsg.time = tb.getMinSec()
            $('#templates-message').tmpl(paramsMsg).appendTo('#feed')
            SS.client.scroll.down('#feed', 450)

    ###      
    else
       log 'here we have an error - another room is being entered'
    ###     
         
              
    if not server   
      
      defer++ 
      
      #here we should have a validation for gameId and roomId and prizeId and move these into load app
            
      self.set cPrizeId:0
      self.set cRoomId:0
          
      if LocalStorage.get 'cGameId'
        self.set cGameId:LocalStorage.get 'cGameId'
        cGameId = LocalStorage.get 'cGameId'
      else
        self.set cGameId:0
        cGameId = 0
        
      if LocalStorage.get 'cUgId'
        self.set cUgId:LocalStorage.get 'cUgId'  
        cUgId = LocalStorage.get 'cUgId'  
      else
        self.set cUgId:0
        cUgId = 0  

                 
      
      
      SS.server.app.init (response) -> 
        $.each response,(idx)-> Config[idx] = @   # get whatever configs server sends
        log 'server says:' + Config.msg        
        #if response then $('#main').show() else displaySignInForm()
        
        maybeComplete()
      
      if  cUgId*1  > 0
        defer++
        userGameObj = new Models.UserGame id: cUgId
        userGameObj.fetch success:(userGameObj)->
          self.set cUserGame:userGameObj
          self.set cPrizeId:userGameObj.get('prizeid')
          self.set cRoomId:userGameObj.get('roomid')
          maybeComplete()  
      else
        self.set cUserGame:null
        
      if  cGameId*1  > 0
        defer++
        gameObj = new Models.Game id: cGameId
        gameObj.fetch success:(gameObj)->
          self.set cGame:gameObj                    
          maybeComplete()  
      else
        self.set cGame:null  
        
           
      cRoomId = self.get('cRoomId')      
      # Bind to new incoming message event
      SS.events.on 'newMessage', renderMessage
     
    else
      log 'server side'
      # console.log(LocalStorage)
      # console.log(session)
      true
      
 
    

class Models.Person extends Models.Base
  
  descriptor:
    name: 'persons'
    columns: [
      { title:'ID',          field:'id'         }          
      { title:'Facebook Id', field:'FacebookId'       }
      { title:'First',       field:'FirstName' }
      { title:'Last',        field:'LastName'  }
      { title:'Initials',    field:'Initials'  }
      
            
    ]

  getClass: -> 
    return Models.Person
  
  viewableName:->
    s = ''
    
    $.trim s 

 
class Models.Game extends Models.Base
  
  descriptor:
    name: 'games'
    columns: [
      { title:'ID',                field:'id'         }          
      { title:'Name',              field:'Name'       }
      { title:'StartDate',         field:'StartDate'  }      
      { title:'Swaps',             field:'swaps'  }  
      { title:'Blocks',            field:'blocks'  }
      { title:'Round',             field:'round'  }  
      { title:'GameStarted',       field:'game_started'  }  
      { title:'GameOpened',        field:'game_opened'  }  
      
    ]

  getClass: -> 
    return Models.Game
    
  lockGame:->
    @fetchAction $.extend {action:'lockGame', id:@id}, {}
    
  unlockGame:->
    @fetchAction $.extend {action:'unlockGame', id:@id}, {}  
    
  lockFetchSaveUnlockGame: (o)->  
    @fetchAction $.extend {action:'lockFetchSaveUnlockGame', id:@id},o
          
  getNextGame: (o)->
    @fetchAction $.extend {action:'getNextGame', id:@id, count:1},o
    
  getGameTime: (o)->
    @fetchAction $.extend {action:'getGameTime', id:@id},o  
  
  

class Models.UserGame extends Models.Base
  
  descriptor:
    name: 'users_games'
    columns: [
      { title:'ID',          field:'id'         }          
      { title:'GameId',        field:'gameid', reference:'Game' }
      { title:'UserId',        field:'userid', reference:'User' }
      { title:'RoomId',        field:'roomid', reference:'Room' }
      { title:'PrizeId',       field:'prizeid', reference:'Prize' }

      { title:'date',   field:'date'  }   
      { title:'datetime',   field:'datetime'  }
      
      { title:'Swaps',       field:'swaps'  }  
      { title:'Blocks',      field:'blocks'  }  
      
      { title:'SwapsAvailable',       field:'swaps_available'  }  
      { title:'BlocksAvailable',      field:'blocks_available'  }  
      
            
      { title:'Pos',      field:'pos'  }  
      
      { title:'PrizeIdUser',       field:'prizeid_user', reference:'Prize' }
      { title:'PrizeIdSwap',       field:'prizeid_swap', reference:'Prize' }
      
      { title:'UserType',      field:'user_type'  }  
      
                  
    ]

  getClass: -> 
    return Models.UserGame
  
  
  getUserGamePrize: (o) ->
    @fetchAction $.extend {action:'getUserGamePrize', id:@id},o
    
  
        
  
class Models.Prize extends Models.Base
  
  descriptor:
    name: 'prizes'
    columns: [
      { title:'ID',          field:'id'         }          
      { title:'GameId',        field:'GameId', reference:'Game' }
      { title:'room',        field:'Room', reference:'room' }
      
      { title:'photo',        field:'Photo' }
      { title:'photoSmall',       field:'PhotoSmall' }
      { title:'Name',       field:'Name' }
      { title:'Description',       field:'Description' }
      { title:'Price',       field:'Price' }
      { title:'PurchaseUrl',       field:'PurchaseUrl' }
      
      { title:'UserId',       field:'UserId', reference:'User' }
      { title:'Status',       field:'Status' }
      
      { title:'TotalSwaps',       field:'TotalSwaps' }
      { title:'TotalBlocks',       field:'TotalBlocks' }
      { title:'TotalViews',       field:'TotalViews' }
      
      { title:'Uri',       field:'uri' }
      { title:'Sku',       field:'sku' }
      
                        
    ]

  getClass: -> 
    return Models.Prize
  
  getRandomAvailPrize: (o)->
    @fetchAction $.extend {action:'getRandomAvailPrize'},o
    
  getActivePrizesWithUsers: (o)->
    @fetchAction $.extend {action:'getActivePrizesWithUsers'},o  
    


class Models.Room extends Models.Base
  
  descriptor:
    name: 'rooms'
    columns: [
      { title:'ID',          field:'id'   }          
      { title:'Name',        field:'name' }
      { title:'RoomId',        field:'room_id' }
                       
    ]

  getClass: -> 
    return Models.Room
    
 
  getGameRoomsWithPrizes: (o)->
    @fetchAction $.extend {action:'getGameRoomsWithPrizes'},o
    
    
class Models.Coupon extends Models.Base
  
  descriptor:
    name: 'coupons'
    columns: [
      { title:'ID',          field:'id'   }          
      { title:'gameid',        field:'gameid' }
      { title:'userid',        field:'userid' }
      
      { title:'prizeid',        field:'prizeid' }
      { title:'ugid',        field:'ugid' }
      { title:'first_name',        field:'first_name' }
      { title:'last_name',        field:'last_name' }
      
      { title:'address_1',        field:'address_1' }
      { title:'address_2',        field:'address_2' }
      
      { title:'city',        field:'city' }
      { title:'state',        field:'state' }
      { title:'zip',        field:'zip' }
      { title:'subscribe',        field:'subscribe' }
      { title:'email',        field:'email' }      
    ]

  getClass: -> 
    return Models.Room    
  
class Models.Session extends Models.Base
  descriptor:
    name:'sessions'
    columns: [
      { title: 'ID', field: 'id' }
      { title: 'OriginUrl', field: 'origin' }
      { title: 'Login', field: 'login' } 
      { title: 'Settings', field: 'settings' } # local settings
    ]
  getClass: -> Models.Session
  bind:(action,f)-> # listen for an action (name), bind to Config
    SS.events.on action, (message) -> 
      log 'event received from server: ' + stringify message
      # alert(message)
      f message if f
  clear:(key)-> LocalStorage.unset key
  retrieve:(key)-> LocalStorage.get key
  store:(o,persistence)->
    $.each o,(key,val)->
      LocalStorage.set key, val, 60





class Models.Timebase
  constructor:->
    @localDelta = 0
    @second = 1000
    @minute = 60*@second
    @hour = 60*@minute
    @day = 24*@hour
    @week = 7*@day
    @year = 52*@week
    @month = @year / 12
    @never = 1336600000000*2  # arbitrary large number
    @serviceTimeOffset = Config.serviceTimeOffset
    @midday = 12
    @evening = 19
    if not tb then tb = @
    if not server # keep the local time in sync
      @update()
      setInterval @update, Config.heartbeat*1000
      
  update:-> # usually called automatically
    self = @
    
    SS.server.app.time (result)-> 
      self.localDelta = new Date().getTime() - result.timestamp
      Config.trigger 'heartbeat'
      
  date:(s)-> # if s is 'xxxx-xx-xx' this date will be UTC, to get local date as string use .format('UTC:yyyy-mm-dd')
    if not s then return new Date @time()
    if typeof s is 'number' then return new Date s
    sd = @splitdate s
    
    #console.log(sd)
    
    if sd.length == 3
      new Date Date.UTC(sd.y,sd.m,sd.d)
    else
      log 'here for new date'
      console.log(sd)
      new Date Date.UTC(sd.y*1,sd.m*1,sd.d*1, sd.hour*1, sd.min*1, sd.sec*1)
      
    
  splitdate:(s)->
    #console.log(s)
    ymd = s.split('-')  # yyyy-mm-dd
    if s.length == 10
      { y:ymd[0],m:+ymd[1]-1,d:ymd[2] }
    else
      comp = ymd[2].split('T')
      comp1 = comp[1].split(':')      
      comp2 = comp1[2].split('.')
           
      
      object_for_return = { y:ymd[0],m:+ymd[1]-1,d:comp[0],hour:comp1[0], min:comp1[1], sec:comp2[0]}
      
      
  set_mysql_date: (s)->
    s_obj = @splitdate(s)    
    
    mysql_date = s_obj.y+'-'+s_obj.m+'-'+s_obj.d
    
    if s_obj.hour
      mysql_date +=' '+s_obj.hour+':'+s_obj.min+':'+s_obj.sec
      
    return mysql_date  
    

  set_mysql_date_from_server: (dateObj)->
    year = dateObj.getFullYear()
    month = dateObj.getMonth() *1 + 1
    if month.length == 1 then month = "0"+month
    day = dateObj.getDate()
    if day.length == 1 then day = "0"+day
    
    hour = dateObj.getHours()
    if hour.length == 1 then hour = "0"+hour    
    min = dateObj.getMinutes()
    if min.length == 1 then min = "0"+min    
    sec = dateObj.getSeconds()
    if sec.length == 1 then sec = "0"+sec

    return year+'-'+month+'-'+day+' '+hour+':'+min+':'+sec
    
    
    
  
  utc:(s)->
    sd = @splitdate(s)
    
    if sd.length == 3    
      Date.UTC(sd.y,sd.m,sd.d)
    else     
      Date.UTC(sd.y,sd.m,sd.d, sd.hour, sd.min, sd.sec)
      
    
  time:-> +new Date() - @localDelta
  
  dateUtc: -> 
    now = new Date() 
    now_utc = new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(),  now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds())
    now_utc
  
  # result in ticks!
  timezoneOffset:-> 
    return -60000 * new Date().getTimezoneOffset()
    # bug in chrome, not fixed yet ... 
    now = @date()
    jan1 = new Date(now.getFullYear(), 0, 1, 0, 0, 0, 0)
    june1 = new Date(now.getFullYear(), 6, 1, 0, 0, 0, 0)
    temp = jan1.toGMTString()
    jan2 = new Date(temp.substring(0, temp.lastIndexOf(" ")-1))
    temp = june1.toGMTString()
    june2 = new Date(temp.substring(0, temp.lastIndexOf(" ")-1))
    std_time_offset = (jan1 - jan2) # / (1000 * 60 * 60)
    daylight_time_offset = (june1 - june2) # / (1000 * 60 * 60)
    dst = '0'
    if (std_time_offset isnt daylight_time_offset) 
      # positive is southern, negative is northern hemisphere
      hemisphere = std_time_offset - daylight_time_offset;
      if hemisphere >= 0
        std_time_offset = daylight_time_offset;
      dst = '1'
      return daylight_time_offset
    std_time_offset
  
  serviceTime: (date)-> # date is ISO string, and interpreted as utc date
    if typeof date isnt 'string' then log 'bad date in serviceTime'
    utcdate = @date date
    +utcdate + @serviceTimeOffset
    
    
  serviceMidnightTimestamp: (s)-> 
    shifted = s + tb.serviceTimeOffset
    shiftedObj = new Date(shifted)    
    shiftedMidnightDate = shiftedObj.format("UTC:yyyy-mm-dd") 
    shiftedMidnight = +tb.date(shiftedMidnightDate)
    shiftedMidnight - tb.serviceTimeOffset # back to utc

  
  dateOfDayFollowing: (fromDateStr,targetDay='Thursday')->  
    now = @date fromDateStr # as if utc
    
    while now.format('UTC:dddd') isnt targetDay 
      now = new Date(+now + tb.day)      
    now.format 'UTC:yyyy-mm-dd' # return
    
  dayPart: (hour)-> if hour < @midday then 'morning' else if hour > @evening then 'evening' else 'midday'
  
    
  slashDate: (dateStr)-> dateStr.substring(5).replace('-','/') # yyyy-mm-dd to mm/dd
  slashDateFull: (dateStr)-> dateStr.substr(5,2)+'/'+dateStr.substr(8,2)+'/'+dateStr.substr(2,2)
  
  # weird but handy day functions
  dayFrom: (today,offset=0) -> 
    if typeof today is 'string' # xxxx-xx-xx
      s = @date(+@date(today) + offset).format 'UTC:yyyy-mm-dd'
    else
      s = @date(+@date(today) + offset).format 'yyyy-mm-dd'
    s
  dayBefore: (today,num=1) -> @dayFrom today,-@day*num
  dayAfter: (today,num=1) -> @dayFrom today,@day*num
  weekBefore: (today,num=1) -> @dayFrom today,-@week*num
  weekAfter: (today,num=1) -> @dayFrom today,@week*num
  monthBefore: (today,num=1) -> @dayFrom today,-@month*num
  monthAfter: (today,num=1) -> @dayFrom today,@month*num
  
  numDays: (start,finish) -> Math.round((+@date(finish) - +@date(start)) / @day) # input:yyyy-mm-dd
  numHours: (start,finish) -> Math.round((+@date(finish) - +@date(start)) / @hour) # input:yyyy-mm-dd
  numMinutes: (start,finish) -> Math.round((+@date(finish) - +@date(start)) / @minute) # input:yyyy-mm-dd
  numSeconds: (start,finish) -> Math.round((+@date(finish) - +@date(start)) / @second) # input:yyyy-mm-dd
  
  numDaysUntil: (finish) ->  @numDays @date().format('yyyy-mm-dd'),finish # @numDays @date().format('UTC:yyyy-mm-dd'),finish
  
  getHoursMinutesSeconds: (seconds) ->
    
    r = seconds % (60*60*@second)
    
    h = (seconds - r) / (60*60*@second)
    seconds = seconds - h*(60*60*@second)
    
    r = seconds % (60*@second)    
    m = (seconds - r) / (60*@second)     
        
    seconds = seconds - m*(60*@second)            
    s = Math.round(seconds / @second)   
    
    if h.length == 1 then h = "0"+h
    if m.length == 1 then m = "0"+m
    if s.length == 1 then s = "0"+s
    
    return  {hours:h, minutes:m, seconds:s}
    
  getMinSec: ->
    date = @date()
    
    min = date.getMinutes()
    if min.length == 1 then min = "0"+min
    
    sec = date.getSeconds()
    if sec.length == 1 then sec = "0"+sec
    
    min+":"+sec
  
      
  

class Models.UrlParams extends Models.Base
  descriptor:
    name:'urlparams'

  initialize:->
    super
    self = @

  checkDate: (dateStr)->
    try     
      testDate = tb.date(dateStr)
      convertedDate = testDate.format 'UTC:yyyy-mm-dd'
      return false if dateStr isnt convertedDate      
      now = tb.date().format 'UTC:yyyy-mm-dd'
      return false if convertedDate < now
    catch ex
      return false
    return true

  
  validate: (params)->
    if not params then params = {}    
    params


class Models.Tracker extends Models.Base
  descriptor:
    name:'tracker'

  initialize:->
    super
    self = @
    @qParams = {}
    if server then return
    qUrl = unescape(window.location.search)
    qUrl= qUrl.substring(qUrl.indexOf('?') + 1)
    if qUrl.indexOf('&') is 0 then qUrl = qUrl.substring(1)
    qUrlParams = if qUrl then qUrl.split('&') else []
    gmtrk = LocalStorage.get('gmtrk')
    trkParams = if gmtrk then gmtrk.split('&') else []
    #trkParams = trkParams.concat qUrlParams

    $.each trkParams,->
      if @ is '' then return true
      pair = @split '='   
      if pair[0] not in ['destination']
        self.qParams[pair[0]] = pair[1]
      true

    $.each qUrlParams,->
      if @ is '' then return true
      pair = @split '='      
      self.qParams[pair[0]] = pair[1]
      true

    qs = []
    $.each self.qParams,(k,v)-> qs.push k+'='+v
    LocalStorage.set 'gmtrk',qs.join('&')

    # ab paths
    @abpath = @qParams.abpath
    if @abpath
      LocalStorage.set 'abpath', @abpath, 7
    else    
      @abpath = LocalStorage.get 'abpath'
      if not @abpath
        @abpath = if Math.random() <= 0.5 then 'A' else 'B'
        LocalStorage.set 'abpath', @abpath, 7
    log "abpath:" + @abpath
    
  
  abPath:(path)-> # set or get
    if path?
      @abpath = path
      LocalStorage.set 'abpath', @abpath, 7
    @abpath                                
    
  conversions:(options)->

  queryParams:-> @qParams
  
  getParam: (name)->@qParams[name]
  

class Models.Countdown extends Models.Base
  constructor:  ->
    log 'constructor'

  init: (opts) ->
    
    @target_id = opts.target_id
    @start_time = opts.start_time
    @action_on_finish = opts.action_on_finish
        
    
    @reset()
    window.tick = =>
      @tick()
    @interval = setInterval(window.tick, 1000)

  reset: ->
    time = @start_time.split(':')
    @hours = (time[0])*1
    @minutes = (time[1])*1
    @seconds = (time[2])*1
    
    #console.log(@hours+" - "+@minutes+" "+@seconds)
    
    @updateTarget()

  tick: ->
    [seconds, minutes, hours] = [@seconds, @minutes, @hours]
         
    if seconds > 0 or minutes > 0 or hours > 0
    
      if minutes is 0 and seconds is 0
        @minutes = 59
        @hours = hours-1
        @seconds = 59
      else
        if seconds is 0
          @minutes = minutes - 1
          @seconds = 59
        else
          @seconds = seconds - 1
       
              
    if @seconds*1 == 0 and @minutes *1 == 0 and @hours == 0
      clearInterval(@interval)
      @action_on_finish {}
    else
      @updateTarget()

  updateTarget: ->
    seconds = @seconds
    seconds = '0' + seconds if seconds < 10
    
    minutes = @minutes
    minutes = '0' + minutes if minutes < 10
    
    hours = @hours
    hours = '0' + hours if hours < 10
    
    
    
    $(@target_id).html(hours + ":" + minutes + ":" + seconds)
    
    
