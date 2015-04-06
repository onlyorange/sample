exports.config =

  # HTTP server (becomes secondary server when HTTPS is enabled)
  http:
    port:         3000
    hostname:     "ec2-23-22-235-185.compute-1.amazonaws.com"
  
  # HTTPS server (becomes primary server if enabled)
  https:
    enabled:      false
    port:         443
    domain:       "ec2-23-22-235-185.compute-1.amazonaws.com"

  # HTTP(S) request-based API
  api:
    enabled:      true
    prefix:       'api'
    https_only:   false

  # Show customizable 'Incompatible Browser' page if browser does not support websockets
  browser_check:
    enabled:      false
    strict:       true
  
  # Facebook config values
  facebook: 
    app_id:       "273188282803852"
    app_secret:   "c48a92d9c829191b85a27dda98efb9dc"
