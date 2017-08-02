<?php
/**
 * The maintenanceIssues view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 * @since      1.2
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeMaintenanceIssuesController))
  die;

/**
 * @var ShortcodeMaintenanceIssuesController $this
 * @var int                                  $attMaxIssues
 * @var int                                  $jobId
 * @var mixed[]                              $issues
 * @var bool                                 $addIssue
 * @var bool                                 $skipReviewScreen
 * @var string                               $redirectUrl
 * @var string                               $redirectUrlError
 * @var string                               $redirectUrlContinue
 * @var string                               $paramJobId
 * @var string                               $paramPostId
 * @var int                                  $maxFiles
 */

$formAttributes=myHome()->adminPostHandler->formAttributes('maintenanceIssues','POST',$redirectUrl,$redirectUrlError);
$formAttributes['params']['myHomeRedirectUrlContinue']=$redirectUrlContinue;
$formAttributes['params'][$paramJobId]=$jobId;
// Post ID is used by doPostMaintenance() to retrieve the cached attributes for this post, and then, check how many issues is the user allow to submit
$formAttributes['params'][$paramPostId]=get_the_ID();
$formAttributes['params']['myHomeSkipReviewScreen']=(int)$skipReviewScreen;

$error=$this->restoreVar('error');

$moreIssuesAllowed=!($attMaxIssues&&count($issues)>=$attMaxIssues);

if(!$moreIssuesAllowed)
  $issues=array_slice($issues,0,$attMaxIssues,true);
else if(!$issues||$addIssue)
  $issues[]=['id'=>'-1', // Negative IDs represent new issues
    'title'=>'',
    'description'=>'',
    'deletable'=>$issues&&$addIssue, // Mark as deletable only if it's not the first issue
    'files'=>[]];
?>
<form action="<?php $this->appendFormUrl($formAttributes); ?>" class="mh-wrapper mh-wrapper-maintenance-issues" enctype="multipart/form-data" method="POST">
  <?php $this->appendFormParams($formAttributes,2); ?>
  <div class="mh-section mh-section-maintenance-issues-form">
    <?php if($error): ?>
      <div class="mh-error mh-error-maintenance-issues"><?php echo esc_html($error); ?></div>
    <?php endif; ?>
    <div class="mh-body mh-body-maintenance-issues">
      <?php foreach($issues as $issue): ?>
        <?php
        // Used to know which images have been deleted
        $filesIds=array_map(function (array $file){
          return $file['id'];
        },$issue['files']);
        ?>
        <div class="mh-block mh-block-maintenance-issues">
          <?php if($issue['deletable']): ?>
            <div class="mh-row mh-row-maintenance-issues-delete">
              <div class="mh-cell mh-cell-maintenance-issues-delete">
                <a class="mh-link mh-link-maintenance-issues-delete"
                  href="javascript:void(0);"><?php _e('Delete this issue','myHome'); ?></a>
              </div>
            </div>
          <?php endif; ?>
          <input name="myHomeIssues[<?php echo $issue['id']; ?>][existingFiles]" type="hidden"
            value="<?php echo implode(',',$filesIds); ?>">

          <div class="mh-row mh-row-maintenance-issues-title">
            <div class="mh-cell mh-cell-maintenance-issues-field"><?php _e('Issue','myHome'); ?></div>
            <div class="mh-cell mh-cell-maintenance-issues-input"><input maxlength="100"
                name="myHomeIssues[<?php echo $issue['id']; ?>][title]" required type="text"
                value="<?php echo esc_attr($issue['title']); ?>"></div>
          </div>
          <div class="mh-row mh-row-maintenance-issues-description">
            <div class="mh-cell mh-cell-maintenance-issues-field"><?php _e('Description','myHome'); ?></div>
            <div class="mh-cell mh-cell-maintenance-issues-input"><textarea maxlength="5000"
                name="myHomeIssues[<?php echo $issue['id']; ?>][description]" required
                rows="5"><?php echo esc_html($issue['description']); ?></textarea></div>
          </div>
          <div class="mh-row mh-row-maintenance-issues-files">
            <div class="mh-cell mh-cell-maintenance-issues-field"><?php _e('Attached Images','myHome'); ?></div>
            <div class="mh-cell mh-cell-maintenance-issues-input">
              <div class="mh-maintenance-issues-file-base">
                <a class="mh-link mh-link-maintenance-issues-delete-file" href="javascript:void(0);"
                  title="<?php _e('Delete','myHome'); ?>">&times;</a>
              <span class="mh-button mh-button-maintenance-issues-select-file">
                <span><?php _e('Select File...','myHome'); ?></span>
                <input data-name="myHomeIssues[<?php echo $issue['id']; ?>][files][]" type="file">
              </span>
                <span class="mh-file-name"></span>
              </div>
              <div class="mh-maintenance-issues-files-existing">
                <?php foreach($issue['files'] as $file): ?>
                  <div class="mh-maintenance-issues-file">
                    <input name="myHomeIssues[<?php echo $issue['id']; ?>][files][<?php echo $file['id']; ?>]"
                      type="hidden" value="true">
                    <?php if(false): // Not working yet - skip ?>
                      <a class="mh-link mh-link-maintenance-issues-delete-file" href="javascript:void(0);"
                        title="<?php _e('Delete','myHome'); ?>">&times;</a>
                    <?php endif; ?>
                    <a class="mh-maintenance-issues-thumbnail-link"
                      href="<?php echo $this->photoDownloadUrl($file['url'], false, true, 'client'); ?>" target="_blank"><img
                        class="mh-image mh-image-maintenance-issues-thumbnail"
                        src="<?php echo $this->photoDownloadUrl($file['url'], true, true, 'client'); ?>"></a>
                    <span class="mh-file-name"><?php echo $file['title']; ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="mh-maintenance-issues-files-new"></div>
              <a class="mh-link mh-link-maintenance-issues-add-file"
                href="javascript:void(0);"><?php _e('Add More Files','myHome'); ?></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="mh-footer mh-footer-maintenance-issues">
      <div class="mh-row mh-row-maintenance-issues-buttons">
        <div class="mh-cell mh-cell-maintenance-issues-buttons">
          <?php if($moreIssuesAllowed): ?>
            <button class="mh-button mh-button-maintenance-issues-submit" name="myHomeSubmit" type="submit"
              value="addIssue"><?php _ex('Submit &amp; Add More','myHome'); ?></button>
          <?php endif; ?>
          <button class="mh-button mh-button-maintenance-issues-submit" name="myHomeSubmit" type="submit"
            value="continue"><?php _ex('Submit &amp; Continue','myHome'); ?></button>
        </div>
      </div>
    </div>
  </div>
  <div class="mh-loading mh-loading-maintenance-issues-image" id="divMyHomeLoadingMaintenanceIssues"></div>
