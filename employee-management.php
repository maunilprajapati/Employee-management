<?php
/*
Plugin Name: Employee Management
Plugin URI: 
Description: Declares a plugin that will create a custom post type displaying custom post type.
Version: 1.0
Author: Maunil Prajapati
Author URI: Not available
License: GPLv2
*/
?>
<style type="text/css">
	.cvf_pag_loading {padding: 20px;}
	.cvf-universal-pagination ul {margin: 0; padding: 0;}
	.cvf-universal-pagination ul li {display: inline; margin: 3px; padding: 4px 8px; background: #FFF; color: black; }
	.cvf-universal-pagination ul li.active:hover {cursor: pointer; background: #1E8CBE; color: white; }
	.cvf-universal-pagination ul li.inactive {background: #7E7E7E;}
	.cvf-universal-pagination ul li.selected {background: #1E8CBE; color: white;}
</style>
<?php
function employee_custom_post_type() {
	$labels = array(
		'name'                => __( 'employee' ),
		'singular_name'       => __( 'employee'),
		'menu_name'           => __( 'employee'),
		'parent_item_colon'   => __( 'Parent employee'),
		'all_items'           => __( 'All employee'),
		'view_item'           => __( 'View employee'),
		'add_new_item'        => __( 'Add New employee'),
		'add_new'             => __( 'Add New'),
		'edit_item'           => __( 'Edit employee'),
		'update_item'         => __( 'Update employee'),
		'search_items'        => __( 'Search employee'),
		'not_found'           => __( 'Not Found'),
		'not_found_in_trash'  => __( 'Not found in Trash')
	);
	$args = array(
		'label'               => __( 'Employee'),
		'description'         => __( 'Best employee'),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields'),
		'public'              => true,
		'hierarchical'        => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'has_archive'         => true,
		'can_export'          => true,
		'exclude_from_search' => false,
	        'yarpp_support'       => true,
		'taxonomies' 	      => array('post_tag'),
		'publicly_queryable'  => true,
		'capability_type'     => 'page'
);
	register_post_type( 'employee', $args );
}
add_action( 'init', 'employee_custom_post_type', 0 );

// Let us create Taxonomy for Custom Post Type
add_action( 'init', 'create_employee_custom_taxonomy', 0 );
 
//create a custom taxonomy name it "type" for your posts
function create_employee_custom_taxonomy() {
 
  $labels = array(
    'name' => _x( 'department', 'taxonomy general name' ),
    'singular_name' => _x( 'department', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search department' ),
    'all_items' => __( 'All department' ),
    'parent_item' => __( 'Parent department' ),
    'parent_item_colon' => __( 'Parent department:' ),
    'edit_item' => __( 'Edit department' ), 
    'update_item' => __( 'Update department' ),
    'add_new_item' => __( 'Add New department' ),
    'new_item_name' => __( 'New department Name' ),
    'menu_name' => __( 'department' ),
  ); 	
 
  register_taxonomy('department',array('employee'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'type' ),
  ));
}
function employee_plugin_flush_rewrites() {
        employee_custom_post_type();
        flush_rewrite_rules();
}
function employee_plugin_uninstall() {
  // Uninstallation stuff here
     unregister_post_type( 'employee' );
}
register_activation_hook( __FILE__, 'employee_plugin_flush_rewrites' );
register_uninstall_hook( __FILE__, 'employee_plugin_uninstall' );


function create_employee_front_end(){
if(isset($_POST['submit'])){

 	$name  			= $_POST['employee_name'];
	$description	= $_POST['employee_descreption'];
	$salary			= $_POST['employee_salary'];
	$department		= $_POST['department'];
	$photo			= $_POST['featured'];

	// Create post object
	$post = array(
	  'post_title'    => $name,
	  'post_content'  => $description,
	  'post_type'     =>'employee',
	  'post_status'   => 'publish',
	  'post_author'   => 1
	);
 
	$post_id = wp_insert_post( $post, $wp_error='' );
	 
	if($post_id!=0){
	 
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');


		$uploaddir = wp_upload_dir();
		$file = $_FILES['featured' ];
		$uploadfile = $uploaddir['path'] . '/' . basename( $file['name'] );
		move_uploaded_file( $file['tmp_name'] , $uploadfile );
		$filename = basename( $uploadfile );
		$wp_filetype = wp_check_filetype(basename($filename), null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
			'post_content' => '',
			'post_status' => 'inherit',
			'menu_order' => $_i + 1000
			);
		$attach_id = wp_insert_attachment( $attachment, $uploadfile );

		update_post_meta($post_id,'_thumbnail_id',$attach_id);
		set_post_thumbnail( $post_id, $thumbnail_id );
		//meta value insert
		add_post_meta($post_id, 'employee_salary', $salary );
		// term insert
		wp_set_object_terms( $post_id, $department, 'department' );


		echo "Successfully added";
	}	
}	 
?> 		 
<h1>Add Employee</h1>
<form action="" method="post" enctype="multipart/form-data">
		<table width="295" height="346" border="0">
		  <tr>
		    <td>Name</td>
		    <td><input type="text" name="employee_name" /></td>
		  </tr>
		  <tr>
		    <td>Description</td>
		    <td><textarea name="employee_descreption" ></textarea></td>
		  </tr>
		  <tr>
		    <td>Salary</td>
		    <td><input type="text" name="employee_salary" /></td>
		  </tr>
		  <tr>
		    <td>Department</td>
		    <td>
				<?php //$tax = get_object_taxonomies('department');
				   // $taxterms = get_terms( $tax, 'orderby=count&offset=1&hide_empty=0' );
				    $taxterms = get_terms( array(
					    'taxonomy' => 'department',
					    'hide_empty' => false,
					) );
				?>
				<select name='department' id='department'>
				    <option value=''>--Select Department--</option>
				    <?php 
				    foreach ( $taxterms as $term ) { 
				        echo '<option value="' . $term->slug . '" >' . $term->name . '</option>',"\n"; 
				    } ?>
				</select>
		    </td>
		  </tr>
		  <tr>
		    <td>Upload Image </td>
		    <td><input type="file" name="featured" /></td>
		  </tr>
		  <tr>
		    <td>&nbsp;</td>
		    <td><input type="submit" name="submit"  value="Submit"/></td>
		  </tr>
		</table>
</form>
<?php }
add_shortcode('create_employee','create_employee_front_end');

//View Employees

function view_employees_front_end1(){

	if(isset($_POST['update'])){
			$id 			= $_POST['postid'];
		 	$name  			= $_POST['employee_name'];
			$description	= $_POST['employee_descreption'];
			$salary			= $_POST['employee_salary'];
			$department		= $_POST['department'];
			$photo			= $_POST['featured'];

			// Create post object
			$post = array(
				'ID' 			=> $id,
				'post_title'    => $name,
				'post_content'  => $description,
				'post_type'     =>'employee',
				'post_status'   => 'publish',
				'post_author'   => 1
			);
		 
			$post_id = wp_update_post( $post, $wp_error='' );
			 
			if($post_id!=0){
			 
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');


				$uploaddir = wp_upload_dir();
				
				if(!empty($_FILES['featured']['tmp_name'])){
					$file = $_FILES['featured' ];
					$uploadfile = $uploaddir['path'] . '/' . basename( $file['name'] );
					move_uploaded_file( $file['tmp_name'] , $uploadfile );
					$filename = basename( $uploadfile );
					$wp_filetype = wp_check_filetype(basename($filename), null );
					$attachment = array(
						'post_mime_type' => $wp_filetype['type'],
						'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
						'post_content' => '',
						'post_status' => 'inherit',
						'menu_order' => $_i + 1000
						);
					$attach_id = wp_insert_attachment( $attachment, $uploadfile );

					update_post_meta($post_id,'_thumbnail_id',$attach_id);
					set_post_thumbnail( $post_id, $thumbnail_id );
				}
				//meta value insert
				update_post_meta($post_id, 'employee_salary', $salary );
				// term insert
				wp_set_object_terms( $post_id, $department, 'department' );


				echo "Successfully updated";
			}	
		}
		if($_GET['updateID']){
			$postid = $_GET['updateID'];
		 ?>
			<form class="form-inline" id="editCPTForm" method="POST" action="" enctype="multipart/form-data">
					<table width="295" height="346" border="0">
					  <tr>
					    <td>Name</td>
					    <td>
							<input type="hidden" name="postid" value="<?php echo $postid;?>" />
					    	<input type="text" name="employee_name" value="<?php echo get_the_title($postid);?>" /></td>
					  </tr>
					  <tr>
					    <td>Description</td>
					    <td><textarea name="employee_descreption" ><?php 
						    	$content_post = get_post($postid);
								$content = $content_post->post_content;
								$content = apply_filters('the_content', $content);
								echo $content = str_replace(']]>', ']]&gt;', $content);?>
					    </textarea></td>
					  </tr>
					  <tr>
					    <td>Salary</td>
					    <td><input type="text" name="employee_salary" value="<?php echo get_post_meta($postid, 'employee_salary', TRUE); ?>" /></td>
					  </tr>
					  <tr>
					    <td>Department</td>
					    <td>
							<?php //$tax = get_object_taxonomies('department');
							   // $taxterms = get_terms( $tax, 'orderby=count&offset=1&hide_empty=0' );
							    $taxterms = get_terms( array(
								    'taxonomy' => 'department',
								    'hide_empty' => false,
								) );
							?>
							<select name='department' id='department'>
							    <option value=''>--Select Department--</option>
							    <?php 
							   $selectedDepart =  wp_get_object_terms( $postid, 'department', array( 'fields' => 'names' ) );

							    foreach ( $taxterms as $term ) {
							    		$selected = 'selected';

							        echo '<option value="' . $term->slug . '" >' . $term->name . '</option>',"\n"; 
							    } ?>
							</select>
					    </td>
					  </tr>
					  <tr>
					    <td>Upload Image </td>
					    <td><input type="file" name="featured" />
						<?php if (has_post_thumbnail( $postid ) ): ?>
						  <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $postid ), 'single-post-thumbnail' ); ?>
						  <img src="<?php echo $image[0]; ?>" width="100" height="100">

						<?php endif; ?>
					    </td>
					  </tr>
					  <tr>
					    <td>&nbsp;</td>
					    <td><input type="submit" name="update"  value="Submit"/></td>
					  </tr>
					</table>
			</form>
		<?php }else{

		$query1 = new WP_Query(array('post_type' => 'employee'));
		query_posts( $query1 );

		?>
		<section class="content">
		<table>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Salary</th>
				<th>Department</th>
				<th>Photo</th>
				<th>Action</th>
			</tr>
				<?php
				while ( $query1->have_posts() ) : $query1->the_post();  
				?>
			<tr>
				<td><?php echo get_the_title(); ?></td>
				<td><?php echo get_the_content(); ?></td>  
				<td><?php echo get_post_meta(get_the_ID(), 'employee_salary', TRUE);?></td>
				<td><?php 
					$terms = get_the_terms( get_the_ID(), 'department' );
					if ( !empty( $terms ) ) { 
					foreach($terms as $term) {
					   	echo $term->name;
					}
					}

				?>
				<td>
				<?php  
   					if (has_post_thumbnail( get_the_ID() ) ): ?>
						  <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-post-thumbnail' ); ?>
						  <img src="<?php echo $image[0]; ?>" width="100" height="100">

						<?php endif; ?>
            	
				</td>
				<td>
				<?php 
					global $wp;
						$baseurl = home_url( $wp->request );
					
					?>
					
				<a href="<?php echo add_query_arg( 'updateID', get_the_ID(), $baseurl );?>">Edit</a> | <a href="../update.php">Delete</a>
				</td>  
			</tr>

				<?php
				endwhile;
				// Reset Query
				wp_reset_query();

				
				?>
		</table>
	</section>


<?php
		}
}
add_shortcode('view_employees','view_employees_front_end');

function show_post(){
	if(isset($_POST['update'])){
			$id 			= $_POST['postid'];
		 	$name  			= $_POST['employee_name'];
			$description	= $_POST['employee_descreption'];
			$salary			= $_POST['employee_salary'];
			$department		= $_POST['department'];
			$photo			= $_POST['featured'];

			// Create post object
			$post = array(
				'ID' 			=> $id,
				'post_title'    => $name,
				'post_content'  => $description,
				'post_type'     =>'employee',
				'post_status'   => 'publish',
				'post_author'   => 1
			);
		 
			$post_id = wp_update_post( $post, $wp_error='' );
			 
			if($post_id!=0){
			 
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');


				$uploaddir = wp_upload_dir();
				
				if(!empty($_FILES['featured']['tmp_name'])){
					$file = $_FILES['featured' ];
					$uploadfile = $uploaddir['path'] . '/' . basename( $file['name'] );
					move_uploaded_file( $file['tmp_name'] , $uploadfile );
					$filename = basename( $uploadfile );
					$wp_filetype = wp_check_filetype(basename($filename), null );
					$attachment = array(
						'post_mime_type' => $wp_filetype['type'],
						'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
						'post_content' => '',
						'post_status' => 'inherit',
						'menu_order' => $_i + 1000
						);
					$attach_id = wp_insert_attachment( $attachment, $uploadfile );

					update_post_meta($post_id,'_thumbnail_id',$attach_id);
					set_post_thumbnail( $post_id, $thumbnail_id );
				}
				//meta value insert
				update_post_meta($post_id, 'employee_salary', $salary );
				// term insert
				wp_set_object_terms( $post_id, $department, 'department' );


				echo "Successfully updated";
			}	
		}
		if($_GET['deleteID']){
			
				$deletid = $_GET['deleteID'];
			    $deletedSuc = wp_delete_post($deletid, true);

		}
		if($_GET['updateID']){
			$postid = $_GET['updateID'];
		 ?>
			<form class="form-inline" id="editCPTForm" method="POST" action="" enctype="multipart/form-data">
					<table width="295" height="346" border="0">
					  <tr>
					    <td>Name</td>
					    <td>
							<input type="hidden" name="postid" value="<?php echo $postid;?>" />
					    	<input type="text" name="employee_name" value="<?php echo get_the_title($postid);?>" /></td>
					  </tr>
					  <tr>
					    <td>Description</td>
					    <td><textarea name="employee_descreption" ><?php 
						    	$content_post = get_post($postid);
								$content = $content_post->post_content;
								$content = apply_filters('the_content', $content);
								echo $content = str_replace(']]>', ']]&gt;', $content);?>
					    </textarea></td>
					  </tr>
					  <tr>
					    <td>Salary</td>
					    <td><input type="text" name="employee_salary" value="<?php echo get_post_meta($postid, 'employee_salary', TRUE); ?>" /></td>
					  </tr>
					  <tr>
					    <td>Department</td>
					    <td>
							<?php //$tax = get_object_taxonomies('department');
							   // $taxterms = get_terms( $tax, 'orderby=count&offset=1&hide_empty=0' );
							    $taxterms = get_terms( array(
								    'taxonomy' => 'department',
								    'hide_empty' => false,
								) );
							?>
							<select name='department' id='department'>
							    <option value=''>--Select Department--</option>
							    <?php 
							   $selectedDepart =  wp_get_object_terms( $postid, 'department', array( 'fields' => 'names' ) );

							    foreach ( $taxterms as $term ) {
							    		
					    		?>
				    			<option value="<?php echo $term->slug;?>" <?php if( $selectedDepart[0] == $term->name ): ?> selected="selected"<?php endif; ?>><?php echo $term->name;?></option>
							     

							    <?php } ?>
							</select>
					    </td>
					  </tr>
					  <tr>
					    <td>Upload Image </td>
					    <td><input type="file" name="featured" />
						<?php if (has_post_thumbnail( $postid ) ): ?>
						  <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $postid ), 'single-post-thumbnail' ); ?>
						  <img src="<?php echo $image[0]; ?>" width="100" height="100">

						<?php endif; ?>
					    </td>
					  </tr>
					  <tr>
					    <td>&nbsp;</td>
					    <td><input type="submit" name="update"  value="Submit"/></td>
					  </tr>
					</table>
			</form>
		<?php }else{
	?>
	<div class="col-md-12 content">
    <div class = "inner-box content no-right-margin darkviolet">
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // This is required for AJAX to work on our page
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

            function cvf_load_all_posts(page){
                // Start the transition
                $(".cvf_pag_loading").fadeIn().css('background','#ccc');

                // Data to receive from our server
                // the value in 'action' is the key that will be identified by the 'wp_ajax_' hook 
                var data = {
                    page: page,
                    action: "demo-pagination-load-posts"
                };

                // Send the data
                $.post(ajaxurl, data, function(response) {
                    // If successful Append the data into our html container
                    $(".cvf_universal_container").html(response);
                    // End the transition
                    $(".cvf_pag_loading").css({'background':'none', 'transition':'all 1s ease-out'});
                });
            }

            // Load page 1 as the default
            cvf_load_all_posts(1);

            // Handle the clicks
            $('.cvf_universal_container .cvf-universal-pagination li.active').live('click',function(){
                var page = $(this).attr('p');
                cvf_load_all_posts(page);

            });

        }); 
        </script>
        <div class = "cvf_pag_loading">
            <div class = "cvf_universal_container">
                <div class="cvf-universal-content"></div>
            </div>
        </div>

    </div>      
