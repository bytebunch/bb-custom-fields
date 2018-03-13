<?php
class BBWP_CF_CustomPostType
{
  private $post_type_names;
  public $prefix = 'bbwpcustomfields';

  // Class constructor
  public function __construct()
  {
      $user_created_post_types = SerializeStringToArray(get_option($this->prefix('user_created_post_types')));

      // Add action to register the post type, if the post type does not already exist
      if($user_created_post_types && is_array($user_created_post_types) && count($user_created_post_types) >= 1){
        $this->post_type_names = $user_created_post_types;
        add_action( 'init', array( &$this, 'register_post_type' ) );
      }


  }

  public function prefix($string = '', $underscore = "_"){
    return $this->prefix.$underscore.$string;
  }

  // Method which registers the post type
  public function register_post_type()
  {
      foreach($this->post_type_names as $key=>$postType){
        if( ! post_type_exists( $key ) )
        {
          // We set the default labels based on the post type name and plural. We overwrite them with the given labels.
          $labels =
              array(
                  'name'                  => _x( $postType['label'], 'post type general name' ),
                  'singular_name'         => _x( $postType['singular_label'], 'post type singular name' ),
                  'add_new'               => _x( 'Add New', strtolower( $postType['singular_label'] ) ),
                  'add_new_item'          => __( 'Add New ' . $postType['singular_label'] ),
                  'edit_item'             => __( 'Edit ' . $postType['singular_label'] ),
                  'new_item'              => __( 'New ' . $postType['singular_label'] ),
                  'all_items'             => __( 'All ' . $postType['label'] ),
                  'view_item'             => __( 'View ' . $postType['singular_label'] ),
                  'search_items'          => __( 'Search ' . $postType['label'] ),
                  'not_found'             => __( 'No ' . strtolower( $postType['label'] ) . ' found'),
                  'not_found_in_trash'    => __( 'No ' . strtolower( $postType['label'] ) . ' found in Trash'),
                  'parent_item_colon'     => '',
                  'menu_name'             => $postType['label']
                );



          // Same principle as the labels. We set some defaults and overwrite them with the given arguments.
          $args =
              array(
                  'label'                 => $postType['label'],
                  'labels'                => $labels,
                  'public'                => true,
                  'show_ui'               => true,
                  'capability_type'       => 'post',
                  'has_archive'           => true,
                  'hierarchical'          => false,
                  'show_in_nav_menus'     => true,
                  'menu_position'         => null,
                  '_builtin'              => false,
                  //'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields'),
                  'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields')
              );
          // Register the post type
          register_post_type( $key, $args );

        }//if end here
      }// foreach ends here

  }

}
