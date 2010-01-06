<?php
/*
 || UI.LAYOUT
 */


/*
 * Initial functions
 * Starts adding the helper ysJQueryUICore core files jquery.ui
 * Call set_layout_configuration_files function ();
 */
if(!function_exists('ui_core_exist')){
  use_helper('ysJQueryUICore');
}
set_layout_configuration_files();

/**
 *
 * Add the context all the files needed to enable basic
 * ui.layout and can be run the widget. View settings.yml
 * @param string $type default value: null; if $type = 'CSS' Add only stylesheets
 *        if $type = 'JS' Add only javascripts files .
 */
function set_layout_configuration_files($type = null){
  $ui_css = sfConfig::get('app_ys_jquery_ui_layout_css', array());
  $ui_js = sfConfig::get('app_ys_jquery_ui_layout_js', array());
  $css_path = get_ui_layout_css_path();
  $js_path  = get_ui_layout_js_path();
  $type = strtolower(trim($type));
  switch ($type) {
    case 'css':
      foreach($ui_css as $style){
        add_css_configuration_file($style, 'last' , $css_path);
      }
      break;
    case 'js':
      foreach($ui_js as $js){
        add_js_configuration_file($js, 'last' , $js_path);
      }
      break;
    default:
      foreach($ui_css as $style){
        add_css_configuration_file($style, 'last' , $css_path);
      }
      foreach($ui_js as $js){
        add_js_configuration_file($js, 'last' , $js_path);
      }
      break;
  }
}

/**
 * UI.Layout creates a 'page-layout' that has auto-sizing 'center pane'
 * surrounded by up to four collapsible and resizable 'border panes'
 * (north, south, east & west). It can also create multiple headers &
 * footers inside each pane.
 * @param string $layoutName The layout name
 * @param string $selector jQuery Selector
 * @param array() $configurations Array. See the options and events in
 *        http://layout.jquery-dev.net/documentation.html#Options
 */
function ui_layout_configure_to($layoutName,$selector,$configurations = array()){
  $support  = core_init_javasacript_tag();
  $support .= 'var ' . $layoutName . ';';
  $pattern = ui_layout_pattern($configurations);
  $utilityMethods = '';
  if(!is_array($configurations) || !sizeof($configurations) > 0){
    $configurations = array('applyDefaultStyles' => true);
  }else{
    if(!isset($configurations['name'])){
      $configurations['name'] = $layoutName;
    }
    $utilityMethods = ui_layout_utility_methods_pattern($layoutName,$configurations);
  }

  if(isset($configurations['cache']) && $configurations['cache'] === true){
    echo add_jquery_support('window','unload', like_function("layoutState.save('$layoutName')"));
    $pattern = "$.extend( $pattern , layoutState.load('$layoutName'))";
  }

  $jsVar =  like_function($layoutName . ' = ' . jquery_support(
                                                  $selector,
                                                  'layout' ,
                                                  $pattern,
                                                  true,
                                                  $utilityMethods));

  $support .= jquery_support($selector,'ready',$jsVar);
  echo $support .= core_end_javasacript_tag() ;
}

/**
 * Internal function
 * @param array() $configuration Configuration array
 * @return string JSON Array.
 * @see ui_layout_configure_to()
 */
function ui_layout_pattern($configuration){
  $pattern = '';
  unset($configuration['addToggleBtn'],
        $configuration['addOpenBtn'],
        $configuration['addCloseBtn'],
        $configuration['addPinBtn'],
        $configuration['allowOverflow'],
        $configuration['resetOverflow'],
        $configuration['cache']);
  if(!is_array($configuration) || !sizeof($configuration) > 0){
    $configuration = array('applyDefaultStyles' => true);
  }
  return $pattern = json_encode($configuration);
}

/**
 * TODO documentation
 * @param string $layoutName
 * @param string $configuration
 * @return <type>
 */
function ui_layout_utility_methods_pattern($layoutName,$configuration){
    $pattern = ';';
    if(isset($configuration['addToggleBtn']))
      $pattern .= get_ui_layout_utility_methods($layoutName,'addToggleBtn', $configuration['addToggleBtn']);
    if(isset($configuration['addOpenBtn']))
      $pattern .= get_ui_layout_utility_methods($layoutName,'addOpenBtn', $configuration['addOpenBtn']);
    if(isset($configuration['addCloseBtn']))
      $pattern .= get_ui_layout_utility_methods($layoutName,'addCloseBtn', $configuration['addCloseBtn']);
    if(isset($configuration['addPinBtn']))
      $pattern .= get_ui_layout_utility_methods($layoutName,'addPinBtn', $configuration['addPinBtn']);
    if(isset($configuration['allowOverflow'])){
      $method = 'allowOverflow';
      if($configuration['allowOverflow'] == 'this')
        $pattern .= sprintf("%s.%s(%s);",$layoutName,$method,$configuration['allowOverflow']);
      else
        $pattern .= sprintf("%s.%s('%s');",$layoutName,$method,$configuration['allowOverflow']);
    }
    if(isset($configuration['resetOverflow'])){
      $method = 'resetOverflow';
      if($configuration['resetOverflow'] == 'this')
        $pattern .= sprintf("%s.%s(%s);",$layoutName,$method,$configuration['resetOverflow']);
      else
        $pattern .= sprintf("%s.%s('%s');",$layoutName,$method,$configuration['resetOverflow']);
    }
    if($pattern == ';')
      $pattern = '';
    return $pattern;
}

/**
 * TODO documentation
 * @param string $layoutName
 * @param string $method
 * @param string $utilitiMethods
 */
function get_ui_layout_utility_methods($layoutName,$method, $utilitiMethods){
    $pattern = '';
    if(is_array($utilitiMethods) && sizeof($utilitiMethods > 0)){
        foreach($utilitiMethods as $selector => $pane){
            $pattern .= sprintf("%s.%s( '%s', '%s' );",$layoutName,$method,$selector,$pane);
        }
    }
    return $pattern ;
}

/**
 * .css
 * @return <type> jquery.layout Directory Configuration files. See app.yml
 */

function get_ui_layout_css_path(){
  $ui_css_dir = sfConfig::get('app_ys_jquery_ui_layout_css_dir', '/ysJQueryUIPlugin/css');
  $path = $ui_css_dir . '/';
  return $path;
}

/**
 * .js
 * @return <type> jquery.layout Directory Configuration files. See app.yml
 */
function get_ui_layout_js_path(){
  $ui_js_dir = sfConfig::get('app_ys_jquery_ui_layout_js_dir', '/ysJQueryUIPlugin/js/jquery/layout');
  $path = $ui_js_dir . '/';
  return $path;
}
/*
 || END UI.LAYOUT
 */