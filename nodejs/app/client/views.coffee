
Backbone = @Backbone
Views = @Views = {}
Models = @Models
server = false
Config = @Config

log = {}
stringify = (s) -> JSON.stringify s
jsonparse = (s) -> Config.jsonparse(s)

Model = Backbone.Model
Collection = Backbone.Collection
View = Backbone.View

# support functions
tb = tbDate = tbTime = DSTOffset = null

compareDates = (lowerdate,greaterdate) ->
  ldate = fromDPtoDate(lowerdate)  
  gdate = fromDPtoDate(greaterdate)
  return gdate > ldate

fromDPtoDate = (DPValue) ->
  parts = DPValue.split('/')
  return new Date("20"+parts[2],+parts[0]-1,parts[1])
  

images_root = null

templateMgr = 
  templates: {}
  get: (id, cb) ->
    version = Config.views.getVersion(id) # every template used by views has a version <name>-<version>.html
    idstr = id+'-'+version
    tObj = @templates[idstr]
    if not tObj then @templates[idstr] = tObj = { callbacks: [], template:null, requested:false }
    if tObj.template 
      if cb then cb tObj.template
      return
    if cb then tObj.callbacks.push cb
    # Only request once if we are using the callbacks array
    if not tObj.requested
      $.get "templates/" + idstr + ".tmpl", (template)-> # use public/templates/*.html
        tObj.template = template
        $.each tObj.callbacks, -> @ template
      tObj.requested = true
  clear:-> @templates = {} # clear the templates (good for debugging)



Views.init = -> # client only, problems with include order
  Config = window.Config
  Models = window.Models
  log = Config.log 'views'
  tb = Config.timebase 
  tbTime = (s)-> tb.time(s)
  tbDate = (s)-> tb.date(s)
  DSTOffset = Config.DSTOffset
  images_root = '/images/'
  
    
  # precache some things needed by everybody on load
  $.each ['footer_public','header_public'],->
    templateMgr.get @
    true
  @

  

_progerr = (identity)-> 
  log identity + ':programmer error!!!'
  0
  
# base classes----------
class Base extends View
  name: 'default'
  initialize:-> if @options.parent then $(@options.parent).append @$el  
  destroy: -> @$el.remove()

class Views.Page extends Base
  destroy:->
    super
    do @header.destroy if @header
    do @footer.destroy if @footer
    log 'page destroy'
    
class Views.Panel extends Base
class Views.Widget extends Base
class Views.Overlay extends Views.Panel
  initialize:->
    super
    #$(@el).addClass 'overlay'
  render: ->

class Views.Message extends Views.Page
  initialize:-> # options: html, effect, opacity
    log 'initialize'
  render:->
    # overlay
    self = @
    $el = @$el
    $el.html @options.html
    @$ol = $el.overlay
      effect: @options.effect or 'fade'
      opacity: @options.opacity or 0.6
      closeOnClick: if @options.closeOnClick isnt 'undefined' then @options.closeOnClick else true
      onShow: ->
        $(self.options.parent).append $el
        # $el.css width:"80%", height:"80%" no work
        if (self.options.onShow) then self.options.onShow() 
        log 'showing'
      onHide: -> 
        log 'removing'
        $el.remove()
      nopadding: if @options.nopadding isnt 'undefined' then @options.nopadding else false
      topoffset: if @options.topoffset then @options.topoffset else 100
      width    : if @options.width then @options.width else null
  message:(s)->
    @$el.html s
  # Modify the close behavior.  Particularly for click 
  modifyclose:(opts)->
    $el = @$el
    opts = $.extend {}, { effect : 'fade', onHide:-> $el.remove() }, opts
    @$ol.overlay.modify(opts)
  destroy:->
    $('.overlay').remove() # no idea how you're really supposed to make this go away programmatically
    @$el.remove()


class Views.Header extends Views.Panel  
  
  initialize: ->
    super
    $el = @$el
    $el.attr id:"header_public"
    @name = 'header_public'    
              
  render: ->
    self = @
    user = Config.user
    opts = images_root:images_root
    if self.name is 'header_public' 
      templateMgr.get @name, (t)-> self.$el.html _.template t,opts      
    else
      log 'we are here'
        
  destroy:->
    if @notifyBind
      # log 'unbinding header heartbeat'
      Config.off 'heartbeat',@update,@

     

class Views.Footer extends Views.Panel
  initialize:->
    @$el.attr {id:"footer" }
    if @options.parent then $(@options.parent).append @$el
    @name = 'footer_public'
  render:->
    self = @
    templateMgr.get self.name, (t)->
      self.$el.html _.template(t,{year:tbDate().getFullYear()})      
     
    @


class Views.PublicPage extends Views.Page
  initialize:->
    super
        
    @header = new Views.Header { parent: @el }        
    
    @name = 'public_page_main'
    @defaultContent = new Views.PublicContent $.extend({},@options,{ parent: @el, name: @name })
     
    @footer = new Views.Footer { parent: @el }
  
  render: ->
    self = @
    @header.render()
    @defaultContent.render ()->      
      self.footer.render()
    
class Views.PublicContent extends Views.Panel
  initialize:->
    if @options.parent then $(@options.parent).append @$el
    @name = @options.name
    @$el.attr('id','wrap')
  
  render:(cb)->
    self = @
    status = parent: null, referrer: document.referrer, user: LocalStorage.get('user.last')
    
    # here we should do the facebook connect
    
    opts = {images_root:images_root}

    templateMgr.get self.name, (t)->
      self.$el.html _.template t, opts

      $("#pregame").click ->
        Config.appRouter.navigate '#register', trigger:true

      if cb then cb()


class Views.RegisterPage extends Views.Page
  initialize:->
    super
        
    @header = new Views.Header { parent: @el }
            
    @name = 'register'
    
    @defaultContent = new Views.RegisterPageContent $.extend({},@options,{ parent: @el, name: @name })
     
    @footer = new Views.Footer { parent: @el }
  
  render: ->
    self = @
    @header.render()
    @defaultContent.render ()->      
      self.footer.render()
    
