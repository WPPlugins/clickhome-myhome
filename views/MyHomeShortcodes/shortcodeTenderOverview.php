<?php
/**
 * The tenderOverview view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 * @since      1.5
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeTenderOverviewController))
  die;

/**
 * @var ShortcodeTenderOverviewController $this
 * @var string                            $attSelections
 * @var string[]                          $fieldNames
 */

global $mh_site_url;
global $tender;
//$details = $tender->details;
// $documents=$tender->documents;
//$photos = $tender->photos;

//var_dump($tender);
//echo '</br></br>';
//var_dump($details);
?>
<div class="mh-wrapper mh-wrapper-tender-overview">
  <h2 class="entry-title">
    <a href="<?php echo esc_html($mh_site_url); ?>">Home</a> 
    <i class="mh-breadcrumb-next">/</i> 
    <?php echo esc_html($tender->housetypename); ?>
  </h2>
	<!-- <h1><?php echo esc_html($tender->housetypename); ?></h1> -->

	<?php //print_r($details); ?>

  <div class="mh-section mh-card mh-tender-overview-details mh-show-info">
    <div class="mh-slideshow-main">
	    <?php foreach($tender->houseType->imageIds as $photoUrl): ?>
			  <div style="background-image: url('<?php echo $this->photoDownloadUrl($photoUrl, false, null, 'client'); ?>');"></div>
      <?php endforeach; ?>
		</div>
    <div class="mh-slideshow-carousel">
	    <?php foreach($tender->houseType->imageIds as $photoUrl): ?>
        <div style="background-image:url('<?php echo $this->photoDownloadUrl($photoUrl, true, null, 'client'); ?>');"></div>
      <?php endforeach; ?>
		</div>
    <div class="mh-info-overlay">
      <a class="mh-toggle" onclick="jQuery('.mh-tender-overview-details').toggleClass('mh-show-info')"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
			<div class="mh-row">
			  <div class="mh-cell mh-name">Facade</div>
			  <div class="mh-cell mh-value"><?php echo esc_html($tender->facadename); ?></div>
			</div>
      <div class="mh-row">
			  <div class="mh-cell mh-name">Size</div>
			  <div class="mh-cell mh-value"><?php echo esc_html($tender->houseType->totalArea); ?>m<sup>2</sup></div>
			</div>
      <div class="mh-row">
			  <div class="mh-cell mh-name">Bedrooms</div>
			  <div class="mh-cell mh-value"><?php echo esc_html($tender->houseType->bedroomsCount); ?></div>
			</div>
      <div class="mh-row">
			  <div class="mh-cell mh-name">Bathrooms</div>
			  <div class="mh-cell mh-value"><?php echo esc_html($tender->houseType->bathroomsCount); ?></div>
			</div>
      <div class="mh-row">
			  <div class="mh-cell mh-name">Stories</div>
			  <div class="mh-cell mh-value"><?php echo esc_html($tender->houseType->storeys); ?></div>
			</div>
      <div class="mh-row">
			  <div class="mh-cell mh-name">Parking</div>
			  <div class="mh-cell mh-value"><?php echo esc_html($tender->houseType->carportsCount); ?></div>
			</div>

      <?php //foreach($details as $field=>$value): ?>
		    <?php //if($value != '' && $field != 'houseDesign' && $field != 'description'): ?>
			  <!-- <div class="mh-row">
			    <div class="mh-cell mh-name"><?php //echo esc_html($fieldNames[$field]); ?></div>
			    <div class="mh-cell mh-value"><?php //echo $value; ?></div>
			  </div> -->
		    <?php //endif; ?>
      <?php //endforeach; ?>
    </div>
  </div>

	<div class="mh-tender-desc">
		<p><?php echo esc_html($tender->houseType->description); ?></p>
	</div>
</div>

<script src="<?php echo MH_URL_SCRIPTS; ?>/shortcodeTenderOverview.js" type="text/javascript"></script>
<script type="text/javascript">
  jQuery(function($){
    _.extend(mh.tenders.overview, {
      data: {
        tender: <?php echo json_encode($tender) ?>, 
      }
    });
    //mh.tenders.overview.tenderOverview.init();
  });
</script>