</form>
<script type="text/javascript">
  jQuery(function($){
    if(typeof($.fn.validate==="function"))
      $(".mh-wrapper-maintenance-issues").validate(
        {
          message:"<?php _e('Please fill in all required fields','myHome'); ?>",
          feedbackClass:"mh-error"
        });

    var thumbnailLink=$(".mh-maintenance-issues-thumbnail-link");
    var image=$("#imgMyHomeMaintenanceIssuesImage");
    var addFile=$(".mh-link-maintenance-issues-add-file");
    var issues=$(".mh-body-maintenance-issues");

    var firstImage=thumbnailLink.first().attr("href");
    if(firstImage!==undefined)
    {
      image.attr("src",firstImage);
      $("#divMyHomeLoadingMaintenanceIssues").show();
    }

    thumbnailLink.click(function(){
      image.attr("src",$(this).attr("href"));
      $("#divMyHomeLoadingMaintenanceIssues").show();

      return false;
    });

    image.on("load",function(){
      $("#divMyHomeLoadingMaintenanceIssues").hide();
    });

    $(".mh-link-maintenance-issues-delete").click(function(){
      if(!confirm("<?php _e('Are you sure you want to delete this issue?'); ?>"))
        return;

      $(this).parent().parent().parent().remove();
    });

    addFile.each(function(){
      showAddMoreFiles($(this).parent());
    });

    addFile.click(function(){
      var parent=$(this).parent();
      var container=parent.children(".mh-maintenance-issues-files-new");

      if(!showAddMoreFiles(parent))
        return;

      var div=parent.children(".mh-maintenance-issues-file-base")
        .clone()
        .addClass("mh-maintenance-issues-file")
        .removeClass("mh-maintenance-issues-file-base");

      var input=div.find("input");
      input.attr("name",input.data("name"));
      input.removeAttr("data-name");

      container.append(div);

      showAddMoreFiles(parent);
    });

    issues.on("change",".mh-button-maintenance-issues-select-file input[type=file]",
      function(){
        var filename=$(this).val();

        var parts=filename.match(/\\([^\\]+)$/);

        if(parts!==null)
          filename=parts[1];
        else
        {
          parts=filename.match(/\/([^\/]+)$/);

          if(parts!==null)
            filename=parts[1];
        }

        $(this).parent().parent().children(".mh-file-name")
          .empty()
          .append(filename);
      });

    issues.on("click",".mh-link-maintenance-issues-delete-file",function(){
      var container=$(this).parent().parent().parent();
      $(this).parent().remove();

      showAddMoreFiles(container);
    });

    function showAddMoreFiles(filesContainer){
      var link=filesContainer.find(".mh-link-maintenance-issues-add-file");

      if(filesContainer.find(".mh-maintenance-issues-file").get().length>=<?php echo $maxFiles; ?>)
      {
        link.hide();
        return false;
      }
      else
      {
        link.show();
        return true;
      }
    }
  });
</script>
