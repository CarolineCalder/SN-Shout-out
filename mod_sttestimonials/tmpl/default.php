<?php

/**
 * @copyright   Copyright © 2016 - All rights reserved.
 * @license     GNU General Public License v2.0
 */
 
defined('_JEXEC') or die;

// load only if not already being loaded
if(!in_array('Html2Text\Html2Text', get_declared_classes())) {
    include_once('modules/mod_sttestimonials/lib/Html2Text.php');        
}

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root() . 'media/com_sttestimonials/css/jquery.rateyo.min.css');
$doc->addScript(JUri::root() . 'media/com_sttestimonials/js/jquery.rateyo.min.js');
$doc->addScript(JUri::root() . 'modules/mod_sttestimonials/assets/app.js');

jimport('joomla.application.component.helper');
$cparams = JComponentHelper::getParams('com_sttestimonials');
$config = JFactory::getConfig();

if(!function_exists('limit')) {
    function limit($string, $limit) {
        
        // no limit if 0 is specified
        if($limit == 0) return $string;
        
        if (str_word_count($string, 0) > $limit) {
            $words = str_word_count($string, 2);
            $pos = array_keys($words);
            $string = substr($string, 0, $pos[$limit]) . '...';
        }
        return $string;
    }
}

?>

<style type="text/css">
    .mod-st-testimonial-<?php echo $module->id;?> .slide {
        text-align: right;
    }
    .mod-st-testimonial-<?php echo $module->id;?> .st-testimonial-author {
        font-weight: normal;
        font-size:18px;
        color:#0063a7;
        margin-right:38px;
    }
    .mod-st-testimonial-<?php echo $module->id;?> blockquote {
        margin: 0 10px 0 10px;
        padding: 0.5em 10px;
        quotes: "\201C""\201D";
    }
    /*
    .mod-st-testimonial-<?php echo $module->id;?> blockquote:before {
        color: <?php echo $params->get('accent', '#ccc');?>;
        content: open-quote;
        font-size: 4em;
        line-height: 0.1em;
        margin-right: 0.25em;
        vertical-align: -0.4em;
    }
    .mod-st-testimonial-<?php echo $module->id;?> blockquote:after {
        color: <?php echo $params->get('accent', '#ccc');?>;
        content: close-quote;
        font-size: 4em;
        line-height: 0.5em;
        margin-right: 0.25em;
        vertical-align: -0.4em;
    }
    */
    .mod-st-testimonial-<?php echo $module->id;?> blockquote p {
        font-style: italic;
        display: inline-block;
        padding: 5px;
    }
    
    .mod-st-testimonial-<?php echo $module->id;?> .st-author-img {
        width: 128px;
        height: 128px;
        margin: 0 auto;
    }
    .mod-st-testimonial-<?php echo $module->id;?> .img-circle {
        border-radius: 50%;
        border: 1px solid #eee;
        padding: 5px;
        height: 128px;
        width: 128px;
    }
    .mod-st-testimonial-<?php echo $module->id;?> .rating-wrapper {
        padding: 15px 0;    
    }
    .mod-st-testimonial-<?php echo $module->id;?> .rating-disp {
        margin: 0 auto;    
    }
    .mod-st-testimonial-<?php echo $module->id;?> .bx-wrapper .bx-pager {
        bottom:0px;
        position: relative;
        padding-top: 0px;
    }
    .st-testimonials-powered-by {
        font-size: 10px;
        float: right;
        margin-top: -50px;
    }
</style>

