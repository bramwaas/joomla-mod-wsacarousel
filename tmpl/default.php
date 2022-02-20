<?php
/**
 * @version $Id: default.php 
 * @package wsacarousel
 * @subpackage wsacarousel Module
 * @copyright Copyright (C) 2018 wsacarousel, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: https://www.waasdorpsoekhan.nl
 * @author email contact@waasdorpsoekhan.nl
 * @developer A.H.C. Waasdorp
 *
 *
 * wsacarousel is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wsacarousel is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WsaCarousel. If not, see <http://www.gnu.org/licenses/>.
 * 0.2.0
 * ook voor eigen javascript 3 wsacarousel
 * 1.0.6 20-2-2022 adjustments for J4
 */
// no direct access
defined('_JEXEC') or die ('Restricted access'); 
use Joomla\CMS\Factory;
$doc = Factory::getDocument ();

if ($params->get('include_twbs_js') == "1") { $carousel_class = 'wsacarousel';} else {$carousel_class = 'carousel';}
$wcag = $params->get('wcag', 1) ? ' tabindex="0"' : ''; 


if(!is_numeric($duration = $params->get('duration'))) $duration = 600;
if(!is_numeric($delay = $params->get('delay'))) $delay = 3000;

/* change duration of transformation
   needs a change in  .emulateTransitionEnd(600) in Carousel.prototype.slide = function (type, next)
   otherwise the slide disappears afte 0.6 sec.
   solved for BS4 no change needed anymore.
*/
if(!is_numeric($slide_width = $params->get('image_width'))) $slide_width = 240;
if(!is_numeric($slide_height = $params->get('image_height'))) $slide_height = 160;
$slide_heightprc = ($slide_width > 0 ) ?  100 * $slide_height / $slide_width : 75;
if ($params->get('twbs_version',4) == "3") {
    $carousel_item_left = 'item.left';
    $carousel_item_right = 'item.right';
    $carousel_item_next = 'item.next';
    $carousel_item_prev = 'item.prev';
    $carousel_control_css = '.'. $carousel_class .'-control{ 
    display: -webkit-box; 
    display: -ms-flexbox;
    display: flex;
    align-items: center;
    justify-content: center;
    filter: alpha(opacity=1);
    opacity: 0.01;}';
     
} else {  /* twbs version = 4.3 */
    $carousel_item_left =  $carousel_class .'-item-left';
    $carousel_item_right =  $carousel_class .'-item-right';
    $carousel_item_next =  $carousel_class .'-item-next';
    $carousel_item_prev =  $carousel_class .'-item-prev';
    $carousel_control_css = '.'. $carousel_class .'-control{
    filter: alpha(opacity=1);
    opacity: 0.01;}';
}

$decl = $carousel_control_css . "
#wsacarousel-loader" . $mid . "
{
 " . $style['slider'] . "
height: auto;
max-width: 100%;
 overflow: hidden;
}
#wsacarousel-loader" . $mid . " .showOnHover {
	opacity: 0;
	-webkit-transition: opacity 200ms ease 50ms;
	transition: opacity 200ms ease 50ms;
}
#wsacarousel-loader" . $mid . ":hover .showOnHover,
#wsacarousel-loader" . $mid . ".focused .showOnHover {
	opacity: 1;
}

#wsacarousel" . $mid . "
{ 
position: relative;
width: " . $count * 100 . "%; 
}

@media (min-width: 768px) {
#wsacarousel-loader" . $mid . "
{
" . $style['slider'] . "
}
#wsacarousel" . $mid . "
{
 width: 100%;
}
}
#wsacarousel-container" . $mid . "  .".  $carousel_class ."-inner .".  $carousel_class ."-caption{
position: " . ($params->get('caption_overlay', 1) == '1' ? 'absolute':'relative') . "; 
bottom: 0;
padding:0;
left: 0;
right: 0;
right:  calc(" . $style['marginr'] . ");
font-size: 12px;
line-height: 15.6px;
background: RGBA(0,0,0,0.65)
}
.wsacarousel-caption {
color: #fff;
text-align: center;
}
#wsacarousel-container" . $mid . " .".  $carousel_class ."-item-inner{
position: relative;
width: " . 100/$count . "%; 
float: left;
}

#wsacarousel-container" . $mid . " .".  $carousel_class ."-item-content{
float: left;
margin-bottom: " . $style['marginb'] . ";
width: 100%;
width:  calc(100% - " . $style['marginr'] . ");
}
#wsacarousel-container" . $mid . " .".  $carousel_class ."-item-height{
float: left;
width: 0;
height: 0;
padding: 0 0 " . $slide_heightprc . "% 0 ;
margin: 0;
padding-bottom: calc(" . $slide_heightprc . "% - " . $slide_heightprc / 100 . "*" . $style['marginr'] . ");
}


#wsacarousel-container" . $mid . " .".  $carousel_class ."-item-img{
" . $style['image'] . "
}
	
