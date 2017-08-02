<?php
/**
 * The contract view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeContractController))
  die;

/**
 * @var ShortcodeContractController $this
 * @var string                      $attMode
 * @var string[]                    $attHideFields
 * @var stdClass                    $jobDetails
 * @var string[]                    $fieldNames
 */

// Filter any field whose name is unknown
foreach($jobDetails as $field=>$value)
  if(!isset($fieldNames[$field]))
    unset($jobDetails->$field);

// Remove hidden fields
foreach($attHideFields as $field)
  if(property_exists($jobDetails,$field)) // Some values may be null - isset() should not be used here
    unset($jobDetails->$field);

// Keep only some fields if simple mode is selected
if($attMode==='simple'){
  $allowedFields=['job',
    'clienttitle',
    'lotaddress',
    'housetype',
    'facade'];

  foreach($jobDetails as $field=>$value)
    if(!in_array($field,$allowedFields))
      unset($jobDetails->$field);
}
?>
<div class="mh-wrapper mh-wrapper-contract">
  <?php foreach($jobDetails as $field=>$value): ?>
    <div class="mh-row mh-row-contract">
      <div class="mh-cell mh-cell-contract-name"><?php echo esc_html($fieldNames[$field]); ?></div>
      <div class="mh-cell mh-cell-contract-value"><?php echo esc_html($value); ?></div>
    </div>
  <?php endforeach; ?>
</div>