<div class="mod-st-testimonial-<?php echo $module->id;?> sttestimonial">
    
    <?php 
        // calculate average rating
        $total = 0;
        $average = 0;
        if(count($objects)) {
            foreach($objects as $item) {
               $total += $item['rating'];
            }
            $average = round($total/count($objects), 2);
        }
        // text limit if applicable
        $limit = $params->get('text_limit', 0);
        
    ?>
    
    <!-- aggregate review meta data start -->
    <span itemscope itemtype="http://schema.org/Service">
      <meta itemprop="name" content="<?php echo $cparams->get('business_name', $config->get('sitename'));?>" />
      <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="<?php echo $average;?>" />
        <meta itemprop="reviewCount" content="<?php echo count($objects);?>" />
      </span>
    </span>
    <!-- aggregate review meta data end -->    
    
    <?php if(count($objects)) : ?>
        <div class="slider-<?php echo $module->id;?>">
            <?php foreach($objects as $o) : ?>
                <div class="slide" itemprop="review" itemscope itemtype="http://schema.org/Review">
                    <meta itemprop="itemReviewed" content="Services">
                    <?php if($params->get('display_image', 1) == 1) : ?>
                        <?php $default = "/media/com_sttestimonials/img/unknown.png";
                        if($o['author_pic'] != null && $o['author_pic'] != "") {
                            $img = explode(',', $o['author_pic']);
                            $default = "/media/com_sttestimonials/uploads/".$img[0];
                        }?>                
                        <div class="st-author-img">
                            <img class="img-circle" src="<?php echo $default;?>" />
                        </div>
                    <?php endif; ?>
                        <?php if($params->get('display_rating', 1) == 1) : ?>
                            <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                                <meta itemprop="worstRating" content = "1"/>
                                <meta itemprop="ratingValue" content = "<?php echo $o[rating];?>">
                                <meta itemprop="bestRating" content = "5">
                                <div class="rating-wrapper">
                                    <div id="rating-mod-<?php echo $module->id . '-' . $o[id];?>" class="rating-disp"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <blockquote class="st-testimonial-text" itemprop="description">
                        <?php if($params->get('strip_html', 0) == 1) : ?>
                            <?php 
                                $html = new \Html2Text\Html2Text($o[testimonial]);
                                echo limit($html->getText(), $limit);
                            ?>
                        <?php else: ?>
                            <?php 
                                echo limit(strip_tags($o[testimonial], '<p><a><div>'), $limit);
                            ?>
                        <?php endif; ?>
                        <?php if(intval($params->get('text_limit', 0)) > 0) : ?>
                            <a href="index.php?option=com_sttestimonials&view=testimonial&id=<?php echo $o[id];?>">Read more</a>
                        <?php endif;?>          
                    </blockquote>
                    <div class="st-testimonial-author-details">
                        <?php if($params->get('link_url', 0) == 1 && $o[url] != '' && $o[url] != null) : ?>
                            <span class="st-testimonial-author" itemprop="author">
                                <a href="<?php echo $o[url];?>" target="_blank" rel="nofollow">
                                    <?php echo $o[author];?>
                                </a>
                            </span>
                            <br/>
                        <?php else: ?>
                            <span class="st-testimonial-author" itemprop="author"><?php echo $o[author];?></span>
                            <br/>
                        <?php endif; ?>
                        <?php if($params->get('display_author_creds', 1) == 1) : ?>
                            <span class="st-testimonial-author-creds"><?php echo $o[author_credentials];?></span>
                            <br/>
                        <?php endif; ?>
                        <?php if($params->get('display_url', 0) == 1 && $params->get('link_url', 0) == 0 && $o[url] != '' && $o[url] != null) : ?>
                            <span class="st-testimonial-author-url" itemprop="author"><a href="<?php echo $o[url];?>" target="_blank" rel="nofollow"><?php echo $o[url];?></a></span>
                            <br/>
                        <?php endif; ?>
                        <?php if($params->get('display_date', 0) == 1) : ?>
                            <span class="st-testimonial-date"><?php echo date('jS F, Y', strtotime($o[date]));?></span>
                        <?php endif; ?>
                        <meta itemprop="datePublished" content="<?php echo date('Y-m-d', strtotime($o[date]));?>">
                    </div>
                    <script type="text/javascript">                
                        jQuery("#rating-mod-<?php echo $module->id . '-' . $o[id];?>").rateYo({
                            halfStar: true,
                            readOnly: true,
                            <?php if($o[rating] != null && $o[rating] > 0) : ?>
                                rating: <?php echo $o[rating];?>
                            <?php endif; ?>    
                        });
                    </script>
                </div>            
            <?php endforeach; ?>
        </div> <!-- end of .slider -->
    <?php else: ?>
        <h4><?php echo JText::_('MOD_ST_TESTIMONIAL_NO_ITEMS_EXIST');?></h4>
    <?php endif; ?>
</div> <!-- end of .mod-st-testimonial -->

<script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery('.slider-<?php echo $module->id;?>').bxSlider({
        mode: "<?php echo $params->get('mode', 'horizontal');?>",
        speed: <?php echo $params->get('speed', 500);?>,
        slideMargin: <?php echo $params->get('slideMargin', 0);?>,
        startSlide: <?php echo $params->get('startSlide', 0);?>,        
        randomStart: <?php echo ($params->get('randomStart', 0) == 1) ? 'true' : 'false';?>,
        infiniteLoop: <?php echo $params->get('infiniteLoop', 1) == 1 ? 'true' : 'false';?>,
        adaptiveHeight: <?php echo $params->get('adaptiveHeight', 0) == 1 ? 'true' : 'false';?>,
        responsive: <?php echo $params->get('responsive', 1) == 1 ? 'true' : 'false';?>,
        touchEnabled: <?php echo $params->get('touchEnabled', 1) == 1 ? 'true' : 'false';?>,
        pager: <?php echo $params->get('pager', 1) == 1 ? 'true' : 'false';?>,
        pagerType: "<?php echo $params->get('pagerType', 'full');?>",
        pagerShortSeparator: "<?php echo $params->get('pagerShortSeparator', '/');?>",
        controls: <?php echo $params->get('controls', 0) == 1 ? 'true' : 'false';?>,
        auto: <?php echo $params->get('auto', 0) == 1 ? 'true' : 'false';?>,
        pause: <?php echo $params->get('pause', 4000);?>,
        minSlides: <?php echo $params->get('minSlides', 1);?>,
        maxSlides: <?php echo $params->get('maxSlides', 1);?>,
        moveSlides: <?php echo $params->get('moveSlides', 0);?>,
        slideWidth: <?php echo $params->get('slideWidth', 0);?>
      });
    });
</script>