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
    $is_new_page = 'posts';
    if(isset($_GET['action']) && ($_GET['action'] === 'add-new-upost' || $_GET['action'] === 'upost-edit')){
        $is_new_page = 'new';
    }elseif(isset($_GET['action']) && $_GET['action'] === 'account' || get_query_var( "um_tab" )){
        $is_new_page = 'account';
    }else{
        $is_new_page = 'posts';
    }
    ?>
    <a class="<?php echo (($is_new_page === 'posts')?'active':'') ?>" href="<?php echo get_the_permalink(  ) ?>">Posts</a>
    <a class="<?php echo (($is_new_page === 'new')?'active':'') ?>" href="<?php echo get_the_permalink(  ) ?>?action=add-new-upost">New post</a>
    <div class="welcome_user">
        <?php
        $user = get_user_by( "ID", get_current_user_id(  ) );
        ?>
        <h3 class="welcome_back">Welcome back <strong><?php echo $user->display_name ?></strong></h3>

        <div class="account_menu">
            <a class="<?php echo (($is_new_page === 'account')?'active':'') ?> account_btn" href="#">Account</a>
            <div class="account_popup dnone">
                <ul>
                    <li><a class="<?php echo ((isset($_GET['action']) && $_GET['action'] === 'account' || get_query_var( "um_tab" )) ? 'list_active': '') ?>" href="<?php echo get_the_permalink(  ) ?>?action=account">Profile</a></li>
                    <li><a class="<?php echo ((isset($_GET['action']) && $_GET['action'] === 'upost-users') ? 'list_active': '') ?>" href="<?php echo get_the_permalink(  ) ?>?action=upost-users">Users</a></li>
                    <li><a class="<?php echo ((isset($_GET['action']) && $_GET['action'] === 'upost-support') ? 'list_active': '') ?>" href="<?php echo get_the_permalink(  ) ?>?action=upost-support">Support</a></li>
                    <li><a href="<?php echo wp_logout_url( home_url() ); ?>">Log out</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php

if(isset($_GET['action']) && $_GET['action'] === 'upost-delete' && isset($_GET['id']) && intval(get_post_field( 'post_author', intval($_GET['id']) )) === get_current_user_id(  )){
    if(wp_delete_post(intval($_GET['id']), true)){
        wp_safe_redirect( get_the_permalink(  ) );
        exit;
    }
}

if(isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] === 'upost-duplicate' && !empty($_GET['id'])){
    $post_id = intval($_GET['id']);
    $post_title = get_the_title( $post_id );
    $post_category = get_the_terms( $post_id, 'upcats' );
    if(is_array($post_category) && sizeof($post_category) > 0){
        $post_category = $post_category[0]->term_id;
    }
    
    $post_article = get_post_meta($post_id, 'upost_article_content', true);
    $post_images = get_post_meta($post_id, 'upost_media_images', true);
    $post_pdf = get_post_meta($post_id, 'upost_pdf_file', true);
    $post_type = get_post_meta($post_id, 'type_of_upost', true);

    // Create post object
    $upost_args = array(
        'post_title'    => wp_strip_all_tags( $post_title." - copy" ),
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'   => 'unique-posts',
        'post_author'   => get_current_user_id(  )
    );
    
    $new_post_id = wp_insert_post( $upost_args );
    if($new_post_id > 0){
        wp_set_object_terms( $new_post_id, $post_category, 'upcats' );

        update_post_meta( $new_post_id, 'type_of_upost', $post_type );
		update_post_meta( $new_post_id, 'upost_article_content', $post_article );
		update_post_meta( $new_post_id, 'upost_media_images', $post_images );
		update_post_meta( $new_post_id, 'upost_pdf_file', $post_pdf );

        wp_safe_redirect( get_the_permalink(  )."?action=uposts" );
        exit;
    }
}

