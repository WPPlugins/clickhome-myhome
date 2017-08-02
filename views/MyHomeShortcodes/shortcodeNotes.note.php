<?php
/**
 * The notes.note subview
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeNotesController))
  die;

/**
 * @var ShortcodeNotesController $this
 * @var string[]                 $note
 */
?>
<div class="mh-block mh-block-notes-list">
	<!-- <div class="mh-row mh-row-notes-list-date"><?php echo esc_html($note['date']); ?></div> -->
	<div class="mh-row mh-row-notes-list-author"><?php echo esc_html($note['author']); ?></div>
	<div class="mh-row mh-row-notes-list-content">
		<div class="mh-row-notes-list-subject"><?php echo esc_html($note['subject']); ?></div>
		<!-- <div class="mh-row-notes-list-author"><?php echo esc_html($note['author']); ?></div> -->
    <div class="mh-row-notes-list-date"><?php echo esc_html($note['date']); ?></div>
		<div class="mh-row-notes-list-body"><?php echo nl2br(esc_html($note['body'])); ?></div>
	</div>
</div>
