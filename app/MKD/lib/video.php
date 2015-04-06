<?php

class MKD_Video {

    /**
     * Scene7 Handler
     *
     *
     * @param string $playerid
     * @return string $output returns the modified html string
     */
    function displayVideo($id, $type, $loop) {

        if ($type === "scene7")
        {

            #
            # TODO : define scene7 urls for each environment (this should probably be pulled from a conf file)
            #      : grab video size from content element?
            #
            $videoAsset            = $id;
            $scene7RootDomain      = "http://michaelkors.scene7.com";
            $videoScene7RootFolder = "MichaelKors";
            $videoContainer        = "avia-video";
            // $videoSize             = "1323,750";
            $videoAssetLocation    = $videoScene7RootFolder . "/" . $videoAsset;

            if ($loop == 'true')
            {
                $videoLoop = 1;
                $video_loop_class = "is-looping";
            }

            else
            {
                $videoLoop = 0;
            }

            $output = <<<SCENE7
            <script language='javascript' type='text/javascript' src="$scene7RootDomain/s7viewers/html5/js/VideoViewer.js"></script>
            <div id="scene7-$id" class="video-player video-content $video_loop_class" data-video-type="scene7" data-video-loop="$videoLoop" data-video-id="$id" data-scene7-asset="$videoAssetLocation" data-scene7-root-domain="$scene7RootDomain" data-scene7-root-folder="$videoScene7RootFolder"></div>
SCENE7;

            return $output;

        }

        else if ($type === "youtube")
        {

            /**
             * Youtube Handler
             *
             * @param string $id
             * @param string $type
             * @return string $output returns the modified html string
            */

            if ($loop == true) $loop = 'true';
            if ($loop == false) $loop = 'false';


            $videoElementID = str_replace('_', '-', $id);


            $output = <<<YOUTUBE
                    <div id="video-$videoElementID" class="video-player video-content video-player-youtube" data-video-id="$id" data-video-type="$type" data-video-loop="$loop"></div>
YOUTUBE;

            return $output;
        }
    }

}

?>
