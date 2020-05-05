<?
if(!session_id()) {
    session_start();
}
// Load Facebook SDK
require_once __DIR__ . '/../../assets/php-graph-sdk/src/Facebook/autoload.php';

// Load WP environment
define( 'WP_USE_THEMES', false );
require( '../../../../../wp-load.php' );

// initialize Facebook Classes
$config = [
  'app_id' => get_option( 'fbfpi_appid' ),
  'app_secret' => get_option( 'fbfpi_appsecret'),
  'default_graph_version' => 'v6.0'
  ];
  
if (($config['app_id'] != '') && ($config['app_secret'] != '')) {

	$fb = new Facebook\Facebook($config);

	$fbApp  = new Facebook\FacebookApp( $config['app_id'], $config['app_secret'], $config['default_graph_version'] );
	 
	// Get User Access Token
	$helper = $fb->getRedirectLoginHelper();
	try {
	  $accessToken = $helper->getAccessToken();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	  // When Graph returns an error
	  echo 'Graph returned an error: ' . $e->getMessage();
	  exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	  // When validation fails or other local issues
	  echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  exit;
	}

	// Error handling
	if (! isset($accessToken)) {
	  if ($helper->getError()) {
		// header('HTTP/1.0 401 Unauthorized');
		echo "Error: " . $helper->getError() . "\n";
		echo "Error Code: " . $helper->getErrorCode() . "\n";
		echo "Error Reason: " . $helper->getErrorReason() . "\n";
		echo "Error Description: " . $helper->getErrorDescription() . "\n";
	  } else {
		// header('HTTP/1.0 400 Bad Request');
		echo 'Bad request';
	  }
	  exit;
	}
	 
	// Handling User access token
	// The OAuth 2.0 client handler helps us manage access tokens
	$oAuth2Client = $fb->getOAuth2Client();
	// Get the access token metadata from /debug_token
	$tokenMetadata = $oAuth2Client->debugToken($accessToken);
	// Validation (these will throw FacebookSDKException's when they fail)
	$tokenMetadata->validateAppId($config['app_id']);

	// If you know the user ID this access token belongs to, you can validate it here
	//$tokenMetadata->validateUserId('123');
	$tokenMetadata->validateExpiration();
	 
	if (! $accessToken->isLongLived()) {
	  // Exchanges a short-lived access token for a long-lived one
	  try {
		$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	  } catch (Facebook\Exceptions\FacebookSDKException $e) {
		echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
		exit;
	  }
	}
	 
	$_SESSION['fb_access_token'] = (string) $accessToken;
	// User is logged in with a long-lived access token.

	// Get page access token with user access token
	$page = get_option('fbfpi_fanpage_id');

	$request = new Facebook\FacebookRequest(
		$fbApp,
		$_SESSION['fb_access_token'],//my user access token
		'GET',
		'/'.$page.'?fields=access_token',
		array( 'ADMINISTER' )
	);
	$response  = $fb->getClient()->sendRequest( $request );
	$json           = json_decode( $response->getBody() );
	$page_access    = $json->access_token;
	$tokenMetadata = $oAuth2Client->debugToken($accessToken); 
	$expire = $tokenMetadata->getExpiresAt();

	// save access token and expiration (unixtime int) in WP database
	update_option('fbfpi_accesstoken', $page_access);
	update_option('fbfpi_accesstoken_expire', $expire);

	$url = get_site_url(null, '', 'https') . "/wp-admin/tools.php?page=facebook-fanpage-import%2Fcomponents%2Fadmin%2Fsettings.php";
}
header('Location: ' . $url);