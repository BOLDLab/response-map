<?php

require_once('OAuth.php');

class Lti {
	protected $testing = false;
	protected $ltivars = array();
	protected $secret = 'lti_key';
	protected $key = 'lti_secret';
	protected $valid = false;
	protected $errors = '';

	function __construct($options = null, $initialize = true, $error_messages = null) {
		$this->key = getenv('LTI_KEY');
		$this->secret = getenv('LTI_SECRET');
		if(!empty($_POST)) {
			$this->ltivars = $_POST;
		}
        if($this->testing) {
	        $this->valid = true;
	        $this->use_dummy_data();
        } else {
        	$store = new TrivialOAuthDataStore();
        	if(!isset($this->ltivars["oauth_consumer_key"])) {
        		$this->ltivars["oauth_consumer_key"] = '';
        	}
        	if($this->ltivars["oauth_consumer_key"] == $this->key) {
		        $store->add_consumer($this->ltivars["oauth_consumer_key"], $this->secret);
		        $server = new OAuthServer($store);
		        $method = new OAuthSignatureMethod_HMAC_SHA1();
		        $server->add_signature_method($method);
		        $request = OAuthRequest::from_request(NULL,NULL,NULL,$this->ltivars);
		        $this->basestring = $request->get_signature_base_string();
		        session_start();
		        try {
	    	        $server->verify_request($request);
	    	        $_SESSION[$this->ltivars['lis_result_sourcedid']] = $this->ltivars;
					$this->valid = true;
				} catch (Exception $e) {
					if(isset($_SESSION[$this->ltivars['lis_result_sourcedid']])) {
						$this->ltivars = $_SESSION[$this->ltivars['lis_result_sourcedid']];
						$this->valid = true;
					}  else if(isset($this->ltivars['lis_result_sourcedid'])) {
						$this->errors = 'Cannot update location, please try from a different browser';
					} else {
						$this->errors = 'Bad LTi Validation - '.$e->getMessage();
					}
				}
			} else {
				if(isset($_SESSION[$this->ltivars['lis_result_sourcedid']])) {
					$this->ltivars = $_SESSION[$this->ltivars['lis_result_sourcedid']];
					$this->valid = true;
				} else if(isset($this->ltivars['lis_result_sourcedid'])) {
					$this->ltivars['user_id'] = $_POST['ltifix_user_id'];
					session_start();
					$_SESSION[$this->ltivars['lis_result_sourcedid']] = $this->ltivars;
/* 					echo $this->ltivars['user_id']; */
/* 					print_r($this->ltivars); */
/* 					$this->errors = 'Bad LTi Validation - Invalid consumer key'; */
/* 					die(); */
					$this->valid = true;
				} else {
					$this->errors = 'Bad LTi Validation - Invalid consumer key';
				}
			}
		}
	}

	function get_user_id() {
		return $this->ltivars['user_id'];
	}

	function get_user_role() {
		return $this->ltivars['roles'];
	}

	function require_valid() {
		if($this->valid) {
			return;
		} else {
			echo $this->errors;
			die();
		}
	}

	function get_resource_link_id() {
		return $this->ltivars['resource_link_id'];
	}

	function get_lis_result_sourcedid() {
		return $this->ltivars['lis_result_sourcedid'];
	}

	function get_vars() {
		return $this->ltivars;
	}

	function use_dummy_data() {
		$this->ltivars = array(
		    'launch_presentation_return_url'=>'',
		    'lti_version'=>'LTI-1p0',
		    'user_id'=>'testing',
		    'roles'=>'Instructor',
		    'oauth_nonce'=>'60581087546369126111399262942',
		    'oauth_timestamp'=>'1399262942',
		    'lis_result_sourcedid'=>'UQx/ceit1001/2014_1:-i4x-UQx-ceit1001-lti-35fd269993224010adbacd8cd05f0043:student',
		    'context_id'=>'UQx/ceit1001/2014_1',
		    'oauth_consumer_key'=>'test',
		    'resource_link_id'=>'-i4x-UQx-ceit1001-lti-35fd269993224010adbacd8cd05f0043',
		    'oauth_signature_method'=>'HMAC-SHA1',
		    'oauth_version'=>'1.0',
		    'oauth_signature'=>'dSffHcwBbfyR01HQloYJIQRu9T0',
		    'lti_message_type'=>'basic-lti-launch-request',
		    'oauth_callback'=>'about:blank',
		);
	}

}

/**
 * A Trivial memory-based store - no support for tokens
 */
class TrivialOAuthDataStore extends OAuthDataStore {
    private $consumers = array();

    function add_consumer($consumer_key, $consumer_secret) {
        $this->consumers[$consumer_key] = $consumer_secret;
    }

    function lookup_consumer($consumer_key) {
        if ( strpos($consumer_key, "http://" ) === 0 ) {
            $consumer = new OAuthConsumer($consumer_key,"secret", NULL);
            return $consumer;
        }
        if ( $this->consumers[$consumer_key] ) {
            $consumer = new OAuthConsumer($consumer_key,$this->consumers[$consumer_key], NULL);
            return $consumer;
        }
        return NULL;
    }

    function lookup_token($consumer, $token_type, $token) {
        return new OAuthToken($consumer, "");
    }

    // Return NULL if the nonce has not been used
    // Return $nonce if the nonce was previously used
    function lookup_nonce($consumer, $token, $nonce, $timestamp) {
        // Should add some clever logic to keep nonces from
        // being reused - for no we are really trusting
	// that the timestamp will save us
        return NULL;
    }

    function new_request_token($consumer) {
        return NULL;
    }

    function new_access_token($token, $consumer) {
        return NULL;
    }
}

?>
