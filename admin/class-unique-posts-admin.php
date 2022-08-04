<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Unique_Posts
 * @subpackage Unique_Posts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Unique_Posts
 * @subpackage Unique_Posts/admin
 * @author     Developer Junayed <admin@easeare.com>
 */
class Unique_Posts_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Unique_Posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Unique_Posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/unique-posts-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Unique_Posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Unique_Posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $post;
		if($post && $post->post_type === 'unique-posts'){
			wp_enqueue_media(  );
		}
		wp_enqueue_script( 'pdf-js', UNIQUE_POSTS_ROOT . 'global/pdf.js', array(  ), $this->version, false );
		wp_enqueue_script( 'worker-js', UNIQUE_POSTS_ROOT . 'global/pdf.warker.js', array( 'pdf-js' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/unique-posts-admin.js', array( 'jquery' ), $this->version, false );

	}

	function unique_post_type(){
		$labels = array(
			'name'                => _x( 'Unique Posts', 'Post Type General Name', 'unique-posts' ),
			'singular_name'       => _x( 'Upost', 'Post Type Singular Name', 'unique-posts' ),
			'menu_name'           => __( 'Unique Posts', 'unique-posts' ),
			'parent_item_colon'   => __( 'Parent Upost', 'unique-posts' ),
			'all_items'           => __( 'All Unique Posts', 'unique-posts' ),
			'view_item'           => __( 'View Upost', 'unique-posts' ),
			'add_new_item'        => __( 'Add New Upost', 'unique-posts' ),
			'add_new'             => __( 'Add New', 'unique-posts' ),
			'edit_item'           => __( 'Edit Upost', 'unique-posts' ),
			'update_item'         => __( 'Update Upost', 'unique-posts' ),
			'search_items'        => __( 'Search Upost', 'unique-posts' ),
			'not_found'           => __( 'Not Found', 'unique-posts' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'unique-posts' ),
		);
		
		$args = array(
			'label'               => __( 'unique-posts', 'unique-posts' ),
			'description'         => __( 'Upost news and reviews', 'unique-posts' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'author' ),
			'taxonomies'          => array( 'upcats' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'menu_icon'       	  => 'dashicons-post-status',
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest' => false
		);
		  
		// Registering your Custom Post Type
		register_post_type( 'unique-posts', $args );

		$catlabels = array(
			'name' => _x( 'Categories', 'taxonomy general name' ),
			'singular_name' => _x( 'Category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Categories', 'unique-posts' ),
			'all_items' => __( 'All Categories', 'unique-posts' ),
			'parent_item' => __( 'Parent Category', 'unique-posts' ),
			'parent_item_colon' => __( 'Parent Category:', 'unique-posts' ),
			'edit_item' => __( 'Edit Category', 'unique-posts' ), 
			'update_item' => __( 'Update Category', 'unique-posts' ),
			'add_new_item' => __( 'Add New Category', 'unique-posts' ),
			'new_item_name' => __( 'New Category Name', 'unique-posts' ),
			'menu_name' => __( 'Categories', 'unique-posts' ),
		  );
		// Now register the taxonomy
		register_taxonomy('upcats', array('unique-posts'), array(
			'hierarchical' => true,
			'labels' => $catlabels,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'upcats' ),
		));

		if(get_option( 'vidr_permalinks_flush' ) !== $this->version ){
			flush_rewrite_rules(false);
			update_option( 'vidr_permalinks_flush', $this->version );
		}
	}

	function admin_menu_upost(){
		add_submenu_page( "edit.php?post_type=unique-posts", "Shortcodes", "Shortcodes", "manage_options", "upost-shortcodes", [$this, "upost_shortcodes"], null );
	}

	function upost_shortcodes(){
		?>
		<h3>Shortcodes</h3>
		<hr>
		<p><input type="text" readonly value='[manage_uposts]'></p>
		<p><input type="text" readonly value='[search_upost]'></p>
		<?php
	}

	// Manage table columns
	function manage_unique_posts_columns($columns) {
		unset(
			$columns['title'],
			$columns['taxonomy-upcats'],
			$columns['author'],
			$columns['date'],
		);
	
		$new_columns = array(
			'title' => __('Title', 'unique-posts'),
			'type_of_post' => __('Post type', 'unique-posts'),
			'unique_id' => __('Unique ID', 'unique-posts'),
			'taxonomy-upcats' => __('Category', 'unique-posts'),
			'author' => __('Post Author', 'unique-posts'),
			'date' => __('Date', 'unique-posts'),
		);
	
		return array_merge($columns, $new_columns);
	}

	// View custom column data
	function manage_unique_posts_columns_views($column_id, $post_id){
		switch ($column_id) {
			case 'type_of_post':
				$type = get_post_meta($post_id, 'type_of_upost', true);
				echo ucfirst($type);
				break;
			case 'unique_id':
				echo get_post_meta($post_id, 'upost_uid', true);
				break;
		}
	}

	function unique_post_meta(){
		global $wp_meta_boxes;
		unset($wp_meta_boxes['unique-posts']);
		
		add_meta_box( 'submitdiv', "Save post", 'post_submit_meta_box', 'unique-posts', 'side' );
		add_meta_box( 'type_of_upost', "Post type", [$this, 'type_of_upost_meta_box'], 'unique-posts', 'side' );
		add_meta_box( 'upcatsdiv', "Category", 'post_categories_meta_box', 'unique-posts', 'side', '', ['taxonomy' => 'upcats'] );
		add_meta_box( 'upost_content_section', "Content", [$this, 'upost_content_meta_box'], 'unique-posts', 'advanced' );
	}

	function type_of_upost_meta_box($post){
		$selected = get_post_meta($post->ID, 'type_of_upost', true);
		?>
		<select class="widefat" name="type_of_upost" id="type_of_upost_option">
			<option <?php echo (($selected === 'article')? 'selected': '') ?> value="article">Article</option>
			<option <?php echo (($selected === 'images')? 'selected': '') ?> value="images">Images</option>
			<option <?php echo (($selected === 'pdf')? 'selected': '') ?> value="pdf">PDF</option>
		</select>
		<?php
	}

	function upost_content_meta_box($post){
		$selectedType = get_post_meta($post->ID, 'type_of_upost', true);
		if(empty($selectedType)){
			$selectedType = 'article';
		}
		?>
		<div id="upost_contents">
			<div class="upost_content_box upost_article <?php echo (($selectedType !== 'article')?'dnone':'') ?>">
				<?php
				$article = get_post_meta($post->ID, 'upost_article_content', true);
				wp_editor( wpautop( $article, true ), 'upost_article_content', [
					'media_buttons' => false,
					'editor_height' => 400,
					'textarea_name' => 'upost_article_content'
				] );
				?>
			</div>

			<div class="upost_content_box image_content <?php echo (($selectedType !== 'images')?'dnone':'') ?>">
				<div id="upost_media_images">
					<?php
					$images = get_post_meta($post->ID, 'upost_media_images', true);

					if(is_array($images) && sizeof($images)>0){
						foreach($images as $image){
							?>
							<div class="mimage"><span class="remove_upost_img">+</span><img src="<?php echo $image ?>"><input type="hidden" name="upost_media_images[]" value="<?php echo $image ?>"></div>
							<?php
						}
					}
					?>
				</div>

				<div class="preview_upost_image"></div>

				<button id="add-upost-images" class="button-secondary">Add Images</button>
			</div>

			<div id="upost_document" class="upost_content_box pdf_content <?php echo (($selectedType !== 'pdf')?'dnone':'') ?>">
				<div class="upost_file_previews">
					<?php
					$pdf_url = get_post_meta($post->ID, 'upost_pdf_file', true);
					?>
					<img src="" id="upost_pdf_view">
					<input type="hidden" id="upost_document_file" value="<?php echo $pdf_url ?>" name="upost_pdf_file">
				</div>
				<div class="file-input">
					<button class="upost_document_btn button-secondary">Upload PDF</button>
				</div>
			</div>

		</div>
		<?php
	}

	function save_post_unique_post($post_id){
		$type_of_upost = ((isset($_POST['type_of_upost'])) ? $_POST['type_of_upost']: '');
		$article = ((isset($_POST['upost_article_content'])) ? $_POST['upost_article_content']: '');
		$images = ((isset($_POST['upost_media_images'])) ? $_POST['upost_media_images']: []);
		$pdf = ((isset($_POST['upost_pdf_file'])) ? $_POST['upost_pdf_file']: '');

		if(empty(get_post_meta($post_id, 'upost_uid', true))){
			update_post_meta($post_id, 'upost_uid', get_upost_uid($post_id) );
		}

		if(!empty($type_of_upost)){
			update_post_meta( $post_id, 'type_of_upost', $type_of_upost );
		}
		
		if(isset($_POST['upost_article_content'])){
			update_post_meta( $post_id, 'upost_article_content', $article );
		}

		if(is_array($images) && sizeof($images) > 0){
			update_post_meta( $post_id, 'upost_media_images', $images );
		}
		if(!empty($pdf)){
			update_post_meta( $post_id, 'upost_pdf_file', $pdf );
		}
	}
}
