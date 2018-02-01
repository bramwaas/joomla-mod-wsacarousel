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


$attribs = array('id'=>'bootstrap.min.css', 'integrity' => 'sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u', 'crossorigin' => 'anonymous');
//$this->addStyleSheet('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', 'text/css', null,  $attribs);
$doc->addStyleSheet('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array('version'=>'3.3.7'),  $attribs);

$doc->addScript("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"> , array('version'=>'3.3.7'), array('id'=>'caption.js', 'defer'=>'defer')); // defer .  	
$output = "
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

";
$doc->addCustomTag ( $output );


/* change and nr of slides transition with style */
/* TODO
".carousel-inner > .item {
    position: relative;
    display: none;
    -webkit-transition: 0.6s ease-in-out left;
    -moz-transition: 0.6s ease-in-out left;
    -o-transition: 0.6s ease-in-out left;
    transition: 0.6s ease-in-out left;
}"
*/



?>

<div style="border: 0px !important;">
<div id="djslider-loader<?php echo $mid; ?>" class="djslider-loader djslider-loader-<?php echo $theme ?>" data-animation='<?php echo $animationOptions ?>' data-djslider='<?php echo $moduleSettings ?>'<?php echo $wcag; ?>>
	<div id="djslider<?php echo $mid; ?>" class="djslider djslider-<?php echo $theme; echo $params->get('image_centering', 0) ? ' img-vcenter':'' ?>" style="<?php echo $style['slider'] ?>">
		<!-- Container -->
        <div id="slider-container<?php echo $mid; ?>" class="carousel slide slider-container" data-ride="carousel"
		<!-- twbs data options -->
		data-interval="3000" 
		data-pause="hover"
		data-wrap="true" 
		data-keyboard="true"
		<!-- twbs style options -->
		data-animation='<?php echo $animationOptions ?>'
		data-djslider='<?php echo $moduleSettings ?>'
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
        	<div id="slider<?php echo $mid; ?>" class="carousel-inner djslider-in"   role="listbox">
			$itemnr = 0;          
			<?php foreach ($slides as $slide) { /* per slide */
					$itemnr++;
          			$rel = (!empty($slide->rel) ? 'rel="'.$slide->rel.'"':''); ?>
          			<div class="item item<?php echo $itemnr; if ($itemnr==1) {echo " active"}; ?>" style"="<?php echo $style['slide'] ?>">
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
								<img class="dj-image" src="<?php echo $slide->image; ?>" alt="<?php echo $slide->alt; ?>" <?php echo (!empty($slide->img_title) ? ' title="'.$slide->img_title.'"':''); ?> style="<?php echo $style['image'] ?>"/>
							<?php if (($slide->link && $action==1) || $action>1) { ?>
								</a>
							<?php } ?>
						<?php } ?>
						<?php if($params->get('slider_source') && ($params->get('show_title') || ($params->get('show_desc') && !empty($slide->description) || ($params->get('show_readmore') && $slide->link)))) { ?>
						<!-- Slide description area: START -->
						<div class="carousel-caption slide-desc" style="<?php echo $style['desc'] ?>">
						  <div class="slide-desc-in">	
							<div class="slide-desc-bg slide-desc-bg-<?php echo $theme ?>"></div>
							<div class="slide-desc-text slide-desc-text-<?php echo $theme ?>">
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
								<a href="<?php echo $slide->link; ?>" target="<?php echo $slide->target; ?>" <?php echo $rel; ?> class="readmore"><?php echo ($params->get('readmore_text',0) ? $params->get('readmore_text') : JText::_('MOD_DJIMAGESLIDER_READMORE')); ?></a>
							<?php } ?>
							<div style="clear: both"></div>
							</div>
						  </div>
						</div>
						<!-- Slide description area: END -->
						<?php } ?>						
						
					</div>
                <?php } ?>
        	</div>
        </div>
        <?php if($show->arr || $show->btn) { ?>
        <div id="navigation<?php echo $mid; ?>" class="navigation-container" style="<?php echo $style['navi'] ?>">
        	<?php if($show->arr) { ?>
			<a class="left carousel-control" href="#slider-container<?php echo $mid; ?>" role="button" data-slide="prev">
        	<img id="prev<?php echo $mid; ?>" class="prev-button <?php echo $show->arr==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->prev; ?>" alt="<?php echo $direction == 'rtl' ? JText::_('MOD_DJIMAGESLIDER_NEXT') : JText::_('MOD_DJIMAGESLIDER_PREVIOUS'); ?>"<?php echo $wcag; ?> />
			</a>
			<a class="right carousel-control" href="#slider-container<?php echo $mid; ?>" role="button" data-slide="next">			<img id="next<?php echo $mid; ?>" class="next-button <?php echo $show->arr==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->next; ?>" alt="<?php echo $direction == 'rtl' ? JText::_('MOD_DJIMAGESLIDER_PREVIOUS') : JText::_('MOD_DJIMAGESLIDER_NEXT'); ?>"<?php echo $wcag; ?> />
			</a>
			<?php } ?>
			<?php if($show->btn) { ?>
			<img id="play<?php echo $mid; ?>" class="play-button <?php echo $show->btn==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->play; ?>" alt="<?php echo JText::_('MOD_DJIMAGESLIDER_PLAY'); ?>"<?php echo $wcag; ?> />
			<img id="pause<?php echo $mid; ?>" class="pause-button <?php echo $show->btn==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->pause; ?>" alt="<?php echo JText::_('MOD_DJIMAGESLIDER_PAUSE'); ?>"<?php echo $wcag; ?> />
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
<div class="djslider-end" style="clear: both"<?php echo $wcag; ?>></div>
