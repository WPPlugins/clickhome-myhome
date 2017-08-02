<?php
/**
 * The tasks view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeTasksController))
  die;

/**
 * @var ShortcodeTasksController $this
 * @var string                   $attMode
 * @var mixed[]                  $events
 */
?>
<div class="mh-wrapper mh-wrapper-tasks tasks-layout-<?php echo $attMode; ?>">
  <?php foreach($events as $event): ?>
    <?php //var_dump($event); ?>
    <div class="mh-block mh-block-tasks mh-event-<?php echo strtolower($event['completed']); ?>">
      <?php
        //echo($event['completed']);
        //if ($event['completed']) {
        //  $date = date_create($event['dateactual']);
        //  $date = date_format($date,"d M Y");
        //} else {
          $date = $event['date'];
        //}
      ?>
		  <div class="mh-tasks-event-date"><?php echo esc_html($date); ?></div>

		  <div class="mh-tasks-event-name"><?php echo esc_html($event['name']); ?></div>
		  <div class="mh-tasks-event-status mh-event-<?php echo strtolower($event['completed']); /*=='Completed'?' event-completed':'';*/ ?>"></div>
    </div>
  <?php endforeach; ?>
</div>
