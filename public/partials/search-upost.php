<div id="search_module">
    <div class="search_upost_contents">
        <form method="get" class="search_box">
            <input type="text" name="unique_post_search" placeholder="Verify by Unique ID" value="<?php echo ((isset($_GET['unique_post_search'])) ? $_GET['unique_post_search'] : '') ?>">
            <button type="submit"><svg fill="#009688" width="32px" height="32px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve">
            <g><g><path d="M688.5,424.6c0-72.6-25.8-134.8-77.4-186.4s-113.8-77.4-186.4-77.4s-134.8,25.8-186.4,77.4S160.8,352,160.8,424.6c0,72.6,25.8,134.8,77.4,186.4c51.6,51.6,113.8,77.4,186.4,77.4S559.4,662.6,611,611C662.6,559.4,688.5,497.3,688.5,424.6L688.5,424.6z M990,914.6c0,20.4-7.5,38.1-22.4,53c-14.9,14.9-32.6,22.4-53,22.4c-21.2,0-38.9-7.5-53-22.4l-202-201.4c-70.3,48.7-148.6,73-235,73c-56.1,0-109.8-10.9-161.1-32.7s-95.4-51.2-132.5-88.3c-37.1-37.1-66.5-81.3-88.3-132.5C20.9,534.5,10,480.8,10,424.6s10.9-109.8,32.7-161.1S93.9,168.1,131,131c37.1-37.1,81.3-66.6,132.5-88.3S368.5,10,424.6,10s109.8,10.9,161.1,32.7c51.2,21.8,95.4,51.2,132.5,88.3c37.1,37.1,66.6,81.3,88.3,132.5c21.8,51.2,32.7,104.9,32.7,161.1c0,86.4-24.3,164.7-73,235l202,202C982.7,876.1,990,893.8,990,914.6L990,914.6z"/></g></g>
            </svg></button>
        </form>
    </div>
</div>

<div id="search_results">
    <?php
    global $wpdb;
    if(isset($_GET['unique_post_search'])){
        $uid = $_GET['unique_post_search'];
        if($uid){
            $upost_id = $wpdb->get_var("SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'upost_uid' AND meta_value = '$uid'");

            $uid = urlencode($uid);
            
            if($upost_id){
                $upost = get_post( $upost_id );

                $post_article = get_post_meta($upost->ID, 'upost_article_content', true);
                $post_images = get_post_meta($upost->ID, 'upost_media_images', true);
                $post_pdf = get_post_meta($upost->ID, 'upost_pdf_file', true);
                $post_type = get_post_meta($upost->ID, 'type_of_upost', true);

                ?>
                <div class="result_contents">
                    <div class="upost">
                        <h3><?php echo __($upost->post_title, 'unique-posts') ?></h3>
                        <p class="post-info">Created by: 
                            <b><?php 
                            $user = get_user_by( "ID", $upost->post_author ); 
                            echo $user->display_name;
                            ?></b>, 
                            Date: <b><?php echo get_the_date( "Y/m/d h:i a", $upost->ID ) ?></b>
                        </p>

                        <?php
                        switch ($post_type) {
                            case 'article':
                                echo '<div class="pdf-actions">
                                    <span class="verifield">Verified</span></div>';
                                ?>
                                <div class="article_excerpt">
                                    <p><?php echo wp_trim_words($post_article, 20) ?></p>
                                </div>
                                <a class="view-post uubtn" href="<?php echo get_the_permalink( $upost->ID ).'?uid='.$uid ?>">View this post</a>
                                <?php
                                break;
                            case 'images':
                                if(is_array($post_images) && sizeof($post_images) > 0){
                                    echo '<div class="pdf-actions">
                                    <span class="verifield">Verified</span></div>';
                                    echo '<div id="upost_images">';
                                    foreach($post_images as $image){
                                        ?>
                                        <div class="upost_image">
                                            <img src="<?php echo $image ?>">
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                    <a class="view-post uubtn" href="<?php echo get_the_permalink( $upost->ID ).'?uid='.$uid ?>">View this post</a>
                                    <?php
                                }
                                break;
                            case 'pdf':
                                ?>
								<div class="pdf-actions">
                                    <span class="verifield">Verified</span>
									<div class="post_action_btns">
                                        <a target="_blank" class="upost_btn" href="<?php echo $post_pdf ?>">Open this file</a>
                                        
                                        <?php
                                        $nametitle = str_replace([" ", ",", "'"], "-", $upost->post_title);
                                        $nametitle = str_replace(".", "", $nametitle);
                                        ?>

                                        <a class="download-file upost_btn" data-name="<?php echo strtolower( $nametitle ) ?>" data-pdf="<?php echo $post_pdf ?>" href="<?php echo $post_pdf ?>">Download</a>
                                    </div>
								</div>
                                <div id="upost_pdf_preview" data-src="<?php echo $post_pdf ?>">
                                    <div class="upost_pdf_previews">
                                        <img src="">
                                    </div>
                                </div>
                                
                                <?php
                                break;
                        }
                        ?>
                        
                    </div>
                </div>
                <?php
            }else{
                echo '<div class="nopostFound">No post found for the Unique ID</div>';
            }
        }
    }
    ?>
</div>