#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner > .item {
    -webkit-transition-duration: " . $duration/1000 . "s;
    -moz-transition-duration: " . $duration/1000 . "s; 
    -o-transition-duration: " . $duration/1000 . "s;
    transition-duration: " . $duration/1000 . "s;
}";


if ($count > 1) {
	
$decl = $decl .
"

/* override position and transform in 3.3.x and 4.0.x 
*/
#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner ." . $carousel_item_left . ".active {
  transform: translateX(-" . 100/$count . "%);
}
#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner ." . $carousel_item_right . ".active {
  transform: translateX(" . 100/$count . "%);
}

#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner ." . $carousel_item_next . "  {
  transform: translateX(" . 100/$count . "%)
}
#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner ." . $carousel_item_prev . " {
  transform: translateX(-" . 100/$count . "%)
}

#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner ." . $carousel_item_left . ",
#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner ." . $carousel_item_right . ",
#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner ." . $carousel_item_left . ",
#wsacarousel-container" . $mid . " .".  $carousel_class ."-inner ." . $carousel_item_right . " { 
  transform: translateX(0);
}";
}

$doc->addStyleDeclaration($decl);

if ($count > 1)
{	
$decl =

"
jQuery(document).ready(function() {
jQuery('#wsacarousel" . $mid . " .".  $carousel_class ." .item').each(function(){
  var next = jQuery(this).next();
  if (!next.length) {
    next = jQuery(this).siblings(':first');
  }
  next.children(':first-child').clone().appendTo(jQuery(this));
  " 
;
if ($count > 2)	{
	
$decl = $decl .
"
  for (var i=2;i<". $count . ";i++) {
    next=next.next();
    if (!next.length) {
    	next = jQuery(this).siblings(':first');
  	}
    
    next.children(':first-child').clone().appendTo(jQuery(this));
  }
  ";
}
$decl = $decl .	
"	
});
})
";
$doc->addScriptDeclaration($decl);
}
	

?>

