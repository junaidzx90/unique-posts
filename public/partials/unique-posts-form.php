<?php
$uiderrors = '';
if(isset($_POST['update_front_upost'])){
    $unique_id = ((isset($_POST['unique_id'])) ? $_POST['unique_id'] : '');
    $post_type = ((isset($_POST['post_type'])) ? $_POST['post_type'] : '');
    $post_title = ((isset($_POST['post_title'])) ? $_POST['post_title'] : 'No title');
    $post_category = ((isset($_POST['post_category'])) ? intval($_POST['post_category']) : '');
    $post_title = sanitize_text_field( $post_title );
    $post_article = ((isset($_POST['upost_article_content'])) ? $_POST['upost_article_content'] : '');
    $post_images = ((isset($_FILES['upost_images'])) ? $_FILES['upost_images'] : '');
    $post_pdf = ((isset($_FILES['upost_pdf'])) ? $_FILES['upost_pdf'] : '');

    // Patient form
    $patient_name = ((isset($_POST['patient_name'])) ? $_POST['patient_name'] : '');
    $patient_name = sanitize_text_field( $patient_name );
    $id_number = ((isset($_POST['id_number'])) ? $_POST['id_number'] : '');
    $id_number = intval( $id_number );
    $patient_gender = ((isset($_POST['patient_gender'])) ? $_POST['patient_gender'] : '');
    $patient_age = ((isset($_POST['patient_age'])) ? $_POST['patient_age'] : '');
    $patient_age = intval( $patient_age );
    $patient_email = ((isset($_POST['patient_email'])) ? $_POST['patient_email'] : '');
    $patient_email = sanitize_email( $patient_email );
    $patient_phone = ((isset($_POST['patient_phone'])) ? $_POST['patient_phone'] : '');
    $patient_phone = intval( $patient_phone );
    $start_date = ((isset($_POST['start_date'])) ? $_POST['start_date'] : '');
    $end_date = ((isset($_POST['end_date'])) ? $_POST['end_date'] : '');
    $description = ((isset($_POST['description'])) ? $_POST['description'] : '');
    $description = sanitize_text_field($description);
    $description = stripslashes($description);

    global $wpdb;
    $hasId = $wpdb->get_var("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'upost_uid' AND meta_value = '$unique_id' AND post_id != $upost_id");

    if($hasId){
        $uiderrors = 'This Unique ID is already exist!';
    }else{
		update_post_meta($upost_id, 'upost_uid', $unique_id );
    }
    
    if(empty($post_category)){
        $caterrors = 'C|ategory is required!';
    }

    $image_urls = [];
        $post_pdf_url = '';
        $patient_informations = [];

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
            case 'certificate':
                $patient_informations['patient_name'] = $patient_name;
                $patient_informations['id_number'] = $id_number;
                $patient_informations['patient_gender'] = $patient_gender;
                $patient_informations['patient_age'] = $patient_age;
                $patient_informations['patient_email'] = $patient_email;
                $patient_informations['patient_phone'] = $patient_phone;
                $patient_informations['start_date'] = $start_date;
                $patient_informations['end_date'] = $end_date;
                $patient_informations['description'] = $description;
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
        if(sizeof($patient_informations) > 0){
            update_post_meta( $upost_id, 'patient_informations', $patient_informations );
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

    // Patient form
    $patient_name = ((isset($_POST['patient_name'])) ? $_POST['patient_name'] : '');
    $patient_name = sanitize_text_field( $patient_name );
    $id_number = ((isset($_POST['id_number'])) ? $_POST['id_number'] : '');
    $id_number = intval( $id_number );
    $patient_gender = ((isset($_POST['patient_gender'])) ? $_POST['patient_gender'] : '');
    $patient_age = ((isset($_POST['patient_age'])) ? $_POST['patient_age'] : '');
    $patient_age = intval( $patient_age );
    $patient_email = ((isset($_POST['patient_email'])) ? $_POST['patient_email'] : '');
    $patient_email = sanitize_email( $patient_email );
    $patient_phone = ((isset($_POST['patient_phone'])) ? $_POST['patient_phone'] : '');
    $patient_phone = intval( $patient_phone );
    $start_date = ((isset($_POST['start_date'])) ? $_POST['start_date'] : '');
    $end_date = ((isset($_POST['end_date'])) ? $_POST['end_date'] : '');
    $description = ((isset($_POST['description'])) ? $_POST['description'] : '');
    $description = sanitize_text_field($description);
    $description = stripslashes($description);

    if(empty($post_category)){
        return;
    }

    $image_urls = [];
    $post_pdf_url = '';
    $patient_informations = [];

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
        case 'certificate':
            $patient_informations['patient_name'] = $patient_name;
            $patient_informations['id_number'] = $id_number;
            $patient_informations['patient_gender'] = $patient_gender;
            $patient_informations['patient_age'] = $patient_age;
            $patient_informations['patient_email'] = $patient_email;
            $patient_informations['patient_phone'] = $patient_phone;
            $patient_informations['start_date'] = $start_date;
            $patient_informations['end_date'] = $end_date;
            $patient_informations['description'] = $description;
            
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
		update_post_meta( $post_id, 'patient_informations', $patient_informations );

        if($post_type === 'certificate'){
            if(!empty($patient_email)){
                $unique_id = get_post_meta($post_id, 'upost_uid', true);
                $subject = 'Medical certificate';
                $body = "Your medical certificate is ready <a href='".get_the_permalink($post_id).'?uid='.$unique_id."' target='_blank'>Download</a>";
                $headers = array('Content-Type: text/html; charset=UTF-8');
                
                wp_mail( $patient_email, $subject, $body, $headers);
            }
        }

        wp_safe_redirect( get_the_permalink(  )."?post=$post_id&status=success" );
        exit;
    }
}