class Views.RegisterPageContent extends Views.Panel
  initialize:->
    if @options.parent then $(@options.parent).append @$el
    @name = @options.name
    @$el.attr('id','wrap')
  
  render:(cb)->
    self = @
        
    opts = {images_root:images_root}
        
    name = self.name
    $el = self.$el
    
    
    countdown = null
    
          
       
    gameObj = new Models.Game
    gameObj.getNextGame success:(gameObj)->
    
      if gameObj.length == 1
        
        gameObj = gameObj.at(0)
        Config.cGameId = gameObj.id
        Config.cGame = gameObj
              
        ###       
       console.log(Config.cGame)
       StartDateMysql = tb.set_mysql_date(Config.cGame.get('StartDate'))
       console.log(StartDateMysql)
       ###
       
       
       
       
              
        LocalStorage.set 'cGameId', Config.cGameId, 7
              
        startGame = ->
          log 'start game top'
          
        time_to_game =  gameObj.get('time_to_game')*tb.second
       
        if time_to_game*1 > 0          
          HourMinSec = tb.getHoursMinutesSeconds(time_to_game)                                  
          countdown = new Models.Countdown()
       
          
        
       
       
        templateMgr.get name, (t)->
          $el.html _.template t, opts
       
          if time_to_game*1 > 0 
          
            if countdown  
              opts = {target_id:'#timer', 'start_time':HourMinSec.hours+":"+HourMinSec.minutes+":"+HourMinSec.seconds, 'action_on_finish':startGame}
              countdown.init opts
              # log 'interval'+countdown.interval
          else
            log 'game already started'
            $('#timer').html('Started')
          
          $(".next_game").click ->                  
            Config.appRouter.navigate '#chooseroom', trigger:true   
          
      else
        
        log 'ERROR_DEFAULT no games opened for today - we shoud display something else'        
        Config.appRouter.navigate '#', trigger:true 
        


#choose room spectator

class Views.ChooseRoomSPage extends Views.Page
  initialize:->
    super
        
    @header = new Views.Header { parent: @el }
        
    
    @name = 'choose_room_spectator'
    
    @defaultContent = new Views.ChooseRoomSPageContent $.extend({},@options,{ parent: @el, name: @name })
     
    @footer = new Views.Footer { parent: @el }
  
  render: ->
    self = @
    @header.render()
    @defaultContent.render ()->      
      self.footer.render()
    
class Views.ChooseRoomSPageContent extends Views.Panel
  initialize:->
    if @options.parent then $(@options.parent).append @$el
    @name = @options.name
    @$el.attr('id','wrap')
  
      
  attach_spectator_click_function: ->
    # SET SPECTATOR HERE
    log 'attach the script'
    $("#watch_game").click ->

      # default room id = 3 because the user must enter a room
      userGame = new Models.UserGame
      userGame.set
        roomid:3
        gameid:Config.cGame.get('id')
        prizeid:0
        datetime:tb.time()
        blocks:0
        swaps:0
        blocks_available:0
        swaps_available:0
        user_type:'spectator'

      userGame.save {}, success: (userGame)->                  
        
        Config.cRoomId = 3
        Config.cPrizeId = 0                    
        Config.cUserGame = userGame
        Config.cUgId = userGame.get('id')        
        
        LocalStorage.set 'cUgId', Config.cUgId, 7
        
        
        # console.log(optsMsg)    
        SS.server.app.subscribeToRoom 100, (response) ->
          if response.error 
            alert(response.error) 
            return false
          else 
            log 'subscribe to room ok'  
            Config.appRouter.navigate '#playgame', trigger:true
            return false
      
  render:(cb)->
    
    self = @        
    opts = {images_root:images_root}
    
    templateMgr.get self.name, (t)->
      self.$el.html _.template t, opts
      
      self.attach_spectator_click_function {}
        
      
      
      
            

# choose room view

class Views.ChooseRoomPage extends Views.Page
  initialize:->
    super
        
    @header = new Views.Header { parent: @el }
         
    @name = 'choose_room'
    
    @defaultContent = new Views.ChooseRoomPageContent $.extend({},@options,{ parent: @el, name: @name })
     
    @footer = new Views.Footer { parent: @el }
  
  render: ->
    self = @
    @header.render()
    @defaultContent.render ()->      
      self.footer.render()
    
