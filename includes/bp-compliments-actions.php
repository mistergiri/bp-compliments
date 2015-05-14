<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function bp_compliments_action_start() {
    global $bp;

    if ( ! bp_is_current_component( $bp->compliments->compliments->slug ) || ! bp_is_current_action( 'start' ) ) {
        return;
    }

    if ( bp_displayed_user_id() == bp_loggedin_user_id() ) {
        return;
    }

    check_admin_referer( 'start_compliments' );

    if ( ! bp_compliments_start_compliment( array( 'receiver_id' => bp_displayed_user_id(), 'sender_id' => bp_loggedin_user_id() ) ) ) {
        bp_core_add_message( sprintf( __( 'There was a problem when trying to send compliment to %s, please try again.', 'bp-follow' ), bp_get_displayed_user_fullname() ), 'error' );
    } else {
        bp_core_add_message( sprintf( __( 'Your compliment sent to %s.', 'bp-follow' ), bp_get_displayed_user_fullname() ) );
    }

    // it's possible that wp_get_referer() returns false, so let's fallback to the displayed user's page
    $redirect = wp_get_referer() ? wp_get_referer() : bp_displayed_user_domain();
    bp_core_redirect( $redirect );
}
add_action( 'bp_actions', 'bp_compliments_action_start' );