</div>
<?php  } } 
add_shortcode('view_employees','show_post');

add_action( 'wp_ajax_demo-pagination-load-posts', 'cvf_demo_pagination_load_posts' );

add_action( 'wp_ajax_nopriv_demo-pagination-load-posts', 'cvf_demo_pagination_load_posts' ); 

function cvf_demo_pagination_load_posts() {

    global $wpdb;
    // Set default variables
    $msg = '';

    if(isset($_POST['page'])){
        // Sanitize the received page   
        $page = sanitize_text_field($_POST['page']);
        $cur_page = $page;
        $page -= 1;
        // Set the number of results to display
        $per_page = 3;
        $previous_btn = true;
        $next_btn = true;
        $first_btn = true;
        $last_btn = true;
        $start = $page * $per_page;

        // Set the table where we will be querying data
        $table_name = $wpdb->prefix . "posts";

        // Query the necessary posts
        $all_blog_posts = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM " . $table_name . " WHERE post_type = 'employee' AND post_status = 'publish' ORDER BY post_date DESC LIMIT %d, %d", $start, $per_page ) );

        // At the same time, count the number of queried posts
        $count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(ID) FROM " . $table_name . " WHERE post_type = 'employee' AND post_status = 'publish'", array() ) );

        // Loop into all the posts
            ?>
            <section class="content">
			<table>
				<tr>
					<th>Name</th>
					<th>Description</th>
					<th>Salary</th>
					<th>Department</th>
					<th>Photo</th>
					<th>Action</th>
				</tr> 
		    <?php
        foreach($all_blog_posts as $key => $post): 

            // Set the desired output into a variable
        	$msg .= '<tr>';
                $msg .= '<td>'.$post->post_title.'</td>';  
                $msg .= '<td>'.$post->post_content.'</td>';  
                $msg .= '<td>'.get_post_meta($post->ID, 'employee_salary', TRUE).'</td>';  
                $msg .= '<td>';
                $terms = get_the_terms( $post->ID, 'department' );
                    if ( !empty( $terms ) ) { 
                    foreach($terms as $term) {
                      $msg .= $term->name;
                    }
                    }
                $msg .= '</td>';

                $msg .='<td>';
                if (has_post_thumbnail( $post->ID ) ):;
                	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); 
                $msg .='<img src='. $image[0].' width="100" height="100">';
                endif;
                $msg .='</td>';

                $updateurl = add_query_arg( 'updateID', $post->ID, $baseurl );
                $deleteurl = add_query_arg( 'deleteID', $post->ID, $baseurl );    

                $msg .= '<td><a href='.$updateurl.'>Edit</a> | <a href='.$deleteurl.'>Delete</a></td>';

            	
        	$msg .='</tr>';

        endforeach;

        // Optional, wrap the output into a container
        $msg = "<div class='cvf-universal-content'>" . $msg . "</div><br class = 'clear' />";

        // This is where the magic happens
        $no_of_paginations = ceil($count / $per_page);

        if ($cur_page >= 7) {
            $start_loop = $cur_page - 3;
            if ($no_of_paginations > $cur_page + 3)
                $end_loop = $cur_page + 3;
            else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                $start_loop = $no_of_paginations - 6;
                $end_loop = $no_of_paginations;
            } else {
                $end_loop = $no_of_paginations;
            }
        } else {
            $start_loop = 1;
            if ($no_of_paginations > 7)
                $end_loop = 7;
            else
                $end_loop = $no_of_paginations;
        }

        // Pagination Buttons logic     
        $pag_container .= "
        <div class='cvf-universal-pagination'>
            <ul>";

        if ($first_btn && $cur_page > 1) {
            $pag_container .= "<li p='1' class='active'>First</li>";
        } else if ($first_btn) {
            $pag_container .= "<li p='1' class='inactive'>First</li>";
        }

        if ($previous_btn && $cur_page > 1) {
            $pre = $cur_page - 1;
            $pag_container .= "<li p='$pre' class='active'>Previous</li>";
        } else if ($previous_btn) {
            $pag_container .= "<li class='inactive'>Previous</li>";
        }
        for ($i = $start_loop; $i <= $end_loop; $i++) {

            if ($cur_page == $i)
                $pag_container .= "<li p='$i' class = 'selected' >{$i}</li>";
            else
                $pag_container .= "<li p='$i' class='active'>{$i}</li>";
        }

        if ($next_btn && $cur_page < $no_of_paginations) {
            $nex = $cur_page + 1;
            $pag_container .= "<li p='$nex' class='active'>Next</li>";
        } else if ($next_btn) {
            $pag_container .= "<li class='inactive'>Next</li>";
        }

        if ($last_btn && $cur_page < $no_of_paginations) {
            $pag_container .= "<li p='$no_of_paginations' class='active'>Last</li>";
        } else if ($last_btn) {
            $pag_container .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
        }

        $pag_container = $pag_container . "
            </ul>
        </div>";

        // We echo the final output
        echo 
        '<div class = "cvf-pagination-content">' . $msg . '</div>' . 
        '<div class = "cvf-pagination-nav">' . $pag_container . '</div>';

    }
    // Always exit to avoid further execution
    exit();
}
?>