class Views.ChooseRoomPageContent extends Views.Panel
  initialize:->
    if @options.parent then $(@options.parent).append @$el
    @name = @options.name
    @$el.attr('id','wrap')
  
  
  render_choose_page_content: ->
    
    self = @
    
    name = self.name
    $el = self.$el

    log 'current game '+Config.cGameId 


    tpl_room_details = _.template '

      <div id="<%=room_id%>" class="rooms" data="<%=total_prizes_no%>">        
        <input type="hidden" class="roomid_class" value="<%=roomid%>">
        <%=name%>
      </div>

    '
    
    all_rooms_html = ''

    attach_on_click = (opts) ->
      roomsobj = $(".rooms")
      $.each roomsobj, (index, value)->
        roomid = $(value).children('.roomid_class').val()
        gameid = opts.gameid        
        total_prizes_no = $(value).attr('data')

        $(value).click ->        
          $(value).unbind('click')
          log 'room id '+roomid+'gameid '+gameid+' total_prizes_no '+total_prizes_no
          Models.UserGame::fetchSearch
            roomid:roomid
            gameid:gameid
            user_type:'user'
            success: (users_game)->
              console.log(users_game)
              if users_game.length < total_prizes_no

                # how should we pick prizes? a db function
                #log 'we should be here'  



                Models.Prize::getRandomAvailPrize gameid:gameid, roomid:roomid, success:(prizes)->
                  # console.log(prizes)
                  if prizes.length == 1
                    prize = prizes.at(0)

                    userGame = new Models.UserGame
                    userGame.set
                      roomid:roomid
                      gameid:gameid
                      prizeid:prize.id
                      datetime:tb.time()
                      blocks:0
                      swaps:0
                      blocks_available:0
                      swaps_available:0
                      user_type:'user'

                    userGame.save {}                  
                    # here we should redirect the user in the game screen
                    Config.cRoomId = roomid
                    Config.cPrizeId = prize.id                    
                    Config.cUserGame = userGame
                    
                    Config.cUserGame.set
                      roomid:roomid
                      gameid:gameid
                      prizeid:prize.id
                      datetime:tb.time()
                      blocks:0
                      swaps:0
                      blocks_available:0
                      swaps_available:0
                      user_type:'user'

                    # console.log(optsMsg)    
                    SS.server.app.subscribeToRoom 100, (response) ->
                      if response.error then alert(response.error) else log 'subscribe to room ok'  

                    
                    Config.appRouter.navigate '#playgame', trigger:true
                    return false

                  else
                    # log 'error -  no more prizes - > we should display the proper overlay'
                                        
                    
                    # log 'ERROR_DEFAULT no more prizes, we should have the default page '        
                    # Config.appRouter.navigate '#', trigger:true 
                    Config.appRouter.navigate '#chooserooms', trigger:true
                    return false

              else
                # alert 'no more prizes for you'
                # log 'ERROR_DEFAULT no more prizes, we should have the default page '        
                # Config.appRouter.navigate '#', trigger:true 
                Config.appRouter.navigate '#chooserooms', trigger:true
                return false
                


    displayTemplate = (opts) ->
      templateMgr.get name, (t)->
        $el.html _.template t, opts
        attach_on_click {gameid:opts.gameid}
        


    Models.Room::getGameRoomsWithPrizes gameid:Config.cGameId, success:(rooms)->
      
      # console.log(rooms)
      rooms.each (room)->

        # log room.get('total_users_connected')+' '+room.get('total_prizes_no')
        if room.get('total_users_connected')*1 < room.get('total_prizes_no')*1
          all_rooms_html += tpl_room_details {roomid:room.get('id'), name:room.get('name'), total_prizes_no:room.get('total_prizes_no'), room_id:room.get('room_id')}      

      if all_rooms_html.length > 0          
        displayTemplate {rooms_content:all_rooms_html, gameid:Config.cGameId}
        
      else
         
        $el = self.$el
        name = 'choose_room_spectator'
        templateMgr.get name, (t)->
          $el.html _.template t, opts
          # SET SPECTATOR HERE
          log 'we are here'  
          Views.ChooseRoomSPageContent::attach_spectator_click_function {}                
                              
                    
          
    
  
  render:(cb)->
    
    self = @        
    opts = {images_root:images_root}
    
    name = self.name
    $el = self.$el
    
    gameObj = new Models.Game      
    gameObj.getNextGame success:(gameObj)->
                
      if gameObj.length == 1
        # log 'we still have a new game'
        gameObj = gameObj.at(0)
        Config.cGameId = gameObj.id
        Config.cGame = gameObj

        if Config.cGame.get('game_started')*1 == 1
          log 'we should display no more places for you'
          
          name = 'choose_room_spectator'
          templateMgr.get name, (t)->
            $el.html _.template t, opts
            
            Views.ChooseRoomSPageContent::attach_spectator_click_function {}   
        else
          self.render_choose_page_content {}

        # we should check from here if there are any rooms in the rooms


      else
        log 'ERROR_DEFAULT no games opened for today - we shoud display something else'        
        Config.appRouter.navigate '#', trigger:true 
          
    
        


class Views.PlayGamePage extends Views.Page
  initialize:->
    super
    @header = new Views.Header { parent: @el }
            
    @name = 'play_game'
    
    @defaultContent = new Views.PlayGamePageContent $.extend({},@options,{ parent: @el, name: @name })
     
    @footer = new Views.Footer { parent: @el }
  
  render: ->
    self = @
    @header.render()
    @defaultContent.render ()->      
      self.footer.render()
    
