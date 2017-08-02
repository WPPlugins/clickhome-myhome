<?php
/**
 * The tenderVariations view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 * @since      1.6
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeTenderVariationsController))
  die;

/**
 * @var ShortcodeTenderVariationsController   $this
 * @var string[][]                         $tender
 * @var string[][]                         $selectedVariation
 * @var string[][]                         $packages
 */

global $mh_site_url;
global $tender;
//print_r($tender);
//print_r($tendersUrl);
?>


<div class="mh-wrapper mh-wrapper-tender-variations">
  <h2 class="entry-title">
    <a href="<?php echo esc_html($mh_site_url); ?>">Home</a> <i class="mh-breadcrumb-next">/</i> 
    <a href="<?php echo esc_html($tender->urls->overview); ?>"><?php echo esc_html($tender->housetypename); ?></a> <i class="mh-breadcrumb-next">/</i> 
    Variations
  </h2>

  <div class="row">
    <div class="col-xs-12">
      <div class="padding-15">
        <?php echo $atts['content']; ?>
      </div>
    </div>
  </div>
  
  <div class="row">
    <!-- <h4>Variations</h4> -->
    <div class="col-sm-12 mh-tender-variations">
      <?php if(!count($variations)): ?>
        <div class="mh-no-results">There are no selections to display.</div>
      <?php endif; ?>

      <?php if(isset($variations)): ?>
        <?php foreach($variations as $variation): ?>
          <?php if(count($variation->selections) == 0) continue; ?>
          <?php //var_dump($variation); ?>
          <?php //echo esc_html($variation['name']); ?>
          <div class="mh-card mh-variation padding-bottom-0 margin-bottom-30 clearfix" data-variation-id="<?php echo($variation->tenderVariationId); ?>">
            <h3><?php echo esc_html($variation->variationName); ?></h3>
            <!-- <p class="mh-description"><?php //echo esc_html($variation->Description); ?></p> -->
          
            <!-- Selections -->
            <div class="mh-products margin-right-0">
              <div class="row mh-products-body">
                <?php if(isset($variation->selections)) : ?>
                  <ul class="mh-products-list margin-15">
                      <li class="row mh-product-wrapper mh-product-head">
                        <label class="mh-product">
                          <span class="col-xs-8">Name</span>
                          <span class="col-xs-1"></span>
                          <span class="col-xs-1 text-center">Qty</span>
                          <span class="col-xs-2 text-right">Amount</span>
                        </label>
                      </li>
                    <?php foreach($variation->selections as $selection): //var_dump($selection); ?>
                      <li class="row mh-product-wrapper">
                        <label class="mh-product">
                          <span class="col-xs-8">
                            <!-- <input type="checkbox" class="mh-checkbox" onchange="mh.tenders.packages.sync()" <?php //echo($package->selected?'checked ':' '); ?> id="packageCheckbox[<?php //echo $package->id; ?>]" /> -->
                            <span class="mh-name"><?php echo esc_html($selection->name); ?></span>
                          </span>
                          <span class="col-xs-1">
                            <?php //echo esc_html($selection->description); ?>
                          </span>
                          <span class="col-xs-1 text-center">
                            <?php echo ($selection->qty > 0 ? '+' : '') . $selection->qty; ?>
                          </span>
                          <span class="col-xs-2 text-right">
                            <?php if($selection->sellPrice) echo myHome()->helpers->formatDollars(esc_html($selection->sellPrice)); ?>
                          </span>
                        </label>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <!-- <div class="mh-products-grid">
                    <?php //foreach($variation->selections as $selection): ?>
                      <div class="col-xs-12 col-sm-6 col-md-4 mh-product-wrapper">
                        <div class="mh-product">
                          <div class="mh-top">
                            <div class="mh-img">
                              <div class="mh-photos"><i class="fa fa-photo"></i>3</div>
                            </div>
                            <span class="mh-name"><?php //echo esc_html($selection->name); ?></span>
                          </div>
                          <div class="mh-bottom">
                            <div class="mh-price padding-bottom-15">
                              <?php //printf('x' . $selection->qty . '@'); ?>
                              <?php //printf(__('<strong>$%.02f</strong>ea','myHome'), $selection->sellPrice); ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php //endforeach; ?>
                  </div> -->
                <?php endif; ?>
              </div>
          
              <div class="row mh-products-footer">
	              <div class="col-sm-5 padding-left-30 mh-tender-status">
                  <?php
                    switch($variation->variationStatusId) {
                      case 0:
                          echo '<i class="fa fa-play"></i>';
                          break;
                      case 4:
                          echo '<i class="fa fa-check"></i>';
                          break;
                      case 11:
                          echo '<i class="fa fa-times"></i>';
                          break;
                      default:
                          echo '<i class="fa fa-info"></i>';
                          break;
                    }
                  ?>
                  <h4 class="mh-inline-block">
                    <?php
                      switch($variation->variationStatusId) {
                        case 0:
                            echo 'Ready to sign';
                            break;
                        default:
                            echo esc_html($variation->variationStatusName);
                            break;
                      }
                    ?>
                  </h4>
	              </div>
                <div class="col-sm-3 text-right">
                  Total: <span class="mh-price"><strong><?php echo myHome()->helpers->formatDollars($variation->sellPrice); ?></strong></span>
                </div>
                <div class="col-sm-4">
                  <div class="mh-button-wrapper text-center">
                    <?php //if($variation->variationStatusId == 0) : ?>
                      <!-- Drafting -->
                    <?php if($variation->variationStatusId == 0) : ?>
                      <a class="mh-button" href="javascript:mh.tenders.variations.addSigModal.open(<?php echo($variation->variationId); ?>);">Approve</a>
                    <?php elseif($variation->variationStatusId >= 2) : ?>
                      <a class="mh-button" href="javascript:mh.tenders.variations.viewSigModal.open(<?php echo($variation->variationId); ?>);">View Signature<?php if(count($variation->signatureDocumentIds) > 1) echo 's'; ?></a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Modals -->
	  <div style='display:none'>
      <!-- Approval -->
		  <div id="mh-variation-approve">
        <h2>Approval - <span data-name></span></h2>

        <div class="mh-declaration">
          <?php echo $declaration; ?>
        </div>

        <canvas class="mh-sig-pad"></canvas>

        <div id="cboxFooter">
          <div class="row mh-package-info">
            <div class="col-xs-3 col-sm-2">
              <div class="mh-button-wrapper">
                <a class="mh-button mh-button-sub padding-left-5 padding-right-5" href="javascript:mh.tenders.variations.addSigModal.sigPad.api.clear()">Clear</a>
              </div>
            </div>
            <div class="col-xs-9 col-sm-4 pull-right">
              <div class="mh-button-wrapper">
                <a class="mh-button mh-send-approval" href="javascript:mh.tenders.variations.addSigModal.send()">Send Approval</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- View Signatures -->
		  <div id="mh-variation-sig-view">
        <h2 data-title>&nbsp;</h2>

        <!-- <div class="mh-package-slideshow">
          <div class="slider mh-slideshow-main" data-slideshow-images></div>
        </div> -->
          <div class="mh-slideshow" data-slideshow-images></div>

        <div id="cboxFooter">
          <div class="row mh-package-info">
            <div class="col-xs-3 col-sm-8">
              <!-- <span class="mh-button-wrapper">
                <a class="mh-button mh-button-sub padding-left-5 padding-right-5" href="javascript:mh.tenders.variations.addSigModal.sigPad.api.clear()">Clear</a>
              </span> -->
            </div>
            <div class="col-xs-9 col-sm-4">
              <div class="mh-button-wrapper">
                <a class="mh-button" href="javascript:jQuery.colorbox.close();">Close</a>
              </div>
            </div>
          </div>
        </div>
		  </div>
	  </div>
  </div>
</div>

<script src="<?php echo MH_URL_SCRIPTS; ?>/shortcodeTenderVariations.js" type="text/javascript"></script>
<script type="text/javascript">
  <?php $xhrAttributes=$this->xhrAttributes(array('variationApprove', 'clientDocument')); ?>
  jQuery(function ($) {
    _.extend(mh.tenders.variations, {
      xhr: {
        url: '<?php echo $xhrAttributes['url']; ?>',
        actions: <?php echo json_encode($xhrAttributes['actions']); ?>
      },
      
      data: {
        tender: <?php echo json_encode($tender) ?>,
        variations: <?php echo json_encode($variations) ?>
      }
    });
    mh.tenders.variations.init();
  });
</script>