<?php

class Logging
{
    //------------------------------------------------------------------
    // setup raven php logging
    // TODO : format and put all log handling in separate file
    //------------------------------------------------------------------
    public static function setupRavenLogging($projectConfig)
    {
        // grab raven url if it's defined, else bail from setting up raven
        if (isset($projectConfig['logging']['raven'])) {

            $ravenUrl = $projectConfig['logging']['raven']['url'];
        }
        else {

            return;
        }

        // setup raven lib
        require FRAMEWORK_DIR . '/Libs/Raven/Autoloader.php';
        Raven_Autoloader::register();

        // setup client
        $client = new Raven_Client($ravenUrl);

        // Register error handler callbacks
        $error_handler = new Raven_ErrorHandler($client);
        set_error_handler(array($error_handler, 'handleError'));
        set_exception_handler(array($error_handler, 'handleException'));
    }
}
