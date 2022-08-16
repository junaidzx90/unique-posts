<?php
if(isset($_POST['update_front_upost'])){
    $post_type = ((isset($_POST['post_type'])) ? $_POST['post_type'] : '');
    $post_title = ((isset($_POST['post_title'])) ? $_POST['post_title'] : 'No title');
    $post_category = ((isset($_POST['post_category'])) ? intval($_POST['post_category']) : '');
    $post_title = sanitize_text_field( $post_title );
    $post_article = ((isset($_POST['upost_article_content'])) ? $_POST['upost_article_content'] : '');
    $post_images = ((isset($_FILES['upost_images'])) ? $_FILES['upost_images'] : '');
    $post_pdf = ((isset($_FILES['upost_pdf'])) ? $_FILES['upost_pdf'] : '');

    if(empty($post_category)){
        return;
    }

    $image_urls = [];
    $post_pdf_url = '';

    switch ($post_type) {
        case 'images':
            if(!empty($post_images['name'][0])){
                foreach($post_images['name'] as $key => $val){
                    $image_urls[] = $this->upload_upost_files($post_images, $key);
                }
            }
            break;
        case 'pdf':
            if(!empty($post_pdf['name'])){
                $post_pdf_url = $this->upload_upost_files($post_pdf);
            }
            break;
    }

    // Create post object
    $upost_args = array(
        'ID' => $upost_id,
        'post_title'    => wp_strip_all_tags( $post_title ),
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'   => 'unique-posts'
    );

    wp_update_post( $upost_args );
    wp_set_object_terms( $upost_id, $post_category, 'upcats', true );

    update_post_meta( $upost_id, 'type_of_upost', $post_type );
    update_post_meta( $upost_id, 'upost_article_content', $post_article );

    if(sizeof($image_urls) > 0){
        update_post_meta( $upost_id, 'upost_media_images', $image_urls );
    }
    if(!empty($post_pdf_url)){
        update_post_meta( $upost_id, 'upost_pdf_file', $post_pdf_url );
    }
}

if(isset($_POST['save_front_upost'])){
    $post_type = ((isset($_POST['post_type'])) ? $_POST['post_type'] : '');
    $post_title = ((isset($_POST['post_title'])) ? $_POST['post_title'] : 'No title');
    $post_title = sanitize_text_field( $post_title );
    
    $post_category = ((isset($_POST['post_category'])) ? intval($_POST['post_category']) : '');

    $post_article = ((isset($_POST['upost_article_content'])) ? $_POST['upost_article_content'] : '');
    $post_images = ((isset($_FILES['upost_images'])) ? $_FILES['upost_images'] : '');
    $post_pdf = ((isset($_FILES['upost_pdf'])) ? $_FILES['upost_pdf'] : '');

    if(empty($post_category)){
        return;
    }

    $image_urls = [];
    $post_pdf_url = '';

    switch ($post_type) {
        case 'images':
            if(!empty($post_images['name'][0])){
                foreach($post_images['name'] as $key => $val){
                    $image_urls[] = $this->upload_upost_files($post_images, $key);
                }
            }
            break;
        case 'pdf':
            if(!empty($post_pdf['name'])){
                $post_pdf_url = $this->upload_upost_files($post_pdf);
            }
            break;
    }

    // Create post object
    $upost_args = array(
        'post_title'    => wp_strip_all_tags( $post_title ),
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'   => 'unique-posts',
        'post_author'   => get_current_user_id(  )
    );
    
    $post_id = wp_insert_post( $upost_args );
    if($post_id > 0){
        wp_set_object_terms( $post_id, $post_category, 'upcats' );

        update_post_meta( $post_id, 'type_of_upost', $post_type );
		update_post_meta( $post_id, 'upost_article_content', $post_article );
		update_post_meta( $post_id, 'upost_media_images', $image_urls );
		update_post_meta( $post_id, 'upost_pdf_file', $post_pdf_url );

        wp_safe_redirect( get_the_permalink(  )."?post=$post_id&status=success" );
        exit;
    }
}

$post_title = '';
$post_article = '';
$post_images = '';
$post_pdf = '';
$post_type = 'article';

