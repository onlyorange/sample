<?php
/**
 * ATG API call requests
 *
 * @author juhonglee
 */
class  restCalls {

    public $mk_service_domain;

    public function __construct()
    {
        $this->mk_service_domain = 'http://' . $projectConfig['mk_domain'];
    }

    /**
     * nav api
     *
     * @param string $country country code
     * @param string $lang language code
     * @return mixed json
     */
    public static function getNavData($country, $lang)
    {
        // setup option key vars + name
        $navData            = array();
        $navOptionKeyPrefix = "MKShopNav";
        $countryLang        = ucfirst($country) .
                              ucfirst($lang);
        $navOptionKey       = $navOptionKeyPrefix .
                              $countryLang;
        $navFallbackFile    = THEME_APP_DIR .
                              'MKD/data/MKShopNav.json';

        // try to get nav data from wp_options table
        $navData = get_option($navOptionKey);
        
        // grab nav data from fallback file if it's not in cache
        if(json_decode($navData)) {
        	return $navData;
        } else {
        	$navData = file_get_contents($navFallbackFile);
        	return $navData;
        }
        
    }

    // favorite api testing temp codes
    public function sendPostData($url, $post) {
        $url = $this->mk_service_domain . '/api/v1/login';
        //$post = array("thumbnailURL" => "http://", "title"=>"Nice title", "linkURL"=>"http://dk.com", "siteID"=>"us");
        //$post = array('login'=>'sweden', 'password'=>'xyz');
        $v = json_encode($post);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'AMK: 123'));
        $ch_response = curl_exec($ch);
        if ($ch_response === false) {
            $info = curl_getinfo($ch);
            curl_close($ch);
            die('error occured during curl exec. Additioanl info: ' . var_export($info));
        }
        curl_close($ch);
        $decodedData = json_decode($ch_response);
        if (isset($decodedData->response->status) && $decodedData->response->status == 'ERROR') {
            die('error occured: ' . $decodedData->response->errormessage);
        }

        return $decodedData;
    }

    // Method: POST, PUT, GET etc
    // Data: array("param" => "value") ==> index.php?param=value

    function CallAPI($method, $url, $data = false, $headers = false) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('AMK: 123'));

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                if ($headers)
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            case "GET":
                curl_setopt($curl, CURLOPT_GET, 1);
                if ($headers)
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        return curl_exec($curl);
    }

    /**
     * caching
     *
     * @param string $url: the URI for request
     * @param string $key: the name of the key in key store
     * @param number $timeout: the expiration time for key store
     * @return boolean|string|Ambigous <boolean, mixed>
     */
    public static function requestCache($url, $key, $timeout = 86400)
    {
        // this is the key stored in cache key value store
        $option = get_option($key);

        $data = self::makeRequest($url);

        // if failed allow main to handle failed request
        if($data === false)
        {
            return 'false';
        }

        else
        {
            update_option($key, $data);

            return $option;
        }
    }

    /**
     * request api call
     *
     * @param string $request: the URI for request
     * @return boolean|mixed
     */
    protected static function makeRequest($request) {

        // checks for client json timeout value, if none found, defaults to 1600;
        $api_timeout = (integer) $projectConfig['mk_api_timeout'];
        if ($api_timeout < 1) $api_timeout = 1600;

        $session = curl_init($request);
        // get header for examin response
        // curl_setopt($session, CURLOPT_HEADER, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        // this trick won't work with remote resources. need to find stable way.. :S
        curl_setopt($session, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($session, CURLOPT_TIMEOUT_MS, $api_timeout);
        curl_setopt($session, CURLOPT_HTTPHEADER, array('AMK: 123'));
        $response = curl_exec($session);

        if(!$response) return false;
        curl_close($session);

        // removed header portion...currently return true or false. no status code
        //if (self::statusChecks($response)) {
            //$json = json_decode($response);
        return $response;
        //}
    }

    /**
     * get status if there is header codes
     *
     * @param string $response
     * @return boolean
     */
    protected static function statusChecks($response) {
        // Get HTTP Status code from the response
        $statusCode = array();

        // not really checking anything for now.
        $ok = true;
        // Parse first line of header for header code
        preg_match('/\d\d\d/', $response, $statusCode);
        // Check the HTTP Status code
        switch ($statusCode[0]) {
            case 200:
                $ok_code = true;
                break;
            case 503:
                die('Service unavailable: 503');
                break;
            case 403:
                die('no permission to access: 403');
                break;
            case 400:
                die('failed: 400.');
                break;
            default:
                die('failed: ' . $statusCode[0]);
        }

        return $ok;
    }

    /*
    //get
    //next example will recieve all messages for specific conversation
    $service_url = 'http://example.com/api/conversations/[CONV_CODE]/messages&apikey=[API_KEY]';
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curl);
    $decoded = json_decode($curl_response);
    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
    die('error occured: ' . $decoded->response->errormessage);
    }
    echo 'response ok!';
    var_export($decoded->response);

    //post
    //next example will insert new conversation
    $service_url = 'http://example.com/api/conversations';
    $curl = curl_init($service_url);
    $curl_post_data = array(
            'message' => 'test message',
            'useridentifier' => 'agent@example.com',
            'department' => 'departmentId001',
            'subject' => 'My first conversation',
            'recipient' => 'recipient@example.com',
            'apikey' => 'key001'
    );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curl);
    $decoded = json_decode($curl_response);
    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
    die('error occured: ' . $decoded->response->errormessage);
    }
    echo 'response ok!';
    var_export($decoded->response);

    //put
    //next eample will change status of specific conversation to resolve
    $service_url = 'http://example.com/api/conversations/cid123/status';
    $ch = curl_init($service_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    $data = array("status" => 'R');
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
    $response = curl_exec($ch);
    if ($response === false) {
    $info = curl_getinfo($ch);
    curl_close($ch);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($ch);
    $decoded = json_decode($response);
    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
    die('error occured: ' . $decoded->response->errormessage);
    }
    echo 'response ok!';
    var_export($decoded->response);

    //delete
    $service_url = 'http://example.com/api/conversations/[CONVERSATION_ID]';
    $ch = curl_init($service_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    $curl_post_data = array(
            'note' => 'this is spam!',
            'useridentifier' => 'agent@example.com',
            'apikey' => 'key001'
    );
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    $response = curl_exec($ch);
    if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curl);
    $decoded = json_decode($curl_response);
    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
    die('error occured: ' . $decoded->response->errormessage);
    }
    echo 'response ok!';
    var_export($decoded->response);
    */

}
