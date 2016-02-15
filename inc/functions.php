<?php


if(!function_exists("is_admin_panel")){
  function is_admin_panel(){
    if(is_admin() /*&& is_user_logged_in() && current_user_can('manage_options')*/)
      return true;
    else
      return false;
  }
}

/*if(!function_exists("bbmetaboxes_get_option")){
  function bbmetaboxes_get_option($key = ''){
    return get_option($key);
  }
}*/

/******************************************/
/***** is_admin_panel **********/
/******************************************/
if(is_admin_panel()){

  /******************************************/
  /***** Debug functions start from here **********/
  /******************************************/
  if(!function_exists("alert")){
    function alert($alertText){
    	echo '<script type="text/javascript">';
    	echo "alert(\"$alertText\");";
    	echo "</script>";
    }
  }
  if(!function_exists("db")){
    function db($array1){
    	echo "<pre>";
    	var_dump($array1);
    	echo "</pre>";
  	}
  }

  /******************************************/
  /***** ArraytoSelectList **********/
  /******************************************/
  if(!function_exists("ArraytoSelectList")){
    function ArraytoSelectList($array, $sValue = ""){
      $output = '';
      foreach($array as $key=>$value){
        if($key == $sValue)
          $output .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
        else
          $output .= '<option value="'.$key.'">'.$value.'</option>';
      }
      return $output;
  	}
  }

  /******************************************/
  /***** arrayToSerializeString **********/
  /******************************************/
  if(!function_exists("ArrayToSerializeString")){
    function ArrayToSerializeString($array){
      if(isset($array) && is_array($array) && count($array) >= 1)
        return serialize($array);
      else
        return serialize(array());
    }
  }

  /******************************************/
  /***** SerializeStringToArray **********/
  /******************************************/
  if(!function_exists("SerializeStringToArray")){
    function SerializeStringToArray($string){
      if(isset($string) && is_array($string) && count($string) >= 1)
        return $string;
      elseif(isset($string) && $string && @unserialize($string)){
        return unserialize($string);
      }else
        return array();
    }
  }

  if(!function_exists("BBWPUpdateErrorMessage")){
    function BBWPUpdateErrorMessage(){
      if(get_option('bbwp_update_message'))
        echo '<div class="updated"><p><strong>'.get_option('bbwp_update_message').'</strong></p></div>';
      elseif(get_option('bbwp_error_message'))
        echo '<div class="error"><p><strong>'.get_option('bbwp_error_message').'</strong></p></div>';
      update_option('bbwp_update_message', '');
      update_option('bbwp_error_message', '');
    }
  }



}
