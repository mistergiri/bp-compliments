<?php do_action('bp_before_member_' . bp_current_action() . '_content'); ?>

<?php //bp_compliments_modal_form(); ?>
<div class="yepp-review-header yepp-event-header-wrap">
    <div class="yepp-title-and-count">
        <h3 class="yepp-tab-title">
            <?php echo bp_get_displayed_user_displayname() . '\'s Compliments'; ?>
        </h3>
    </div>
</div>
<div class="comp-user-content">
    <ul class="comp-user-ul">
        <?php
        $compliments = bp_compliments_get_compliments();
        if ($compliments) {
            foreach ($compliments as $comp) {
                $t_id = $comp->term_id;
                $term = get_term_by('id', $t_id, 'compliment');
                $term_meta = get_option("taxonomy_$t_id");
                ?>
                <li>
                    <div class="comp-user-header">
            <span>
                <img style="height: 20px; width: 20px; vertical-align:middle"
                     src='<?php echo esc_attr($term_meta['compliments_icon']) ? esc_attr($term_meta['compliments_icon']) : ''; ?>'
                     class='preview-upload'/>
                <?php echo $term->name; ?>
            </span>
                        <em>
                            <?php echo date_i18n(get_option('date_format'), strtotime($comp->created_at)); ?>
                        </em>
                    </div>
                    <div class="comp-user-msg-wrap">
                        <?php $author_id = $comp->sender_id; ?>
                        <div class="gd-list-item-author comp-user">
                            <div class="comment-meta comment-author vcard">
                                <?php
                                $user = get_user_by('id', $author_id);
                                $name = yepp_bp_member_name(yepp_get_current_user_name($user));
                                $user_link = bp_core_get_user_domain($author_id);
                                ?>
                                <?php echo get_avatar($author_id, 60); ?>
                                <cite><b class="reviewer">
                                        <a href="<?php echo $user_link; ?>" class="url"><?php echo $name; ?></a>
                                    </b>
                                </cite>
                                <?php yepp_get_user_stats($author_id); ?>
                            </div>
                        </div>
                        <div class="comp-user-message">
                            <?php
                            if ($comp->post_id) {
                                echo "For ".bp_get_displayed_user_displayname()."'s review of: <a href='".get_the_permalink($comp->post_id)."'>".get_the_title($comp->post_id)."</a><br/>";
                            }
                            echo stripcslashes($comp->message); ?>
                        </div>
                    </div>
                </li>
            <?php
            }
        } else {
            if (bp_displayed_user_id() == bp_loggedin_user_id()) {
                ?>
                <div class="bp-no-compliments yepp-no-events">
                    <p><?php echo __('Aw, you have no compliments yet. To get some try sending compliments to others.', BP_COMP_TEXTDOMAIN); ?></p>
                    <p><i class="fa fa-trophy"></i></p>
                </div>
            <?php
            } else {
                ?>
                <div class="bp-no-compliments yepp-no-events">
                    <p><?php echo __('Sorry, no compliments just yet.', BP_COMP_TEXTDOMAIN); ?></p>
                    <p><i class="fa fa-trophy"></i></p>
                </div>
            <?php
            }
        }
        ?>
    </ul>
</div>
<?php do_action('bp_after_member_' . bp_current_action() . '_content'); ?>