class Views.PlayGamePageContent extends Views.Panel
  
  initialize:->
    if @options.parent then $(@options.parent).append @$el
    @name = @options.name
    @$el.attr('id','wrap')    
    @countdownGame = new Models.Countdown()
    
    
  
  showInitialGift: (opts)->
    
    showOverlay('#initial_gift', '500px', '640px');
    $('#init_gif_to_game').ready().click ->
      removeOverlay('#initial_gift')
      false
  
    
    
  spectator_check_if_game_has_started: (opts)->  
        
    self = @    
          
    startGameScreenSpectator = ->     
      log 'start game user'        


    time_to_game =  opts.time_to_game*tb.second
    HourMinSec = tb.getHoursMinutesSeconds(time_to_game)              
    
    if time_to_game > 0    

      if @countdownGame

        optsStartGame = {'target_id':'#timergame', 'start_time':HourMinSec.hours+":"+HourMinSec.minutes+":"+HourMinSec.seconds, 'action_on_finish':startGameScreenSpectator}          
        @countdownGame.init optsStartGame
        log 'NEW GAME INIT'
        Config.cUserGame.set 'start_game_intval':@countdownGame.interval


    else
      log 'game already started'        
      $('#timergame').val('--:--:--')
        
    
  
  check_if_game_has_started: (opts)->
          
    self = @    
    #countdownGame = @countdownGame
      

    startGameScreen = ->     
                
      game_started = Config.cGame.get('game_started')      
                
      # if only the game has not started yet
      if game_started*1 == 0      
        
        optsMsg = 
          message_type:'start_new_game'

          gameid:Config.cGameId
          roomid:Config.cRoomId

          user:''
          message: 'Game Started'


        SS.server.app.sendMessage optsMsg, (response) ->
          if response.error 
            alert(response.error) 
          else
            log 'game started we should start a game round'

        
        

                  
    time_to_game =  opts.time_to_game*tb.second
    HourMinSec = tb.getHoursMinutesSeconds(time_to_game)              

    log 'time_to_game'+time_to_game

    if time_to_game > 0    
      # game has not started yet
      log 'here we are to start new game'        
      if @countdownGame
          
        optsStartGame = {'target_id':'#timergame', 'start_time':HourMinSec.hours+":"+HourMinSec.minutes+":"+HourMinSec.seconds, 'action_on_finish':startGameScreen}          
        @countdownGame.init optsStartGame
        log 'NEW GAME INIT'
        Config.cUserGame.set 'start_game_intval':@countdownGame.interval


    else

      # game has started 
      # here we should have spect view

      log 'game already started'
            
      

  
  
  # open info overlay with product details  
  attach_info_click: ->
    
    infoObj = $('.info')        
    $.each infoObj, (index, value) ->
      $(value).click ->
            
        $('#product_detail .inner h3').html($(this).children('.title').val())        
        $('#product_detail .inner p').html($(this).children('.description').val())        				
        $('#product_detail .product_image').html('<img src="../products/' + $('#product_detail .url_prefix').val() + $(this).children('.sku').val() + '_227.png" />')
            
        showOverlay('#product_detail', '500px', '430px')
        return false
            
    $('#return_to_game').click ->    
      removeOverlay('#product_detail')
      return false
     
  
  #broadcast new user
  broadcastNewUser:  (optsSendMessage)->   
    self = @
    optsSendMessage.message_type = 'new_user'            
    SS.server.app.sendMessage optsSendMessage, (response) ->
      if response.error then alert(response.error) else log 'message sent ok'      
  
  
  
  # spectator mouse over on prizes
  spectator_attach_click_function: (opts)->
    self = @
    
    prizesObj = $(".prizes")
    $.each prizesObj, (index, value) ->                         

      $(value).mouseenter ->                
        # $(value).children('.prize_content').children('.overlay').stop().fadeIn(200)
        $(value).children('.prize_content').children('.overlay').css('display', 'block')
        

      $(value).mouseleave ->        
        # $(value).children('.prize_content').children('.overlay').stop().fadeOut(100)
        $(value).children('.prize_content').children('.overlay').css('display', 'none')
        
  
  spectator_attach_room_click_function: ->    
    log 'attach spectator room click function'
    rooms = $(".spectator_rooms")    
    $.each rooms, (index, value) ->      
      $(value).unbind('click')
      
      
      
      $(value).click ->          
          room_id_string = $(value).attr('id')
          comp = room_id_string.split('_')      
          room_id = comp[2]
          
          log 'room:id'+room_id
                              
          Config.cRoomId = room_id
          Config.cUserGame.set 'roomid':room_id
          Config.cUserGame.save {}
          
          
          SS.server.app.subscribeToRoom 100, (response) ->
            if response.error 
              alert(response.error) 
              return false
            else 
              log 'subscribe to room ok'                  
              Config.appRouter.navigate '#playgame'+room_id, true
              
              ###
              clearPage()
              pageView = new Views.PlayGamePage { parent: @bodyEl }
              $(pageView.el).css {'opacity':0.0}
              pageView.render()
              $(pageView.el).animate {opacity: 1.0 }, Config.transition  
              ###
              
              
              return false
                    
        
    
    
  
  attach_share_function: (opts)->
    $("#share").click ->
      
      # share functions on facebook
      
      swaps_available = Config.cUserGame.get('swaps_available')*1      
      Config.cUserGame.set 'swaps_available':swaps_available+1
      Config.cUserGame.save {}
      
      swaps_available_html = +Config.cUserGame.get('swaps_available') - Config.cUserGame.get('swaps')*1      
      $("#swaps_available").html(swaps_available_html) 
            
      
      $("#share").unbind('click')
      return false
  
  attach_click_function: (opts)->
    self = @
    
    log 'click function called'
        
           
    attach_click_function_swap = (optsSwap) ->
      # recheck ugid it might be changed by other user being joined              
      pos = optsSwap.pos
      current_prizeid = optsSwap.current_prizeid
      
      current_ugid = $("#ugid_"+pos).val()

      log 'swap yes was clicked'
            
      # here we should perform the swap if possible
      if $("#blocks_"+pos).val()*1 == 0 
        # no blocks on this prize
        if Config.cUserGame.get('blocks')*1 == 0 && Config.cUserGame.get('swaps')*1 < Config.cUserGame.get('swaps_available')*1
          # no other blocks or swaps were performed by the user


          # my current prize id before change
          user_prizeid = Config.cUserGame.get('prizeid')
          

          # updateing the current usergame config
          Config.cUserGame.set 
            swaps:Config.cUserGame.get('swaps')+1                    
            prizeid: current_prizeid


          # updateing the current usergame in database
          userGame = new Models.UserGame id:Config.cUserGame.id
          userGame.fetch success:(userGame)->
            userGame.set
              swaps_available:Config.cUserGame.get 'swaps_available'
              blocks_available:Config.cUserGame.get 'block_available'
              swaps:Config.cUserGame.get 'swaps'
              blocks:Config.cUserGame.get 'blocks'
              pos: Config.cUserGame.get 'pos'
              prizeid: current_prizeid
              

            userGame.save {}, success: (userGame, response)->  
              Config.cUserGame = userGame


          $("#swaps_available").html(Config.cUserGame.get('swaps_available')*1 - Config.cUserGame.get('swaps')*1)      

          # updating new prize id for the user that has been swaped
          log 'new prize id for current user '+current_ugid+': '+user_prizeid
          userGameSwap = new Models.UserGame id:current_ugid
          userGameSwap.fetch success:(userGameSwap)->
            userGameSwap.set                                            
              prizeid: user_prizeid
            userGameSwap.save {}


          
            
          
          # updating the config for general prize set for this user game
          # Config.cPrizeId = current_prizeid

          # get the usernames
          userSwap = $("#user_"+pos).val()                                                   
          userUser = $("#user_"+ Config.cUserGame.get('pos')).val()


          

          # broadcast all the messages and have the view changed in all screens
          optsMsg =

            message_type:'swap_gift'
            roomid:Config.cRoomId
            gameid:Config.cGameId

            swaps: Config.cUserGame.get('swaps')

            prizeidSwap:current_prizeid
            prizeidUser:user_prizeid

            posSwap:pos
            posUser:Config.cUserGame.get('pos')

            user:userUser                    
            message:userUser+' swaped his prize with : '+userSwap                      

          # console.log(optsMsg)    
          SS.server.app.sendMessage optsMsg, (response) ->
            if response.error then alert(response.error) else log 'message sent ok'  

             
          litebox_switch('#swap_ask', '#swap_confirm', '420px', '125px')  
          return false
          
        else
          # error case
          # alert 'this user has already a block or a swap'  
          
          litebox_switch('#swap_ask', '#swap_invalid_number', '375px', '160px')  
          
          return false

      else
        # here we should loose the swap because he try it on a blocked gift
        # error case
        Config.cUserGame.set 'swaps' : +Config.cUserGame.get('swaps')+1
        Config.cUserGame.save {}
                        
        
        swaps_html =  Config.cUserGame.get('swaps_available')*1 - Config.cUserGame.get('swaps')*1
        if swaps_html < 0 then swaps_html = 0
        $("#swaps_available").html(swaps_html)
        

        userGame = new Models.UserGame id:Config.cUserGame.id
        userGame.fetch success:(userGame)->
          userGame.set
            swaps_available : Config.cUserGame.get 'swaps_available'
            blocks_available: Config.cUserGame.get 'blocks_available'
            swaps : Config.cUserGame.get 'swaps'
            blocks: Config.cUserGame.get 'blocks'
            pos: Config.cUserGame.get 'pos'
          userGame.save {}, success: (userGame, response)->  
            Config.cUserGame = userGame

        userSwap = $("#user_"+pos).val()                                                   
        userUser = $("#user_"+ Config.cUserGame.get('pos')).val()
   

        optsMsg =

          message_type:'swap_gift_unblock'
          roomid:Config.cRoomId
          gameid:Config.cGameId

          
          swapsUser: Config.cUserGame.get('swaps')
          posSwap:pos
          posUser:Config.cUserGame.get('pos')

          user:userUser                    
          message:userSwap+' has his prize unblocked'

        # console.log(optsMsg)    
        SS.server.app.sendMessage optsMsg, (response) ->
          if response.error then alert(response.error) else log 'message sent ok'     
            
        # alert 'Prize already Bblock by another User'                
        litebox_switch('#swap_ask', '#swap_invalid', '375px', '160px')         
        return false
  
        
        
    # if opts.only_attach 
    if true
      $('#swap_to_game').unbind('click') 
      $('#swapinvalid_to_game').unbind('click')  
      $('#swapinvalid_number_to_game').unbind('click')  
      $('#swap_nevermind').unbind('click')  
      $('#block_nevermind').unbind('click') 
      $('#blockinvalid_to_game').unbind('click')
      $('#block_to_game').unbind('click')
        
    #swap remove actions
    $('#swap_to_game').click (event) ->            
      log 'swap configm back to game'
      event.preventDefault()
      removeOverlay('#swap_confirm')
      return false            

    $('#swapinvalid_to_game').click (event) ->            
      log 'swap invalid back to game'
      event.preventDefault()
      removeOverlay('#swap_invalid')
      return false

    $('#swapinvalid_number_to_game').click (event) ->            
      log 'swap invalid invalid back to game'            
      event.preventDefault()
      removeOverlay('#swap_invalid')

      return false  


    $('#swap_nevermind').click (event) ->
      log 'swap nevermid invalid back to game'       
           
      event.preventDefault()
      removeOverlay('#swap_ask') 
      return false

     
    #block remove actions 
    $('#block_nevermind').click (event) ->        
      log 'Block nevermind click attached on remove'                     
      event.preventDefault()
      removeOverlay('#block_ask')
      return false

    $('#block_to_game').click  (event)  ->        
      log 'Block black to game click attached on remove '                        
      event.preventDefault()
      removeOverlay('#block_confirm')  
      return false
        
    # alert 'No More Blocks Available'
    $('#blockinvalid_to_game').click ->        
      removeOverlay('#block_invalid')
      return false    
        
    
    # start foreach    
    prizesObj = $(".prizes")
    $.each prizesObj, (index, value) ->

      pos_string = $(value).attr('id').split('_')
      pos = pos_string[1]

      current_prizeid = $("#prizeid_"+pos).val()
      current_ugid = $("#ugid_"+pos).val()

      #if opts.only_attach         
      if true
        $(value).unbind('mouseenter')              
        $(value).unbind('mouseleave') 
        $("#swap_"+current_prizeid).unbind('click')
        $("#block_"+current_prizeid).unbind('click')
        

      $(value).mouseenter ->                
        # $(value).children('.prize_content').children('.overlay').stop().fadeIn(200)        
        $(value).children('.prize_content').children('.overlay').css('display', 'block')
        

      $(value).mouseleave ->             
        # $(value).children('.prize_content').children('.overlay').stop().fadeOut(100)
        $(value).children('.prize_content').children('.overlay').css('display', 'none')
         
      
      
                 
      
      
      # SWAP ACTIONS      
      if $("#swap_"+current_prizeid) 
        log 'attach swap to pos '+pos
        $("#swap_"+current_prizeid).click (event) ->
                  
          
          log 'SWAP click on swap pressed'
          
          event.preventDefault()
          event.stopPropagation()

          current_ugid = $("#ugid_"+pos).val()
          current_prizeid = $("#prizeid_"+pos).val()

          
          $('#swap_yes').unbind('click')
          $('#swap_yes').click (event) ->            
            log 'SWAP - click on yes pressed'

            event.preventDefault()              
            event.stopPropagation()

            optsSwap =         
              pos:pos
              current_prizeid:current_prizeid

             
            attach_click_function_swap optsSwap                           
            return false
          

          showOverlay('#swap_ask', '350px', '145px')  
          return false
      
      # BLOCK ACTIONS 
      if $("#block_"+current_prizeid) 
                
        $('#block_yes').unbind('click')
        $("#block_"+current_prizeid).click (event) ->
                 
          
          log 'BLOCK click on block pressed'
          
          event.preventDefault()
          event.stopPropagation()
                                

          $('#block_yes').click (event) -> 
           
           
            event.preventDefault()
            event.stopPropagation()
            log 'BLOCK: click pressed on yes '
            # recheck ugid it might be changed by other user being joined              
            current_ugid = $("#ugid_"+pos).val()
            current_prizeid = $("#prizeid_"+pos).val()

            if $("#blocks_"+pos).val()*1 == 0  

              if Config.cUserGame.get('blocks')*1 == 0


                if !Config.cUserGame.get('blocks_available') then Config.cUserGame.set 'blocks_available':1
                  
                Config.cUserGame.set 'blocks' : Config.cUserGame.get('blocks')*1+1
                                    

                userGame = new Models.UserGame id:Config.cUserGame.id
                userGame.fetch success:(userGame)->
                  userGame.set
                    swaps_available: Config.cUserGame.get('swaps_available')*1
                    blocks_available: Config.cUserGame.get('block_available')*1
                    swaps:Config.cUserGame.get('swaps')*1 
                    blocks:Config.cUserGame.get('blocks')*1 
                    pos: Config.cUserGame.get 'pos'
                  userGame.save {}, success: (userGame, response)->  
                    Config.cUserGame = userGame

                                
            
                $("#blocks_available").html(Config.cUserGame.get('blocks_available')*1 - Config.cUserGame.get('blocks')*1)    
                  
                ###
                  Config.cUserGame.set 'block':1
                  Config.cUserGame.save {}
                  ###

                userName = $("#user_"+pos).val()  
                # $("#msg_"+current_prizeid).html('BLOCKED')

                optsMsg = 
                  message_type:'block_gift'
                  pos:pos
                  gameid:Config.cGameId
                  roomid:Config.cRoomId

                  blocks: Config.cUserGame.get('blocks')

                  user:userName
                  message:''+userName+' block his prize'


                SS.server.app.sendMessage optsMsg, (response) ->
                  if response.error then alert(response.error) else log 'message sent ok'

                litebox_switch('#block_ask', '#block_confirm', '420px', '135px')
                return false

              else                  
                
                litebox_switch('#block_ask', '#block_invalid', '375px', '140px')   
                # showOverlay('#block_invalid', '375px', '140px')
                return false

            else                
              # alert 'Prize Already Blocked'             
              litebox_switch('#block_ask', '#block_invalid', '375px', '140px') 
              return false

            false

          showOverlay('#block_ask', '350px', '175px')    
          return false
      
  
  render:(cb)->
    self = @
        
    gameid = Config.cGameId
    roomid = Config.cRoomId
    prizeid = Config.cPrizeId

    log 'prizeid '+prizeid+' roomid '+roomid+' prizeid '+prizeid
    console.log(Config.cUserGame)
        
    opts = {images_root:images_root}
        
    name = self.name
    $el = self.$el
    self_top = self
    
    
    prize_template =  _.template '
    
      <div class="prizes <%=class_css%>" id="prize_<%=pos%>">
          
        <input type="hidden" id="swaps_<%=pos%>" value="<%=swaps%>">        
        <input type="hidden" id="blocks_<%=pos%>" value="<%=blocks%>">        
        <input type="hidden" id="user_<%=pos%>" value="<%=user%>">
        
        <input type="hidden" id="prizeid_<%=pos%>" value="<%=prizeid%>">
        <input type="hidden" id="ugid_<%=pos%>" value="<%=ugid%>">  
                    
          <span class="user"><%=user%></span>
          
          <div id="prize_content_<%=pos%>" class="prize_content">
          
            <img src="<%=image%>" />                       
            
            <div class="overlay" id="overlay_<%=prizeid%>">
            
              <%=content_button_actions%>
            
              <div class="info">
                 <input type="hidden" class="title" value="<%=prize_name%>" />
                 <input type="hidden" class="sku" value="<%=prize_sku%>" />
                 <input type="hidden" class="description" value="<%=prize_description%>" />
                 <input type="hidden" class="uri" value="<%=prize_uri%>" />
              </div>
              
            </div>
            
          </div>
          
          
      </div>
    
    '
    
        
    prizes_content = ''     
    rooms_content = ''
    
    
    
    # display template
    displayTemplate = (opts) ->
    
      if Config.cUserGame.get('user_type') == 'spectator' then name = 'play_game_spectator' 
      
      templateMgr.get name, (t)->
        $el.html _.template t, opts        
        
        #common functions attached        
        $("#feed").niceScroll()
        self_top.attach_info_click {}        
        
        # special user function
        if Config.cUserGame.get('user_type') == 'user'
        
          opts.only_attached = false        
          self_top.check_if_game_has_started opts            
                    
          # console.log(optsSendMessage)    
          self_top.broadcastNewUser opts.optsSendMessage
          if Config.cGame.get('game_started')*1 == 0 then self_top.showInitialGift {}
          
          self_top.attach_share_function {}
          
        # log 'here we have spectator'  
        else
          
          self_top.spectator_attach_click_function opts
          self_top.spectator_check_if_game_has_started opts
          self_top.spectator_attach_room_click_function opts
          
          
        
     
    optsSendMessage = {}
    initial_gift_name = ''
    initial_gift_brand = ''
    initial_gift_image = ''
    
    
    Models.Prize::getActivePrizesWithUsers gameid:gameid, roomid:roomid, success:(prizes)->
      #console.log(prizes) 
      pos = 1
      prizes.each (prize)->
        
        ugid = if prize.get('ug_id') > 0 then prize.get('ug_id') else 0
                        
        if ugid > 0
          userName = 'U'+pos
          
          if +prize.id == +prizeid
            
            # set initial gift values
            initial_gift_name = prize.get('Name')
            initial_gift_brand = ''
            initial_gift_image = prize.get('PhotoSmall')
            
            # user is me
            class_css = 'my_box'
            Config.cUserGame.set id:ugid
            Config.cUserGame.set pos:pos
            
            LocalStorage.set 'cUgId', ugid, 7
            
            Config.cUserGame.save {}              
            
        
            optsSendMessage =             
              prizeid:Config.cPrizeId
              gameid:Config.cGameId
              roomid:Config.cRoomId
              ugid:Config.cUserGame.get('id') 
              pos:Config.cUserGame.get('pos') 
              user:userName
              message:userName+ ' joined the game'

              
                      
            
            content_button_actions = '<div id="actions_'+prize.id+'">
              
              <div id="block_actions_'+prize.id+'" style="display:block">                
                <p>This is your current gift.</p><b>Click to block it.</b><div class="block" id="block_'+prize.id+'">block</div>
              </div>  
              
              <div id="swap_actions_'+prize.id+'" style="display:none">
                <p>Want this gift instead of yours? </p><b>Click to swap.</b><div class="swap" id="swap_'+prize.id+'">Swap</div>                
              </div>  
              
              </div>          
            '
            
          else            
            # user is connected
            class_css = 'box'            
            
            content_button_actions = '<div id="actions_'+prize.id+'">
              
              <div id="block_actions_'+prize.id+'" style="display:none">                
                <p>This is your current gift.</p><b>Click to block it.</b><div class="block" id="block_'+prize.id+'">block</div>
              </div>  
              
              <div id="swap_actions_'+prize.id+'" style="display:block">
                <p>Want this gift instead of yours? </p><b>Click to swap.</b><div class="swap" id="swap_'+prize.id+'">Swap</div>                
              </div>  
              
              
              </div>          
            '
            
        else
          # user is not connected
          class_css = 'box'            
          userName = 'NA'
                    
          content_button_actions = '<div id="actions_'+prize.id+'">
              
            <div id="block_actions_'+prize.id+'" style="display:none">                
              <p>This is your current gift.</p><b>Click to block it.</b><div class="block" id="block_'+prize.id+'">block</div>
              </div>  
              
            <div id="swap_actions_'+prize.id+'" style="display:none">
              <p>Want this gift instead of yours? </p><b>Click to swap.</b><div class="swap" id="swap_'+prize.id+'">Swap</div>                
              </div>  
              
              </div>          
            '
          content_button_actions +=  '
          
          <div id="no_user_'+prize.id+'">No user connected yet</div>
          
          '
         
        
        if Config.cUserGame.get('user_type') == 'spectator' 
        
          content_button_actions = '<p>' + prize.get('Name') + '</p><b>Shop on 6pm.com.</b><a href="' + prize.get('uri') + '"class="buy" target="_blank">Buy</a>'
          
                                
        prizes_content += prize_template  
          pos:pos
          prizeid:prize.get('id')
          user:userName
          image: prize.get('PhotoSmall')
          class_css:class_css
          ugid:ugid
          blocks:if prize.get('blocks') then  prize.get('blocks') else 0
          swaps:if prize.get('swaps') then prize.get('swaps') else 0
          content_button_actions:content_button_actions
                            
          prize_name:prize.get('Name')
          prize_description:prize.get('Description')
          prize_sku:prize.get('sku')
          prize_uri:prize.get('uri')
                            
                
        pos++
      
      
      blocks_available = Config.cUserGame.get('blocks_available') - Config.cUserGame.get('blocks')
      if blocks_available*1 == 0  then blocks_available = '-'
      
      swaps_available = Config.cUserGame.get('swaps_available') - Config.cUserGame.get('swaps')
      if swaps_available*1 == 0  then swaps_available = '-'
      
      if +Config.cGame.get('game_started') == 0 then text_time = 'Time left to start game:' else text_time = 'Time left in this round:'
        
      opts.prizes_content = prizes_content
      opts.round =  Config.cGame.get 'round'
      opts.roundTotal = Config.maxRounds
      opts.swaps_available = swaps_available
      opts.blocks_available = blocks_available
      opts.text_time = text_time
      opts.optsSendMessage = optsSendMessage
      
      opts.initial_gift_name = initial_gift_name
      opts.initial_gift_brand = initial_gift_brand
      opts.initial_gift_image = initial_gift_image
      
      log 'spectator'
      console.log(Config.cUserGame)
      
      newGame = new Models.Game id:Config.cGame.get('id')
      newGame.getGameTime success: (newGame)->
        if newGame.length == 1
          newGame = newGame.at(0)
          if +newGame.get('game_started') != 2
            
            #console.log(opts)      
            # console.log(newGame)
            opts.time_to_game = newGame.get('time_to_game')
            
            opts.rooms_content = ''
            
            if Config.cUserGame.get('user_type') == 'user'            
              displayTemplate opts
            else
              Models.Room::fetchAll                  
                success: (rooms)->                    
                  rooms.each (room)->
                      
                    opts.rooms_content +=  '<a href="javascript:void(0)" class="spectator_rooms '+(if room.get('id')*1 == Config.cUserGame.get('roomid')*1 then "selected_room"  else "room")+'" id="spectator_rooms_'+room.get('id')+'" >'+room.get("name")+'</a>'
                      
                  displayTemplate opts  
              
              
          else
            log 'game is over'
            # log ERROR_DEFAULT
            Config.appRouter.navigate '#', trigger:true
        else
          # log ERROR_DEFAULT
          Config.appRouter.navigate '#', trigger:true
      
      
    
    


