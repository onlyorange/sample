<?php
// main functionality
require_once dirname(__FILE__) . '/main.php';

// optional
//if($themecontroller)
require_once dirname(__FILE__) . '/elements.php';

require_once dirname(__FILE__) . '/custom-posts.php';

// ATG integration
require_once dirname(__FILE__) . '/ATG-integration.php';

// sitemap plugin
require_once dirname(__FILE__) . '/plugin/google-sitemap-generator/sitemap.php';

// load libs
require_once dirname(__FILE__) . '/lib/video.php';

?>
