<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function bp_compliments_screen_compliments() {
    global $bp;

    do_action( 'bp_compliments_screen_compliments' );
    bp_core_load_template( 'members/single/compliments' );
}