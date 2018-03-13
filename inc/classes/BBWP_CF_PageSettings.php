<?php

class BBWP_CF_PageSettings extends BBWP_CustomFields{

  public function __construct(){
    add_action('init', array($this, 'input_handle'));
    add_action( 'admin_menu', array($this,'admin_menu'));
  }// construct function end here

  /******************************************/
  /***** page_bboptions_admin_menu function start from here *********/
  /******************************************/
  public function admin_menu(){
    add_options_page('BBWP Custom Fields', 'BBWP Custom Fields', 'manage_options', $this->prefix, array($this,'add_submenu_page'));
  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page(){

    echo '<div class="wrap bytebunch_admin_page_container"><div id="icon-tools" class="icon32"></div>';

    $metaboxes_select_list = false;
    $user_created_metaboxes = SerializeStringToArray(get_option($this->prefix('user_created_metaboxes')));
    $user_created_pages = SerializeStringToArray(get_option($this->prefix('user_created_pages')));
    $user_created_post_types = SerializeStringToArray(get_option($this->prefix('user_created_post_types')));
    $current_selected_metabox = $this->get_bbcf_option("selected_metabox");

    if(isset($user_created_metaboxes) && is_array($user_created_metaboxes) && count($user_created_metaboxes) >= 1){
      $metaboxes_select_list = '<select class="submit_on_change" name="'.$this->prefix("current_selected_metabox").'">';
      foreach($user_created_metaboxes as $key=>$value){
        if($current_selected_metabox == $key){
          $metaboxes_select_list .= '<option value="'.$key.'" selected="selected">'.$value['metabox_title'].'</option>';
        }
        else{
          $metaboxes_select_list .= '<option value="'.$key.'">'.$value['metabox_title'].'</option>';
          if(!$current_selected_metabox){
            $current_selected_metabox = $key;
            $this->set_bbcf_option("selected_metabox", $key);
          }
        }
      }
      $metaboxes_select_list .= '</select>';
    }

    echo '<h2> BBWP Custom Fields </h2>';

    if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['meta_key']) && $_GET['meta_key']){
      $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($current_selected_metabox));
      BBWPUpdateErrorMessage();
      echo '<form method="post" action="">';
      $BBWPFieldTypes->AddNewFields($_GET['meta_key']);
      echo '</form>';
      return;
    }
    elseif(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['metabox_id']) && $_GET['metabox_id']){
      BBWPUpdateErrorMessage();
      $this->CreateMetaboxForm($user_created_metaboxes, $_GET['metabox_id']);
      return;
    }
    elseif(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['page_slug']) && $_GET['page_slug']){
      BBWPUpdateErrorMessage();
      $this->CreatePageForm($user_created_pages, $_GET['page_slug']);
      return;
    }
    elseif(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['name']) && $_GET['name']){
      BBWPUpdateErrorMessage();
      $this->CreatePostTypeForm($user_created_post_types, $_GET['name']);
      return;
    }

    BBWPUpdateErrorMessage();
    ?>
    <h2 class="nav-tab-wrapper bbwp_nav_wrapper">
      <?php if($metaboxes_select_list){
        echo '<a href="#add-new-fields" class="nav-tab nav-tab-active">Edit or Add Fields</a>';
      }?>
      <a href="#add-new-metabox" class="nav-tab">Meta Boxes</a>
      <a href="#add-new-option-page" class="nav-tab">Option Pages</a>
      <a href="#add-new-custom-post-types" class="nav-tab">Custom Post Types</a>
    </h2>

    <div class="bbwp_tab_nav_content" id="add-new-metabox" style="display:none;">
      <?php
      $this->CreateMetaboxForm($user_created_metaboxes);
      if($metaboxes_select_list){
        echo '<form method="post" action=""><h3>Existing Meta Boxes</h3>';
        $tableColumns = array("metabox_id" => "Meta Box ID", "metabox_title" => "Meta Box Title");
        $BBWPListTable = new BBWPListTable();
        $BBWPListTable->get_columns($tableColumns);
        $BBWPListTable->bulk_actions = array("delete" => "Delete Selected");
        $BBWPListTable->get_sortable_columns(array("metabox_id" => "metabox_id"));
        $BBWPListTable->actions = array('metabox_id' => array('delete', 'edit'));
        $BBWPListTable->prepare_items($user_created_metaboxes);
        $BBWPListTable->display();
        echo '<input type="hidden" name="sort_fields" value="'.$this->prefix('user_created_metaboxes').'" />';
        submit_button('Save Changes', 'primary alignright');
        echo '</form>';
      }
      ?>
    </div><!-- add-new-metabox -->
    <div class="bbwp_tab_nav_content" id="add-new-option-page" style="display:none;">
      <?php
      $this->CreatePageForm($user_created_pages);
      if($user_created_pages && is_array($user_created_pages) && count($user_created_pages) >= 1){
        echo '<form method="post" action=""><h3>Existing Option pages</h3>';
        $tableColumns = array("page_slug" => "Page Slug", "page_title" => "Page Title"/*, 'parent_menu' => "Parent Menu"*/);
        $BBWPListTable = new BBWPListTable();
        $BBWPListTable->get_columns($tableColumns);
        $BBWPListTable->bulk_actions = array("delete" => "Delete Selected");
        $BBWPListTable->get_sortable_columns(array("page_slug" => "page_slug"));
        $BBWPListTable->actions = array('page_slug' => array('delete', 'edit'));
        $BBWPListTable->prepare_items($user_created_pages);
        $BBWPListTable->display();
        echo '<input type="hidden" name="sort_fields" value="'.$this->prefix('user_created_pages').'" />';
        submit_button('Save Changes', 'primary alignright');
        echo '</form>';
      }
      ?>
    </div>
    <div class="bbwp_tab_nav_content" id="add-new-fields">
      <?php
      if($metaboxes_select_list){
        echo '<form method="post" action="">
        <table class="form-table"><tr><th>Selected Meta Box :</th><td>'.$metaboxes_select_list.'</td></tr>';
        echo '</table></form>';

        echo '<form method="post" action="">';
        $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($current_selected_metabox));
        $BBWPFieldTypes->AddNewFields();
        echo '</form>';

        $existing_values = SerializeStringToArray(get_option($this->prefix($current_selected_metabox)));
        if(isset($existing_values) && is_array($existing_values) && count($existing_values) >= 1){
          echo '<form method="post" action="">';
          $tableColumns = array("meta_key" => "Meta Key", "field_title" => "Field Title", 'field_type' => 'Field Type');
          $BBWPListTable = new BBWPListTable();
          $BBWPListTable->get_columns($tableColumns);
          $BBWPListTable->bulk_actions = array("delete" => "Delete Selected");
          $BBWPListTable->get_sortable_columns(array("meta_key" => "meta_key"));
          $BBWPListTable->actions = array('meta_key' => array('delete', 'edit'));
          $BBWPListTable->prepare_items($existing_values);
          $BBWPListTable->display();
          echo '<input type="hidden" name="sort_fields" value="'.$this->prefix($current_selected_metabox).'" />';
          submit_button('Save Changes', 'primary alignright');
          echo '</form>';
        }
      }
      ?>
    </div>
    <div class="bbwp_tab_nav_content" id="add-new-custom-post-types" style="display:none;">
      <?php
      $this->CreatePostTypeForm($user_created_post_types);
      if($user_created_post_types && is_array($user_created_post_types) && count($user_created_post_types) >= 1){
        echo '<form method="post" action=""><h3>Existing Post Types</h3>';
        $tableColumns = array("name" => "Post Type Slug/Name", "label" => "Plural Label");
        $BBWPListTable = new BBWPListTable();
        $BBWPListTable->get_columns($tableColumns);
        $BBWPListTable->bulk_actions = array("delete" => "Delete Selected");
        $BBWPListTable->get_sortable_columns(array("name" => "name"));
        $BBWPListTable->actions = array('name' => array('delete', 'edit'));
        $BBWPListTable->prepare_items($user_created_post_types);
        $BBWPListTable->display();
        echo '<input type="hidden" name="sort_fields" value="'.$this->prefix('user_created_post_types').'" />';
        submit_button('Save Changes', 'primary alignright');
        echo '</form>';
      }
      ?>
    </div>

    <?php echo '</div><!-- main wrap div end here -->';
  }

  private function CreateMetaboxForm($user_created_metaboxes = array(), $edit_metabox = false){
    $edit_metabox_values = array();
    echo '<form method="post" action="">';
    if($edit_metabox && is_array($user_created_metaboxes) && count($user_created_metaboxes) >= 1 && array_key_exists($edit_metabox, $user_created_metaboxes))
    {
      $edit_metabox_values = $user_created_metaboxes[$edit_metabox];
      echo '<input type="hidden" name="update_created_metabox" value="'.$edit_metabox.'" />';
    }
      ?>
      <input type="hidden" name="create_new_metabox" value="<?php echo $this->prefix('create_new_metabox'); ?>" />
      <table class="form-table">
        <tr>
          <th scope="row"><label for="user_created_metaboxes">Meta Box Title: <span class="require_star">*</span></label></th>
          <td>
            <?php $selected_value = ''; if(isset($edit_metabox_values['metabox_title'])){ $selected_value = $edit_metabox_values['metabox_title']; } ?>
            <input type="text" name="user_created_metaboxes" id="user_created_metaboxes" class="regular-text" required="required" value="<?php echo $selected_value; ?>" />
          </td>
        </tr>
        <tr>
          <th scope="row">Select Location: <small>(optional)</small></th>
          <td>
            <?php
            $selected_value = array();
            if(isset($edit_metabox_values['metabox_location']) && is_array($edit_metabox_values['metabox_location'])){
              $selected_value = $edit_metabox_values['metabox_location'];
            }

            $metabox_location_list = array('user_profile' => 'User Profile', 'comment' => 'Comment');
            $args = array('public' => true);
            $post_types = get_post_types( $args, 'names' );
            foreach ( $post_types as $post_type ) {
              if($post_type == 'attachment')
                continue;
              $metabox_location_list[$post_type] = ucfirst(str_ireplace(array("-","_"), array(" ", " "), $post_type));
            }
            $taxonomies = get_taxonomies($args);
            foreach ( $taxonomies as $taxonomy ) {
              if($taxonomy == 'post_format')
                continue;
              $metabox_location_list[$taxonomy] = ucfirst(str_ireplace(array("-","_"), array(" ", " "), $taxonomy));
            }
            echo '<div class="bb_checkboxes_container">';
            foreach ($metabox_location_list as $key => $value) {
              if(in_array($key, $selected_value))
                echo ' <input type="checkbox" id="'.$key.'" value="'.$key.'" name="metabox_location[]" checked="checked" /> <label for="'.$key.'">'.$value.'</label> ';
              else
                echo ' <input type="checkbox" id="'.$key.'" value="'.$key.'" name="metabox_location[]" /> <label for="'.$key.'">'.$value.'</label> ';
              echo '&nbsp;&nbsp;';
            }
            echo '</div>';
            ?>
          </td>
        </tr>

        <?php
        $selected_value = ''; if(isset($edit_metabox_values['metabox_pages'])){ $selected_value = $edit_metabox_values['metabox_pages']; }
        $user_created_pages = SerializeStringToArray(get_option($this->prefix('user_created_pages')));
        if(isset($user_created_pages) && is_array($user_created_pages) && count($user_created_pages) >= 1){
          $pages_select_list = '<select class="" name="metabox_pages"><option value=""> Default </option>';
          foreach($user_created_pages as $key=>$value){
            $selected = '';
            if($selected_value == $key){ $selected = ' selected="selected"'; }
              $pages_select_list .= '<option value="'.$key.'"'.$selected.'>'.$value['page_title'].'</option>';
          }
          $pages_select_list .= '</select>';
          echo '<tr><th scope="row"><label for="">Select Page <small>(optional)</small></label></th><td>'.$pages_select_list.'</td></tr>';
        }

        /*$selected_value = ''; if(isset($edit_metabox_values['metabox_context'])){ $selected_value = $edit_metabox_values['metabox_context']; }
        $context = array("advanced" => "Advanced", "normal" => "Normal", "side" => "side");
        echo '<tr><th scope="row"><label for="metabox_context">Position: <small>(optional)</small>
        <p class="description"> This setting is only for hierarchical post types. i.e page</p>
        </label></th>';
        echo '<td><select name="metabox_context" id="metabox_context">'.ArraytoSelectList($context, $selected_value).'</select></td></tr>';
        */
        $selected_value = ''; if(isset($edit_metabox_values['metabox_priority'])){ $selected_value = $edit_metabox_values['metabox_priority']; }
        $context = array("" => "Default", "high" => "High", "low" => "Low");
        echo '<tr><th scope="row"><label for="metabox_priority">Priority: <small>(optional)</small>
        <p class="description"> This setting is only for hierarchical post types. i.e page</p>
        </label></th>';
        echo '<td><select name="metabox_priority" id="metabox_priority">'.ArraytoSelectList($context, $selected_value).'</select></td></tr>';

        ?>

      </table>
      <?php
    submit_button("Create Metabox");
    echo '</form>';
  }

  /******************************************/
  /***** CreatePageForm function start from here *********/
  /******************************************/
  private function CreatePageForm($user_created_pages = array(), $edit_page = false){
    $edit_page_values = array();
    echo '<form method="post" action="">';
    if($edit_page && is_array($user_created_pages) && count($user_created_pages) >= 1 && array_key_exists($edit_page, $user_created_pages)){
      $edit_page_values = $user_created_pages[$edit_page];
      echo '<input type="hidden" name="update_created_option_page" value="'.$edit_page.'" />';
    }
      ?>
      <input type="hidden" name="create_new_option_page" value="<?php echo $this->prefix('create_new_option_page'); ?>" />
      <table class="form-table">
        <tr>
          <th scope="row"><label for="user_created_pages">Page Name: <span class="require_star">*</span></label></th>
          <td>
            <?php $selected_value = ''; if(isset($edit_page_values['page_title'])){ $selected_value = $edit_page_values['page_title']; } ?>
            <input type="text" name="user_created_pages" id="user_created_pages" class="regular-text" required="required" value="<?php echo $selected_value; ?>" />
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="parent_menu">Select Parent Menu: <span class="require_star">*</span></label></th>
          <td>
            <select id="parent_menu" name="parent_menu">
              <?php
              $pages_list = array(
                "new_menu" => "Create New Parent Menu ",
                 "dashboard" =>"Dashboard",
                 "posts" => 'Posts',
                 "media" => "Media",
                 "pages" =>"Pages",
                 "comments" => 'Comments',
                 "theme" => "Appearance",
                 "plugins" =>"Plugins",
                 "users" => 'Users',
                 "management" => "Tools",
                 "options" => "Settings",
              );
              if(isset($user_created_pages) && is_array($user_created_pages) && count($user_created_pages) >= 1){
                foreach($user_created_pages as $key=>$value){
                  if(isset($value['parent_menu']) && $value['parent_menu'] == "new_menu")
                    $pages_list[$value['page_slug']] = $value['page_title'];
                }
              }
              //global $submenu, $menu, $pagenow;
              $selected_value = ''; if(isset($edit_page_values['parent_menu'])){ $selected_value = $edit_page_values['parent_menu']; }
              echo ArraytoSelectList($pages_list, $selected_value);
            ?>
            </select>
          </td>
        </tr>
      </table>
    <?php
    submit_button('Create Page');
    echo '</form>';
  }

  /******************************************/
  /***** CreatePostTypeForm function start from here *********/
  /******************************************/
  private function CreatePostTypeForm($user_created_post_types = array(), $edit_post_type = false){
    $edit_post_type_values = array();
    echo '<form method="post" action="">';
    if($edit_post_type && is_array($user_created_post_types) && count($user_created_post_types) >= 1 && array_key_exists($edit_post_type, $user_created_post_types)){
      $edit_post_type_values = $user_created_post_types[$edit_post_type];
      echo '<input type="hidden" name="update_created_post_type" value="'.$edit_post_type.'" />';
    }
      ?>
      <input type="hidden" name="create_new_post_type" value="<?php echo $this->prefix('create_new_post_type'); ?>" />
      <h3>Basic Settings</h3>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="name">Post Type Slug: <span class="require_star">*</span></label></th>
          <td>
            <?php $selected_value = ''; if(isset($edit_post_type_values['name'])){ $selected_value = $edit_post_type_values['name']; } ?>
            <input type="text" name="user_created_post_type[name]" id="name" class="regular-text" required="required" value="<?php echo $selected_value; ?>" />
            <br /><span class="bbwpcf-field-description">The post type name/slug. Used for various queries for post type content.</span>
            <p>Slugs should only contain alphanumeric, latin characters. Underscores should be used in place of spaces. Set "Custom Rewrite Slug" field to make slug use dashes for URLs.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="label">Plural Label <span class="require_star">*</span></label></th>
          <td>
            <?php $selected_value = ''; if(isset($edit_post_type_values['label'])){ $selected_value = $edit_post_type_values['label']; } ?>
            <input type="text" name="user_created_post_type[label]" id="name" class="regular-text" required="required" value="<?php echo $selected_value; ?>" />
            <br /><span class="bbwpcf-field-description">Used for the post type admin menu item.</span>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="label">Singular Label <span class="require_star">*</span></label></th>
          <td>
            <?php $selected_value = ''; if(isset($edit_post_type_values['singular_label'])){ $selected_value = $edit_post_type_values['singular_label']; } ?>
            <input type="text" name="user_created_post_type[singular_label]" id="name" class="regular-text" required="required" value="<?php echo $selected_value; ?>" />
            <br /><span class="bbwpcf-field-description">Used when a singular label is needed.</span>
          </td>
        </tr>
      </table>

    <?php
    submit_button('Save Changes');
    echo '</form>';
  }

  /******************************************/
  /***** input_handle function start from here *********/
  /******************************************/
  private function DeleteMetaBoxes($meta_key, $db_key){
    $existing_values = SerializeStringToArray(get_option($db_key));
    $update = false;
    if($existing_values && is_array($existing_values) && count($existing_values) >= 1){
      if(isset($meta_key) && is_array($meta_key) && count($meta_key) >= 1){
        foreach($meta_key as $value){
          if($value && array_key_exists($value, $existing_values)){
            $update = true;
            unset($existing_values[$value]);
            delete_option($this->prefix($value));
            if($value == $this->get_bbcf_option("selected_metabox"))
              $this->set_bbcf_option("selected_metabox",'');
          }
        }
      }
      elseif(isset($meta_key) && $meta_key && array_key_exists($meta_key, $existing_values)){
        $update = true;
        unset($existing_values[$meta_key]);
        delete_option($this->prefix($meta_key));
        if($meta_key == $this->get_bbcf_option("selected_metabox"))
          $this->set_bbcf_option("selected_metabox",'');
      }
      if($update == true){
      update_option($db_key, ArrayToSerializeString($existing_values));
      update_option("bbwp_update_message", 'Your setting have been updated.'); }
    }
  }


  /******************************************/
  /***** input_handle function start from here *********/
  /******************************************/
  public function input_handle(){
    if(isset($_GET['page']) && $_GET['page'] === $this->prefix){

      /* metabox input handling */
      if(isset($_POST['create_new_metabox']) && $_POST['create_new_metabox'] === $this->prefix('create_new_metabox') && isset($_POST['user_created_metaboxes']))
      {
        $update = false;
        $update_message = 'Your setting have been updated.';
        $existing_values = SerializeStringToArray(get_option($this->prefix('user_created_metaboxes')));
        $new_values = array();

        if(isset($_POST['metabox_location']) && is_array($_POST['metabox_location']) && count($_POST['metabox_location']) >= 1){
          $new_values['metabox_location'] = $_POST['metabox_location'];
        }
        if(isset($_POST['metabox_pages'])){
          $new_values['metabox_pages'] = $_POST['metabox_pages'];
        }
        if(isset($_POST['metabox_context'])){
          $new_values['metabox_context'] = $_POST['metabox_context'];
        }
        if(isset($_POST['metabox_priority'])){
          $new_values['metabox_priority'] = $_POST['metabox_priority'];
        }

        $value = BBWPSanitization::Textfield($_POST['user_created_metaboxes']);
        $key = sanitize_key($_POST['user_created_metaboxes']);
        if(isset($_POST['update_created_metabox']) && array_key_exists($_POST['update_created_metabox'], $existing_values)){
          $key = $_POST['update_created_metabox'];
          $update = true;
          $update_message = '<p>Your setting have been updated.</p><p><a href="?page='.$_GET['page'].'">← Back to Main Page</a></p>';
        }
        if($update == false && $key && array_key_exists($key, $existing_values)){
          update_option("bbwp_error_message", 'There was some problem. Please try again with different meta box name.');
        }elseif($value && $key){
          $new_values['metabox_id'] = $key;
          $new_values['metabox_title'] = $value;
          $existing_values[$key] = $new_values;
          update_option($this->prefix('user_created_metaboxes'), ArrayToSerializeString($existing_values));
          $this->set_bbcf_option("selected_metabox", $key);
          update_option("bbwp_update_message", $update_message);
        }
      }

      if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['metabox_id']) && $_GET['metabox_id']){
        $this->DeleteMetaBoxes($_GET['metabox_id'], $this->prefix("user_created_metaboxes"));
      }
      if(isset($_POST['sort_fields']) && $_POST['sort_fields'] === $this->prefix('user_created_metaboxes')){
        if(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete' && isset($_POST['fields']) && is_array($_POST['fields']) && count($_POST['fields']) >= 1)
        {
          $this->DeleteMetaBoxes($_POST['fields'], $this->prefix("user_created_metaboxes"));
        }
        elseif(isset($_POST['sort_field']) && is_array($_POST['sort_field']) && count($_POST['sort_field']) >= 1)
          BBWPFieldTypes::SortFields($_POST['sort_field'], $this->prefix("user_created_metaboxes"));
      }
      /* meta box inputs end here */

      /* create new option page form input start */
      if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['page_slug']) && $_GET['page_slug'])
        BBWPFieldTypes::DeleteFields($_GET['page_slug'], $this->prefix("user_created_pages"));

      if(isset($_POST['sort_fields']) && $_POST['sort_fields'] === $this->prefix('user_created_pages')){
        if(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete' && isset($_POST['fields']) && is_array($_POST['fields']) && count($_POST['fields']) >= 1)
        {
          BBWPFieldTypes::DeleteFields($_POST['fields'], $this->prefix("user_created_pages"));
        }
        elseif(isset($_POST['sort_field']) && is_array($_POST['sort_field']) && count($_POST['sort_field']) >= 1)
          BBWPFieldTypes::SortFields($_POST['sort_field'], $this->prefix("user_created_pages"));
      }

      if(isset($_POST['create_new_option_page']) && $_POST['create_new_option_page'] === $this->prefix('create_new_option_page')){
        if(isset($_POST['user_created_pages']) && $_POST['user_created_pages'] && isset($_POST['parent_menu']) && $_POST['parent_menu'])
        {
          $update = false;
          $update_message = 'Your setting have been updated.';
          $existing_values = SerializeStringToArray(get_option($this->prefix('user_created_pages')));
          $value = BBWPSanitization::Textfield($_POST['user_created_pages']);
          $key = sanitize_key($_POST['user_created_pages']);
          $parent_menu = sanitize_key($_POST['parent_menu']);

          if(isset($_POST['update_created_option_page']) && array_key_exists($_POST['update_created_option_page'], $existing_values)){
            $key = $_POST['update_created_option_page'];
            $update = true;
            $update_message = '<p>Your setting have been updated.</p><p><a href="?page='.sanitize_key($_GET['page']).'">← Back to Main Page</a></p>';
          }
          if($update == false && array_key_exists($key, $existing_values)){
            update_option("bbwp_error_message", 'There was some problem. Please try again with different page name.');
          }elseif($value && $parent_menu && $key){
            $existing_values[$key] = array('page_slug' => $key, 'page_title' => $value, 'parent_menu' => $parent_menu);
            update_option($this->prefix('user_created_pages'), ArrayToSerializeString($existing_values));
            update_option("bbwp_update_message", $update_message);
          }
        }
      }
      /* create new option page form input end */

      /* create new post type form input start */
      if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['name']) && $_GET['name'])
        BBWPFieldTypes::DeleteFields($_GET['name'], $this->prefix("user_created_post_types"));

      if(isset($_POST['sort_fields']) && $_POST['sort_fields'] === $this->prefix('user_created_post_types')){
        if(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete' && isset($_POST['fields']) && is_array($_POST['fields']) && count($_POST['fields']) >= 1)
        {
          BBWPFieldTypes::DeleteFields($_POST['fields'], $this->prefix("user_created_post_types"));
        }
        elseif(isset($_POST['sort_field']) && is_array($_POST['sort_field']) && count($_POST['sort_field']) >= 1)
          BBWPFieldTypes::SortFields($_POST['sort_field'], $this->prefix("user_created_post_types"));
      }
      if(isset($_POST['create_new_post_type']) && $_POST['create_new_post_type'] === $this->prefix('create_new_post_type')){
        if(isset($_POST['user_created_post_type']) && $_POST['user_created_post_type'] && is_array($_POST['user_created_post_type']) && count($_POST['user_created_post_type']) >= 1 && isset($_POST['user_created_post_type']['name']) && $_POST['user_created_post_type']['name']){
          $update = false;
          $update_message = 'Your setting have been updated.';
          $existing_values = SerializeStringToArray(get_option($this->prefix('user_created_post_types')));
          $new_values = array();
          $array_index = '';
          foreach($_POST['user_created_post_type'] as $key=>$value){
            if(isset($_POST['user_created_post_type'][$key]) && $_POST['user_created_post_type'][$key]){
              if($key == 'name'){
                $new_values[$key] = BBWPSanitization::Textfield(strtolower($value));
                $array_index = $new_values['name'];
              }
              else
                $new_values[$key] = BBWPSanitization::Textfield($value);
            }
          }
          if(isset($_POST['update_created_post_type']) && array_key_exists($_POST['update_created_post_type'], $existing_values)){
            $array_index = $_POST['update_created_post_type'];
            $update = true;
            $update_message = '<p>Your setting have been updated.</p><p><a href="?page='.sanitize_key($_GET['page']).'">← Back to Main Page</a></p>';
          }
          if($update == false && array_key_exists($array_index, $existing_values)){
            update_option("bbwp_error_message", 'There was some problem. Please try again with different page name.');
          }elseif($new_values && is_array($new_values) && count($new_values) >= 1 && $array_index ){
            $existing_values[$array_index] = $new_values;
            update_option($this->prefix('user_created_post_types'), ArrayToSerializeString($existing_values));
            update_option("bbwp_update_message", $update_message);
          }

        }
      }
      /* create new post type form input end */

      if(isset($_POST[$this->prefix("current_selected_metabox")]) && array_key_exists($_POST[$this->prefix("current_selected_metabox")], SerializeStringToArray(get_option($this->prefix('user_created_metaboxes'))))){
        $this->set_bbcf_option("selected_metabox", $_POST[$this->prefix("current_selected_metabox")]);
        update_option("bbwp_update_message", 'Your setting have been updated.');
      }

      $current_selected_metabox = $this->get_bbcf_option("selected_metabox");
      if($current_selected_metabox){
        $BBWPFieldTypes = new BBWPFieldTypes($this->prefix($current_selected_metabox));
        if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['meta_key']) && $_GET['meta_key'])
          BBWPFieldTypes::DeleteFields($_GET['meta_key'], $this->prefix($current_selected_metabox));
        if(isset($_POST['sort_fields']) && $_POST['sort_fields'] === $this->prefix($current_selected_metabox)){
    			if(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete' && isset($_POST['fields']) && is_array($_POST['fields']) && count($_POST['fields']) >= 1){
            BBWPFieldTypes::DeleteFields($_POST['fields'], $this->prefix($current_selected_metabox));
    			}
    			elseif(isset($_POST['sort_field']) && is_array($_POST['sort_field']) && count($_POST['sort_field']) >= 1)
    				BBWPFieldTypes::SortFields($_POST['sort_field'], $this->prefix($current_selected_metabox));
    		}
        $BBWPFieldTypes->UpdateFields();
      }

    } // if isset page end here
  } // input handle function end here

}// class end here