<div class="wsacarousel<?php echo $params->get('moduleclass_sfx') ?>" style="border: 0px !important;">
<div id="wsacarousel-loader<?php echo $mid; ?>" class="wsacarousel-loader wsacarousel-loader-<?php echo $theme ?>"  <?php echo $wcag; ?>>
	<div id="wsacarousel<?php echo $mid; ?>" class=" wsacarousel-<?php echo $theme; echo $params->get('image_centering', 0) ? ' img-vcenter':'' ?>">
		<!-- Container with data-options (animation and wsa-carousel only for info) -->
        <div id="wsacarousel-container<?php echo $mid; ?>" class="<?php echo $carousel_class; ?> slide " data-ride="<?php echo $carousel_class; ?>"
		data-interval="<?php echo $delay + $duration; ?>" 
		data-pause="hover"
		data-wrap="true" 
		data-keyboard="true"
		data-duration="<?php echo $duration; ?>"
		>
		
		<!-- Indicators -->
		<?php /* nog even niet TODO
                        <ol class="<?php echo $carousel_class; ?>-indicators">
                            <li data-target="#slider-container<?php echo $mid; ?>" data-slide-to="0" class="active"></li>
                            <li data-target="#slider-container<?php echo $mid; ?>" data-slide-to="1"></li>
                            <li data-target="#slider-container<?php echo $mid; ?>" data-slide-to="2"></li>
                        </ol>
		 */ ?>
			<!-- Wrapper for slides -->
        	<div id="wsacarousel-inner<?php echo $mid; ?>" class="<?php echo $carousel_class; ?>-inner"   role="listbox">
			<?php $itemnr = 0; 
			 foreach ($slides as $slide) { /* per slide */
					$itemnr++;
          			$rel = (!empty($slide->rel) ? 'rel="'.$slide->rel.'"':''); ?>
          			<div class="<?php echo $carousel_class; ?>-item item item<?php echo $itemnr; if ($itemnr==1) echo " active"; ?>" <?php if($slide->delay > 0) echo 'data-interval="' . $slide->delay  . '" '; ?>><div class="<?php echo $carousel_class; ?>-item-inner">
          			    <div class="<?php echo $carousel_class; ?>-item-content">
          				<?php if($slide->image) { 
          					$action = $params->get('link_image',1);
          					if($action > 1) {
								$desc = $params->get('show_desc') ? 'title="'.(!empty($slide->title) ? htmlspecialchars($slide->title.' ') : '').(!empty($slide->description) ? htmlspecialchars('<small>'.strip_tags($slide->description,"<p><a><b><strong><em><i><u>").'</small>') : '').'"':'';
	          					if($jquery) {
	          						$attr = 'class="image-link" data-'.$desc;
	          						
	          					} else {
	          						$attr = 'rel="lightbox-slider'.$mid.'" '.$desc;
	          					}
							} else {
								$attr = $rel;
							}
          					?>
	            			<?php if (($slide->link && $action==1) || $action>1) { ?>
								<a <?php echo $attr; ?> href="<?php echo ($action>1 ? $slide->image : $slide->link); ?>" target="<?php echo $slide->target; ?>">
							<?php } ?>
								<img class="<?php echo $carousel_class; ?>-item-img" src="<?php echo $slide->image; ?>" alt="<?php echo $slide->alt; ?>" <?php echo (!empty($slide->img_title) ? ' title="'.$slide->img_title.'"':''); ?>"/>
							<?php if (($slide->link && $action==1) || $action>1) { ?>
								</a>
							<?php } ?>
						<?php } ?>
						</div>
						<div class="<?php echo $carousel_class; ?>-item-height"></div>
						<?php if($params->get('slider_source') && ($params->get('show_title') || ($params->get('show_desc') && !empty($slide->description) || ($params->get('show_readmore') && $slide->link)))) { ?>
						<!-- Slide description area: START -->
						<div class="<?php echo $carousel_class; ?>-caption" >
							<?php if($params->get('show_title')) { ?>
							<div class="slide-title">
							<?php if($params->get('link_title') && $slide->link) { ?><a href="<?php echo $slide->link; ?>" target="<?php echo $slide->target; ?>" <?php echo $rel; ?>><?php } ?>
										<?php echo $slide->title; ?>
									<?php if($params->get('link_title') && $slide->link) { ?></a><?php } ?>
							</div>
							<?php } ?>
							
							<?php if($params->get('show_desc')) { ?>
							<div class="slide-text">
									<?php if($params->get('link_desc') && $slide->link) { ?>
									<a href="<?php echo $slide->link; ?>" target="<?php echo $slide->target; ?>" <?php echo $rel; ?>>
										<?php echo strip_tags($slide->description,"<br><span><em><i><b><strong><small><big>"); ?>
									</a>
									<?php } else { ?>
										<?php echo $slide->description; ?>
									<?php } ?>
							</div>
							<?php } ?>
							
							<?php if($params->get('show_readmore') && $slide->link) { ?>
								<a href="<?php echo $slide->link; ?>" target="<?php echo $slide->target; ?>" <?php echo $rel; ?> class="readmore"><?php echo ($params->get('readmore_text',0) ? $params->get('readmore_text') : JText::_('MOD_WSACAROUSEL_READMORE')); ?></a>
							<?php } ?>
						</div>
						<!-- Slide description area: END -->
						<?php } ?>						
						
					</div></div>
                <?php } ?>
        	</div>
        </div>
        <?php if($show->arr || $show->btn) { ?>
        <div id="navigation<?php echo $mid; ?>" class="navigation-container" style="<?php echo $style['navi'] ?>">
        	<?php if($show->arr) { ?>
			<a class="left <?php echo $carousel_class; ?>-control <?php echo $carousel_class; ?>-control-prev" href="#wsacarousel-container<?php echo $mid; ?>" role="button" data-slide="prev">
        	<img id="prev<?php echo $mid; ?>" class="prev-button <?php echo $show->arr==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->prev; ?>" alt="<?php echo $direction == 'rtl' ? JText::_('MOD_WSACAROUSEL_NEXT') : JText::_('MOD_WSACAROUSEL_PREVIOUS'); ?>"<?php echo $wcag; ?> />
			</a>
			<a class="right <?php echo $carousel_class; ?>-control <?php echo $carousel_class; ?>-control-next" href="#wsacarousel-container<?php echo $mid; ?>" role="button" data-slide="next">			<img id="next<?php echo $mid; ?>" class="next-button <?php echo $show->arr==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->next; ?>" alt="<?php echo $direction == 'rtl' ? JText::_('MOD_WSACAROUSEL_PREVIOUS') : JText::_('MOD_WSACAROUSEL_NEXT'); ?>"<?php echo $wcag; ?> />
			</a>
			<?php } ?>
			<?php /* if($show->btn) { ?>
			<img id="play<?php echo $mid; ?>" class="play-button <?php echo $show->btn==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->play; ?>" alt="<?php echo JText::_('MOD_WSACAROUSEL_PLAY'); ?>"<?php echo $wcag; ?> />
			<img id="pause<?php echo $mid; ?>" class="pause-button <?php echo $show->btn==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->pause; ?>" alt="<?php echo JText::_('MOD_WSACAROUSEL_PAUSE'); ?>"<?php echo $wcag; ?> />
			<?php } */ ?>
        </div>
        <?php } ?>
        <?php if($show->idx) { ?>
		<div id="cust-navigation<?php echo $mid; ?>" class="<?php echo $params->get('idx_style', 0) ? 'navigation-numbers' : 'navigation-container-custom' ?> <?php echo $show->idx==2 ? 'showOnHover':'' ?>">
			<?php $i = 0; foreach ($slides as $slide) { 
				?><span class="load-button<?php if ($i == 0) echo ' load-button-active'; ?>"<?php echo $wcag; ?>><?php if($params->get('idx_style')) echo ($i+1) ?></span><?php 
			$i++; } ?>
        </div>
        <?php } ?>
    </div>
</div>
</div>
<div class="wsa<?php echo $carousel_class; ?>-end" style="clear: both"<?php echo $wcag; ?>></div>
