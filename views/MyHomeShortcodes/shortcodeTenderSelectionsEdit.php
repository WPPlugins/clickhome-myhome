<?php
/**
 * The TenderSelectionsEdit view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 * @since      1.5
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeTenderSelectionsEditController))
  die;

/**
 * @var ShortcodeTenderSelectionsEditController  $this
 * @var mixed[]                                 $category
 */

global $tender;
global $mh_site_url;

$optionSectionsTitles=
  ['current'=>__('Current Selections','myHome'),
    'alternatives'=>__('Alternatives','myHome'),
    'upgrades'=>__('Upgrades','myHome')];
?>

<form class="mh-wrapper mh-wrapper-tender-selection-edit">
  <h2 class="entry-title">
    <a href="<?php echo esc_html($mh_site_url); ?>">Home</a> <i class="mh-breadcrumb-next">/</i> 
	  <a href="<?php echo esc_html($tender->urls->overview); ?>"><?php echo esc_html($tender->housetypename); ?></a> <i class="mh-breadcrumb-next">/</i> 
	  <a href="<?php echo esc_html($tender->urls->selections); ?>">Selections</a> <i class="mh-breadcrumb-next">/</i> 
	  <?php echo esc_html($category->categoryName); ?>
  </h2>

  <div class="row">
    <div class="col-xs-12">
      <div class="mh-header padding-bottom-15">
        <?php echo $atts['content']; ?>
      </div>

      <div class="mh-header-category padding-bottom-30">
        <?php echo esc_html($category->description); ?>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-sm-3">
      <!-- <h4>Categories</h4> -->
      <?php if($categories): ?>
        <ul class="mh-product-categories">
          <?php 
            foreach($categories as $cat) {
              $mainBadge = '<small class="' . ($cat->outStandingCount <= 0 ? 'mh-done' : '') . '" style="display:' . ($cat->outStandingCount == 0 ? 'none' : 'block') . ';">';
              $mainBadge .= ' <i class="fa fa-check" style="display:' . ($cat->outStandingCount > 0 ? 'none' : 'block') . ';"></i>';
              $mainBadge .= ' <i class="fa fa-exclamation" style="display:' . ($cat->outStandingCount <= 0 ? 'none' : 'block') . ';"></i>';
              $mainBadge .= '</small>';

              $subBadge = '<small>';
              $subBadge .= ' <i class="fa fa-check"></i>';
              $subBadge .= ' <span></span>';
              $subBadge .= '</small>';
            
              //$outstandingHtml = '<span class="mh-outstanding" style="display: ' . ($cat->outstanding>0?'inline-block':'') . ';"><small>' . $cat->outstanding . '</small> remain</span>';
              //$outstandingHtml .= '<i class="fa fa-check" style="display: ' . ($cat->outstanding<=0?'inline-block':'') . ';"></i>';

              if($category->optionCategoryId == $cat->optionCategoryId) {
	              echo '<li class="mh-active">';
	              echo '  <label>' . $mainBadge . '<span title="' . esc_html($cat->categoryName) . '">' . esc_html($cat->categoryName) . '</span></label>';
	              echo '  <ul>';
                foreach($category->selections as $sel) {
	                echo '    <li>';
	                echo '      <a href="#mh-placeholder-' . $sel->placeholderId . '" data-placeholder-id="' . $sel->placeholderId . '">';
	                echo          $sel->placeholderName . $subBadge;
	                echo '      </a>';
	                echo '    </li>';
                }
	              echo '  </ul>';
	              echo '</li>';
              } else {
	              echo '<li><a href="' . esc_html($cat->editUrl) . '">' . $mainBadge . '<span title="' . esc_html($cat->categoryName) . '">' . esc_html($cat->categoryName) . '</span></a></li>';
	            }
            } 
          ?>
        </ul>
      <?php else: ?>
        No package categories to display.
      <?php endif; ?>
    </div>

    <div class="col-sm-9">
      <?php if($category): ?>
        <?php foreach($category->selections as $selection): //var_dump($selection); ?>
          <a name="mh-placeholder-<?php echo $selection->placeholderId; ?>"></a>
          <div class="mh-card mh-products margin-bottom-15" data-placeholder-id="<?php echo $selection->placeholderId ?>">
            <div class="mh-products-header">
              <?php if(!$tender->isSelectionsClientEditable): ?>
              <div class="mh-alert mh-tender-status margin-bottom-15">
                  <i class="fa fa-lock"></i>
                  <h4>Selections are currently locked.</h4><br/>
                  Editing of selections is disabled.
              </div>
              <?php endif; ?>

              <h3 class="mh-row"><?php echo esc_html($selection->placeholderName); ?></h3>
              <?php if($selection->phDescription) echo '<p>' . esc_html($selection->phDescription) . '</p>'; ?>
              
              <div class="mh-alert mh-alert-success mh-succesfully-changed">
                <div>
                  Selections successfully changed! <a href="javascript:window.location.reload(true);">Refresh to update order</a>.
                </div>
              </div>
            </div>

            <?php
              $optionSections = (object) [
                'current' => [],
                'alternatives' => [],
                'upgrades' => []
              ];
              
              foreach($selection->substitutionOptions as $option) {
                if($option->selectCount > 0)
                  $optionSections->current[]=$option;
                else if(!$option->upgradePrice)
                  $optionSections->alternatives[]=$option;
                else
                  $optionSections->upgrades[]=$option;
              }
            ?>

            <?php foreach($optionSections as $section=>$options): ?>
              <?php if(!count($options)) continue; ?>
              <div class="mh-<?php echo $section; ?>">
                <div class="mh-products-header">
                  <h5><?php echo $optionSectionsTitles[$section]; ?></h5>
                </div>

                <!-- Selections -->
                <div class="mh-products-body">
                  <div class="mh-products-grid row">
                    <?php foreach($options as $option): ?>
                      <div class="col-sm-6 col-lg-4 mh-product-wrapper">
                        <input type="checkbox" class="mh-checkbox" onchange="mh.tenders.selectionsEdit.sync()" <?php echo($section=='current'?'checked ':' '); echo(!$tender->isSelectionsClientEditable?'disabled ':' '); ?> data-option-id="<?php echo (int)$option->optionId?>" data-placeholder-id="<?php echo $selection->placeholderId; ?>" id="productCheckbox[<?php echo $selection->placeholderId; ?>][<?php echo $option->optionId; ?>]" />
                        <div class="mh-product">
                          <div class="mh-top" onclick="mh.tenders.selectionsEdit.detailsModal.open(<?php echo($selection->placeholderId . ',' . $option->optionId); ?>);">
                            <!-- Type -->
                            <?php if(!$option->upgrade): ?>
                              <div class="mh-option-type mh-isalternate"><span>Alternate</span></div>
                            <?php else: ?>
                              <div class="mh-option-type mh-isupgrade"><span>Upgrade</span></div>
                            <?php endif; ?>

                            <!-- Tools -->
                            <div class="mh-icons">
                              <div class="mh-icon">
                                <a class="mh-icon-note <?php echo($option->clientComment?'mh-has-note':''); ?>"><i class="fa fa-sticky-note" onclick="mh.tenders.selectionsEdit.toggleNote(<?php echo $option->optionId; ?>);"></i><span class="tip">Add / Edit Note</span></a>
                              </div>
                            </div>

                            <!-- Thumbnail -->
                            <div class="mh-img">
                              <?php if($option->thumbnail): ?><div class="mh-photos"><i class="fa fa-photo"></i><?php echo count($option->documents); ?></div><?php endif; ?>
                              <img src="<?php echo $this->photoDownloadUrl($option->thumbnail, false, null); ?>" class="<?php if(!$option->thumbnail) echo 'contain'; ?>">
                            </div>
          
                            <!-- Name & Description-->
                            <a class="mh-name"><?php echo esc_html($option->optionName); ?></a>
                            <div class="mh-description">
                              <?php echo esc_html($option->optionDescription); ?>
                            </div>
                          </div>

                          <div class="mh-bottom">
                            <!-- Price -->
                            <input type="hidden" data-upgrade-price="<?php echo ($option->upgradePrice); ?>">
                            <?php if($atts['showitemprices'] && $option->upgradePrice): ?>
                              <div class="mh-price">
                                <?php //printf('x' . $selection->qty . '@'); ?>
                                <?php printf(__('<strong>$%.02f</strong>ea','myHome'),$option->upgradePrice); ?>
                              </div>
                            <?php endif; ?>

                            <div class="mh-flex-row">
                              <!-- Quantity -->
                              <?php if($option->quantityReqd && $atts['showitemquantities']): ?>
                                <div class="mh-quantity">
                                    <!-- <small>qty</small> -->
                                    <!-- <input type="number" class="mh-quantity-input" data-parent="#<?php //echo $divId; ?>" max="100" min="0" name="myHomeQuantity[<?php //echo (int)$selection['id']; ?>][<?php //echo (int)$option['id']; ?>]" step="1" value="<?php //echo (int)$option['count']; ?>" <?php //echo(!$tender->isSelectionsClientEditable?'disabled ':' '); ?> /> -->
                                    <input type="number" class="mh-quantity-input" onchange="mh.tenders.selectionsEdit.sync();" max="100" min="0" step="1" value="<?php echo (int)$option->selectCount; ?>" <?php echo(!$tender->isSelectionsClientEditable?'disabled ':' '); ?> name="myHomeQuantity[<?php echo $selection->placeholderId; ?>][<?php echo $option->optionId; ?>]" />
                                    <a class="decrement" onclick="mh.tenders.selectionsEdit.adjustQuantityBy(-1);">-</a>
                                    <a class="increment" onclick="mh.tenders.selectionsEdit.adjustQuantityBy(1);">+</a>
                                </div>
                              <?php else: ?>
                                  <input type="hidden" class="mh-quantity-input" value="<?php echo $section==='current'?1:0; ?>" <?php echo(!$tender->isSelectionsClientEditable?'disabled ':' '); ?> name="myHomeQuantity[<?php echo $selection->placeholderId; ?>][<?php echo $option->optionId; ?>]" />
                              <?php endif; ?>

                              <!-- Select -->
                              <div class="mh-select">
                                <label class="mh-button-wrapper mh-button-block" for="productCheckbox[<?php echo $selection->placeholderId; ?>][<?php echo $option->optionId; ?>]">
                                  <a class="mh-button">Select<span>ed</span><i></i></a>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Note Textarea -->
                        <div class="mh-note">
                          <h6>Note</h6>
                          <i class="fa fa-times" onclick="mh.tenders.selectionsEdit.toggleNote(<?php echo $option->optionId; ?>);"></i>
                          <textarea placeholder="Keep a note..." maxlength="250" name="myHomeComment[<?php echo $selection->placeholderId; ?>][<?php echo $option->optionId; ?>]" <?php  echo !$tender->isSelectionsClientEditable?'disabled ':''; ?>><?php echo esc_attr($option->clientComment); ?></textarea>
                          <a class="mh-button mh-button-md" onclick="mh.tenders.selectionsEdit.sync()" <?php  echo !$tender->isSelectionsClientEditable?'disabled ':''; ?>>Save</a>
                        </div>

                        <!-- Ajax Loading -->
                        <i class="mh-loading"></i>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>

            <?php if($atts['showrunningquantities'] || $atts['showrunningprices']): ?>
              <div class="mh-sticky-footer" id="mh-sticky-<?php echo (int)$selection->placeholderId?>">
                <?php //if($atts['showrunningprices']): ?>
                  <!-- <span class="mh-row mh-hide"><?php _e('Total: $','myHome'); ?><span class="mh-total-price"></span></span> -->
                <?php //endif; ?>

                <div class="pull-right">
                  <?php if($atts['showrunningquantities']): ?>
                    <span class="mh-row mh-hide">
                      <span class="mh-selections-remain">
                        <span class="mh-total-quantity-selected"></span> / <?php echo $selection->totalCount ?> options selected, 
                      </span>
                      <span class="mh-total-quantity-remain"></span> remain</span>
                    </span>
          
                    <span class="mh-row mh-hide">
                      <span class="mh-selections-complete">Selection Complete</span>
                      <span class="mh-quantity-extra"></span>
                    </span>
                  <?php endif; ?>

                  <a class="mh-button mh-button-md" href="<?php echo esc_html($tender->urls->overview); ?>"><?php _e('Done','myHome'); ?></a>
                </div>
            </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  <a class="mh-button mh-button-sub pull-right" href="<?php echo esc_html($tender->urls->selections); ?>"><?php _e('Back to Selections','myHome'); ?></a>
