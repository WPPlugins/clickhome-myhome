<?php
/**
 * The tenderPackages view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 * @since      1.6
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeTenderPackagesController))
  die;

/**
 * @var ShortcodeTenderPackagesController   $this
 * @var string[][]                         $selectedPackage
 * @var string[][]                         $packages
 */

global $mh_site_url;
global $tender;
?>

<div class="mh-wrapper mh-wrapper-tender-package">
  <h2 class="entry-title">
    <a href="<?php echo esc_html($mh_site_url); ?>">Home</a> <i class="mh-breadcrumb-next">/</i> 
    <a href="<?php echo esc_html($tender->urls->overview); ?>"><?php echo esc_html($tender->housetypename); ?></a> <i class="mh-breadcrumb-next">/</i> 
    Packages
  </h2>
  
  <div class="row">
    <div class="col-sm-3">
      <!-- <h4>Package Types</h4> -->
      <?php if($categories): ?>
        <ul class="mh-product-categories">
          <?php 
            foreach($categories as $cat) {
              $mainBadge = '<small class="mh-danger" style="display:' . (count($cat->packages) > 0 ? 'inline-block' : 'none') . ';">';
              $mainBadge .= ' <span>' . (string)count($cat->packages) . '</span>';
              //$mainBadge .= ' <i class="fa fa-check" style="display:' . (count($cat->packages) > 0 ? 'none' : 'block') . ';"></i>';
              //$mainBadge .= ' <i class="fa fa-exclamation" style="display:' . (count($cat->packages) <= 0 ? 'none' : 'block') . ';"></i>';
              $mainBadge .= '</small>';

              if($category->id == $cat->id)
	              echo '<li class="mh-active"><label>' . $mainBadge . '<span title="' . esc_html($cat->name) . '">' . esc_html($cat->name) . '</span></label></li>';
              else
	              echo '<li><a href="' . esc_html($cat->editUrl) . '">' . $mainBadge . '<span title="' . esc_html($cat->name) . '">' . esc_html($cat->name) . '<span></a></li>';
            } 
          ?>
        </ul>
      <?php else: ?>
        No package categories to display.
      <?php endif; ?>
    </div>

    <div class="col-sm-9">
      <?php if($category): ?>
        <div class="mh-card mh-products margin-bottom-15">

          <div class="mh-products-header">
            <?php if(!$tender->isPackagesClientEditable): ?>
            <div class="mh-alert mh-tender-status margin-bottom-15">
                <i class="fa fa-lock"></i>
                <h4>Packages are currently locked.</h4><br/>
                Editing of packages is disabled.
            </div>
            <?php endif; ?>

            <h3 class="mh-row"><?php echo esc_html($category->name); ?></h3>
            <?php if(isset($category->description)) echo '<p>' . esc_html($category->description) . '</p>'; //var_dump($category);  ?>
          
            <!-- <div class="mh-alert">
              <div>
                Selections successfully changed! <a href="javascript:window.location.reload(true);">Refresh to update order</a>.
              </div>
            </div>

            <h5><?php //echo $optionSectionsTitles[$section]; ?></h5> -->
          </div>

          <!-- Packages -->
          <div class="mh-products-body">
            <?php //echo($category->viewTypeName); ?>
            <?php switch($category->viewTypeCode): 
              case 'I':
              case 'D': ?>
                <div class="mh-products-grid row">
                  <?php foreach($category->packages as $package): //var_dump($package); ?>
                    <div class="col-sm-6 col-lg-4 mh-product-wrapper">
                      <input type="checkbox" class="mh-checkbox" onchange="mh.tenders.packages.sync()" <?php echo($package->selected?'checked ':' '); echo(!$tender->isPackagesClientEditable?'disabled ':' '); ?> data-package-id="<?php echo $package->id ?>" id="packageCheckbox[<?php echo $package->id; ?>]" />
                      <div class="mh-product">
                        <div class="mh-top" onclick="mh.tenders.packages.detailsModal.open(<?php echo($category->id . ',' . $package->id); ?>);">
                          <!-- Thumbnail -->
                          <?php if($category->viewTypeCode == 'I'): ?>
                            <div class="mh-img">
                              <?php if($package->thumbnail): ?><div class="mh-photos"><i class="fa fa-photo"></i><?php echo count($package->imageIds); ?></div><?php endif; ?>
                              <img src="<?php echo $this->photoDownloadUrl($package->thumbnail, true, null); ?>" class="<?php if(!$package->thumbnail) echo 'contain'; ?>">
                            </div>
                          <?php endif; ?>
          
                          <!-- Name & Description-->
                          <a class="mh-name"><?php echo esc_html($package->name); ?></a>
                          <div class="mh-description">
                            <?php echo esc_html($package->description); ?>
                          </div>
                        </div>

                        <div class="mh-bottom">
                          <!-- Price -->
                          <?php if($atts['showitemprices'] && $package->sellPrice): ?>
                            <div class="mh-price" data-upgrade-price="<?php echo $package->sellPrice; ?>">
                              <?php //printf('x' . $selection->qty . '@'); ?>
                              <?php echo $package->sellPrice; ?>
                            </div>
                          <?php endif; ?>

                          <div class="mh-flex-row">
                            <!-- Select -->
                            <div class="mh-select">
                              <label class="mh-button-wrapper mh-button-block" for="packageCheckbox[<?php echo $package->id; ?>]">
                                <a class="mh-button">Select<span>ed</span><i></i></a>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Note Textarea
                      <div class="mh-note">
                        <h6>Note</h6>
                        <textarea placeholder="Keep a note..." maxlength="250" name="myHomeComment[<?php //echo $selection['id']; ?>][<?php //echo $package['Id']; ?>]"><?php //echo esc_attr($option['comment']); ?></textarea>
                        <a class="mh-button mh-button-md" onclick="mh.tenders.packages.sync()">Save</a>
                      </div> -->

                      <!-- Ajax Loading -->
                      <i class="mh-loading"></i>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php break; ?>
              <?php case 'L'; ?>
                <ul class="mh-products-list">
                <?php foreach($category->packages as $package): //var_dump($package); ?>
                    <li class="row mh-product-wrapper">
                      <input type="hidden" data-id value="<?php echo $package->id ?>" />
                      <label class="mh-product">
                        <span class="col-xs-4">
                          <input type="checkbox" class="mh-checkbox" onchange="mh.tenders.packages.sync()" <?php echo($package->selected?'checked ':' '); ?> id="packageCheckbox[<?php echo $package->id; ?>]" />
                          <a class="mh-name" href="javascript:mh.tenders.packages.detailsModal.open(<?php echo($category->id . ',' . $package->id); ?>);"><?php echo esc_html($package->name); ?></a>
                        </span>
                        <span class="col-xs-6">
                          <?php echo esc_html($package->description); ?>
                        </span>
                        <span class="col-xs-2">
                          <?php if($package->sellPrice) echo myHome()->helpers->formatDollars(esc_html($package->sellPrice)); ?>
                        </span>
                      </label>
                    </li>
                <?php endforeach; ?>
                </ul>
              <?php break; ?>
              <?php default: ?>
                  <?php echo($category->viewTypeName); ?>
              <?php break; ?>
            <?php endswitch; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modals -->