class Views.ClaimPrizePage extends Views.Page
  initialize:->
    super
        
    @header = new Views.Header { parent: @el }
            
    @name = 'claim_prize'
    
    @defaultContent = new Views.ClaimPrizePageContent $.extend({},@options,{ parent: @el, name: @name })
     
    @footer = new Views.Footer { parent: @el }
  
  render: ->
    self = @
    @header.render()
    @defaultContent.render ()->      
      self.footer.render()
    
class Views.ClaimPrizePageContent extends Views.Panel
  initialize:->
    if @options.parent then $(@options.parent).append @$el
    @name = @options.name
    @$el.attr('id','wrap')
  
  render:(cb)->
    self = @
        
    opts = {images_root:images_root}
        
    name = self.name
    $el = self.$el
    
           
    userGameObj = new Models.UserGame id:Config.cUserGame.get('id')
    
    userGameObj.getUserGamePrize success:(userGameObj)->
    
      if userGameObj.length == 1
        
        
        userGameObj = userGameObj.at(0)                        
       
        log 'user game final'
        console.log(userGameObj)
       
        templateMgr.get name, (t)->
          $el.html _.template t, opts
          
          $('#submit_claim').click (event) ->
          
            event.preventDefault()
          
            formdata = $('#claim_form').serializeObject()
            errors = ""
            
            console.log(formdata)
            
            if formdata.first_name == "" then errors += "First Name<br/>"                     

            if formdata.last_name == "" then errors += "Last Name.<br/>"                          

            if formdata.address_1 == "" then errors += "Address 1<br/>"            

            if formdata.city == "" then  errors += "City<br/>"
            
            if formdata.state == 0 then   errors += "State<br/>"            

            if formdata.zip == "Zip*" || formdata.zip == "" then errors += "Zip Code<br/>"
                        
            if formdata.email == "" then  errors += "Email<br/>"

            if formdata.agree_toc == null then errors += "<br/>Please agree to the terms and conditions."
            
            if formdata.of_age == null then   errors += "<br/>Please confirm that you are over 18 years of age.";
            
            console.log(errors)
            
            if errors != "" 
            
              if errors.split("<br/>").length > 3 then error_count = 3 else error_count = errors.split("<br/>").length
                  
              $('#form_errors .inner .errors').html(errors)
              $('.blackout').fadeIn(250)


              $('#form_errors').show().animate {width: '400px', height: $('#form_errors .inner').height() - (30 + (10 * error_count)) }, 150, ->
                $('#form_errors .inner').fadeIn(100)

              $('#error_ok').ready().click (event) -> 
                event.preventDefault()
                $('.blackout').fadeOut(200)
                $('#form_errors').hide().css({
                  'width': '0',
                  'height': '0'
                }).children('.inner').css('display', 'none')
                return false
                  
              return false  
                  
                  
              
            else 
              log 'here we should save the claim'
              Coupon = new Models.Coupon
              Coupon.set
                gameid:     userGameObj.get('gameid')
                userid:     userGameObj.get('userid')
                prizeid:    userGameObj.get('prizeid')
                datetime:   tb.time()
                ugid:       userGameObj.get('id')
                first_name: $("#first_name").val()
                last_name:  $("#last_name").val()
                address_1:  $("#address_1").val()
                address_2:  $("#address_2").val()
                city:  $("#city").val()
                state:  $("#state").val()
                zip:  $("#zip").val()
                email:  $("#email").val()
                subscribe: if $("#subscribe").attr('checked') == true then 1 else 0
                  
              Coupon.save {}
                
              LocalStorage.set 'cGameId', null, 7
              LocalStorage.set 'cUgId', null, 7
                                
              Config.cPrizeId = null
              Config.cGameId = null
              Config.cGame = null
              Config.cUserGame = null
                                
              Config.appRouter.navigate '#claimthanks', trigger:true
              return false  
        
       
      else
        log 'we should not have this situation'
        



