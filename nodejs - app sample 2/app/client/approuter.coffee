log = {}
stringify = (s) -> JSON.stringify s

pageView = null
clearPage =->
  if not pageView then return
      
  pageView.destroy()
  
  
  

class AppRouter extends Backbone.Router
  initialize:->
    log = Config.log 'approuter'
    log 'initing router'
    @bodyEl = $('body')[0] 
              
  defaultRoute: (actions) ->
    log 'routed to default with actions: '+actions
   
    log 'routed to default with actions: '+actions
    @navigate '#',trigger:false                   
    if Config.session.entry 
      entry = Config.session.entry
      Config.session.entry = null 
      if entry.hash then LocalStorage.set 'nexturl', entry.hash, 1
      
      if entry.hash
        Config.appRouter.navigate entry.hash,trigger:true
        return  
    
        
      
      
    if Config.user
      # as long as user is valid default goes here
    else
      clearPage()
      pageView = new Views.PublicPage { parent: @bodyEl }
      $(pageView.el).css {'opacity':0.0 }    
      pageView.render()
      $(pageView.el).animate {opacity: 1.0},Config.transition

  

  pageView: null

  register: ->
    clearPage()
    pageView = new Views.RegisterPage { parent: @bodyEl }
    $(pageView.el).css {'opacity':0.0}
    pageView.render()
    $(pageView.el).animate {opacity: 1.0 }, Config.transition
    
  
  chooseRoom: ->
    clearPage()
    pageView = new Views.ChooseRoomPage { parent: @bodyEl }
    $(pageView.el).css {'opacity':0.0}
    pageView.render()
    $(pageView.el).animate {opacity: 1.0 }, Config.transition 
  
  chooseRoomS: ->
    clearPage()
    pageView = new Views.ChooseRoomSPage { parent: @bodyEl }
    $(pageView.el).css {'opacity':0.0}
    pageView.render()
    $(pageView.el).animate {opacity: 1.0 }, Config.transition 
    
  playGame: ->
    clearPage()
    pageView = new Views.PlayGamePage { parent: @bodyEl }
    $(pageView.el).css {'opacity':0.0}
    pageView.render()
    $(pageView.el).animate {opacity: 1.0 }, Config.transition  
  
  
  claimPrize: ->
    
    clearPage()
    pageView = new Views.ClaimPrizePage { parent: @bodyEl }
    $(pageView.el).css {'opacity':0.0}
    pageView.render()
    $(pageView.el).animate {opacity: 1.0 }, Config.transition  
  
  claimThanks: ->
    
    clearPage() 
    pageView = new Views.ClaimThanksPage { parent: @bodyEl }
    $(pageView.el).css {'opacity':0.0}
    pageView.render()
    $(pageView.el).animate {opacity: 1.0 }, Config.transition  
  
  chat: ->
    clearPage()
    pageView = new Views.ChatPage { parent: @bodyEl }
    $(pageView.el).css {'opacity':0.0}
    pageView.render()
    $(pageView.el).animate {opacity: 1.0 }, Config.transition   
  
  destroy:->clearPage()
  
  
        
AppRouter.customer = {} #once get privileges working right, can put customer options here
AppRouter.admin = {}


AppRouter.all = # all refers to logged in users
  
  
  "infopage/:name"                    : "infoPage"
  "*actions"                          : "defaultRoute"  ## otherwise      

# Couple links are duplicates for 'all' (ie. private links)
# and Public links for general browser without login
AppRouter.gmPublic =
  
  "login"                             : "login"
  "register"                          : "register"
  "chooseroom"                        : "chooseRoom"
  "chooserooms"                       : "chooseRoomS"
  "playgame"                          : "playGame"
  "playgame1"                         : "playGame"
  "playgame2"                         : "playGame"
  "playgame3"                         : "playGame"
  "claimprize"                        : "claimPrize"
  "claimthanks"                       : "claimThanks"  
  "chat"                              : "chat"
  "thanks"                            : "thanks"  
  "forgot"                            : "forgot"
  "reset/validate/:id"                : "resetPassword"  
  "*actions"                          : "defaultRoute"
  

@AppRouter = AppRouter
