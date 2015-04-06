<?php

class ProjectConfig
{
    public static function get($topLevel = false)
    {
        //------------------------------------------------------------------
        // setup environment
        // (NOTE : SU_ENVIRONMENT should be defined in wp-config.php
        //         by config mgmt code (chef), but we flip to
        //         sandbox just in case it hasn't been found)
        //------------------------------------------------------------------
        if (!defined('SU_ENVIRONMENT'))
        {
            define('SU_ENVIRONMENT', 'sandbox');
        }


        //------------------------------------------------------------------
        // get project config from client json file
        //------------------------------------------------------------------

        $projectConfigFile = dirname(__FILE__) . '/../../../../../client.json';


        //------------------------------------------------------------------
        // parse project config and setup for environment
        //------------------------------------------------------------------
        if (file_exists($projectConfigFile))
        {

            $projectConfig = json_decode(file_get_contents($projectConfigFile), true);

            if ($topLevel == true) $projectConfig = $projectConfig;
            else $projectConfig = $projectConfig['environments'][SU_ENVIRONMENT];

            return $projectConfig;
        }

        else
        {
            return false;
        }
    }
}

