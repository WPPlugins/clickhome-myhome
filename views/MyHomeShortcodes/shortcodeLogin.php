<?php
/**
 * The login view
 *
 * @package    MyHome
 * @subpackage ViewsShortcodes
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Exit if not called from the controller
if(!isset($this)||!($this instanceof ShortcodeLoginController))
  die;

/**
 * @var ShortcodeLoginController $this
 * @var string                   $attFacebook
 * @var string                   $loginRedirect
 * @var string                   $loginHelpUrl
 * @var string                   $facebookAppId
 * @var string                   $facebookError
 */

$formAttributes=myHome()->adminPostHandler->formAttributes('login','POST',$loginRedirect);

$facebook=$attFacebook==='yes';

$error=$this->restoreVar('error');
$job=$this->restoreVar('job','');
$username=$this->restoreVar('username','');

// If there is no error but a Facebook error, display it
// Also, if $facebookError is not empty, the page doesn't reload if a Facebook session is detected - this prevents
// infinite reload loops if the job list associated with the email is empty
if(!$error&&$facebookError)
  $error=$facebookError;
?>
<div class="mh-wrapper mh-wrapper-login">
	<div class="mh-header">
		<h2>Log In</h2>
	</div>
	<form action="<?php $this->appendFormUrl($formAttributes); ?>" class="mh-section mh-section-login-login" method="POST">
		<?php
		$this->appendFormParams($formAttributes,4);
		?>
		<?php if($error): ?>
		<div class="mh-error mh-error-login-login"><?php echo esc_html($error); ?></div>
		<?php endif; ?>
		<div class="mh-body mh-body-login-login">
			<div class="mh-row mh-row-login-login-job-number">
				<!--<div class="mh-cell mh-cell-login-login-field"><?php _e('Job Number:','myHome'); ?></div>-->
				<div class="mh-cell mh-cell-login-login-input">
					<input maxlength="20" name="myHomeJobNumber" required type="text" placeholder="<?php _e('Job Number','myHome'); ?>"
						   value="<?php echo esc_attr($job); ?>" autocorrect="off" autocapitalize="off" spellcheck="false">
				</div>
			</div>
			<div class="mh-row mh-row-login-login-username">
				<!--<div class="mh-cell mh-cell-login-login-field"><?php _e('Username:','myHome'); ?></div>-->
				<div class="mh-cell mh-cell-login-login-input">
					<input maxlength="50" name="myHomeUsername" required type="text" placeholder="<?php _e('Username','myHome'); ?>"
						   value="<?php echo esc_attr($username); ?>" autocorrect="off" autocapitalize="off" spellcheck="false">
				</div>
			</div>
			<div class="mh-row mh-row-login-login-password">
				<!--<div class="mh-cell mh-cell-login-login-field"><?php _e('Password:','myHome'); ?></div>-->
				<div class="mh-cell mh-cell-login-login-input">
					<input maxlength="20" name="myHomePassword" required placeholder="<?php _e('Password','myHome'); ?>"
						   type="password" autocorrect="off" autocapitalize="off" spellcheck="false">
				</div>
			</div>
			<?php if ($loginHelpUrl != ''): ?>
				<a class="mh-login-loginhelp" href="<?php echo esc_html($loginHelpUrl); ?>"><?php _ex('Trouble logging in?','Login Form','myHome'); ?></a>
			<?php endif; ?>
			<br/>
		<!--</div>
		<div class="mh-body mh-footer-login-login">-->
			<div class="mh-row mh-row-login-login-button">
				<div class="mh-cell mh-cell-login-login-button">
					<span class="mh-button-wrapper mh-button-block"><button class="mh-button mh-button-login-login-submit mh-show-loading" type="submit">
						<?php _ex('Login','Login Form','myHome'); ?>
					</button></span>
				</div>
			</div>
		</div>
	</form>

	<!-- If [MyHome.Login facebook=yes] aatr -->
	<?php if($facebook): ?>
	<div class="mh-footer mh-section-login-facebook">
		<!--<div class="mh-text mh-text-separation mh-text-login-facebook-or">
			<div class="mh-text-separation-line">
				<div></div>
			</div>
			<div class="mh-text-separation-text"><?php _ex('Or','Facebook login','myHome'); ?></div>
			<div class="mh-text-separation-line">
				<div></div>
			</div>
		</div>
		<div class="fb-login-button" data-cookie="true" data-max-rows="1" data-scope="public_profile,email,user_photos,publish_actions"
			 data-size="large"><?php _e('Login with Facebook','myHome'); ?></div>-->

		<a class="btn btn-block myhome_facebook_btn" onclick="FB.login()">
			<?php _e('Continue with Facebook', 'myHome'); ?>
		</a>

		<div id="fb-root"></div>
		<script type="text/javascript">
			window.fbAsyncInit=function(){
				FB.init(
					{
						appId:"<?php echo $facebookAppId; ?>",
						cookie:true,
						xfbml:true,
						version:"v2.4"
					});

				FB.Event.subscribe("auth.login",function(){
					location.reload();
				});

				<?php if(!$facebookError): ?>
					FB.getLoginStatus(function(res){
						if(res.status==="connected")
							location.reload();
					});
				<?php endif; ?>
			};

			(function(){
				var script=document.createElement("script");
				script.async=true;
				script.src=document.location.protocol+"//connect.facebook.net/en_US/all.js";

				document.getElementById("fb-root").appendChild(script);
			}());
		</script>
	</div>
	<?php endif; ?>

	<div class="mh-login-loading"></div>

	<script type="text/javascript">
		jQuery(function($){
			jQuery('.mh-show-loading').on('click', function() {
				var $loginWrapper = jQuery('.mh-wrapper-login');
				var willValidate = true;
				$loginWrapper.find('input[required]').each(function() {
					if($(this).val() == '') willValidate = false;
					//console.log($(this).val());
				});

				if( willValidate) {
					$loginWrapper.addClass('mh-logging-in');
				}
			});

			//if(typeof($.fn.validate==="function"))
			//  $(".mh-wrapper-login").validate({
			//	  message:"<?php _e('Please fill in all required fields','myHome'); ?>",
			//	  feedbackClass:"mh-error"
			//  });
		});
	</script>
</div>
