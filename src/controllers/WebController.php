<?php
/**
 * Created by PhpStorm.
 * User: carnoldus
 * Date: 2016-09-26
 * Time: 5:22 AM
 */

namespace carnoldus\controllers;

/**
 * Class WebController
 * @package carnoldus\controllers
 *
 * Web Controller class provides a web interface for Xero data access
 */
class WebController
{

    /**
     * @var \Twig_Environment $twig Template library class
     */
    private $twig;

    /**
     * WebController constructor.
     * @param $templateDir The directory where the twig templates are stored
     */
    public function __construct($templateDir)
    {
        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem($templateDir));
    }

    /**
     * @return string The rendered web interface
     */
    public function html()
    {
        $template = $this->twig->loadTemplate('index.html');
        $vars = [];

        //  Set the default state of the page
        $vars['accountsAllLastUpdate'] = $this->getUpdatedDateString(PUBLICDIR.'/docs/AccountsAll.xml');
        $vars['contactsOnlyVendorsLastUpdate'] = $this->getUpdatedDateString(PUBLICDIR.'/docs/ContactsOnlyVendors.xml');

        $vars['accountsAllDownloadButtonsVisibility'] = ($vars['accountsAllLastUpdate'] == 'Never') ? ' hidden' : '';
        $vars['contactsOnlyVendorsDownloadButtonsVisibility'] = ($vars['contactsOnlyVendorsLastUpdate'] == 'Never') ? ' hidden' : '';

        return $template->render($vars);
    }

    /**
     * @param $file     The file path for the file we want to examine
     * @return string   A formatted date for display, or "Never" if the file does not exist
     */
    private function getUpdatedDateString($file)
    {
        return file_exists($file) ? date('m/d/y', filemtime($file)) : 'Never';
    }

}