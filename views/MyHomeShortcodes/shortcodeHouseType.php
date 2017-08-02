<?php
/**
 * The houseType view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 * @since      1.3
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeHouseTypeController))
  die;

/**
 * @var ShortcodeHouseTypeController $this
 * @var MyHomeHouseType              $houseType
 */
?>
<div class="mh-wrapper mh-wrapper-house-type">
  <div class="mh-wrapper mh-wrapper-tender-overview">
    <div class="mh-section mh-card mh-tender-overview-details mh-show-info">
      <div class="mh-slideshow-main">
	      <?php foreach($houseType->housedocs as $doc): ?>
          <?php $attachment=myHome()->advertising->docFindAttachment($doc->url); ?>
          <?php if($attachment): ?>
			      <div style="background-image: url('<?php echo esc_url(wp_get_attachment_image_src($attachment->ID,'full')[0]); ?>');"></div>
          <?php endif; ?>
        <?php endforeach; ?>
		  </div>
      <div class="mh-slideshow-carousel">
	      <?php foreach($houseType->housedocs as $doc): ?>
          <?php $attachment=myHome()->advertising->docFindAttachment($doc->url); ?>
          <?php if($attachment): ?>
			      <img src="<?php echo esc_url(wp_get_attachment_image_src($attachment->ID,'full')[0]); ?>" />
          <?php endif; ?>
        <?php endforeach; ?>
		  </div>
      <div class="mh-info-overlay">
        <a class="mh-toggle" onclick="jQuery('.mh-tender-overview-details').toggleClass('mh-show-info')"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
        <?php //foreach($details as $field=>$value): ?>
		      <?php //if($value != '' && $field != 'houseDesign' && $field != 'description'): ?>
			    <!--<div class="mh-row">
			      <div class="mh-cell mh-name"><?php //echo esc_html($fieldNames[$field]); ?></div>
			      <div class="mh-cell mh-value"><?php //echo $value; ?></div>
			    </div>-->
		      <?php //endif; ?>
        <?php //endforeach; ?>

			  <div class="mh-row">
			    <div class="mh-cell mh-name">Size</div>
			    <div class="mh-cell mh-value"><?php echo esc_html($houseType->size); ?></div>
			  </div>
			  <div class="mh-row">
			    <div class="mh-cell mh-name">Bedrooms</div>
			    <div class="mh-cell mh-value"><?php echo esc_html($houseType->bedqty); ?></div>
			  </div>
			  <div class="mh-row">
			    <div class="mh-cell mh-name">Bathrooms</div>
			    <div class="mh-cell mh-value"><?php echo esc_html($houseType->bathqty); ?></div>
			  </div>
			  <div class="mh-row">
			    <div class="mh-cell mh-name">Carparks</div>
			    <div class="mh-cell mh-value"><?php echo esc_html($houseType->garageqty); ?></div>
			  </div>
        <?php if($houseType->hasTheatreRoom()): ?>
			    <div class="mh-row">
			      <div class="mh-cell mh-name">Theatre</div>
			      <div class="mh-cell mh-value"><i class="fa fa-check"></i></div>
			    </div>
        <?php endif; ?>
        <?php if($houseType->hasStudyRoom()): ?>
			    <div class="mh-row">
			      <div class="mh-cell mh-name">Study</div>
			      <div class="mh-cell mh-value"><i class="fa fa-check"></i></div>
			    </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
