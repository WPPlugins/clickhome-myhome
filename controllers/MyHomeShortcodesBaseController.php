<?php

/**
 * The MyHomeShortcodesBaseController class
 *
 * @package    MyHome
 * @subpackage Controllers
 * @since      1.2
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Do not attempt to redefine the class
if(class_exists('MyHomeShortcodesBaseController'))
  return;

/**
 * The MyHomeShortcodesBaseController class
 *
 * Abstract class for shortcode view controllers
 */
abstract class MyHomeShortcodesBaseController extends MyHomeBaseController{
  /**
   * Returns the download URL (with Content-Type: inline) for a given document ID
   *
   * This method is called from several shortcodes (House Details, Photos, and Maintenance Issues) to insert images
   * downloaded from the API
   *
   * @see  ShortcodeDocumentsController::doGet()
   * @uses MyHomeShortcodesBaseController::$formAttributes to generate the appropriate GET URL for the document action
   * @param int  $documentId the document ID
   * @param bool $thumb      whether the document should be retrieved using the thumbs API call
   * @param bool $cache      whether the document cache should be used
   * @return string the download URL
   */
  protected function photoDownloadUrl($data, $thumb, $cache=false, $authType='system'){
    if(myHome()->helpers->is_base64($data)) {
      return 'data:image/jpeg;base64,' . $data;
    } else if(is_numeric($data)) {
      //if($this->photoFormAttributes===null) 
        //$this->photoFormAttributes=myHome()->adminPostHandler->formAttributes('document', 'GET', null, null, $authType) ;//$authType == 'client' ? 'clientDocument' : 'systemDocument','GET');
        $this->photoFormAttributes=myHome()->adminPostHandler->formAttributes($authType == 'client' ? 'clientDocument' : 'systemDocument', 'GET');
      //$this->photoFormAttributes['params']['myHomeAuth'] = $authType;
      $this->photoFormAttributes['params']['myHomeDocumentId'] = $data;
      $this->photoFormAttributes['params']['myHomeInline'] = (int)true; // add_query_arg() ignores parameters with a boolean false value
      $this->photoFormAttributes['params']['myHomeThumb'] = (int)$thumb;
      $this->photoFormAttributes['params']['myHomeCache'] = (int)$cache;
      
      //myHome()->log->info(serialize($this->photoFormAttributes['params']));
      //var_dump(add_query_arg($this->photoFormAttributes['params'],$this->photoFormAttributes['url']));
      return add_query_arg($this->photoFormAttributes['params'],$this->photoFormAttributes['url']);
    } else {
      return MH_URL_IMAGES . '/noPhoto.gif';
    }
  }

  /**
   * Returns a GET parameter
   *
   * @since 1.5
   * @param string $param the parameter name
   * @return string|null the parameter value or null if not found
   */
  protected function getParam($param){
    if(isset($_GET[$param]))
      return $_GET[$param];
    else
      return null;

    // Not using query_vars() - see MyHome::setupHooks()
    /*
    global $wp_query;

    // $param must be registered by MyHome::onQueryVars()
    if(isset($wp_query->query_vars[$param]))
      return $wp_query->query_vars[$param];
    else
      return null;
    */
  }

  /**
   * Settings for the document action - used by photoDownloadUrl()
   *
   * @var mixed[]
   */
  private $photoFormAttributes=null;
}
