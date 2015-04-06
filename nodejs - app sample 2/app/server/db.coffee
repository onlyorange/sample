Backbone = require 'backbone'
_ = require('underscore')._
Db = require '../../config/db'
$ = require 'jquery'


Config = require '../shared/config'
Models = require '../shared/models'


log = Config.log 'db'
stringify = (s) -> JSON.stringify s
tb    = null

_tmpl = 
  readpage: _.template 'SELECT * FROM  `<%= table %>` ORDER BY  `<%= field %>` <%= order %> LIMIT <%= start %>,<%= max %> '
  orderby: _.template ' ORDER BY `<%= field %>` <%= order %> '
  limit: _.template ' LIMIT <%= start %>,<%= max %> '
  readall: _.template 'SELECT * FROM `<%= table %>` '
  countall: _.template 'SELECT COUNT(*) AS count FROM `<%= table %>` '
  read: _.template 'SELECT * FROM `<%= table %>` WHERE `id` =<%= id %> '    
  create: _.template 'INSERT INTO `<%= table %>` (<%= columns %>) VALUES ( <%= values %>) '
  update: _.template 'UPDATE `<%= table %>` SET <%= sets %> WHERE `id` =<%= id %> ' 
  destroy: _.template 'DELETE FROM `<%= table %>` WHERE `id` = <%= id %> '

_getIds = (ids)->
  idlist = []
  $.each ids,->
    idlist.push '`id` ='+@ 
  return 'WHERE '+idlist.join(' OR ')
  
_err = (method,err,qstr)->
  if err 
    log '***database error in ' + method + ':' + err
    log 'query:'+qstr
  return err

_dateStr = (date)-> # assume don't need to pad with 0's
  if typeof date is 'string' 
    if date.indexOf('-') is 4 then return date # literal, no change  needed
    date = new Date(date)
  return date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()

_dateParse = (dateStr)-> # assume yyyy-mm-dd (like sql)
  if typeof dateStr is 'string'
    dateParts = dateStr.split '-'
    if dateParts.length > 1 then return new Date(dateParts[0],dateParts[1]-1,dateParts[2]).getTime()
  return dateStr

