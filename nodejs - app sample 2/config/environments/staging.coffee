exports.config =

  # HTTP server (becomes secondary server when HTTPS is enabled)
  http:
    port:         3003
    hostname:     "0.0.0.0"
  
  # HTTPS server (becomes primary server if enabled)
  https:
    enabled:      false
    port:         443
    domain:       "www.socketstream.org"

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