class Views.ClaimThanksPage extends Views.Page
  initialize:->
    super
        
    @header = new Views.Header { parent: @el }
            
    @name = 'claim_thanks'
    
    @defaultContent = new Views.ClaimThanksPageContent $.extend({},@options,{ parent: @el, name: @name })
     
    @footer = new Views.Footer { parent: @el }
  
  render: ->
    self = @
    @header.render()
    @defaultContent.render ()->      
      self.footer.render()
    
class Views.ClaimThanksPageContent extends Views.Panel
  initialize:->
    if @options.parent then $(@options.parent).append @$el
    @name = @options.name
    @$el.attr('id','wrap')
  
  render:(cb)->
    self = @
        
    opts = {images_root:images_root}
        
    name = self.name
    $el = self.$el
     
       
    templateMgr.get name, (t)->
      $el.html _.template t, opts
          
       
    


# chat view only for demo purposes / not used 
class Views.ChatPage extends Views.Page
  initialize:->
    super
       
    
    @header = new Views.Header { parent: @el }
            
    @name = 'chat'
    
    @defaultContent = new Views.ChatPageContent $.extend({},@options,{ parent: @el, name: @name })
     
    @footer = new Views.Footer { parent: @el }
  
  render: ->
    self = @
    @header.render()
    @defaultContent.render ()->      
      self.footer.render()
    
class Views.ChatPageContent extends Views.Panel
  initialize:->
    if @options.parent then $(@options.parent).append @$el
    @name = @options.name
    @$el.attr('id','wrap')
  
  render:(cb)->
    self = @
    status = parent: null, referrer: document.referrer, user: LocalStorage.get('user.last')
    
    opts = {images_root:images_root}
    
    displayMainScreen = ->
      $('#signIn').fadeOut(230) and $('#main').show()
    
    displaySignInForm = ->
      $('#signIn').show().submit ->
        SS.server.app.signIn $('#signIn').find('input').val(), (response) ->
          $('#signInError').remove()
          displayMainScreen()
        false
    
    templateMgr.get self.name, (t)->
      self.$el.html _.template t, opts      
      displaySignInForm()
      # Bind to Submit button
      $('form#sendMessage').submit ->
        newMessage = $('#newMessage').val()
        SS.server.app.sendMessage newMessage, (response) ->
          if response.error then alert(response.error) else $('#newMessage').val('')
        false
              
      if cb then cb()