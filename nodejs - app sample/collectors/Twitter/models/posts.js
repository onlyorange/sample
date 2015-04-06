var Client = require('node-rest-client').Client;

var config = require('jsonconfig');
config.load(['../../config/settings.json']);
var Aggregator = config.apps.APIS.record_media.server;



function Tweet (id, created_time, user_id, username, full_name, profile_picture, 
	type, tags, location, platform, link, thumbnail, low_resolution, standard_resolution, 
	status, text) {

console.log("------------> INSIDE " + text);


client = new Client();

var thispost = Aggregator + "id=" + id + "&created_time=" + created_time + "&user_id=" + user_id + "&username=" + username + "&full_name=" + full_name + "&profile_picture=" + profile_picture + "&type=" + type + "&tags=" + tags + "&location=" + location + "&platform=" + platform + "&link=" + link + "&thumbnail=" + thumbnail + "&low_resolution=" + low_resolution + "&standard_resolution=" + standard_resolution + "&status=" + status + "&text=" + text;

client.get(thispost, function(data, response){
            // parsed response body as js object
            console.log(data);
            // raw response
            //console.log(thispost);
        });


// /media?id=361623922703208450&
// created_time=twitter.com
// &user_id=Sun%20Jul%2028%2023:07:16%20+0000%202013&username=1620082567&full_name=katy%20cat&profile_picture=Lilkitty_purry&type=text&tag=kittens&location=location&platform=twitter&link=http://a0.twimg.com/profile_images/378800000190683940/626015004701c0e0caba613879db89ac_normal.jpeg&thumbnail=&low_resolution=standard_resolution=&status=&text=



// rest.post(Aggregator, {
// 	data: { 
// 	'id': id,
// 	'created_time': created_time,
// 	'user_id': user_id,
// 	'username': username,
// 	'full_name': full_name,
// 	'profile_picture': profile_picture,
// 	'type': type,
// 	'tags': tags,
// 	'location': location,
// 	'platform': platform,
// 	'link': link,
// 	'thumbnail': thumbnail,
// 	'low_resolution': low_resolution,
// 	'standard_resolution': standard_resolution,
// 	'status': status,
// 	'text': text
// 	},
// 	}).on('complete', 

// 	function(data, response) {
// 		// if (response.statusCode == 201) {
// 		// // you can get at the raw response like this...
// 		// }
// 		console.log(response.statusCode);
// 	});
}


// rest.post(Aggregator, {
// 	data: { 
// 	'id': id,
// 	'created_time': created_time,
// 	'user_id': user_id,
// 	'username': username,
// 	'full_name': full_name,
// 	'profile_picture': profile_picture,
// 	'type': type,
// 	'tags': tags,
// 	'location': location,
// 	'platform': platform,
// 	'link': link,
// 	'thumbnail': thumbnail,
// 	'low_resolution': low_resolution,
// 	'standard_resolution': standard_resolution,
// 	'status': status,
// 	'text': text
// 	},
// 	}).on('complete', 

// 	function(data, response) {
// 		// if (response.statusCode == 201) {
// 		// // you can get at the raw response like this...
// 		// }
// 		console.log(response.statusCode);
// 	});
// }


module.exports.send = Tweet;


// id=12312&created_time=23&user_id=234&username=2345&full_name=234234&

// profile_picture=sdf&type=text&tags=6sec&location=nyc&platform=twitter&

// link=http&thumbnail=sdfasdfasdf&low_resolution=low&standard_resolution=standard&

// status=status&text=text
