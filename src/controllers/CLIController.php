<?php
/**
 * Created by PhpStorm.
 * User: carnoldus
 * Date: 2016-09-27
 * Time: 3:03 PM
 */

namespace carnoldus\controllers;

use carnoldus\controllers\DataController;

class CLIController
{

    /**
     * @var \carnoldus\controllers\DataController The controller that handles data requests to Xero
     */
    private $dataController;

    /**
     * @var array $allowableCommands A list of allowable command strings and their associated
     * meta information, such as help descriptions
     */
    private $allowableCommands = [
        '-h'=>[
            'method'=>'help',
            'help'=>'Provides help info regarding possible commands (and got you here)'],
        'backup'=>[
            'method'=>'backup',
            'help'=>'Runs the backup for the provided type (eg, backup AccountsAll)']
    ];

    /**
     * CLIController constructor.
     */
    public function __construct()
    {
        $this->dataController = new DataController();
    }

    /**
     * @param $args         The args passed to the command line
     * @return mixed|string The output of the command or error
     */
    public function exec($args)
    {
        //  Check to ensure we can do what we're asked
        if(!isset($this->allowableCommands[$args[1]])){
            return $this->padResponse('The entered command is not recognized. Try -h for options.');
        }

        //  Grab the method name
        $commandMeta = $this->allowableCommands[$args[1]];

        //  Check to see if the method exists and is callable. If it is,
        //  call it and pass in the arg array
        if(is_callable([$this, $commandMeta['method']])){
            return call_user_func(array($this, $commandMeta['method']), $args);
        }

        //  We should never make it this far...
        return '';
    }

    /**
     * @param $args     The args passed to the command line
     * @return string   Information about the success or failure of the backup process
     */
    private function backup($args)
    {
        $backupRequest = isset($args[2]) ? $args[2] : null;

        if(!is_null($backupRequest)){

            $response = $this->dataController->performBackup($backupRequest, 'xml', false);

            if($response->success){
                return  $this->padResponse(
                        "\033[32m".$response->notice."\033[0m".PHP_EOL.
                        'The file backup can be found at '.$response->location);
            }else{
                return  $this->padResponse(
                        "\033[31m".$response->notice."\033[0m".PHP_EOL.
                        'This is most likely due to an invalid backup request');
            }

        }else{

            return $this->padResponse('Invalid backup type. Try AccountsAll or ContactsOnlyVendors');

        }

    }

    /**
     * @param $args     The args passed to the command line
     * @return string   A text summary of all available commands and how to use them
     */
    private function help($args)
    {
        $helpStrings = array_map(function($key, $value){

            return "\033[36m".$key."\e[0m\t\t".$value['help'];

        }, array_keys($this->allowableCommands), $this->allowableCommands);


        return $this->padResponse(implode(PHP_EOL, $helpStrings));
    }

    /**
     * @param $text         The text to pad
     * @param int $leading  The amount of leading line breaks to add
     * @param int $trailing The amound of trailing line breaks to add
     * @return string       The passed text, padded out on either end by line breaks
     */
    private function padResponse($text, $leading = 1, $trailing = 2)
    {
        $response = '';

        while ($leading) {
            $response .= PHP_EOL;
            $leading--;
        }

        $response .= $text;

        while ($trailing) {
            $response .= PHP_EOL;
            $trailing--;
        }

        return $response;

    }

}