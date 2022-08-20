<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Unique_Posts
 * @subpackage Unique_Posts/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Unique_Posts
 * @subpackage Unique_Posts/public
 * @author     Developer Junayed <admin@easeare.com>
 */
class Unique_Posts_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode( 'manage_uposts', [$this, "manage_user_post"] );
		add_shortcode( 'search_upost', [$this, "search_user_post"] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/unique-posts-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( 'datatables', 'https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js', array(  ), $this->version, false );
		wp_enqueue_script( 'jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js', array(  ), $this->version, false );
		wp_enqueue_script( 'jszip-utils', 'https://cdnjs.cloudflare.com/ajax/libs/jszip-utils/0.1.0/jszip-utils.min.js', array( 'jszip' ), $this->version, false );
		wp_enqueue_script( 'FileSaver', 'https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js', array( 'jszip-utils' ), $this->version, false );
		
		wp_enqueue_script( 'pdf-js', UNIQUE_POSTS_ROOT . 'global/pdf.js', array(  ), $this->version, false );
		wp_enqueue_script( 'worker-js', UNIQUE_POSTS_ROOT . 'global/pdf.warker.js', array( 'pdf-js', 'FileSaver' ), $this->version, false );
		wp_enqueue_script( 'jspdf', UNIQUE_POSTS_ROOT . 'global/jspdf.min.js', array(  ), $this->version, false );
		wp_enqueue_script( 'html2canvas', UNIQUE_POSTS_ROOT . 'global/html2canvas.js', array(  ), $this->version, false );
		wp_enqueue_script( 'html2pdf', 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js', array(  ), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/unique-posts-public.js', array( 'jquery', 'worker-js', 'jspdf', 'html2canvas', 'html2pdf' ), $this->version, true );

	}

	function upload_upost_files($file, $index = null){
		$wpdir = wp_upload_dir(  );
		$max_upload_size = wp_max_upload_size();
		if($index !== null){
			$imageFileType = strtolower(pathinfo($file['name'][$index],PATHINFO_EXTENSION));
		}else{
			$imageFileType = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
		}

		$filename = rand(10,99999);

		$folderPath = $wpdir['basedir'];
		$uploadPath = $folderPath."/".$filename.".".$imageFileType;
		$uploadedUrl = $wpdir['baseurl']."/".$filename.".".$imageFileType;

		if($index !== null){
			if (move_uploaded_file($file["tmp_name"][$index], $uploadPath)) {
				return $uploadedUrl;
			}
		}else{
			if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
				return $uploadedUrl;
			}
		}
	}

	function manage_user_post(){
		ob_start();
		if(is_user_logged_in(  )){
			require_once plugin_dir_path( __FILE__ )."partials/unique-posts-manage.php";
		}
		return ob_get_clean();
	}

	function get_patient_certificate($post_id){
		$informations = get_post_meta($post_id, 'patient_informations', true);
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
			$start_date = date("j F, Y", strtotime($informations['start_date']));
			$end_date = date("j F, Y", strtotime($informations['end_date']));
			$description = $informations['description'];
			$unique_id = get_post_meta($post_id, 'upost_uid', true);
		}
		
		$output = '<style>
		h2.entry-title.fusion-post-title {
			text-align: center;
		}
		</style>';
		$output .= '<div id="medical_certificate">';
		$output .= '<div class="certificate_header">';
		$output .= '<div class="logo"><img src="'.get_option('upost_company_logo').'"></div>';
		$output .= '</div>';

		$output .= '<div class="certificate_contents">';
		$output .= '<div class="company_information">';
		$output .= '<ul>';
		$output .= '<li><h3 class="compname">'.get_option('upost_company_name').'</h3></li>';
		$output .= '<li><p class="comptag">'.get_option('upost_company_tag').'</p></li>';
		$output .= '<li>Tel: '.get_option('upost_company_tel').'</li>';
		$output .= '<li>Address: '.get_option('upost_company_address').'</li>';
		$compEmail = get_option('upost_company_email');
		$output .= '<li>Email: '.$compEmail.'</li>';
		$website = get_option('upost_company_website');
		$output .= '<li>Website: '.$website.'</li>';
		$output .= '</ul>';
		$output .= '</div>';

		$output .= '<h3 class="patient_info_title">Patient Information</h3>';
		$output .= '<div class="patient_informations">';
		$output .= '<div class="row">';
		$output .= '<div class="info_heading">Patient Name:</div>';
		$output .= '<div class="info_value">'.$patient_name.'</div>';
		$output .= '</div>';
		$output .= '<div class="row">';
		$output .= '<div class="info_heading">ID Number:</div>';
		$output .= '<div class="info_value">'.$id_number.'</div>';
		$output .= '</div>';
		$output .= '<div class="row">';
		$output .= '<div class="info_heading">Phone Number:</div>';
		$output .= '<div class="info_value">'.$patient_phone.'</div>';
		$output .= '</div>';
		$output .= '</div>';

		$post_date = get_the_date( "j F, Y", $post_id );
		$output .= '<h3 class="notes_title">Doctor\'s Notes '.$post_date.'</h3>';
		$output .= '<div class="notes">';

		$notes = get_option('upost_doctors_notes');
		$notes = preg_replace("/%patient_name%/", "<strong>$patient_name</strong>", $notes);
		$notes = preg_replace("/%patient_diagnosis%/", "<strong>$description</strong>", $notes);
		$notes = preg_replace("/%start_date%/", "<strong>$start_date</strong>", $notes);
		$notes = preg_replace("/%end_date%/", "<strong>$end_date</strong>", $notes);
		$notes = preg_replace("/%unique_id%/", "<strong>$unique_id</strong>", $notes);
		$doctorname = get_option('upost_doctors_name');
		$notes = preg_replace("/%doctor_name%/", "<strong>$doctorname</strong>", $notes);
		$specialty = get_option('upost_doctors_specialty');
		$notes = preg_replace("/%doctor_specialty%/", "<strong>$specialty</strong>", $notes);
		$notes = preg_replace("/%post_date%/", "<strong>$post_date</strong>", $notes);

		$output .= wpautop( $notes, true );

		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="certificate_footer">';
		$output .= '<div class="certificate_contents">';
		$output .= '<div class="footer_head">';
		$output .= '<span class="line"></span>';
		$output .= '<div class="foo_title">This medical certificate is generated by '.str_replace(['https://', 'http://'], '', get_bloginfo( 'url' )).'</div>';
		$output .= '<span class="line"></span>';
		$output .= '</div>';
		$output .= '<div class="footer_bottom">';
		$output .= '<div class="very_code">Unique verification code: '.$unique_id.'</div>';
		$output .= '<div class="authenticity">Verify Authenticity: <a href="'.get_option('upost_authenticity_url').'">'.get_option('upost_authenticity_url').'</a> </div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		
		return $output;
	}

	function unique_post_contents($the_content){
		global $post, $wpdb;

		$add_content = true; 

		if( $post->post_password && !post_password_required() ){
			$add_content = true;
		}

		if( $post->post_password && post_password_required() ){
			$add_content = false;
		}

		if($post && $post->post_type === 'unique-posts' && $add_content){
			if(!current_user_can( 'administrator' ) && !isset($_GET['uid'])){
				wp_safe_redirect( home_url( '404' ) );
				exit;
			}

			if(isset($_GET['uid']) && !empty($_GET['uid'])){
				$uid = urldecode($_GET['uid']);

				$upost_id = $wpdb->get_var("SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'upost_uid' AND meta_value = '$uid'");

				if(!$upost_id){
					wp_safe_redirect( home_url( '404' ) );
					exit;
				}
			}

			$post_article = get_post_meta($post->ID, 'upost_article_content', true);
			$post_images = get_post_meta($post->ID, 'upost_media_images', true);
			$post_pdf = get_post_meta($post->ID, 'upost_pdf_file', true);
			$post_type = get_post_meta($post->ID, 'type_of_upost', true);

			switch ($post_type) {
				case 'article':
					$the_content .= $post_article;
					break;
				case 'images':
					if(is_array($post_images) && sizeof($post_images) > 0){
						$the_content .= '<div id="upost_image_preview"><img src=""></div>';
						$the_content .= '<div id="upost_images">';
						$ac = 'active';
						foreach($post_images as $image){
							$the_content .= '<div class="upost_image '.$ac.'">
								<img src="'.$image.'">
							</div>';
							$ac = '';
						}
						
						$the_content .= '</div>';

						$nametitle = str_replace([" ", ",", "'"], "-", $post->post_title);
						$nametitle = str_replace(".", "", $nametitle);

						$the_content .= '<a class="download-images upost_btn" data-name="'.strtolower( __( $nametitle ) ).'">Download</a>';
					}
					break;
				case 'pdf':
					$the_content .= '<div id="upost_pdf_preview" data-src="'.$post_pdf.'">';
					$the_content .= '<div class="pdf-actions">';
					$the_content .= '<span></span>';
					$the_content .= '<div class="btns">';
					$the_content .= '<a target="_blank" class="upost_btn" href="'.$post_pdf.'">Open this file</a>';
					
					$nametitle = str_replace([" ", ",", "'"], "-", $post->post_title);
					$nametitle = str_replace(".", "", $nametitle);
					
					$the_content .= '<a class="download-file upost_btn" data-name="'.strtolower( $nametitle ).'" data-pdf="'.$post_pdf.'" href="'.$post_pdf.'">Download</a>';
					
					$the_content .= '</div>';
					$the_content .= '</div>';
					$the_content .= '<div class="upost_pdf_previews">';
					$the_content .= '<span class="leftPdfPage">❮</span>';
					$the_content .= '<img src="">';
					$the_content .= '<span class="rightPdfPage">❯</span>';
					$the_content .= '</div>';
					$the_content .= '</div>';
					break;
				case 'certificate':
					$unique_id = get_post_meta($post->ID, 'upost_uid', true);
					?>
					<div class="cetificate_action">
						<a href="" target="_blank" class="upost_btn" data-name="<?php echo 'certificate-'.$unique_id ?>" id="certificate_download">Download</a>
					</div>
					<?php
					echo $this->get_patient_certificate($post->ID);
					break;
			}
		}

		return $the_content;
		
	}

	function search_user_post(){
		ob_start();
		require_once plugin_dir_path( __FILE__ )."partials/search-upost.php";
		return ob_get_clean();
	}
}
