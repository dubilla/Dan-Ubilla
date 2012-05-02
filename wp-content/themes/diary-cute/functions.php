<?php
if ( function_exists('register_sidebar') )
    register_sidebar( array(
        'before_widget' => '<div class="tabshow1-t"></div><div class="contentlist1">',
        'after_widget' => '</div><div class="tabshow1-b"></div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
?>