_escStr = (str)->
  return if typeof str is "string" then str.replace(/\'/g,"''") else str  # sql magic


_encrypt = (str)->
  shasum = crypto.createHash 'sha1'
  shasum.update str
  shasum.digest 'hex'


_doQuery = (fnName,qstr,cb) ->
  #log qstr  # DEBUG QUERIES
  Db.client.query qstr,(err, results, fields) ->
    _err fnName,err,qstr
    if err 
      log err + ': error from database, qstr: ' + qstr
      cb {success:false,info:err.message} 
    else cb {success:true,results:results} 
    
exports.db =
  init: ->
   log 'db init'
   tb    = Config.timebase



  dbinfo:-> { user: Db.client.user, password: Db.client.password, host: Db.client.host, port: Db.client.port, database: Db.client.database }

  dispatch: (params,descriptor,cb) ->
    if params.action # for special requests
      @[params.action](params,descriptor,cb)
      return
      
    switch params._method
      when 'read'
        if not params.id or params.id is 'all'
          @readall params,descriptor,cb
        else if $.isArray(params.id)
          @readmulti params.id,descriptor,cb #id is array of ids
        else 
          @read params.id,descriptor,cb 
      when 'update'
        @update params.id,params.model,descriptor,cb
      when 'delete'
        @destroy params.id,descriptor,cb
      when 'create'
        @create params.model,descriptor,cb
      
  readall: (params,descriptor,cb) -> 
    qstr = _tmpl.readall {table:descriptor.name}
    if params.orderby
      if !params.order then params.order = 'ASC'
      qstr += ' ' + _tmpl.orderby {field:params.orderby, order:params.order }
    if params.start
      if !params.max then params.max = 30
      qstr += ' ' + _tmpl.limit { start:params.start, max:params.max }
    _doQuery 'readall',qstr,cb

  countall: (params,descriptor,cb) ->
    qstr = _tmpl.countall { table:descriptor.name }
    Db.client.query qstr, (err, results, fields)->
      _err 'countall', err, qstr
      if err then cb { success:false, info:error }
      else
        cb { success:true, results:results }

  readmulti: (ids,descriptor,cb) -> _doQuery 'readmulti',_tmpl.readall({table:descriptor.name})+' '+_getIds(ids),cb

  readpage:(params,descriptor,cb) -> 
    if !params.order then params.order = 'ASC'
    _doQuery 'readpage',_tmpl.readpage {table:descriptor.name,field:params.orderby,order:params.order,start:params.start,max:params.max},cb
  read: (id,descriptor,cb) ->
    qstr = _tmpl.read({table:descriptor.name,id:id})
    Db.client.query qstr,(err, results, fields) ->  
      _err 'read', err, qstr
      res = if results.length then results[0] else {}
      cb(if err then { success:false, info:err } else { success:true, results:res } )
  create: (model,descriptor,cb) ->
    keys = []
    vals = []
    retObj = {}
    $.each descriptor.columns,()->
      # log 'field:'+@field + ', model: '+ model[@field]
      if (not @field) or (@field is 'id') or (not model) or (not model[@field]?) then return true
      keys.push "`"+@field+"`"
      #log 'field name:'+@title
      if (@type is 'date') then vals.push "'"+_dateStr(model[@field])+"'" else vals.push "'"+_escStr(model[@field])+"'"
      retObj[@field] = model[@field]
    qstr = _tmpl.create { table: descriptor.name, columns: keys.join(','), values: vals.join(',') } 
    log 'create: ' + qstr
    Db.client.query qstr,(err, results, fields) ->
      _err 'create',err, qstr
      if err
        cb {success:false,info:err}
      else
        retObj['id'] = results.insertId
        cb {success:true,results:retObj} 

  update: (id,model,descriptor,cb)->
    sets = []
    # log 'updating with '+stringify model
    $.each descriptor.columns,->
      if (not @field) or (@field is 'id') or (not model[@field]?) then return true
      if (@type is 'date') then sets.push "`"+@field+"`"+" = '" + _dateStr(model[@field]) + "'"
      else sets.push "`"+@field+"`"+" = '" + _escStr(model[@field]) + "'"
    qstr = _tmpl.update { table:descriptor.name, id:id, sets:sets.join(',') }
    log 'update: '+ qstr
    _doQuery 'update',qstr,cb

  destroy: (id,descriptor,cb)-> _doQuery 'destroy',_tmpl.destroy({table:descriptor.name,id:id}),cb

  rawQuery: (qstr,f)->Db.client.query(qstr,f)
  
  dbClient: Db.client
  
  search: (params,descriptor,cb)->
    where = []
    $.each params,(k,v)->
      if k.indexOf('_') is 0 or (k is 'id' and v is 'all') or k is 'action' then return true  # change action to _action at some point
      where.push "`"+k+"` ='"+v+"'"
    _doQuery 'search',"SELECT * FROM `"+descriptor.name+"` WHERE "+where.join(' AND '), cb 
      
  
  lockFetchSaveUnlockGame: (params, descriptor, cb)->
    
    # log 'lock'
    # console.log(Db.client)
    
    qstr  = "LOCK TABLES `"+descriptor.name+"` AS `g` WRITE "
    Db.client.query qstr, (err, results, fields) ->
      _err 'lockFetchSaveUnlockGame', err, qstr
      if err
        cb success:false, info:err
      else
        
        # log 'lock ok done'
        
        qstr = "SELECT `g`.* FROM `"+descriptor.name+"` AS `g` WHERE `g`.`id`='"+params.id+"' "
        
                
        
        Db.client.query qstr, (err, results, fields) ->
          _err 'lockFetchSaveUnlockGame', err, qstr
          if err
            cb success:false, info:err
          else
            
            log 'we have game details'
            # cb success:true, results:results 
            if params.message_type == 'start_new_game'
              game = results[0]
              if +game.game_started == 1
                log 'game already started'                          
                
                qstr  = "UNLOCK TABLES"
                Db.client.query qstr, (err, results, fields) ->
                  _err 'lockFetchSaveUnlockGame', err, qstr
                  if err
                    cb success:false, info:err
                  else      
                    #$.each results,(k,room)->
                    # log 'we are here and is success'
                    cb success:false  

                cb success:false
              else
                log 'we should update the game'
                
                
                # log 'update'
                # console.log(Db.client)
                
                qstr = "UPDATE `games` SET `game_started`='1' WHERE `id`='"+params.id+"'"
                Db.client.query qstr, (err, results, fields) ->
                  _err 'lockFetchSaveUnlockGame', err, qstr
                  if err
                    cb success:false, info:err
                  else      
                    #$.each results,(k,room)->
                    # log 'we are here and is success'

                    qstr  = "UNLOCK TABLES"

                    Db.client.query qstr, (err, results, fields) ->
                      _err 'lockFetchSaveUnlockGame', err, qstr
                      if err
                        cb success:false, info:err
                      else      
                        #$.each results,(k,room)->
                        # log 'we are here and is success'
                        cb success:true  

                    # cb success:true 


                #cb success:true, results:results  
            else

              if params.message_type == 'start_new_round'
              
                game = results[0]
                if +game.round == +params.round
                  log 'room already started'                          

                  qstr  = "UNLOCK TABLES"
                  Db.client.query qstr, (err, results, fields) ->
                    _err 'lockFetchSaveUnlockGame', err, qstr
                    if err
                      cb success:false, info:err
                    else      
                      #$.each results,(k,room)->
                      # log 'we are here and is success'
                      cb success:false  

                  cb success:false
                else
                  log 'we should update the game and start new round'


                  # log 'update'
                  # console.log(Db.client)

                  qstr = "UPDATE `games` SET `round`='"+params.round+"' WHERE `id`='"+params.id+"'"
                  Db.client.query qstr, (err, results, fields) ->
                    _err 'lockFetchSaveUnlockGame', err, qstr
                    if err
                      cb success:false, info:err
                    else      
                      #$.each results,(k,room)->
                      # log 'we are here and is success'

                      qstr  = "UNLOCK TABLES"

                      Db.client.query qstr, (err, results, fields) ->
                        _err 'lockFetchSaveUnlockGame', err, qstr
                        if err
                          cb success:false, info:err
                        else      
                          #$.each results,(k,room)->
                          # log 'we are here and is success'
                          cb success:true 
              
              
              
              cb success:true, results:results  
      
      
      
     
     
   
  lockGame: (params, descriptor, cb)->
  
    qstr  = "LOCK TABLES `games` WRITE, `games` AS `g` WRITE, `users_games` WRITE "
    
    log qstr
    
    Db.client.query qstr, (err, results, fields) ->
      _err 'lockGame', err, qstr
      if err
        cb success:false, info:err
      else      
        #$.each results,(k,room)->
        # log 'we are here and is success'
        cb success:true  
    
  unlockGame: (params, descriptor, cb)->
  
    qstr  = "UNLOCK TABLES"
    
    log qstr
    
    Db.client.query qstr, (err, results, fields) ->
      _err 'unlockGame', err, qstr
      if err
        cb success:false, info:err
      else      
        #$.each results,(k,room)->          
        cb success:true
  
  
   
  getUserGamePrize: (params, descriptor, cb)->
    
    qstr     = "SELECT `ug`.*, `p`.`Name`, `p`.`PhotoSmall`  "
        
    qstr    += " FROM " + descriptor.name + " as ug LEFT JOIN `prizes` AS `p` ON `ug`.`prizeid` = `p`.`id` " 
    
    where = []    
    where.push '`ug`.`id` = "'+params.id+'"'
            
    qstr += ' WHERE ' + where.join (' AND ')
            
    #log qstr
       
    Db.client.query qstr, (err, results, fields) ->
      _err 'getUserGamePrize', err, qstr
      if err
        cb success:false, info:err
      else      
        #$.each results,(k,room)->          
        cb success:true, results:results
        
        
   
  getActivePrizesWithUsers: (params, descriptor, cb)->
    qstr     = "SELECT `p`.*, `ug`.`id` AS `ug_id`, `ug`.`datetime`, `ug`.`userid`, `ug`.`blocks`, `ug`.`swaps` "
        
    qstr    += " FROM " + descriptor.name + " as p LEFT JOIN `users_games` AS `ug` ON `p`.`id`=`ug`.`prizeid` AND `ug`.`user_type`='user' " 
    
    where = []
    where.push '`p`.`GameId` = "'+params.gameid+'"'
    where.push '`p`.`Room` = "'+params.roomid+'"'
            
    qstr += ' WHERE ' + where.join (' AND ')
    
    qstr += ' ORDER BY `p`.`id` ASC '      
    
    #log qstr
       
    Db.client.query qstr, (err, results, fields) ->
      _err 'getActivePrizesWithUsers', err, qstr
      if err
        cb success:false, info:err
      else      
        #$.each results,(k,room)->          
        cb success:true, results:results  
    
  
  
  
  
  getRandomAvailPrize: (params, descriptor, cb)->
    qstr     = "SELECT `p`.* "
        
    qstr    += " FROM " + descriptor.name + " as p LEFT JOIN `users_games` AS `ug` ON `p`.`id`=`ug`.`prizeid` AND `ug`.`user_type`='user'" 

    where = []
    where.push '`p`.`GameId` = "'+params.gameid+'"'
    where.push '`p`.`Room` = "'+params.roomid+'"'
            
    qstr += ' WHERE ' + where.join (' AND ')
    
    qstr += ' AND `ug`.id IS NULL ORDER BY RAND() LIMIT 1'      
    
    #log qstr
       
    Db.client.query qstr, (err, results, fields) ->
      _err 'getRandomAvailPrize', err, qstr
      if err
        cb success:false, info:err
      else      
        #$.each results,(k,room)->          
        cb success:true, results:results  
        
        
    


  getGameRoomsWithPrizes: (params, descriptor, cb)->
  
    #SELECT `r`.*, (SELECT COUNT(`p`.id) FROM `prizes` AS `p` WHERE `p`.`Room`=`r`.`id` AND `p`.`GameId`='3' ) AS `nr_prizes` , (SELECT COUNT(`ug`.id) FROM `users_games` AS `ug` WHERE `ug`.`roomid`=`r`.`id` AND `ug`.`gameid`='3' AND `ug`.`user_type`='user' ) FROM `rooms` AS `r` GROUP BY `r`.`id`
    
        
    qstr     = "SELECT `r`.*,  (SELECT COUNT(`p`.id) FROM `prizes` AS `p` WHERE `p`.`Room`=`r`.`id` AND `p`.`GameId`='"+params.gameid+"' ) AS `total_prizes_no`, (SELECT COUNT(`ug`.id) FROM `users_games` AS `ug` WHERE `ug`.`roomid`=`r`.`id` AND `ug`.`gameid`='"+params.gameid+"' AND `ug`.`user_type`='user' ) as `total_users_connected`  "
        
    qstr    += " FROM " + descriptor.name + " as r  " 

    where = []
    
      
    log qstr   
       
    Db.client.query qstr, (err, results, fields) ->
      _err 'getGameRoomsWithPrizes', err, qstr
      if err
        cb success:false, info:err
      else      
        #$.each results,(k,room)->          
        cb success:true, results:results  

  getNextGame: (params, descriptor, cb)->
    
    qstr     = "SELECT `g`.*, TIMESTAMPDIFF(SECOND, NOW(),`g`.`StartDate`) as `time_to_game` "
    dUTC     = tb.date().getTime()
    dService = dUTC + tb.serviceTimeOffset
    
    # log 'server'
    # log dService
    
    date_now = tb.date(dService).format('yyyy-mm-dd H:m:s')
    # log date_now
    
    qstr    += " FROM " + descriptor.name + " as g" 

    where = []
    # where.push '`g`.`StartDate` >= "'+date_now+'"'
    where.push ' (`g`.`StartDate` >= NOW()  OR (`g`.`StartDate`<=NOW() AND `game_started`="1") )'
    where.push '   `g`.`game_opened`="1" '
        
    qstr += ' WHERE ' + where.join (' AND ')
    qstr += ' ' + _tmpl.orderby { field: 'g`.`StartDate', order:'ASC' }
            
    if params.count
      qstr += ' ' + _tmpl.limit  { start: 0, max: +params.count }
    
    log qstr
    Db.client.query qstr, (err, results, fields) ->
      _err 'getNextGame', err, qstr
      if err
        cb success:false, info:err
      else
        cb success:true, results:results  
  
    
   getGameTime: (params, descriptor, cb)->
    
    qstr     = "SELECT `g`.*, TIMESTAMPDIFF(SECOND, NOW(),`g`.`StartDate`) as `time_to_game` "
    dUTC     = tb.date().getTime()
    dService = dUTC + tb.serviceTimeOffset
    
    log 'server'
    log dService
    
    date_now = tb.date(dService).format('yyyy-mm-dd H:m:s')
    log date_now
    
    qstr    += " FROM " + descriptor.name + " as g" 

    where = []
    # where.push '`g`.`StartDate` >= "'+date_now+'"'
    where.push '`g`.`id` =  "'+params.id+'" '
        
    qstr += ' WHERE ' + where.join (' AND ')
    qstr += ' ' + _tmpl.orderby { field: 'g`.`StartDate', order:'ASC' }
            
    if params.count
      qstr += ' ' + _tmpl.limit  { start: 0, max: +params.count }
    
    Db.client.query qstr, (err, results, fields) ->
      _err 'getGameTime', err, qstr
      if err
        cb success:false, info:err
      else
        cb success:true, results:results    

  
          