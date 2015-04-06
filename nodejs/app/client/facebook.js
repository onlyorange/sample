if (typeof(SS.client['facebook']) == 'undefined') SS.client['facebook'] = {};
//
// client side faceabook helpers
//

// facebook variables
var _fbUserId = '',
	_fbAccessToknen = '';

// callback functions
var _loggedInCallback,
	_anonCallback;

// external function for getting the fb user
exports.getUserId = function() {
	return _fbUserId;
}
exports.fbInit = function(args) {
	fbInit(args);
}


$(document).ready(function(){  	
  	// handle some bindings
  	$('#btnFacebookLogin').bind('click', function() {
  		fbLogin(); return false;
  	});
});

//
// Inits our facebook connection
// @param args:
//	successCallback -> The callback function for when the users is either already logged in or successfully oAuth's in
//	
function fbInit(args) {
	
	//
	// Some facebook auto-include stuff
	(function(d, debug){
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
		ref.parentNode.insertBefore(js, ref);
	}(document, /*debug*/ true));

	// map any passed in calllback functons
	if( args.loggedInCallback )
		_loggedInCallback = args.loggedInCallback;
	if( args.anonCallback )
		_anonCallback = args.anonCallback 
	
	window.fbAsyncInit = function() {
		
		FB.init({
		  appId      : args.appId
		  , channelUrl : args.url + 'channel.html'
		  , status     : true
		  , cookie     : true
		  , xfbml      : true
		});
	
		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
			    
			    _fbUserId = response.authResponse.userID;
	    		_fbAccessToken = response.authResponse.accessToken;
	    		
	    		var data = { // the data we're going to send around
    				userId: _fbUserId
    				, accessToken: _fbAccessToken
    			};
    			
	    		// send up to the server
	    		ss.rpc('facebook.setUserData', data, function(res) {
						//console.log(res);
				});
				
				// if provide, callback to the main page
				if( _loggedInCallback) 
					_loggedInCallback(data);
	    		
	  		} else if (response.status === 'not_authorized') {
				if( _anonCallback )
					_anonCallback(data);
			} else {
				// not logged into facebook
	  		}
	 	});
	};
};


//
// triggers a facebook login modal
function fbLogin() {
	FB.login(function(response) {
		
		var data = {};
		
		if( response.authResponse ) {
			user = response.authResponse;
			
			_fbUserId = user.userID; // set this so it can be access globally
			
			data = { // data we can pass back to the server/caller
				status: 'success'
				, userId : user.userID
				, accessToken : user.accessToken
			};
			
			// hit the server
			ss.rpc('facebook.callback', data, function(res) {
				//console.log(res);
			});
			
			// if provided, callback to the page
			if( _loggedInCallback) 
				_loggedInCallback(data);
		} else {
			
			// if provided, notify the page of this cancel action
			if( _anonCallback )
				_anonCallback({ status: 'cancel' });
		}
		
	});
}

