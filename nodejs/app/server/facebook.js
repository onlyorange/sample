exports.actions = function(req, res, ss) {

	req.use('session');

	return {
		
		//
		// Handles an oauth callback from facebook
		callback: function(data) {
			if( data.status === 'success' )
			{
				// Do some custom auth logic
				req.session.setUserId(data.userId);
				req.session.facebookAccessToken = data.accessToken;
			}
			else
			{
				res('not oauthed');
			} 
		},
		
		//
		// Sets the facebook user id from the client value
		setUserData: function(data) {
			// sets our user data
			req.session.setUserId(data.userId);
			req.session.facebookAccessToken = data.accessToken;
			
			res(req.session.userId);
		}
	}	
}