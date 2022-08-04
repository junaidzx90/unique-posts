<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Unique_Posts
 * @subpackage Unique_Posts/public/partials
 */
?>

<div id="upost_dashboard">
<div id="profile_tabs">
    <?php
    $is_new_page = isset($_GET['action']) && ($_GET['action'] === 'add-new-upost' || $_GET['action'] === 'upost-edit');
    ?>
    <a class="<?php echo ((!$is_new_page)?'active':'') ?>" href="<?php echo get_the_permalink(  ) ?>">Posts</a>
    <a class="<?php echo (($is_new_page)?'active':'') ?>" href="?action=add-new-upost">New post</a>
    <div class="welcome_user">
        <?php
        $user = get_user_by( "ID", get_current_user_id(  ) );
        ?>
        <h3 class="welcome_back">Welcome back <strong><?php echo $user->display_name ?></strong></h3>
        <a href="<?php echo wp_logout_url( home_url() ); ?>">Log out</a>
    </div>
</div>

<?php

if(isset($_GET['action']) && $_GET['action'] === 'upost-delete' && isset($_GET['id']) && intval(get_post_field( 'post_author', intval($_GET['id']) )) === get_current_user_id(  )){
    if(wp_delete_post(intval($_GET['id']), true)){
        wp_safe_redirect( get_the_permalink(  ) );
        exit;
    }
}

$upost_id = null;
if(isset($_GET['action']) && $_GET['action'] === 'upost-edit' && isset($_GET['id']) && intval(get_post_field( 'post_author', intval($_GET['id']) )) === get_current_user_id(  )){
    $upost_id = intval($_GET['id']);
    require_once plugin_dir_path( __FILE__ )."unique-posts-form.php";
}elseif(isset($_GET['action']) && $_GET['action'] === 'add-new-upost'){
    require_once plugin_dir_path( __FILE__ )."unique-posts-form.php";
}else{ 
    if(isset($_GET['status']) && $_GET['status'] === 'success' && isset($_GET['post']) && !empty($_GET['post']) && intval(get_post_field( 'post_author', $_GET['post'] )) === get_current_user_id(  )){ ?>
    <div class="success_popup">
        <div class="popup_contents">
            <h3>The post has been successfully created.</h3>
            <table>
                <tr>
                    <th>User name</th>
                    <td>
                        <?php
                        $user = get_user_by( "ID", get_current_user_id(  ) );
                        echo ucfirst($user->display_name);
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Unique ID</th>
                    <td><?php echo  get_post_meta($_GET['post'], 'upost_uid', true); ?></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>
                        <?php echo get_the_date( "F j, Y, g:i a", $_GET['post'] ) ?>
                    </td>
                </tr>
            </table>
            <a href="<?php echo get_the_permalink(  ) ?>">Close</a>
        </div>
    </div>
    <?php } ?>
    <table class="uposts_table table-hover" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Title</th>
                <th>Unique ID</th>
                <th>Post Type</th>
                <th>Category</th>
                <th>Date</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Title</th>
                <th>Unique ID</th>
                <th>Post Type</th>
                <th>Category</th>
                <th>Date</th>
            </tr>
        </tfoot>
        <tbody>
            <?php
            $args = [
                'post_type' => 'unique-posts',
                'post_status' => ['publish', 'draft'],
                'orderby' => 'date',
                'order' => 'DESC',
                'numberposts' => -1,
                'author' => get_current_user_id(  )
            ];

            $uposts = get_posts($args);
            if($uposts){
                foreach($uposts as $upost){
                    ?>
                    <tr id="post-<?php echo $upost->ID ?>">
                        <td>
                            <strong class="mbl-th">Title</strong>
                            <div><a class="post_title_link" href="<?php echo get_the_permalink( $upost->ID ) ?>"><?php echo __($upost->post_title, 'unique-posts') ?></a></div>
                            <div>
                                <a class="upost-edit-btn" href="?action=upost-edit&id=<?php echo $upost->ID ?>">Edit</a>
                                <a class="upost-del-btn" href="?action=upost-delete&id=<?php echo $upost->ID ?>">Delete</a>
                            </div>
                        </td>
                        <td>
                            <strong class="mbl-th">Unique ID</strong>
                            <div><?php echo  get_post_meta($upost->ID, 'upost_uid', true) ?></div>
                        </td>
                        <td>
                            <strong class="mbl-th">Post Type</strong>
                            <div>
                                <?php 
                                $type = get_post_meta($upost->ID, 'type_of_upost', true);
                                echo __(ucfirst($type), 'unique-posts');
                                ?>
                            </div>
                        </td>
                        <td>
                            <strong class="mbl-th">Category</strong>
                            <div><?php
                             $term_obj_list = get_the_terms( $upost->ID, 'upcats' );
                             $terms_string = join(', ', wp_list_pluck($term_obj_list, 'name'));
                             echo $terms_string;
                            ?></div>
                        </td>
                        <td>
                            <strong class="mbl-th">Date</strong>
                            <div><?php echo get_the_date( "F j, Y, g:i a", $upost->ID ) ?></div>
                        </td>
                    </tr>
                    <?php
                }
            }else{
                echo '<tr>
                    <td colspan="5">No posts found!</td>
                </tr>';
            }
            ?>
        </tbody>
    </table>
    <?php } ?>
</div>