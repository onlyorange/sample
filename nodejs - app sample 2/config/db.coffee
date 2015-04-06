try
  global.mysql = require 'mysql'
catch e
  console.log "Error: mysql is missing. "

_ = require('underscore')._


database = 'game'

sqlClient = mysql.createConnection { user: 'game', password: 'game' }
sqlClient.host = 'localhost'  
sqlClient.port = 3306
sqlClient.query 'USE '+ database
console.log database+' database ready'

_.extend exports,  
  init: ->


  client: sqlClient

  close: ->
    # sqlClient.end()



