<?php
class SA_Post_Type{
    /**
     * SA_Post_Type constructor.
     */
    public function __construct()
    {
        $this->initHooks();
    }

    /**
     * List all init hooks related with post type events
     */
    public function initHooks()
    {
        add_action( 'init', array( $this, 'register_post_type') );
    }

    /**
     * Register post type
     */
    public function register_post_type()
    {
        $eventLabels = array(
            'name'               => __( 'Events', __TEXTDOMAIN__ ),
            'singular_name'      => __( 'Event', __TEXTDOMAIN__ ),
            'menu_name'          => __( 'Events', __TEXTDOMAIN__ ),
            'name_admin_bar'     => __( 'Event', __TEXTDOMAIN__ ),
            'add_new'            => __( 'Add New', __TEXTDOMAIN__ ),
            'add_new_item'       => __( 'Add New Event', __TEXTDOMAIN__ ),
            'new_item'           => __( 'New Event', __TEXTDOMAIN__ ),
            'edit_item'          => __( 'Edit Event', __TEXTDOMAIN__ ),
            'view_item'          => __( 'View Event', __TEXTDOMAIN__ ),
            'all_items'          => __( 'All Events', __TEXTDOMAIN__ ),
            'search_items'       => __( 'Search Events', __TEXTDOMAIN__ ),
            'parent_item_colon'  => __( 'Parent Events:', __TEXTDOMAIN__ ),
            'not_found'          => __( 'No events found.', __TEXTDOMAIN__ ),
            'not_found_in_trash' => __( 'No events found in Trash.', __TEXTDOMAIN__ )
        );

        $evenArgs = array(
            'labels'             => apply_filters('sa_event_post_type_label', $eventLabels),
            'description'        => __( 'Description.', __TEXTDOMAIN__ ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'event' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
        );

        register_post_type( 'event', apply_filters('sa_event_post_type_args', $evenArgs) );
    }
}
new SA_Post_Type();