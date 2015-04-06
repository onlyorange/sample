if typeof module != 'undefined' && module.exports
  Models = exports
  Backbone = require 'backbone'
  _ = require('underscore')._
  Db = require('../../config/db')
  server = true
else
  Backbone = window.Backbone  
  _ = window._
  Collections = @Colletions = {}
  server = false

Collection = Backbone.Collection
Model = Backbone.Model

log = (s) -> console.log s
stringify = (s) -> JSON.stringify s

class Base extends Collection



