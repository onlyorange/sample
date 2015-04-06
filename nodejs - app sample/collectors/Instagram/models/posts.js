var Client = require('node-rest-client').Client;

var config = require('jsonconfig');
config.load(['../../config/settings.json']);
var Aggregator = config.apps.APIS.record_media.server;


var Instagram = require('instagram-node-lib');


exports.get_recent_posts= function(req, res){

  var tag = req;

  Instagram.tags.recent({ name: tag, complete: function(data, pagination){

      console.dir("------- recent result set ------ ");
      //console.dir(data);

      for (var key in data) {

        // most(?) useful instagram elements
        var id = data[key].id;
        var user_id = data[key].user.id;
        var username = data[key].user.username;
        var profile_picture = data[key].user.profile_picture;
        var full_name = data[key].user.full_name;
        var link = data[key].link;
        var type = data[key].type;
        var tags = data[key].tags;
        var location = data[key].location;
        var created_time = data[key].created_time;
        var low_resolution = data[key].images.low_resolution.url;
        var thumbnail = data[key].images.thumbnail.url;
        var standard_resolution = data[key].images.standard_resolution.url;
       	var status = "-";
        var platform = "instagram"
        if (data[key].caption.text) {
        	var caption = data[key].caption.text;
        }
        else {
        	var caption = "";
        }

        client = new Client();

        var thispost = Aggregator + "id=" + id + "&created_time=" + created_time + "&user_id=" + 
        user_id + "&username=" + username + "&full_name=" + full_name + "&profile_picture=" + 
        profile_picture + "&type=" + type + "&tags=" + tags + "&location=" + location + "&platform=" + 
        platform + "&link=" + link + "&thumbnail=" + thumbnail + "&low_resolution=" + low_resolution + 
        "&standard_resolution=" + standard_resolution + "&status=" + status + "&text=" + caption;

        client.get(thispost, function(data, response){
            // parsed response body as js object
            console.log(data);
            // raw response
            //console.log(thispost);
        });

   	  }
   	}

	});
}
