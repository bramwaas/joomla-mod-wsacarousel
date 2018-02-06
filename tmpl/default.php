<?php
/**
 * @version $Id: default.php 
 * @package wsacarousel
 * @subpackage wsacarousel Module
 * @copyright Copyright (C) 2018 wsacarousel, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: https://www.waasdorpsoekhan.nl
 * @author email contact@waasdorpsoekhan.nl
 * @developer A.H.C. waasdorp
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
 * along with DJ-ImageSlider. If not, see <http://www.gnu.org/licenses/>.
 *
 */
// no direct access
defined('_JEXEC') or die ('Restricted access'); 
use Joomla\CMS\Factory;
$doc = Factory::getDocument ();

$wcag = $params->get('wcag', 1) ? ' tabindex="0"' : ''; 

if(!is_numeric($duration = $params->get('duration'))) $duration = 0;
if(!is_numeric($delay = $params->get('delay'))) $delay = 3000;

/* change duration of transformation
   needs als a change in  .emulateTransitionEnd(600) in Carousel.prototype.slide = function (type, next)
   otherwise the slide disappears afte 0.6 sec.
*/
if(!is_numeric($slide_width = $params->get('image_width'))) $slide_width = 240;
if(!is_numeric($slide_height = $params->get('image_height'))) $slide_height = 160;

$decl = "
#wsacarousel-loader" . $mid . "
{
 " . $style['slider'] . "
 max-width: 100%;
 overflow: hidden;
}

#wsacarousel" . $mid . "
{
width: " . $count * 100 . "%; 
}

@media (min-width: 768px) {
#wsacarousel" . $mid . "
{
 width: 100%;
}
}
#wsacarousel-container" . $mid . "   .carousel-inner .carousel-caption{
position: relative; /* or absolute */
bottom: 0;
padding:0;

}
#wsacarousel-container" . $mid . " .carousel-item-inner{
position: relative;
width: " . 100/$count . "%;
float: left;
}
#wsacarousel-container" . $mid . " .carousel-item-img{
" . $style['image'] . "
}
@media all and (transform-3d), (-webkit-transform-3d) {
#wsacarousel-container" . $mid . " .carousel-inner > .item {
    -webkit-transition-duration: " . $duration/1000 . "s;
    -moz-transition-duration: " . $duration/1000 . "s; 
    -o-transition-duration: " . $duration/1000 . "s;
    transition-duration: " . $duration/1000 . "s;
}";


if ($count > 1) {
	
$decl = $decl .
"	
/* override position and transform in 3.3.x */
#wsacarousel-container" . $mid . " .carousel-inner .item.left.active {
  transform: translateX(-" . 100/$count . "%);
}
#wsacarousel-container" . $mid . " .carousel-inner .item.right.active {
  transform: translateX(" . 100/$count . "%);
}

#wsacarousel-container" . $mid . " .carousel-inner .item.next {
  transform: translateX(" . 100/$count . "%)
}
#wsacarousel-container" . $mid . " .carousel-inner .item.prev {
  transform: translateX(-" . 100/$count . "%)
}

#wsacarousel-container" . $mid . " .carousel-inner .item.right,
#wsacarousel-container" . $mid . " .carousel-inner .item.left { 
  transform: translateX(0);
}";
}
$decl = $decl .
"
}";
$doc->addStyleDeclaration($decl);

if ($count > 1)
{	
$decl =

"
jQuery(document).ready(function() {
jQuery('#wsacarousel" . $mid . " .carousel .item').each(function(){
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

<div style="border: 0px !important;">
<div id="wsacarousel-loader<?php echo $mid; ?>" class="wsacarousel-loader wsacarousel-loader-<?php echo $theme ?>"  <?php echo $wcag; ?>>
	<div id="wsacarousel<?php echo $mid; ?>" class="wsacarousel wsacarousel-<?php echo $theme; echo $params->get('image_centering', 0) ? ' img-vcenter':'' ?>">
		<!-- Container with data-options (animation and wsa-carousel only for info) -->
        <div id="wsacarousel-container<?php echo $mid; ?>" class="carousel slide " data-ride="carousel"
		data-interval="<?php echo $delay; ?>" 
		data-pause="hover"
		data-wrap="true" 
		data-keyboard="true"
		>
		
		<!-- Indicators -->
		<?php /* nog even niet TODO
                        <ol class="carousel-indicators">
                            <li data-target="#slider-container<?php echo $mid; ?>" data-slide-to="0" class="active"></li>
                            <li data-target="#slider-container<?php echo $mid; ?>" data-slide-to="1"></li>
                            <li data-target="#slider-container<?php echo $mid; ?>" data-slide-to="2"></li>
                        </ol>
		 */ ?>
			<!-- Wrapper for slides -->
        	<div id="wsacarousel-inner<?php echo $mid; ?>" class="carousel-inner"   role="listbox">
			<?php $itemnr = 0; 
			 foreach ($slides as $slide) { /* per slide */
					$itemnr++;
          			$rel = (!empty($slide->rel) ? 'rel="'.$slide->rel.'"':''); ?>
          			<div class="carousel-item item item<?php echo $itemnr; if ($itemnr==1) echo " active"; ?>"><div class="carousel-item-inner">
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
								<img class="carousel-item-img" src="<?php echo $slide->image; ?>" alt="<?php echo $slide->alt; ?>" <?php echo (!empty($slide->img_title) ? ' title="'.$slide->img_title.'"':''); ?>"/>
							<?php if (($slide->link && $action==1) || $action>1) { ?>
								</a>
							<?php } ?>
						<?php } ?>
						<?php if($params->get('slider_source') && ($params->get('show_title') || ($params->get('show_desc') && !empty($slide->description) || ($params->get('show_readmore') && $slide->link)))) { ?>
						<!-- Slide description area: START -->
						<div class="carousel-caption" >
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
			<a class="left carousel-control" href="#slider-container<?php echo $mid; ?>" role="button" data-slide="prev">
        	<img id="prev<?php echo $mid; ?>" class="prev-button <?php echo $show->arr==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->prev; ?>" alt="<?php echo $direction == 'rtl' ? JText::_('MOD_WSACAROUSEL_NEXT') : JText::_('MOD_WSACAROUSEL_PREVIOUS'); ?>"<?php echo $wcag; ?> />
			</a>
			<a class="right carousel-control" href="#slider-container<?php echo $mid; ?>" role="button" data-slide="next">			<img id="next<?php echo $mid; ?>" class="next-button <?php echo $show->arr==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->next; ?>" alt="<?php echo $direction == 'rtl' ? JText::_('MOD_WSACAROUSEL_PREVIOUS') : JText::_('MOD_WSACAROUSEL_NEXT'); ?>"<?php echo $wcag; ?> />
			</a>
			<?php } ?>
			<?php if($show->btn) { ?>
			<img id="play<?php echo $mid; ?>" class="play-button <?php echo $show->btn==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->play; ?>" alt="<?php echo JText::_('MOD_WSACAROUSEL_PLAY'); ?>"<?php echo $wcag; ?> />
			<img id="pause<?php echo $mid; ?>" class="pause-button <?php echo $show->btn==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->pause; ?>" alt="<?php echo JText::_('MOD_WSACAROUSEL_PAUSE'); ?>"<?php echo $wcag; ?> />
			<?php } ?>
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
<div class="wsacarousel-end" style="clear: both"<?php echo $wcag; ?>></div>
