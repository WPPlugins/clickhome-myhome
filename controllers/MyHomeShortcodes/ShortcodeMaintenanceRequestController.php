<?php

/**
 * The ShortcodeMaintenanceRequestController class
 *
 * @package    MyHome
 * @subpackage ControllersShortcodes
 * @since      1.2
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Do not attempt to redefine the class
if(class_exists('ShortcodeMaintenanceRequestController'))
  return;

/**
 * The ShortcodeMaintenanceRequestController class
 *
 * Controller for the Maintenance Request shortcode
 *
 * @since 1.2
 */
class ShortcodeMaintenanceRequestController extends ShortcodeMaintenanceBaseController{
  /**
   * {@inheritDoc}
   */
  protected function doPostMaintenance(array $params){
    list($maintenanceType,$postId)=$this->extractParams(['myHomeMaintenanceType',
      self::$PARAM_POST_ID],$params);

    if(!$maintenanceType)
      throw new MyHomeException('Maintenance type not provided');
    if(!$postId)
      throw new MyHomeException('Post ID not provided');

    // Check if the maintenance type requested is valid
    $maintenanceTypes=$this->retrieveMaintenanceTypes();
    if(!$maintenanceTypes)
      throw new MyHomeException('No maintenance types available');
    if(!isset($maintenanceTypes[$maintenanceType]))
      throw new MyHomeException('Wrong maintenance type: '.$maintenanceType);

    $cachedPostAtts=$this->retrievePostAtts($postId);
    if(!is_array($cachedPostAtts))
      throw new MyHomeException('Post cached attributes not available');


    // Retrieve the cached available maintenance types set at the source page and check if this maintenance type is available
    $maintenanceTypeLowercase=strtolower($maintenanceType); // The shortcodes for maintenance types are case insensitive
    $availableMaintenanceTypes=$this->retrieveAttsAvailableMaintenanceTypes($cachedPostAtts);
    if(isset($availableMaintenanceTypes[$maintenanceTypeLowercase])&&
      !$availableMaintenanceTypes[$maintenanceTypeLowercase]
    ) // If not in the array, it is available
      throw new MyHomeException('Maintenance type is not available at this time: '. $maintenanceType . ' (possibly validate shortcode attributes)');

    $authentication=myHome()->session->getAuthentication();

    $jobParams=['type'=>$maintenanceType,
      'name'=>sprintf('Created by ClickHome.MyHome WordPress Plugin'),
      'description'=>null];

    // Create the new job as requested
    $jobResponse=myHome()->api->post('maintenancejobs',$jobParams,$authentication,false);

    if($jobResponse===null||!isset($jobResponse->id))
      $this->notifyApiError(__('Maintenance job creation failed','myHome'));

    // Return an array containing the parameters to be appended to the redirect URL (eg array("jobId"=>"10"))
    return [self::$PARAM_JOB_ID=>$jobResponse->id];
  }

  /**
   * {@inheritDoc}
   */
  protected function doShortcodeMaintenance(array $atts){
    // Store the attributes in the cache
    $this->cachePostAtts($atts);

    $availableMaintenanceTypes=$this->retrieveAttsAvailableMaintenanceTypes($atts);

    $maintenanceTypes=$this->retrieveMaintenanceTypes();
    if(!$maintenanceTypes)
      throw new MyHomeException('No maintenance types available');

    $redirectUrl=$this->maintenancePagePermalink('issues');
    $redirectUrlError=$this->maintenancePagePermalink('request');

    $paramPostId=static::$PARAM_POST_ID;

    // Values passed to the view:
    // * availableMaintenanceTypes: the array of available maintenance types (eg array("90DayM"=>true,"Manufact"=>false))
    // * maintenanceTypes: the array of all the known maintenance types (eg array("90DayM"=>"90 Day Maintenance"))
    // * redirectUrl: URL to redirect to on success
    // * redirectUrlError: URL to redirect to on error
    // * paramPostId: parameter name for the post ID of the current page
    $this->loadView('shortcodeMaintenanceRequest','MyHomeShortcodes',
      compact('availableMaintenanceTypes','maintenanceTypes','redirectUrl','redirectUrlError','paramPostId'));
  }

  /**
   * Retrieves the available maintenance types from a given attributes array
   *
   * <p>Each attribute is made up of:</p>
   * <ul>
   * <li>Maintenance code (eg "90DayM") - case insensitive</li>
   * <li>Either "disable" to disable the maintenance type unconditionally or two integer numbers separated by a
   * comma:</li>
   * <ul>
   * <li>The first day after the handover date when the maintenance type becomes available</li>
   * <li>The last day after the handover date when the maintenance type becomes available (Optional)</li>
   * </ul>
   * </ul>
   * <p>Examples:</p>
   * <ul>
   * <li>"90DayM=30,115" enables the 90 Day Maintenance between 30 and 115 days after the handover date</li>
   * <li>"Manufact=150," enables the Manufact Maintenance 150 days after the handover date</li>
   * <li>"MaintReq=disable" disables the MaintReq Maintenance</li>
   * </ul>
   * <p>Maintenance types not included in the shortcode are always available</p>
   *
   * @param string[] $atts shortcode attributes
   * @return bool[] the available maintenance types, indexed by maintenance type code (eg
   *                       array("90DayM"=>true,"Manufact"=>false))
   * @throws MyHomeException if handover date is not available or is wrong
   */
  private function retrieveAttsAvailableMaintenanceTypes(array $atts){
    // Get the handover date from the cached job details
    $jobDetails=myHome()->session->getJobDetails();
    //var_dump($jobDetails);
    if(!isset($jobDetails->handoverdate))
      throw new MyHomeException('Handover date not available');

    try{
      $handoverDate=myHome()->wpDateTime($jobDetails->handoverdate);
    }
    catch(Exception $e){
      throw new MyHomeException('Wrong handover date: '.$jobDetails->handoverdate);
    }

    $now=myHome()->wpDateTime();

    // Don't take time into account
    $handoverDate->setTime(0,0);
    $now->setTime(0,0);

    $difference=$handoverDate->diff($now); // diff() returns a positive number if handover was in the past
    $daysPast=$difference->days;
    if($difference->invert)
      $daysPast*=-1;

    $availableMaintenanceTypes=[];

    // Check every shortcode attribute; each $attribute represents a maintenance type
    foreach($atts as $attribute=>$value){
      //var_dump($attribute);
      // Check if the maintenance type is disabled
      if(strtolower($value)==='disable')
        $availableMaintenanceTypes[$attribute]=false;
      // Check if the maintenance type is conditionally enabled
      else if(preg_match('|(\d+),(\d*)|',$value,$limits)){ // Note that the second number is optional
        $minDays=(int)$limits[1];

        if($limits[2]!=='')
          $maxDays=(int)$limits[2];
        else
          $maxDays=null;

        // If no maximum days are set, check for the minimum days past
        if($maxDays===null)
          $availableMaintenanceTypes[$attribute]=$daysPast>=$minDays;
        // Otherwise, check for both
        else if($minDays<=$maxDays)
          $availableMaintenanceTypes[$attribute]=$daysPast>=$minDays&&$daysPast<=$maxDays;
        else
          myHome()->handleError(sprintf('Wrong "%s" attribute: %u>%u',$attribute,$minDays,$maxDays));
      }
      else
        myHome()->handleError(sprintf('Wrong "%s" attribute: %s',$attribute,$value));
    }

    //var_dump($availableMaintenanceTypes);
    return $availableMaintenanceTypes;
  }
}