$upost_id = null;
if(isset($_GET['action']) && $_GET['action'] === 'upost-edit' && isset($_GET['id']) && intval(get_post_field( 'post_author', intval($_GET['id']) )) === get_current_user_id(  )){
    $upost_id = intval($_GET['id']);
    require_once plugin_dir_path( __FILE__ )."unique-posts-form.php";
}elseif(isset($_GET['action']) && $_GET['action'] === 'account' || get_query_var( "um_tab" )){
    require_once plugin_dir_path( __FILE__ )."account.php";
}elseif(isset($_GET['action']) && $_GET['action'] === 'add-new-upost'){
    require_once plugin_dir_path( __FILE__ )."unique-posts-form.php";
}elseif(isset($_GET['action']) && $_GET['action'] === 'upost-users'){
    print_r("<div class='upwarning'>Upcoming Feature.</div>");
}elseif(isset($_GET['action']) && $_GET['action'] === 'upost-support'){
    print_r("<div class='upwarning'>Upcoming Feature.</div>");
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
                            <?php echo get_the_date( "Y/m/d h:i a", $_GET['post'] ) ?>
                        </td>
                    </tr>
                </table>
                <a href="<?php echo get_the_permalink(  ) ?>?action=uposts">Close</a>
            </div>
        </div>
        <?php } ?>
        <table class="uposts_table table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Actions</th>
                    <th>Title</th>
                    <th>Unique ID</th>
                    <th>Post Type</th>
                    <th>Category</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Actions</th>
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
                        $type = get_post_meta($upost->ID, 'type_of_upost', true);
                        ?>
                        <tr id="post-<?php echo $upost->ID ?>">
                            <td>
                                <strong class="action_btn">
                                    <span class="action_btn_arrow"><i class="fa-solid fa-sort-down"></i></span>
                                    <span>View</span>
                                </strong>
    
                                <div class="action_popup dnone">
                                    <ul>
                                        <li><a class="upost-edit-btn" href="<?php echo get_the_permalink(  ) ?>?action=upost-edit&id=<?php echo $upost->ID ?>">Edit</a></li>
                                        <li><a href="<?php echo get_the_permalink(  ) ?>?action=upost-duplicate&id=<?php echo $upost->ID ?>">Duplicate</a></li>
                                        <li>
                                            <a class="shareBTN" href="#">Share</a>
                                            <div class="sharing_popup dnone">
                                                <div class="popup_contents urlsharepcontent">
                                                    <h3>Share</h3>
                                                    <div class="url_field">
                                                        <?php 
                                                        $uid = get_post_meta($upost->ID, 'upost_uid', true);
                                                        $uid = urldecode($uid);
                                                        ?>
                                                        <input type="url" value="<?php echo get_the_permalink( $upost->ID ).'?uid='.$uid ?>" class="share_post_url">
                                                        <div class="copyTooltip">
                                                            <button class="copy_post_url">
                                                                <span class="tooltiptext">Copy to clipboard</span>Copy
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <span class="closeUrlPopup">Close</span>
                                                </div>
                                            </div>
                                        </li>
    
                                        <?php
                                        if($type === 'pdf'){
                                            $post_pdf = get_post_meta($upost->ID, 'upost_pdf_file', true);
                                            $nametitle = str_replace([" ", ",", "'"], "-", $upost->post_title);
                                            $nametitle = str_replace(".", "", $nametitle);
    
                                            echo '<li><a class="download-file" data-name="'.strtolower( $nametitle ).'" data-pdf="'.$post_pdf.'" href="'.$post_pdf.'">Download PDF</a></li>';
                                        }
                                        ?>
    
                                        <li><a class="upost-del-btn" href="<?php echo get_the_permalink(  ) ?>?action=upost-delete&id=<?php echo $upost->ID ?>">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <strong class="mbl-th">Title</strong>
                                <div><a class="post_title_link" href="<?php echo get_the_permalink( $upost->ID ) ?>"><?php echo __($upost->post_title, 'unique-posts') ?></a></div>
                            </td>
                            <td>
                                <strong class="mbl-th">Unique ID</strong>
                                <div><?php echo  get_post_meta($upost->ID, 'upost_uid', true) ?></div>
                            </td>
                            <td>
                                <strong class="mbl-th">Post Type</strong>
                                <div>
                                    <?php 
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
                                <div><?php echo get_the_date( "Y/m/d h:i a", $upost->ID ) ?></div>
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
    <?php
}
?>
</div>