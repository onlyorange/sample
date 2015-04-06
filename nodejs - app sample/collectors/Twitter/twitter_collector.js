

var tracktag = "facetorture";


var express = require('express')
  , http = require('http')
  , posts = require('./models/posts.js')
  , path = require('path');

var app = express();

//--- configuration settings
var ntwitter = require('ntwitter');
var request = require('request');
//var Posts = require('./models/posts');

var config = require('jsonconfig');
config.load(['../../config/settings.json']);

app.configure(function(){
  app.set('port', process.env.PORT || config.apps.collectors.twitter.port);
  app.use(express.logger('dev'));
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(app.router);
  // app.use(express.static(path.join(__dirname, 'public')));
});

app.configure('development', function(){
  app.use(express.errorHandler());
});

// Define ntwitter object
var tweety = new ntwitter({
      consumer_key: config.apps.collectors.twitter.consumer_key,
      consumer_secret: config.apps.collectors.twitter.consumer_secret,
      access_token_key: config.apps.collectors.twitter.access_token_key,
      access_token_secret: config.apps.collectors.twitter.access_token_secret
});

// Verify credentials
tweety.verifyCredentials(function(err, data) {
  if (err)
    console.log(err);

    console.log(data.screen_name + " is logged in");
});

// Listen in for keyword

tweety.stream('statuses/filter', {'track':tracktag}, function(stream) {

  stream.on('data', function (data) {
      console.log("-----------------------------------------");
      console.log("Text: " + data.text);
      
      var sent = "no";

      if (data.retweeted_status) {
        console.log("Retweet");
        sent = "yes";
      }

      else if (!data.retweeted_status) {
        if (data.entities.media) {
          console.log("-----------------------------------------");
          console.log("MEDIA ---> Text: " + data.text);

            for (var i=0; i< data.entities.media.length; i++){
              console.log ("------> url ("+ i + "): " + data.entities.media[i].url);
              console.log ("------> image url ("+ i + "): " + data.entities.media[i].media_url);
              console.log (" ");
            }

          console.log("-----> IMAGE!");
          sent = "yes";
          var type = "photo";

          // posts.send(data.id, data.user.id, data.user.screen_name, 
          // data.user.profile_image_url, data.user.name, data.entities.media[0].url, 
          // type, "twitter", tracktag, "location", data.created_at, data.entities.media[0].media_url, 
          // data.entities.media[0].media_url, data.entities.media[0].media_url, "", 
          // data.text, data.source);

          posts.send(
              data.id,              
              data.created_at,
              data.user.id, 
              data.user.screen_name, 
              data.user.name, 
              data.user.profile_image_url, 
              "image", 
              tracktag, 
              "-", 
              "twitter.com",
              data.entities.media[0].url,
              data.entities.media[0].media_url,
              data.entities.media[0].media_url,
              data.entities.media[0].media_url,
              "-",
              data.text);

        }
        if (data.entities.urls){
          for (var i = 0; i < data.entities.urls.length; i++) {
            var vinematch = data.entities.urls[i].expanded_url.match(/vine.co/);
            //data.entities.urls[i].url.toString().indexOf('vine.co') > -1)
            console.log("urls: " + data.entities.urls[i].expanded_url + " --> " + vinematch);
                
                if (data.entities.urls[i].expanded_url.match(/vine.co/) == "vine.co") {
                  // SaveVine(data.id, data.user.id, data.user.screen_name, 
                  //   data.user.profile_image_url, data.user.name, data.entities.urls[i].expanded_url, 
                  //   "vine", "tags", "location", data.created_at, data.entities.urls[i].expanded_url, 
                  //   data.entities.urls[i].expanded_url, data.entities.urls[i].expanded_url, "status", 
                  //   data.text, data.source);

                  console.log("----> VINES!");
                  sent = "yes";

                  posts.send(
                  data.id,              
                  data.created_at,
                  data.user.id, 
                  data.user.screen_name, 
                  data.user.name, 
                  data.user.profile_image_url, 
                  "vine", 
                  tracktag, 
                  "-", 
                  "twitter.com",
                  data.entities.urls[i].expanded_url,
                  data.entities.urls[i].expanded_url,
                  data.entities.urls[i].expanded_url,
                  data.entities.urls[i].expanded_url,
                  "-",
                  data.text);

                }
              }
         }
          if (sent == "no") {
            console.log("-----> just text...");
            sent = "yes";            
            var type = "text";

            posts.send(
              data.id,              
              data.created_at,
              data.user.id, 
              data.user.screen_name, 
              data.user.name, 
              data.user.profile_image_url, 
              "text", 
              tracktag, 
              "-", 
              "twitter.com",
              "-",
              "-",
              "-",
              "-",
              "-",
              data.text);

        }
      }
  });
});

http.createServer(app).listen(app.get('port'), function(){
  console.log("Express server listening on port " + app.get('port'));
});
