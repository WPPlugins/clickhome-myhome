<?php
/**
 * The notes view
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
 * @var int                      $attLimit
 * @var string                   $attHideNew
 * @var string[]                 $attHideFields
 * @var mixed[]                  $notes
 */

if($attLimit)
  $notes=array_slice($notes,0,$attLimit);

// Remove (empty) hidden fields
foreach($attHideFields as $field)
  $notes=array_map(function (array $note) use ($field){
    $note[$field]='';

    return $note;
  },$notes);

// Prepend "By" to each note author, if this field is not hidden
if(!in_array('author',$attHideFields))
  $notes=array_map(function (array $note){
    $note['author']=sprintf(_x('By %s','Note Author','myHome'),$note['author']);

    return $note;
  },$notes);
?>


<div class="mh-wrapper mh-wrapper-notes">
	<?php if($attHideNew==='false'): ?>
	<div class="mh-section mh-section-notes-new">
		<div class="mh-row mh-row-notes-list-date">New Note</div>
		<div class="mh-row mh-row-notes-list-content">
			<div class="mh-row-notes-new-subject">
				<input id="inputMyHomeNewNoteSubject" maxlength="250"
					   placeholder="<?php _e('Type here to start a new note...','myHome'); ?>" type="text">
			</div>
			<div class="mh-row-notes-new-body">
				<textarea id="textareaMyHomeNewNoteBody" maxlength="10000"></textarea>
			</div>
			<div class="mh-row-notes-new-buttons mh-clearfix" id="divMyHomeNewNoteButtons">
				<div class="mh-loading mh-loading-notes-new-buttons" id="divMyHomeLoadingNotes"></div>
				<button class="mh-button mh-button-new-note-cancel" id="buttonMyHomeNewNoteCancel" type="button">
					<?php _ex('Cancel','New Note','myHome'); ?>
				</button>
				<button class="mh-button mh-button-new-note-ok" id="buttonMyHomeNewNoteOk" type="button">
					<?php _ex('OK', 'New Note','myHome'); ?>
				</button>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="mh-section mh-section-notes-list" id="divMyHomeNotesList">
		<?php
		foreach($notes as $note)
		// Same effect, but more efficient: include __DIR__.'/shortcodeNotes.note.php';
		$this->loadView(['shortcodeNotes','note'],'MyHomeShortcodes',compact('note'));
		?>
	</div>
</div>

<script src="<?php echo MH_URL_SCRIPTS; ?>/shortcodeNotes.js" type="text/javascript"></script>
<script>
  <?php $xhrAttributes = $this->xhrAttributes('notes'); ?>

  jQuery(function ($) {
    _.extend(mh.notes, {
  	  xhr: {
        url: '<?php echo $xhrAttributes['url']; ?>',
        actions: <?php echo json_encode($xhrAttributes['actions']); ?>
      }
    });
    mh.notes.init();
  });
</script>
