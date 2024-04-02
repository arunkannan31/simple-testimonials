<?php
/**
 * Plugin Name: Simple Testimonials
 * Description: A WordPress plugin for managing and displaying testimonials.
 * Version: 1.0
 * Author: Arunkumar Kannan
 */

// Register Custom Post Type
function simple_testimonials_register_post_type() {
    $labels = array(
        'name'                  => _x( 'Testimonials', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Testimonial', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Testimonials', 'text_domain' ),
        'archives'              => __( 'Testimonial Archives', 'text_domain' ),
        'attributes'            => __( 'Testimonial Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Testimonial:', 'text_domain' ),
        'all_items'             => __( 'All Testimonials', 'text_domain' ),
        'add_new_item'          => __( 'Add New Testimonial', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Testimonial', 'text_domain' ),
        'edit_item'             => __( 'Edit Testimonial', 'text_domain' ),
        'update_item'           => __( 'Update Testimonial', 'text_domain' ),
        'view_item'             => __( 'View Testimonial', 'text_domain' ),
        'view_items'            => __( 'View Testimonials', 'text_domain' ),
        'search_items'          => __( 'Search Testimonial', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into Testimonial', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this Testimonial', 'text_domain' ),
        'items_list'            => __( 'Testimonials list', 'text_domain' ),
        'items_list_navigation' => __( 'Testimonials list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter Testimonials list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Testimonial', 'text_domain' ),
        'description'           => __( 'Customer testimonials', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-format-quote',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type( 'testimonial', $args );
}
add_action( 'init', 'simple_testimonials_register_post_type', 0 );

// Add Custom Fields to Testimonials
function simple_testimonials_custom_fields() {
    add_meta_box(
        'testimonial_fields',
        'Testimonial Details',
        'simple_testimonials_display_fields',
        'testimonial',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'simple_testimonials_custom_fields' );

function simple_testimonials_display_fields( $post ) {
    // Retrieve existing values from the database
    $customer_name = get_post_meta( $post->ID, 'customer_name', true );
    $customer_position = get_post_meta( $post->ID, 'customer_position', true );
    $company_name = get_post_meta( $post->ID, 'company_name', true );
    $testimonial_date = get_post_meta( $post->ID, 'testimonial_date', true );
    ?>
    <p>
        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" value="<?php echo esc_attr( $customer_name ); ?>" />
    </p>
    <p>
        <label for="customer_position">Customer Position:</label>
        <input type="text" id="customer_position" name="customer_position" value="<?php echo esc_attr( $customer_position ); ?>" />
    </p>
    <p>
        <label for="company_name">Company Name:</label>
        <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr( $company_name ); ?>" />
    </p>
    <p>
        <label for="testimonial_date">Testimonial Date:</label>
        <input type="date" id="testimonial_date" name="testimonial_date" value="<?php echo esc_attr( $testimonial_date ); ?>" />
    </p>
    <?php
}

// Save Custom Fields Data
function simple_testimonials_save_fields( $post_id ) {
    if ( isset( $_POST['customer_name'] ) ) {
        update_post_meta( $post_id, 'customer_name', sanitize_text_field( $_POST['customer_name'] ) );
    }
    if ( isset( $_POST['customer_position'] ) ) {
        update_post_meta( $post_id, 'customer_position', sanitize_text_field( $_POST['customer_position'] ) );
    }
    if ( isset( $_POST['company_name'] ) ) {
        update_post_meta( $post_id, 'company_name', sanitize_text_field( $_POST['company_name'] ) );
    }
    if ( isset( $_POST['testimonial_date'] ) ) {
        update_post_meta( $post_id, 'testimonial_date', sanitize_text_field( $_POST['testimonial_date'] ) );
    }
}
add_action( 'save_post', 'simple_testimonials_save_fields' );

// Shortcode for Displaying Testimonials
function simple_testimonials_shortcode() {
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => -1
    );
    $testimonials = new WP_Query( $args );

    $output = '<div class="simple-testimonials">';
    if ( $testimonials->have_posts() ) {
        while ( $testimonials->have_posts() ) {
            $testimonials->the_post();
            $customer_name = get_post_meta( get_the_ID(), 'customer_name', true );
            $customer_position = get_post_meta( get_the_ID(), 'customer_position', true );
            $company_name = get_post_meta( get_the_ID(), 'company_name', true );
            $testimonial_date = get_post_meta( get_the_ID(), 'testimonial_date', true );
            $testimonial_text = get_the_content();
            $output .= '<div class="testimonial">';
            $output .= '<p><strong>' . esc_html( $customer_name ) . '</strong>, ' . esc_html( $customer_position ) . ' - ' . esc_html( $company_name ) . ' - ' . esc_html( $testimonial_date ) . '</p>';
            $output .= '<blockquote>' . wpautop( $testimonial_text ) . '</blockquote>';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>No testimonials found.</p>';
    }
    $output .= '</div>';
    wp_reset_postdata();
    return $output;
}
add_shortcode( 'simple_testimonials', 'simple_testimonials_shortcode' );

// Add CSS Styles
function simple_testimonials_styles() {
    wp_enqueue_style( 'simple-testimonials-style', plugin_dir_url( __FILE__ ) . 'css/simple-testimonials.css' );
}
add_action( 'wp_enqueue_scripts', 'simple_testimonials_styles' );