$post_title = '';
$post_article = '';
$post_images = '';
$post_pdf = '';
$post_type = 'article';
$unique_id = get_post_meta($upost_id, 'upost_uid', true);

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
                    <option <?php echo (($post_type === 'certificate')?'selected': '') ?> value="certificate">Certificate</option>
                </select>
            </div>
        </div>
        <div class="upost_input">
            <div class="label_box">
                <label for="post_title">Post title</label>
            </div>
            <div class="input_box">
                <input required ype="text" required id="post_title" name="post_title" value="<?php echo stripslashes($post_title) ?>">
            </div>
        </div>
        
        <?php
        if($upost_id !== null){
            ?>
            <div class="upost_input">
                <div class="label_box">
                    <label for="unique_id">Unique ID</label>
                </div>
                <div class="input_box">
                    <input ype="text" required minlength="6" maxlength="6" id="unique_id" name="unique_id" value="<?php echo ((isset($_POST['unique_id'])) ? $_POST['unique_id']: $unique_id ) ?>">
                    <?php
                    if(!empty($uiderrors)){
                        echo '<p style="margin: 0; color: red;">'.$uiderrors.'</p>';
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>

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
                <div id="certificate_content" class="upost_content <?php echo (($post_type !== 'certificate') ? 'dnone': '') ?>">
                    <?php
                    $informations = get_post_meta($upost_id, 'patient_informations', true);
                    $patient_name = null;
                    $id_number = null;
                    $patient_gender = null;
                    $patient_age = null;
                    $patient_email = null;
                    $patient_phone = null;
                    $start_date = null;
                    $end_date = null;
                    $description = null;
                    if(!empty($informations) && is_array($informations)){
                        $patient_name = $informations['patient_name'];
                        $id_number = $informations['id_number'];
                        $patient_gender = $informations['patient_gender'];
                        $patient_age = $informations['patient_age'];
                        $patient_email = $informations['patient_email'];
                        $patient_phone = $informations['patient_phone'];
                        $start_date = $informations['start_date'];
                        $end_date = $informations['end_date'];
                        $description = $informations['description'];
                    }
                    ?>
                    <div class="patient_row">
                        <div class="upost_field">
                            <label for="patient_name">Patient Name</label>
                            <input type="text" name="patient_name" id="patient_name" value="<?php echo $patient_name ?>">
                        </div>
                        <div class="upost_field">
                            <label for="id_number">ID Number</label>
                            <input type="number" name="id_number" id="id_number" value="<?php echo $id_number ?>">
                            <p class="hints">Patient ID or Student number</p>
                        </div>
                    </div>
                    <div class="patient_row">
                        <div class="upost_field">
                            <label for="patient_gender">Gender</label>
                            <select name="patient_gender" id="patient_gender">
                                <option <?php echo (($patient_gender === 'male') ? 'selected': '') ?> value="male">Male</option>
                                <option <?php echo (($patient_gender === 'female') ? 'selected': '') ?> value="female">Female</option>
                                <option <?php echo (($patient_gender === 'other') ? 'selected': '') ?> value="other">Other</option>
                            </select>
                        </div>
                        <div class="upost_field">
                            <label for="patient_age">Age</label>
                            <input type="number" name="patient_age" id="patient_age" value="<?php echo $patient_age ?>">
                        </div>
                    </div>
                    <div class="patient_row">
                        <div class="upost_field">
                            <label for="patient_email">Email</label>
                            <input type="email" name="patient_email" id="patient_email" value="<?php echo $patient_email ?>">
                        </div>
                        <div class="upost_field">
                            <label for="patient_phone">Phone Number</label>
                            <input type="number" name="patient_phone" id="patient_phone" value="<?php echo $patient_phone ?>">
                        </div>
                    </div>
                    <div class="patient_row">
                        <div class="upost_field">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="<?php echo $start_date ?>">
                        </div>
                        <div class="upost_field">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="<?php echo $end_date ?>">
                        </div>
                    </div>
                    <div class="patient_desc_row">
                        <div class="upost_field">
                            <label for="description">Description of the medical diagnosis</label>
                            <textarea name="description" id="description"><?php echo $description ?></textarea>
                        </div>
                    </div>

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