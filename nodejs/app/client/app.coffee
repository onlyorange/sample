# Client-side code

# This function is called automatically once the websocket is setup




# client!

stringify = (s) -> JSON.stringify s


started = false
log = null

if window.appResetTime then setTimeout -> # nasty hack for socketstream/websockets race condition in staging/deployment mode
    if not @started then window.location.reload()
  , window.appResetTime

exports.init = -> # called automatically when websocket connection is established. Do not rename/delete                   
  
  @started = true
  
  # app.coffee comes first so these are inside 'init'
  @Model = Backbone.Model
  @Collection = Backbone.Collection
  @View = Backbone.View
  @Router = Backbone.Router

  Config = @Config  # config options (+ app globals here)     
  _.extend Config, Backbone.Events  # global events

  Models = @Models
  Views = @Views
  Templates = @Templates
  log = Config.log 'app'
  Models.init()
  
  defer = 0

  Config.session = session = new Models.Session session_id: LocalStorage.get('session_id') # set by socketstream/connect 
  Config.timebase = new Models.Timebase
  Config.app = new Models.Application
  Config.cGameId = 0
  
     
  
  session.entry = { hash: if location.hash then location.hash.substring(1) else '' } 
  
  
  Config.appRouter = new AppRouter routes:AppRouter.gmPublic
           
  
  Views.init()
  
   
  maybeStartup =->
    if --defer then return        
    Backbone.history.start silent:true     
    Config.appRouter.navigate '#_', trigger:true 

    
  
  defer++
  Config.app.loadApp
    error:-> log 'server down, dying, probably won\'t happen'
    success:-> 
      log 'app phase loadapp'  
      
      Config.cGame = Config.app.get('cGame')
      Config.cGameId = Config.app.get('cGameId')
      Config.cRoomId = Config.app.get('cRoomId')
      Config.cPrizeId = Config.app.get('cPrizeId')
      Config.cUserGame = Config.app.get('cUserGame')
      Config.cRound = 0
      Config.cEndRound = 0
      
      ###
      log 'user game'
      console.log(Config.app.get('cUserGame'))
      ###
      
      ###
      Config.cUserGame = Config.app.get('cUserGame')
      ###
      
      # how do I hook up facebook init here?
      
      
      maybeStartup()  
  
      
    
  true

    
 

