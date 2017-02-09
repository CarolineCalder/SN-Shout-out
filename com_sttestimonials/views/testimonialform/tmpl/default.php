<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Sttestimonials
 * @author     Sopan Technologies <info@sopantech.com>
 * @copyright  2016 Sopan Technologies
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

jimport('joomla.application.component.helper');
$params = JComponentHelper::getParams('com_sttestimonials');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_sttestimonials', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_sttestimonials/js/form.js');
$doc->addStyleSheet(JUri::root() . 'media/com_sttestimonials/css/jquery.rateyo.min.css');
$doc->addScript(JUri::root() . 'media/com_sttestimonials/js/jquery.rateyo.min.js');
/*
if($this->item->state == 1){
	$state_string = 'Publish';
	$state_value = 1;
} else {
	$state_string = 'Unpublish';
	$state_value = 0;
}
if($this->item->id) {
	$canState = JFactory::getUser()->authorise('core.edit.state','com_sttestimonials.testimonial');
} else {
	$canState = JFactory::getUser()->authorise('core.edit.state','com_sttestimonials.testimonial.'.$this->item->id);
}*/
?>
<script type="text/javascript">
	if (jQuery === 'undefined') {
		document.addEventListener("DOMContentLoaded", function (event) {
		    document.getElementById('form-testimonial').addEventListener('submit', function(evt) {
		        if(tinymce.get('jform_testimonial').getContent() === '') {
		            document.getElementById("jform_testimonial-lbl").className += ' invalid';
                    document.getElementById('system-message-container').innerHTML = '<div class="alert alert-error"><p>Error: Testimonial text cannot be empty.</p></div>';
                    return false;         
		        }
		        if(document.getElementById('jform_author_pic').value !== '') {
                    document.getElementById('jform_author_pic_hidden').value = document.getElementById('jform_author_pic').value;
                }
            });		
		});
	} else {
		jQuery(document).ready(function () {
			jQuery('#form-testimonial').on('submit', function (event) {
			    				
                if(tinymce.get('jform_testimonial').getContent() === '') {
                    jQuery('#jform_testimonial-lbl').addClass('invalid');
                    jQuery('#system-message-container').html('<div class="alert alert-error"><p>Error: Testimonial text cannot be empty.</p></div>');
                    return false;
                }

        		if(jQuery('#jform_author_pic').val() != ''){
        			jQuery('#jform_author_pic_hidden').val(jQuery('#jform_author_pic').val());
        		}
                
    		});
		});
	}
</script>

<style type="text/css">
    .st-testimonials-powered-by {
        font-size: 10px;
        float: right;
        margin-top: -40px;
    }
</style>

<?php if($params->get('enable_captcha', 0) == 1) { ?>
    <script type="text/javascript">
      var onloadRecaptchaCallback = function() {
        grecaptcha.render('g-recaptcha-widget', {
          'sitekey' : "<?php echo trim($params->get('captcha_site_key'));?>",
          'theme' : "<?php echo $params->get('captcha_theme', 1);?>"
        });        
      };
    </script>
<?php } ?>

