Backbone = require 'backbone'
_ = require('underscore')._
$ = require 'jquery'
jsdom = require 'jsdom'
request = require 'request'
querystring = require 'querystring'

db = require('./db').db
Models = require './models'
Utils = require '../shared/utils'
Config = require '../shared/config'



log = Config.log 'ajaxutil'
stringify = (s) -> JSON.stringify s
tb = -> Config.timebase



exports.actions = 
 log 'actions'

