<?php

class BBWP_CustomFields{

  public $prefix = 'bbwpcustomfields';
  static $bbcf = array();
  //public $url = BBWPMETABOXES_URL;

  public function __construct(){
    self::$bbcf = SerializeStringToArray(get_option($this->prefix.'_options'));
    add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
    add_filter( 'plugin_action_links_'.BBWP_CF_PLUGIN_FILE, array($this, 'plugin_action_links') );
    register_activation_hook(BBWP_CF_PLUGIN_FILE, array($this, 'PluginActivation'));
    //register_deactivation_hook(BBWP_CF_PLUGIN_FILE, array($this, 'PluginDeactivation'));
  }// construct function end here

  public function prefix($string = '', $underscore = "_"){
    return $this->prefix.$underscore.$string;
  }

  public function plugin_action_links( $links ) {
     $links[] = '<a href="'. esc_url(get_admin_url(null, 'options-general.php?page='.$this->prefix)) .'">Settings</a>';
     return $links;
  }

  public function PluginActivation() {
    $ver = "0.0.1";
    if(!(isset(self::$bbcf['ver']) && self::$bbcf['ver'] == $ver))
      $this->set_bbcf_option('ver', $ver);
  }

  /*public function PluginDeactivation(){
    delete_option($this->prefix.'_options');
  }*/

  public function get_bbcf_option($key){
    if(isset(self::$bbcf[$key]))
      return self::$bbcf[$key];
    else
      return NULL;
  }

  public function set_bbcf_option($key, $value){
      self::$bbcf[$key] = $value;
      update_option($this->prefix.'_options', ArrayToSerializeString(self::$bbcf));
  }


  public function wp_admin_style_scripts() {

    //if(isset($_GET['page']) && $_GET['page'] == $this->prefix){

      global $wp_scripts;
      $ui = $wp_scripts->query('jquery-ui-core');

      wp_enqueue_script('uploads');
      wp_enqueue_script( 'postbox' );
      wp_enqueue_media();

      if (is_ssl())
        $url = "https://code.jquery.com/ui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
      else
        $url = "http://code.jquery.com/ui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";

      wp_register_style( 'jquery-ui', $url, array(), $ui->ver);
      wp_enqueue_style('jquery-ui');

      wp_register_style( $this->prefix.'_wp_admin_css', BBWP_CF_URL . '/css/style.css', array('wp-color-picker'), '1.0.0' );
      wp_enqueue_style($this->prefix.'_wp_admin_css');

      wp_register_script( $this->prefix.'_wp_admin_script', BBWP_CF_URL . '/js/script.js', array('jquery', 'jquery-ui-sortable' ,'jquery-ui-datepicker', 'wp-color-picker'), '1.0.0' );
      wp_enqueue_script( $this->prefix.'_wp_admin_script' );


      //$js_variables = array('prefix' => $this->prefix."_");
      //wp_localize_script( $this->prefix.'_wp_admin_script', $this->prefix, $js_variables );
    //}
  }

}
