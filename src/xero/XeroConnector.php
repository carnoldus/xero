<?php
/**
 * Created by PhpStorm.
 * User: carnoldus
 * Date: 2016-09-24
 * Time: 10:09 PM
 */

//  Pull the Xero class into the global namespace
namespace {
    require ROOTDIR.'/vendor/xero/xerooauth-php/lib/XeroOAuth.php';
}

//  And now we have two namespaces in one file. Sweet. This is off to a great start
namespace carnoldus\xero {

    /**
     * Class XeroConnector
     * @package carnoldus\xero
     *
     * Wrapper class for the wrapper library, because the lib as-is wants
     * you to set config values in the actual lib itself. Plus their
     * implementation of the config is a bit hairy and this is cleaner
     */
    class XeroConnector extends \XeroOAuth
    {

        /**
         * @var array $config_vars The authentication data for a Xero
         * connection
         */
        private $configVars = [

            //  Request info
            'application_type' => 'Private',
            'oauth_callback' => 'oob',
            'user_agent' => 'Data Backup',

            //  X509 keys and certs
            'consumer_key' => PUBLICKEY,
            'shared_secret' => PRIVATEKEY,
            'rsa_private_key' => ROOTDIR.'/config/certs/privatekey.pem',
            'rsa_public_key' => ROOTDIR.'/config/certs/publickey.cer',

            //  "Session" info. Took me too long to figure this out, but
            //  the wrapper class is universalized and needs session data
            //  set even when using a private, stateless connection. It
            //  just uses the same token set as the X509 keys and a blank
            //  session ID
            'access_token' => PUBLICKEY,
            'access_token_secret' => PRIVATEKEY,
            'session_handle' => '',

            //  API info
            'core_version' => '2.0',
            'payroll_version' => '1.0',
            'file_version' => '1.0' ];

        /**
         * XeroConnector constructor.
         */
        public function __construct()
        {
            //  Use our local config variables
            parent::__construct($this->configVars);

        }

        /**
         * @param $request          The request name
         * @param array $filter     Filters to apply (where, etc) to the returned data
         * @param string $format    The format the data should return in. Default XML
         * @return string|bool      The response in string form, or false on failure
         */
        public function get($request, $filter = [], $format = 'xml')
        {

            //  Create the url and get the response
            $url = $this->url($request);
            $response = $this->request('GET', $url, $filter, '', $format);

            //  If the response is good, return it
            if(isset($response['code']) && $response['code'] == 200){
                return $response['response'];
            }

            //  If it isn't, well...
            return false;

        }

    }
}