<div class="testimonial-edit front-end-edit">
	<?php if (!empty($this->item->id)): ?>
		<h1>Edit <?php echo $this->item->id; ?></h1>
	<?php else: ?>
		<h1>Add Testimonial</h1>
	<?php endif; ?>

	<form id="form-testimonial"
		  action="<?php echo JRoute::_('index.php?option=com_sttestimonials&task=testimonial.save'); ?>"
		  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
		
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<?php echo $this->form->renderField('testimonial'); ?>
	
	<div class="control-group">
        <div class="control-label"><label id="jform_rating-lbl" for="jform_rating" class="hasTooltip required invalid" title="" data-original-title="<strong>Rating</strong><br />Rating for this testimonial">
        Rating<span class="star">&nbsp;*</span></label>
    </div>
        <div class="controls">
            <div id="rating"></div>
            <input type="hidden" name="jform[rating]" id="jform_rating" value="" class="required" aria-required="true" required="required">
        </div>
    </div>

	<?php echo $this->form->renderField('author'); ?>

	<?php echo $this->form->renderField('author_credentials'); ?>

	<?php echo $this->form->renderField('author_pic'); ?>

	<?php if (!empty($this->item->author_pic)) :
		foreach ((array) $this->item->author_pic as $singleFile) : 
			if (!is_array($singleFile)) :
				echo '<a href="' . JRoute::_(JUri::root() . 'media/com_sttestimonials/uploads/' . DIRECTORY_SEPARATOR . $singleFile, false) . '">' . $singleFile . '</a> ';
			endif;
		endforeach;
	endif; ?>
	<input type="hidden" name="jform[author_pic][]" id="jform_author_pic_hidden" value="<?php echo str_replace('Array,', '', implode(',', (array) $this->item->author_pic)); ?>" />
	<?php echo $this->form->renderField('url'); ?>

	<?php //echo $this->form->renderField('date'); ?>


    <?php if($params->get('enable_captcha', 0) == 1) { ?>
        <div class="control-group">
            <div class="control-label">
                <label class="hasTooltip" title="" data-original-title="<strong>Captcha</strong><br /> Are you human?"></label>
            </div>
            <div class="controls">
                <span id="g-recaptcha-widget"></span>
                <span id="captcha-error" style="display:none; color: red;">Captcha verification failed</span>
            </div>
        </div>
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadRecaptchaCallback&render=explicit" async defer>
    <?php } ?>


	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

	<?php if(empty($this->item->created_by)): ?>
		<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />
	<?php else: ?>
		<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
	<?php endif; ?>
	<?php if(empty($this->item->modified_by)): ?>
		<input type="hidden" name="jform[modified_by]" value="<?php echo JFactory::getUser()->id; ?>" />
	<?php else: ?>
		<input type="hidden" name="jform[modified_by]" value="<?php echo $this->item->modified_by; ?>" />
	<?php endif; ?>				<div class="fltlft" <?php if (!JFactory::getUser()->authorise('core.admin','sttestimonials')): ?> style="display:none;" <?php endif; ?> >
                <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
                <?php echo JHtml::_('sliders.panel', JText::_('ACL Configuration'), 'access-rules'); ?>
                <fieldset class="panelform">
                    <?php echo $this->form->getLabel('rules'); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                </fieldset>
                <?php echo JHtml::_('sliders.end'); ?>
            </div>
				<?php if (!JFactory::getUser()->authorise('core.admin','sttestimonials')): ?>
                <script type="text/javascript">
                    jQuery.noConflict();
                    jQuery('.tab-pane select').each(function(){
                       var option_selected = jQuery(this).find(':selected');
                       var input = document.createElement("input");
                       input.setAttribute("type", "hidden");
                       input.setAttribute("name", jQuery(this).attr('name'));
                       input.setAttribute("value", option_selected.val());
                       document.getElementById("form-testimonial").appendChild(input);
                    });
                </script>
             <?php endif; ?>
		<div class="control-group">
			<div class="controls">

				<?php if ($this->canSave): ?>
					<button id="stt-add-btn" type="submit" class="validate btn btn-primary">
						<?php echo JText::_('JSUBMIT'); ?>
					</button>
				<?php endif; ?>
				<a class="btn"
				   href="<?php echo JRoute::_('index.php?option=com_sttestimonials&task=testimonialform.cancel'); ?>"
				   title="<?php echo JText::_('JCANCEL'); ?>">
					<?php echo JText::_('JCANCEL'); ?>
				</a>
			</div>
		</div>

		<input type="hidden" name="option" value="com_sttestimonials"/>
		<input type="hidden" name="task"
			   value="testimonialform.save"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

<a class="st-testimonials-powered-by" href="http://www.sopantech.com" target="_blank">Web Development Company</a>

<script type="text/javascript">
    jQuery( document ).ready(function() {
        <?php if($this->item->rating != null && $this->item->rating > 0) : ?>
            jQuery('#jform_rating').val(<?php echo $this->item->rating;?>);
        <?php endif; ?>  
        jQuery('#rating').rateYo({
            halfStar: true,
            onSet: function(rating, instance) {
                jQuery('#jform_rating').val(rating);
            },
            <?php if($this->item->rating != null && $this->item->rating > 0) : ?>
                rating: <?php echo $this->item->rating;?>
            <?php endif; ?>    
        });
        <?php if($params->get('enable_captcha', 0) == 1) { ?>
            jQuery('#stt-add-btn').on('click', function(e) {
                var response = grecaptcha.getResponse();
                if(response.length === 0) {
                    e.preventDefault();
                    jQuery('#captcha-error').show();
                }    
            });
        <?php } ?>
        jQuery(function() {            
            jQuery('input[type="file"]').on('change', function() {
                if (parseInt(this.files.length) > 1) {
                    alert("You can only upload one file");
                }
            });    
        });
    });
</script>