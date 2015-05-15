<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BP_Compliments {
    /**
     * The compliments ID.
     */
    public $id = 0;

    /**
     * The user ID of receiver.
     */
    public $receiver_id;

    /**
     * The user ID of sender.
     */
    var $sender_id;

    /**
     * Constructor.
     *
     * @param int $receiver_id The user ID of the user you want to compliment.
     * @param int $sender_id The user ID initiating the compliment request.
     */
    public function __construct( $receiver_id = 0, $sender_id = 0 ) {
        if ( ! empty( $receiver_id ) && ! empty( $sender_id ) ) {
            $this->receiver_id   = (int) $receiver_id;
            $this->sender_id = (int) $sender_id;
        }
    }


    /**
     * Saves a compliment into the database.
     */
    public function save() {
        global $wpdb, $bp;

        // do not use these filters
        // use the 'bp_compliments_before_save' hook instead
        $this->receiver_id   = apply_filters( 'bp_compliments_receiver_id_before_save',   $this->receiver_id,   $this->id );
        $this->sender_id = apply_filters( 'bp_compliments_sender_id_before_save', $this->sender_id, $this->id );

        do_action_ref_array( 'bp_compliments_before_save', array( &$this ) );

        $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->compliments->table_name} ( receiver_id, sender_id, created_at ) VALUES ( %d, %d, %s )", $this->receiver_id, $this->sender_id, current_time( 'mysql' ) ) );
        $this->id = $wpdb->insert_id;

        do_action_ref_array( 'bp_compliments_after_save', array( &$this ) );

        return $result;
    }

    /**
     * Deletes a compliment from the database.
     */
    public function delete() {
        global $wpdb, $bp;

        return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->compliments->table_name} WHERE id = %d", $this->id ) );
    }

    /**
     * Get the sender IDs for a given user.
     */
    public static function get_senders( $user_id ) {
        global $bp, $wpdb;
        return $wpdb->get_col( $wpdb->prepare( "SELECT sender_id FROM {$bp->compliments->table_name} WHERE receiver_id = %d", $user_id ) );
    }

    /**
     * Get the sender IDs for a given user.
     */
    public static function get_compliments( $user_id ) {
        global $bp, $wpdb;
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bp->compliments->table_name} WHERE receiver_id = %d", $user_id ) );
    }

    /**
     * Get the user IDs that a user is receivers.
     */
    public static function get_receivers( $user_id ) {
        global $bp, $wpdb;
        return $wpdb->get_col( $wpdb->prepare( "SELECT receiver_id FROM {$bp->compliments->table_name} WHERE sender_id = %d", $user_id ) );
    }

    /**
     * Get the senders / receivers counts for a given user.
     */
    public static function get_counts( $user_id ) {
        global $bp, $wpdb;

        $senders = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->compliments->table_name} WHERE receiver_id = %d", $user_id ) );
        $receivers = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->compliments->table_name} WHERE sender_id = %d", $user_id ) );

        return array( 'senders' => $senders, 'receivers' => $receivers );
    }


    /**
     * Deletes all compliments for a given user.
     *
     */
    public static function delete_all_for_user( $user_id ) {
        global $bp, $wpdb;

        $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->compliments->table_name} WHERE receiver_id = %d OR sender_id = %d", $user_id, $user_id ) );
    }
}