<div style='display:none'>
  <div id="mh-package-details" class="mh-products">
    <h2 data-title>&nbsp;</h2>
    <div class="mh-products-grid">
      <div class="mh-product-wrapper">
        <input type="checkbox" id="modalCheckbox" class="mh-checkbox" data-checkbox onchange="mh.tenders.packages.sync()" data-package-id="" />
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
                  <!-- Quantity
                  <?php //if($option['quantityRequired'] && $attShowItemQuantities): ?>
                    <div class="mh-quantity">
                        <small>qty</small>
                        <input type="number" class="mh-quantity-input" data-quantity onchange="mh.tenders.packages.sync();" max="100" min="0" step="1" />
                    </div> -->
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

<script src="<?php echo MH_URL_SCRIPTS; ?>/shortcodeTenderPackages.js" type="text/javascript"></script>
<script type="text/javascript">
  <?php $xhrAttributes = $this->xhrAttributes(['packageEdit', 'systemDocument']); ?>

  jQuery(function ($) {
    _.extend(mh.tenders.packages, {
      xhr: {
        url: '<?php echo $xhrAttributes['url']; ?>',
        actions: <?php echo json_encode($xhrAttributes['actions']); ?>
      },
      
      vars: {
        tenderId: <?php echo $tender->tenderid; ?>,
        categoryId: <?php echo isset($category) ? $category->id : 'null'; ?>
      },
      
      data: {
        tender: <?php echo json_encode($tender) ?>, 
        categories: <?php echo json_encode($categories) ?>, // As XHR func does not yet return
        category: <?php echo json_encode($category) ?>
      }
    });
    mh.tenders.packages.init();
  });
</script>