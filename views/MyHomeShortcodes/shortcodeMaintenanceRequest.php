<?php
/**
 * The maintenanceRequest view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 * @since      1.2
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeMaintenanceRequestController))
  die;

/**
 * @var ShortcodeMaintenanceRequestController $this
 * @var bool[]                                $availableMaintenanceTypes
 * @var string[]                              $maintenanceTypes
 * @var string                                $redirectUrl
 * @var string                                $redirectUrlError
 * @var string                                $paramPostId
 */
$formAttributes=myHome()->adminPostHandler->formAttributes('maintenanceRequest','POST',$redirectUrl,$redirectUrlError);
// Post ID is used by doPostMaintenance() to retrieve the cached attributes for this post, and then, check if the maintenance type is allowed according to said attributes
$formAttributes['params'][$paramPostId]=get_the_ID();

// Request codes not included in $availableMaintenanceTypes are considered available
?>
<form action="<?php $this->appendFormUrl($formAttributes); ?>" class="mh-wrapper mh-wrapper-maintenance-request" method="POST">
  <?php
  $this->appendFormParams($formAttributes,2);
  ?>
  <?php foreach($maintenanceTypes as $code=>$name): ?>
    <?php
		  $codeLowercase=strtolower($code);
		  //echo $availableMaintenanceTypes[$codeLowercase];
		  //echo $code . ' : ' . $name;
    ?>
    <?php if(isset($availableMaintenanceTypes[$codeLowercase])) ://|| $availableMaintenanceTypes[$codeLowercase]): ?>
      <div class="mh-block mh-block-maintenance-request">
        <button class="mh-button mh-button-maintenance-request" name="myHomeMaintenanceType" type="submit" value="<?php echo esc_attr($code); ?>"><?php echo esc_html($code); ?></button> &nbsp; 
		<?php echo($name); ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</form>
