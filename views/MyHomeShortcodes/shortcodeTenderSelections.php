<?php
/**
 * The tenderSelections view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 * @since      1.5
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeTenderSelectionsController))
  die;

/**
 * @var ShortcodeTenderSelectionsController $this
 * @var string[][]                         $atts
 * @var string[][]                         $selections
 */

global $mh_site_url;
global $tender;
?>

<div class="mh-wrapper mh-wrapper-tender-selection">
  <h2 class="entry-title">
    <a href="<?php echo esc_html($mh_site_url); ?>">Home</a> <i class="mh-breadcrumb-next">/</i> 
    <a href="<?php echo esc_html($tender->urls->overview); ?>"><?php echo esc_html($tender->housetypename); ?></a> <i class="mh-breadcrumb-next">/</i> 
    Selections
  </h2>

  <?php //var_dump($tender); ?>
  <!-- <div class="row mh-sub-title">
    <div class="col-sm-6">
      <?php if( $tender->isSelectionsClientEditable ): ?>
      <?php endif; ?>
    </div>
    <div class="col-sm-6 text-right">
      <?php if( $tender->isSelectionsClientEditable ): ?>
        <span>Selection period expires <?php //echo esc_attr($tender->selectionExpiryDate); ?></span>
      <?php else: ?>
        <span>Selections are currently closed</span>
      <?php endif; ?>
    </div>
  </div> -->

  <div class="row">
    <div class="col-xs-12">
      <div class="padding-15">
        <?php echo $atts['content']; ?>
      </div>
    </div>
  </div>

  <?php if(!count($categories)): ?>
    <div class="mh-no-results">There are no selections to display.</div>
  <?php endif; ?>

  <div class="row mh-flex-row mh-flex-wrap">
    <?php foreach($categories as $selection): ?>
	    <?php //var_dump($selection); ?>
      <div class="col-xs-12 col-md-6 mh-flex-1 padding-30">
        <div class="mh-block mh-tndr-sln-cat mh-flex-column">
	        <div class="mh-tndr-sln-cat-dtls mh-flex-1">
		        <div class="mh-section mh-section-tender-selection-selection-image">
			      <?php if($selection->categoryImage): ?>
			        <img class="mh-image mh-image-tender-selection-selection-thumbnail" src="<?php echo $this->photoDownloadUrl($selection->categoryImage, true, null); ?>">
			      <?php endif; ?>
		        </div>
		        <div class="mh-section mh-section-tender-selection-selection-text mh-clearfix">
			      <div class="mh-row mh-row-tender-selection-selection-name"><a href="<?php echo esc_attr($selection->editUrl); ?>"><?php echo esc_html($selection->categoryName); ?></a></div>
			      <div class="mh-row mh-row-tender-selection-selection-description"><?php echo esc_html($selection->description); ?></div>
		  
				      <?php if($selection->currentSelections): ?>
			        <div class="mh-tndr-sln-edt-smmry">
				        <h5>Current Selections:</h5>

				        <ul>
				          <?php foreach($selection->currentSelections as $item): ?>
					        <li>
					          <?php 
                      if($atts['showitemquantities']) echo('x' . $item->selectCount . ' - '); 
                      echo($item->optionName); 
                    ?>
					        </li>
				          <?php endforeach; ?>
				        </ul>
			        </div>
			      <?php endif; ?>
		        </div>
	        </div>

	        <div class="mh-tndr-sln-edt-btm">
		        <!--<?php if($atts['showrunningquantities']): ?>
              <?php if($selection->outStandingCount > 0): ?>
		            <span class="mh-row mh-tndr-sln-edt-otstndng mh-rmn">
		              <span class="mh-total"><?php echo count($selection->currentSelections) ?></span> / 
		              <?php echo $selection->totalSelections ?> selected, <span class="mh-rmn-count"><small><?php echo $selection->outStandingCount ?></small> remain</span>
		            </span>
              <?php else: ?>
                <span class="mh-row mh-tndr-sln-edt-otstndng mh-cmpltd"><?php _e('All Selections Completed','myHome'); ?></span>
              <?php endif; ?>
            <?php endif; ?>-->

            <?php if($selection->editUrl): ?>
              <span class="mh-button-wrapper">
                <?php if($tender->isSelectionsClientEditable): ?>
                  <a class="mh-button mh-button-tender-overview-edit" href="<?php echo esc_attr($selection->editUrl); ?>"><?php _e('Edit Selections','myHome'); ?></a>
                <?php else: ?>
                  <a class="mh-button mh-button-tender-overview-edit" href="<?php echo esc_attr($selection->editUrl); ?>"><?php _e('View Selections','myHome'); ?></a>
                <?php endif; ?>
              </span>
            <?php endif; ?>
	        </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

	<!--<div class="mh-bottom-nav clearfix">
		<a class="mh-button mh-button-sub pull-right" href="<?php echo esc_html($tender->urls->overview); ?>"><?php _e('Back to Overview','myHome'); ?></a>
	</div>-->
</div>

<!-- Scripts -->
<script src="<?php echo MH_URL_SCRIPTS; ?>/shortcodeTenderSelections.js" type="text/javascript"></script>
<script>
  <?php $xhrAttributes = $this->xhrAttributes(['selectionEdit', 'systemDocument']); ?>

  jQuery(function ($) {
    _.extend(mh.tenders.selections, {
      data: {
        tender: <?php echo json_encode($tender) ?>,
        categories: <?php echo json_encode($categories) ?> // As XHR func does not yet return
      },
    });
    //mh.tenders.selections.init();
  });
</script>