<!--   <div class="mh-section mh-section-house-type-description-images-wrapper">
    <div class="col-xs-12 col-sm-4 mh-section mh-section-house-type-features">
      <div class="mh-block mh-block-house-type-size"><?php echo esc_html($houseType->size); ?></div>
      <div class="mh-block mh-block-house-type-bedrooms" title="<?php _ex('Bedrooms','House Type','myHome'); ?>"><span
          class="mh-icon">&nbsp;</span> <?php echo (int)$houseType->bedqty; ?></div>
      <div class="mh-block mh-block-house-type-bathrooms" title="<?php _ex('Bathrooms','House Type','myHome'); ?>"><span
          class="mh-icon">&nbsp;</span> <?php echo (float)$houseType->bathqty; ?></div>
      <div class="mh-block mh-block-house-type-garages" title="<?php _ex('Carparks','House Type','myHome'); ?>"><span
          class="mh-icon">&nbsp;</span> <?php echo (int)$houseType->garageqty; ?></div>
      <?php if($houseType->hasTheatreRoom()): ?>
        <div class="mh-block mh-block-house-type-theatre" title="<?php _ex('Theatre','House Type','myHome'); ?>"><span
            class="mh-icon">&nbsp;</span></div>
      <?php endif; ?>
      <?php if($houseType->hasStudyRoom()): ?>
        <div class="mh-block mh-block-house-type-study" title="<?php _ex('Study','House Type','myHome'); ?>"><span
            class="mh-icon">&nbsp;</span></div>
      <?php endif; ?>
    </div>

    <div class="col-xs-12 col-sm-8 mh-section mh-section-house-type-images">
      <div class="mh-block mh-block-house-type-images carousel">
        <?php foreach($houseType->housedocs as $doc): ?>
          <?php
          $attachment=myHome()->advertising->docFindAttachment($doc->url);
          ?>
          <?php if($attachment): ?>
            <?php
            $imageSrc=wp_get_attachment_image_src($attachment->ID,'full');
            ?>
            <div><img class="mh-image mh-image-house-type-image" src="<?php echo esc_url($imageSrc[0]); ?>"></div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div> -->

  <div class="mh-section mh-section-house-type-description"><?php echo nl2br(esc_html($houseType->description)); ?></div>
  <?php if($houseType->planoptions): ?>
    <div class="mh-section mh-section-house-type-planoptions">
      <div class="mh-header mh-header-house-type-planoptions"><?php _e('Floor Plan Options','myHome'); ?></div>
      <div class="mh-body mh-body-house-type-planoptions">
        <?php foreach($houseType->planoptions as $planoption): ?>
          <div class="mh-block mh-block-house-type-planoption">
            <div class="mh-block mh-block-house-type-planoption-images-wrapper">
              <div class="mh-block mh-block-house-type-planoption-images carousel">
                <?php foreach($planoption->planoptiondocs as $doc): ?>
                  <?php
                  $attachment=myHome()->advertising->docFindAttachment($doc->url);
                  ?>
                  <?php if($attachment): ?>
                    <div><?php echo wp_get_attachment_image($attachment->ID,[150,150]); ?></div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="mh-block mh-block-house-type-planoption-description">
              <div class="mh-row mh-row-house-type-planoption-title"><?php echo esc_html($planoption->name); ?></div>
              <div
                class="mh-row mh-row-house-type-planoption-description"><?php echo nl2br(esc_html($planoption->description)); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
  <?php if($houseType->facades): ?>
  <div class="mh-section mh-section-house-type-facades">
    <div class="mh-header mh-header-house-type-facades"><?php _e('Facade Options','myHome'); ?></div>
    <div class="mh-body mh-body-house-type-facades">
      <?php foreach($houseType->facades as $facade): ?>
        <div class="mh-block mh-block-house-type-facade">
          <div class="mh-block mh-block-house-type-facade-images carousel">
            <?php foreach($facade->facadedocs as $doc): ?>
              <?php
              $attachment=myHome()->advertising->docFindAttachment($doc->url);
              ?>
              <?php if($attachment): ?>
                <div><?php echo wp_get_attachment_image($attachment->ID,[150,150]); ?></div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <div class="mh-block mh-block-house-type-facade-title"><?php echo esc_html($facade->name); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="<?php echo MH_URL_SCRIPTS; ?>/shortcodeHouseType.js" type="text/javascript"></script>
<script type="text/javascript">
  jQuery(function($){
    //mh.houseType.init();
  });
</script>
