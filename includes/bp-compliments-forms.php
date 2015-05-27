<?php
function handle_compliments_form_data() {
    if (isset($_POST['comp-modal-form'])) {
        $term_id = strip_tags(esc_sql($_POST['term_id']));
        $post_id = strip_tags(esc_sql($_POST['post_id']));
        $receiver_id = strip_tags(esc_sql($_POST['receiver_id']));
        $message = strip_tags(esc_sql($_POST['message']));
        $args = array(
            'term_id' => (int) $term_id,
            'post_id' => (int) $post_id,
            'message' => $message,
            'sender_id' => get_current_user_id()
        );
        if ($receiver_id) {
            $args['receiver_id'] = $receiver_id;
        }
        bp_compliments_start_compliment($args);
    }
}
add_action( 'bp_init', 'handle_compliments_form_data', 99 );


function bp_compliments_modal_form($pid = 0, $receiver_id = 0) {
    ?>
    <div class="comp-modal">
        <div class="comp-modal-content-wrap">
            <div class="comp-modal-title">
                <h2>Choose Your Compliment Type:</h2>
            </div>
            <div class="comp-modal-content">
                <form action="" method="post">
                    <?php
                    $args = array(
                        'hide_empty' => false,
                        'orderby'  => 'id'
                    );
                    $terms = get_terms( 'compliment', $args );
                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                        echo '<ul class="comp-form-ul">';
                        $count = 0;
                        foreach ( $terms as $term ) {
                            $count++;
                            $t_id = $term->term_id;
                            $term_meta = get_option( "taxonomy_$t_id" );
                            ?>
                            <li>
                                <label>
                                    <input type="radio" name="term_id" value="<?php echo $term->term_id; ?>" <?php if ($count == 1) { echo 'checked="checked"'; } ?>>
                                <span>
                                    <img style="height: 20px; width: 20px; vertical-align:middle" src='<?php echo esc_attr( $term_meta['compliments_icon'] ) ? esc_attr( $term_meta['compliments_icon'] ) : ''; ?>' class='preview-upload'/>
                                    <?php echo $term->name; ?>
                                </span>
                                </label>
                            </li>
                        <?php
                        }
                        echo '</ul>';

                        ?>
                        <textarea name="message" maxchar="100"></textarea>
                        <input type="hidden" name="post_id" value="<?php echo $pid; ?>"/>
                        <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>"/>
                        <div class="whoop-pop-buttons">
                            <button type="submit" class="comp-submit-btn" name="comp-modal-form" value="submit">Send</button>
                            <a class="bp-comp-cancel" href="#">Cancel</a>
                        </div>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery('a.bp-comp-cancel').click(function (e) {
                                    e.preventDefault();
                                    var container = jQuery('.comp-modal');
                                    container.hide();
                                });
                            });
                        </script>
                    <?php
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
<?php
}

function bp_compliments_modal_ajax()
{
    check_ajax_referer('bp-compliments-nonce', 'bp_compliments_nonce');
    bp_compliments_modal_form();
    wp_die();
}

//Ajax functions
add_action('wp_ajax_bp_compliments_modal_ajax', 'bp_compliments_modal_ajax');

//Javascript
add_action('bp_after_member_home_content', 'bp_compliments_js');
function bp_compliments_js() {
    $ajax_nonce = wp_create_nonce("bp-compliments-nonce");
    ?>
    <div class="comp-modal" style="display: none;">
        <div class="comp-modal-content-wrap">
            <div class="comp-modal-title comp-loading-icon">
                <i class="fa fa-cog fa-spin"></i>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('a.compliments-popup').click(function (e) {
                e.preventDefault();
                var container = jQuery('.comp-modal');
                container.show();
                var data = {
                    'action': 'bp_compliments_modal_ajax',
                    'bp_compliments_nonce': '<?php echo $ajax_nonce; ?>'
                };

                jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function (response) {
                    container.replaceWith(response);
                });
            });
        });
    </script>
<?php
}