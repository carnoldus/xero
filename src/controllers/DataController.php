<?php
/**
 * Created by PhpStorm.
 * User: carnoldus
 * Date: 2016-09-26
 * Time: 7:28 PM
 */

namespace carnoldus\controllers;

use carnoldus\xero\XeroConnector;

/**
 * Class DataController
 * @package carnoldus\controllers
 *
 * Data Controller class provides restricted api-like access to the Xero
 * data store.
 */
class DataController
{
    /**
     * @var array $allowableRequests A list of allowable request strings,
     * which acts like a set of prepared statements for the get() method.
     * If the requested action isn't on the list, it's not allowed
     */
    protected $allowableRequests = [
        'AccountsAll'=>[
            'request'=>'Accounts',
            'filter'=>[]],
        'ContactsOnlyVendors'=>[
            'request'=>'Contacts',
            'filter'=>['Where' => 'IsSupplier==true']]
    ];

    /**
     * DataController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $request          The request name
     * @param string $format    The response format, default XML
     * @param bool $webResponse Whether this response will go out to the web or not.
     *                          Dictates things like setting headers, etc
     * @return string           A JSON encoded object containing information about
     *                          the response (success, location, updated and notice)
     */
    public function performBackup($request, $format = 'xml', $webResponse = true)
    {
        //  Get the data from Xero
        $data = $this->get($request, $format);

        if($data !== false){
            $success = $this->saveToDisk($data, $request, $format);
            $notice = $success ? 'Backup Success' : 'Backup Failed';
        }else{
            $success = false;
            $notice = 'Connection to Xero Failed';
        }

        $filepath = DOCSPATH.$request.'.'.$format;
        $location = $webResponse ? $_SERVER['SERVER_NAME'].$filepath : PUBLICDIR.$filepath;

        $response = new \stdClass();
        //  Return true or false
        $response->success = $success;
        //  Note that even if the save failed, there may be a file present from an old backup
        $response->location = file_exists(PUBLICDIR.$filepath) ? $location : null;
        //  Grab the last updated date. Could be for an old file, could be a new one, could be never
        $response->updated = $this->getUpdatedDateString(PUBLICDIR.$filepath);
        //  Put an optional notice for display
        $response->notice = $notice;


        if($webResponse){

            //  Encode
            $response = json_encode($response);
            //  Set the header, or else jQuery gets upset
            header('Content-type:application/json;charset=utf-8');
            //  Return
            return $response;

        }else{

            //  Return
            return $response;
        }
    }

    /**
     * @param $request  A request type, which must have a match in
     *                  $allowableRequests in order to function
     * @return string   The returned value from the service
     */
    protected function get($request, $format = 'xml')
    {
        //  Check to ensure we can do what we're asked
        if(!isset($this->allowableRequests[$request])){
            return false;
        }

        //  Grab the relevant params
        $params = $this->allowableRequests[$request];

        //  Make the call
        $xero = new XeroConnector();
        $response = $xero->get($params['request'], $params['filter'], $format);

        //  Return either the valid data, or false on failure
        return $response;
    }

    /**
     * @param $data     The string to write to disk
     * @param $filename The filename to write to
     * @param $ext      The file extension of the data type
     * @return bool     true on success, false otherwise
     */
    protected function saveToDisk($data, $filename, $ext)
    {
        return (file_put_contents(PUBLICDIR.DOCSPATH.$filename.'.'.$ext, $data, LOCK_EX) !== false);
    }

    /**
     * @param $file     The file path for the file we want to examine
     * @return string   A formatted date for display, or "Never" if the file does not exist
     */
    protected function getUpdatedDateString($file)
    {
        return file_exists($file) ? date('m/d/y', filemtime($file)) : 'Never';
    }

}