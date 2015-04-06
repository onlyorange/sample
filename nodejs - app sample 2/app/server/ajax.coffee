# ajax wrapper
Backbone = require 'backbone'
_ = require('underscore')._
db = require('./db').db
Models = require '../shared/models'
Utils = require '../shared/utils'
Config = require '../shared/config'

log = Config.log 'ajax'
stringify = (s) -> JSON.stringify s

exports.actions = 
  _dispatch:(params,descriptor,cb) ->
    if params.model then params.model = JSON.parse params.model
    if not params._method then params._method = 'read'
    if params.id 
      try 
        params.id = JSON.parse params.id
      catch err
        # log 'id not parsed'
    params._session = @session
    db.dispatch params,descriptor,cb
    
 

# as one liner:
_.each Models,(model)-> if model::descriptor and model::descriptor.name then exports.actions[model::descriptor.name] = (params,cb)-> @_dispatch params,model::descriptor,cb
  
  