<?php
/*
 * copied from enfolder
*/
$pointers = array();
$screens = array('page','post');

foreach($screens as $screen)
{
    $pointers[] = array(
        'id' => 'builder-button-pointer',   // unique id for this pointer
        'screen' => $screen, // this is the page hook we want our pointer to show on
        'target' => '#avia-builder-button', // the css selector for the pointer to be tied to, best to use ID's
        'title' => 'Layout Builder',
        'content' => __('','swedenWp' ),
        'position' => array(
                       'edge' => 'left', //top, bottom, left, right
                       'align' => 'middle' //top, bottom, left, right, middle
       )
    );

}