if($upost_id !== null){
    $post_title = get_the_title( $upost_id );
    $post_article = get_post_meta($upost_id, 'upost_article_content', true);
    $post_images = get_post_meta($upost_id, 'upost_media_images', true);
    $post_pdf = get_post_meta($upost_id, 'upost_pdf_file', true);
    $post_type = get_post_meta($upost_id, 'type_of_upost', true);
}
?>
<div id="upost-form">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="upost_input">
            <div class="label_box">
                <label for="post_type">Post type</label>
            </div>
            <div class="input_box">
                <select name="post_type" id="post_type">
                    <option <?php echo (($post_type === 'article')?'selected': '') ?> value="article">Article</option>
                    <option <?php echo (($post_type === 'images')?'selected': '') ?> value="images">Images</option>
                    <option <?php echo (($post_type === 'pdf')?'selected': '') ?> value="pdf">PDF</option>
                </select>
            </div>
        </div>
        <div class="upost_input">
            <div class="label_box">
                <label for="post_title">Post title</label>
            </div>
            <div class="input_box">
                <input trequired ype="text" required id="post_title" name="post_title" value="<?php echo stripslashes($post_title) ?>">
            </div>
        </div>
        <div class="upost_input">
            <div class="label_box">
                <label for="post_title">Category</label>
            </div>
            <div class="input_box">
                <select style="width: fit-content" required name="post_category">
                    <option value="">Select</option>
                    <?php
                    $term_obj_list = [];
                    if($upost_id !== null){
                        $term_obj_list = get_the_terms( $upost_id, 'upcats' );
                        $term_obj_list = wp_list_pluck( $term_obj_list, 'term_id' );
                        if(sizeof($term_obj_list) > 0){
                            $term_obj_list = $term_obj_list[0];
                        }
                    }

                    $terms = get_terms(
                        array(
                            'taxonomy'   => 'upcats',
                            'hide_empty' => false,
                        )
                    );
                    
                    if ( ! empty( $terms ) && is_array( $terms ) ) {
                        foreach ( $terms as $term ) { ?>
                            <option <?php echo (($term->term_id == $term_obj_list)?'selected': '') ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                            <?php
                        }
                    } 
                    ?>
                </select>
            </div>
        </div>
        <div class="upost_input">
            <div class="label_box">
                <label for="post_content">Post content</label>
            </div>
            <div class="input_box">
                <div id="article_content" class="upost_content <?php echo (($post_type !== 'article') ? 'dnone': '') ?>">
                    <?php
                    wp_editor( wpautop( $post_article, true ), 'upost_article_content', [
                        'media_buttons' => false,
                        'editor_height' => 300,
                        'textarea_name' => 'upost_article_content'
                    ] );
                    ?>
                </div>
                <div id="images_content" class="upost_content <?php echo (($post_type !== 'images') ? 'dnone': '') ?>">
                    <div class="upost_image_previews">
                        <?php
                        if(is_array($post_images) && sizeof($post_images)>0){
                            foreach($post_images as $img){
                                ?>
                                <div class="single_img">
                                    <img src="<?php echo esc_url_raw( $img ) ?>">
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <label for="upload_upost_images">Upload Images
                        <input type="file" accept="image/png, image/gif, image/jpeg" multiple name="upost_images[]" id="upload_upost_images">
                    </label>
                </div>
                <div id="pdf_content" class="upost_content <?php echo (($post_type !== 'pdf') ? 'dnone': '') ?>">
                    <div class="upost_pdf_previews">
                        <img src="">
                        <input type="hidden" id="pdf_loader" value="<?php echo esc_url_raw( $post_pdf ) ?>">
                    </div>
                    <label for="upload_upost_pdf"><span>Upload PDF</span>
                        <input type="file" accept="application/pdf,application/vnd.ms-excel" name="upost_pdf" id="upload_upost_pdf">
                    </label>
                </div>
            </div>
        </div>
        <div class="upost_input">
            <div class="label_box"></div>
            <div class="input_box">
                <button id="save_upost" name="<?php echo (($upost_id !== null)? 'update_front_upost': 'save_front_upost') ?>" type="submit"><?php echo (($upost_id !== null)? 'Update post': 'Generate ID') ?></button>
            </div>
        </div>
    </form>
</div>