</form>

<!-- Modals -->
<div style="display:none;">
  <div id="mh-selection-details" class="mh-products">
    <div class="mh-products-grid">
      <h2 data-title>&nbsp;</h2>
      <div class="mh-product-wrapper">
        <input type="checkbox" id="modalCheckbox" class="mh-checkbox" data-checkbox onchange="mh.tenders.selectionsEdit.sync()" data-option-id="" data-placeholder-id="" />
        <div class="mh-product">
          <div class="mh-slideshow" data-slideshow-images></div>

          <div id="cboxFooter" class="row">
            <div class="col-md-7">
              <h6>Description</h6>
              <p data-description>&nbsp;</p>
            </div>
            <div class="col-md-5">
              <div class="mh-bottom">
                <div class="mh-price">
                  <h6>Price</h6>
                  <p data-price>&nbsp;</p>
                </div>

                <div class="mh-flex-row">
                  <!-- Quantity -->
                  <?php //if($option['quantityRequired'] && $atts['showitemquantities']): ?>
                    <div class="mh-quantity">
                        <small>qty</small>
                        <input type="number" class="mh-quantity-input" data-quantity onchange="mh.tenders.selectionsEdit.sync();" max="100" min="0" step="1" />
                    </div>
                  <?php //else: ?>
                      <!-- <input type="hidden" class="mh-quantity-input" name="myHomeQuantity[<?php //echo (int)$option['id']; ?>]" value="<?php //echo $section==='current'?1:0; ?>" <?php //echo(!$tender->isSelectionsClientEditable?'disabled ':' '); ?> /> -->
                  <?php //endif; ?>

                  <!-- Select -->
                  <div class="mh-select">
                    <label class="mh-button-wrapper mh-button-block" for="modalCheckbox">
                      <a class="mh-button">Select<span>ed</span><i></i></a>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="<?php echo MH_URL_SCRIPTS; ?>/shortcodeTenderSelectionsEdit.js" type="text/javascript"></script>
<script>
  <?php $xhrAttributes = $this->xhrAttributes(['selectionEdit', 'systemDocument']); ?>

  jQuery(function ($) {
    _.extend(mh.tenders.selectionsEdit, {
      xhr: {
        url: '<?php echo $xhrAttributes['url']; ?>',
        actions: <?php echo json_encode($xhrAttributes['actions']); ?>
      },
      
      data: {
        tender: <?php echo json_encode($tender) ?>,
        categories: <?php echo json_encode($categories) ?>, // As XHR func does not yet return
        category: <?php echo json_encode($category) ?>
      },
    });
    mh.tenders.selectionsEdit.init();
  });
</script>