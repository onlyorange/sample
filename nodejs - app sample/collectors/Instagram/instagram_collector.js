
/**
 * Module dependencies.
 */

var express = require('express')
  , http = require('http')
  , posts = require('./models/posts.js')
  , path = require('path');

var app = express();

//--- configuration settings
var Instagram = require('instagram-node-lib');
var Posts = require('./models/posts');
var config = require('jsonconfig');
config.load(['../../config/settings.json']);

var ntwitter = require('ntwitter');
var request = require('request');


app.configure(function(){
  app.set('port', process.env.PORT || config.apps.collectors.instagram.port);
  app.use(express.logger('dev'));
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(app.router);
});

app.configure('development', function(){
  app.use(express.errorHandler());
});

var tracktag = "kittens";

//--- instagram listener
var client_id = config.apps.collectors.instagram.client_id;
var client_secret = config.apps.collectors.instagram.client_secret;
var redirect_uri = config.apps.collectors.instagram.callback_url;

Instagram.set('client_id', client_id);
Instagram.set('client_secret', client_secret);
Instagram.set('callback_url', redirect_uri);

//Instagram.subscriptions.subscribe({ object: 'tag', object_id: tracktag });

//--- instagram GET handshake
app.get('/handleauth', function(request, response){
  Instagram.subscriptions.handshake(request, response); 
});

app.post('/handleauth', function(request, response){
  // parse incoming data

  console.log (request.body);
  var data = request.body;

  for (var key in data) {
    var subscription_id = data[key].subscription_id;
    var changed_aspect = data[key].changed_aspect;
    var object = data[key].object;
    var object_id = data[key].object_id;
    var time = data[key].time;

    // if the required tag exists, get recent for that tag
    if (object_id) {
      posts.get_recent_posts(tracktag, function(request, response){
      });
    }
  };
});


http.createServer(app).listen(app.get('port'), function(){
  console.log("Express server listening on port " + app.get('port'));
  });
