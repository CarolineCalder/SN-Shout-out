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

jimport('joomla.application.component.helper');
$params = JComponentHelper::getParams('com_sttestimonials');

// load only if not already being loaded
if(!in_array('Html2Text\Html2Text', get_declared_classes())) {
    include_once (JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Html2Text.php');
}

JHtml::_('jquery.framework');

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root() . 'media/com_sttestimonials/css/jquery.rateyo.min.css');
$doc->addScript(JUri::root() . 'media/com_sttestimonials/js/jquery.rateyo.min.js');

$class = $params->get('display_author_pic', 1) == 1 ? 'three-forth' : 'full-width';

?>

<style type="text/css">
    .st-testimonials-container .one-forth {
        width: 25%;
        float: left;
    }
    .st-testimonials-container .three-forth {
        width: 75%;
        float: right;
    }
    .st-testimonials-container .full-width {
        width: 100%;
    }
    .st-testimonials-container .clear {
        clear: both;
    }
    .st-testimonials-container .st-author-img {
        text-align: center;        
    }
    .st-testimonials-container .img-circle {
        border-radius: 50%;
        border: 1px solid #eee;
        padding: 5px;
        width: 128px;
        height: 128px;
    }
    .st-testimonials-container .rating {
        margin: 0 auto;
    }
    .st-testimonials-container .st-testimonial-author-details {
        text-align: right;
        font-size: 12px;
        color: #555;
    }
    .st-testimonials-container .st-testimonial-author-name {
        font-weight: bold;
    }
    
    .st-testimonials-container blockquote {
      display:block;
      background: #fff;
      padding: 15px 20px 15px 45px;
      margin: 0 0 20px;
      position: relative;
      
      /*Font*/
      font-family: Georgia, serif;
      font-size: 16px;
      line-height: 1.2;
      color: #666;
      text-align: justify;
      
      /*Borders - (Optional)*/
      border-left: 15px solid <?php echo $params->get('accent_color', '#999');?>;
      border-right: 2px solid <?php echo $params->get('accent_color', '#999');?>;
      
      /*Box Shadow - (Optional)*/
      -moz-box-shadow: 2px 2px 15px #ccc;
      -webkit-box-shadow: 2px 2px 15px #ccc;
      box-shadow: 2px 2px 15px #ccc;
    }
    
    .st-testimonials-container blockquote::before{
      content: "\201C"; /*Unicode for Left Double Quote*/
      
      /*Font*/
      font-family: Georgia, serif;
      font-size: 60px;
      font-weight: bold;
      color: <?php echo $params->get('accent_color', '#999');?>;
      
      /*Positioning*/
      position: absolute;
      left: 10px;
      top:5px;
    }
    
    .st-testimonials-container blockquote::after{
      /*Reset to make sure*/
      content: "";
    }
    
    .st-testimonials-container blockquote a {
      text-decoration: none;
      background: #eee;
      cursor: pointer;
      padding: 0 3px;
      color: #c76c0c;
    }
    
    .st-testimonials-container blockquote a:hover {
     color: #666;
    }
    
    .st-testimonials-container blockquote em{
      font-style: italic;
    }
    
    .st-testimonials-container hr {
        padding: 0;
        border: none;
        border-top: medium double #eee;
        color: #eee;
        text-align: center;
    }
    
    .st-testimonials-container hr:after {
        content: "§";
        display: inline-block;
        position: relative;
        top: -0.7em;
        font-size: 1.5em;
        padding: 0 0.25em;
        background: white;
    }
    
    .st-testimonials-powered-by {
        font-size: 10px;
        float: right;
        margin-top: -40px;
    }
</style>

<?php if ($this->item) : $item = $this->item; ?>
    <div class="st-testimonials-container">
        <?php if($item->state == 1) : ?>
            <div class="st-testimonial-item" itemprop="review" itemscope itemtype="http://schema.org/Review">
                <meta itemprop="itemReviewed" content="Services">
                <?php if($params->get('display_author_pic', 1) == 1) : ?>
                    <?php 
                        $default = "/media/com_sttestimonials/img/unknown.png";
                        if($item->author_pic != null && $item->author_pic != '') {
                            $img = explode(',', $item->author_pic);
                            $default = "/media/com_sttestimonials/uploads/".$img[0];
                        }                        
                    ?>
                    <div class="st-author-img one-forth">
                        <img class="st-author-img img-circle" src="<?php echo $default;?>" />
                        <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                            <meta itemprop="worstRating" content = "1"/>
                            <meta itemprop="ratingValue" content = "<?php echo $item->rating;?>">
                            <meta itemprop="bestRating" content = "5">
                            <div class="rating"></div>
                        </div>
                    </div>
                    
                <?php endif; ?>
                <div class="<?php echo $class;?>">
                    <blockquote class="st-testimonial-text" itemprop="description">
                    <?php 
                        if($params->get('strip_tags', 0) == 1) {
                            $html = new \Html2Text\Html2Text($item->testimonial);
                            echo $html->getText();
                        } else {
                            echo $item->testimonial;
                        }
                    ?>
                    </blockquote>
                    <div class="st-testimonial-author-details">
                        <span class="st-testimonial-author-name" itemprop="author"><?php echo $item->author;?></span>              
                        <br/>
                        <?php if($params->get('display_author_creds', 1) == 1) : ?>
                            <?php 
                                if($item->author_credentials != null && $item->author_credentials != '') {
                                    echo '<span class"st-testimonial-author-creds">'.$item->author_credentials.'</span>';
                                    echo '<br/>';
                                }                             
                            ?>                            
                        <?php endif; ?>                        
                        <?php if($params->get('display_author_url', 1) == 1) : ?>
                            <?php if($item->url != null && $item->url != '') : ?>
                                <span class"st-testimonial-author-url">
                                    <a href="<?php echo $item->url;?>" target="_blank" rel="nofollow"><?php echo $item->url;?></a>
                                </span>
                                <br/>
                            <?php endif; ?> 
                        <?php endif; ?>                        
                        <?php if($params->get('display_date', 1) == 1) : ?>
                            <?php echo '<span class"st-testimonial-date">'.date('jS F, Y', strtotime($item->date)).'</span>';?>
                        <?php endif; ?>
                        <meta itemprop="datePublished" content="<?php echo date('Y-m-d', strtotime($item->date));?>">
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <script type="text/javascript">
                jQuery(".rating").rateYo({
                    halfStar: true,
                    readOnly: true,
                    <?php if($item->rating != null && $item->rating > 0) : ?>
                        rating: <?php echo $item->rating;?>
                    <?php endif; ?>    
                });
            </script>
            <hr>
        <?php endif; ?>
    </div> <!-- end of .st-testimonials-container -->
<?php else:
    echo JText::_('COM_STTESTIMONIALS_ITEM_NOT_LOADED');
endif;?>
<a class="st-testimonials-powered-by" href="http://www.sopantech.com" target="_blank">Web Development Company</a>