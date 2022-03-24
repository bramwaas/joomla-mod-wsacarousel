<?php
/**
 * @version $Id: WsaCarousel.php 
 * @package wsacarousel
 * @subpackage wsacarousel Module
 * @copyright Copyright (C) 2018 -2022 A.H.C. Waasdorp, All rights reserved.
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
 * along with DJ-ImageSlider. If not, see <http://www.gnu.org/licenses/>.
 * 0.0.7
 * 0.2.0 slide delay added.
 * 1.0.6 20-2-2022 adjustments for J4
 * 1.0.7
 * 1.10.0 4-3-2022 using bootstrap css icons  as default navigation icons
 *        8-3-2022 copied from helper.php to comply with Joomla namespaced model
 *        10-3-2022 added doc blocks.
 *        14-3-2022 added svg as default images for navigatition
 */
namespace WaasdorpSoekhan\Module\Wsacarousel\Site\Helper;
// no direct access
defined('_JEXEC') or die ('Restricted access');


use Joomla\CMS\Factory;
// use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentHelperRoute;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;

/**
 * Helper for mod_wsacarousel
 *
 * @since  1.1
 */
class WsacarouselHelper
{
    /**
     * Gets images from the folder
     *
     * @param   mixed  &$params  The parameters set in the administrator section
     *
     * @return  array  $slides  Array of slides sorted in the correct order 
     *
     * @since   1.1
     */
     static function getImagesFromFolder(&$params) {
    	if(!is_numeric($max = $params->get('max_images'))) $max = 20;
        $folder = $params->get('image_folder');
        if(!$dir = @opendir($folder)) return null;
        while (false !== ($file = readdir($dir)))
        {
            if (preg_match('/.+\.(jpg|jpeg|gif|png)$/i', $file)) {
            	// check with getimagesize() which attempts to return the image mime-type 
            	$path = Path::clean(JPATH_ROOT.DS.$folder.DS.$file);
            	if(getimagesize($path)!==FALSE) $files[filemtime($path).$file] = $file;
			}
        }
        closedir($dir);
        
        $sort = $params->get('sort_by');
        
        switch($sort) {
        	case 0:
        		shuffle($files);
        		break;
        	case 3:
        	case 4:
        		ksort($files);
        		break;
        	default:
        		natcasesort($files);
        		break;
        }
        	
        if($sort == 2 || $sort == 4) {
        	$files = array_reverse($files);
        } 
        
		$images = array_slice($files, 0, $max);
		
		$target = self::getSlideTarget($params->get('link'));
		
		foreach($images as $image) {
			$slides[] = (object) array('title'=>'', 'description'=>'', 'image'=>$folder.'/'.$image, 'link'=>$params->get('link'), 'alt'=>$image, 'target'=>$target);
		}
				
		return $slides;
    }
	
    /**
     * Gets images from the component
     *
     * @param   mixed  &$params  The parameters set in the administrator section
     *
     * @return  array  $slides  Array of slides sorted in the correct order
     *
     * @since   1.1
     */
    static function getImagesFromWsaCarousel(&$params) {
	   if(!is_numeric($max = $params->get('max_images'))) $max = 20;
        $catid = $params->get('category',0);
		
		// build query to get slides
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__wsacarousel AS a');
		if (is_numeric($catid)) {
			$query->where('a.catid = ' . (int) $catid);
		}
		// Filter by start and end dates.
		$nullDate	= $db->Quote($db->getNullDate());
		$nowDate	= $db->Quote(Factory::getDate()->format($db->getDateFormat()));
		$query->where('a.published = 1');
		$query->where('(a.publish_up IS NULL OR a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
		$query->where('(a.publish_down IS NULL OR a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');
		
		switch($params->get('sort_by',1)) {
			case 1:
				$query->order('a.ordering ASC');
				break;
			case 2:
				$query->order('a.ordering DESC');
				break;
			case 3:
				$query->order('a.publish_up ASC');
				break;
			case 4:
				$query->order('a.publish_up DESC');
				break;
			default:
				$query->order('RAND()');
				break;
		}

		$db->setQuery($query, 0 , $max);
		$slides = $db->loadObjectList();
		
		foreach($slides as $slide){
			$slide->params = new Registry($slide->params);
			$slide->link = self::getSlideLink($slide);
			$slide->description = self::getSlideDescription($slide, $params->get('limit_desc'));
			$slide->alt = $slide->params->get('alt_attr', $slide->title);
			$slide->img_title = $slide->params->get('title_attr');
			$slide->target = $slide->params->get('link_target','');
			$slide->rel = $slide->params->get('link_rel','');
	
			if(empty($slide->target)) $slide->target = self::getSlideTarget($slide->link);
		}
		
		return $slides;
    }
    /**
     * Gets link for slide
     *
     * @param   mixed  &$slide  The slide object
     *
     * @return  string  $link  String with url of the link
     *
     * @since   1.1
     */
	static function getSlideLink(&$slide) {
		$link = '';
		$db = Factory::getDbo();
		$app = Factory::getApplication();
		
		switch($slide->params->get('link_type', '')) {
			case 'menu':
				if ($menuid = $slide->params->get('link_menu',0)) {
					
					$menu = $app->getMenu();
					$menuitem = $menu->getItem($menuid);
					if($menuitem) switch($menuitem->type) {
						case 'component': 
							$link = Route::_($menuitem->link.'&Itemid='.$menuid);
							break;
						case 'url':
						case 'alias':
							$link = Route::_($menuitem->link);
							break;
					}	
				}
				break;
			case 'url':
				if($itemurl = $slide->params->get('link_url',0)) {
					$link = Route::_($itemurl);
				}
				break;
			case 'article':
				if ($artid = $slide->params->get('id',$slide->params->get('link_article',0))) {
					BaseDatabaseModel::addIncludePath(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'models');
					$model = BaseDatabaseModel::getInstance('Articles', 'ContentModel', array('ignore_request'=>true));
					$model->setState('params', $app->getParams());
					$model->setState('filter.article_id', $artid);
					$model->setState('filter.article_id.include', true); // Include
					$items = $model->getItems();
					if($items && $item = $items[0]) {
						$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
						$link = Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid));
						$slide->introtext = $item->introtext;
					}
				}
				break;
		}
		
		return $link;
	}
	/**
	 * Gets description for slide and truncate if necessary
	 *
	 * @param   mixed  $slide  The slide object
	 * @param   int    $limit  Limit number of charcters for description.
	 *
	 * @return  string  $desc  String with description
	 *
	 * @since   1.1
	 */
	static function getSlideDescription($slide, $limit) {
		$sparams = new Registry($slide->params);
		if($sparams->get('link_type','')=='article' && empty($slide->description)){ // if article and no description then get introtext as description
			if(isset($slide->introtext)) $slide->description = $slide->introtext;
		}
		
		$desc = strip_tags($slide->description);
		
		if($limit && $limit - strlen($desc) < 0) {
			// don't cut in the middle of the word unless it's longer than 20 chars
			if($pos = strpos($desc, ' ', $limit)) $limit = ($pos - $limit > 20) ? $limit : $pos;
			// cut text and add dots
			if(function_exists('mb_substr')) {
				$desc = mb_substr($desc, 0, $limit);
			} else {
				$desc = substr($desc, 0, $limit);
			}
			if(preg_match('/[a-zA-Z0-9]$/', $desc)) $desc.='&hellip;';
			$desc = '<p>'.nl2br($desc).'</p>';
		} else { // no limit or limit greater than description
			$desc = $slide->description;
		}

		return $desc;
	}
	/**
	 * Truncate text
	 *
	 * @param   string  $text  The slide object
	 * @param   int    $limit  Limit number of charcters for description.
	 *
	 * @return  string  $link  String with description
	 *
	 * @since   1.1
	 */
	private function truncateDescription($text, $limit) {
		$text = preg_replace('/{djmedia\s*(\d*)}/i', '', $text);
		$desc = strip_tags($text);
		if($limit && $limit - strlen($desc) < 0) {
			$desc = substr($desc, 0, $limit);
			// don't cut in the middle of the word unless it's longer than 20 chars
			if($pos = strrpos($desc, ' ')) {
				$limit = ($limit - $pos > 20) ? $limit : $pos;
				$desc = substr($desc, 0, $limit);
			}
			// cut text and add dots
			if(preg_match('/[a-zA-Z0-9]$/', $desc)) $desc.='&hellip;';
			$desc = '<p>'.nl2br($desc).'</p>';
		} else { // no limit or limit greater than description
			$desc = $text;
		}

		return $desc;
	}
	/**
	 * Gets animation options from the parameters
	 *
	 * @param   mixed  &$params  The parameters set in the administrator section
	 *
	 * @return  mixed  $options  JSON Array of carousel options
	 *
	 * @since   1.1
	 */
	static function getAnimationOptions(&$params) {
		$transition = $params->get('effect');
		$easing = $params->get('effect_type');
		if(!is_numeric($duration = $params->get('duration'))) $duration = 0;
		if(!is_numeric($delay = $params->get('delay'))) $delay = 3000;
		$autoplay = $params->get('autoplay');
		$looponce = $params->get('looponce', 0);
		if($params->get('slider_type')==2 && !$duration) {
			$transition = 'Sine';
			$easing = 'easeInOut';
			$duration = 400;
		} else switch($transition){
			case 'Linear':
				$easing = '';
				$transition = 'linear';
				if(!$duration) $duration = 400;
				break;
			case 'Back':
				if(!$easing) $easing = 'easeIn';
				if(!$duration) $duration = 400;
				break;
			case 'Bounce':
				if(!$easing) $easing = 'easeOut';
				if(!$duration) $duration = 800;
				break;
			case 'Elastic':
				if(!$easing) $easing = 'easeOut';
				if(!$duration) $duration = 1000;
				break;
			default: 
				if(!$easing) $easing = 'easeInOut';
				if(!$duration) $duration = 400;
		}
		// add transition duration to delay
		$delay = $delay + $duration;
		$css3transition = $params->get('css3') ? self::getCSS3Transition($transition, $easing) : '';
		
        // Joomla 3 - jQuery
		if($transition=='ease') {
				$transition = 'swing';
				$easing = '';
		}
		$transition = $easing.$transition;
		
		
		$options = json_encode(array('auto' => $autoplay, 'looponce' => $looponce, 'transition' => $transition, 'css3transition' => $css3transition, 'duration' => $duration, 'delay' => $delay));
		
		return $options;
	}
	
	/**
	 * Gets css3 transition from transition name and easing
	 *
	 * @param   string  $transition  One of the transition names.
	 * @param   string  $easing      The easing type
	 *
	 * @return  string  $options   css3 transition.
	 *
	 * @since   1.1
	 */
	
	static function getCSS3Transition($transition, $easing) {
	    $doc = Factory::getDocument ();
	    
		switch($easing) {
			
			case '': return 'linear';
			case 'easeInOut':
				switch($transition) {
					case 'Quad': 	return 'cubic-bezier(0.455, 0.030, 0.515, 0.955)';
					case 'Cubic': 	return 'cubic-bezier(0.645, 0.045, 0.355, 1.000)';
					case 'Quart':	return 'cubic-bezier(0.645, 0.045, 0.355, 1.000)';
					case 'Quint': 	return 'cubic-bezier(0.860, 0.000, 0.070, 1.000)';
					case 'Sine': 	return 'cubic-bezier(0.445, 0.050, 0.550, 0.950)';
					case 'Expo': 	return 'cubic-bezier(1.000, 0.000, 0.000, 1.000)';
					case 'Circ': 	return 'cubic-bezier(0.785, 0.135, 0.150, 0.860)';
					case 'Back': 	return 'cubic-bezier(0.680, -0.550, 0.265, 1.550)';
					default: 		return 'ease-in-out';
				}
			case 'easeOut':
				switch($transition) {
					case 'Quad': 	return 'cubic-bezier(0.250, 0.460, 0.450, 0.940)';
					case 'Cubic': 	return 'cubic-bezier(0.215, 0.610, 0.355, 1.000)';
					case 'Quart':	return 'cubic-bezier(0.165, 0.840, 0.440, 1.000)';
					case 'Quint': 	return 'cubic-bezier(0.230, 1.000, 0.320, 1.000)';
					case 'Sine': 	return 'cubic-bezier(0.390, 0.575, 0.565, 1.000)';
					case 'Expo': 	return 'cubic-bezier(0.190, 1.000, 0.220, 1.000)';
					case 'Circ': 	return 'cubic-bezier(0.075, 0.820, 0.165, 1.000)';
					case 'Back': 	return 'cubic-bezier(0.175, 0.885, 0.320, 1.275)';
					default: 		return 'ease-out';
				}
			case 'easeIn':
				switch($transition) {
					case 'Quad': 	return 'cubic-bezier(0.550, 0.085, 0.680, 0.530)';
					case 'Cubic': 	return 'cubic-bezier(0.550, 0.055, 0.675, 0.190)';
					case 'Quart':	return 'cubic-bezier(0.895, 0.030, 0.685, 0.220)';
					case 'Quint': 	return 'cubic-bezier(0.755, 0.050, 0.855, 0.060)';
					case 'Sine': 	return 'cubic-bezier(0.470, 0.000, 0.745, 0.715)';
					case 'Expo': 	return 'cubic-bezier(0.950, 0.050, 0.795, 0.035)';
					case 'Circ': 	return 'cubic-bezier(0.600, 0.040, 0.980, 0.335)';
					case 'Back': 	return 'cubic-bezier(0.600, -0.280, 0.735, 0.045)';
					default: 		return 'ease-in';
				}
			default: return 'ease';
		}
	}
	/**
	 * Gets link target for slide
	 *
	 * @param   string  $link  The slide object
	 *
	 * @return  string  $target  String with deafault target for the link
	 *
	 * @since   1.1
	 */
	static function getSlideTarget($link) {
		if(preg_match("/^http/",$link) && !preg_match("/^".str_replace(array('/','.','-'), array('\/','\.','\-'),Uri::base())."/",$link)) {
			$target = '_blank';
		} else {
			$target = '_self';
		}
		
		return $target;
	}
	/**
	 * Gets navigation variables for carousel
	 *
     * @param   mixed  &$params  The parameters set in the administrator section
	 * @param   int    $mid  The module id
	 *
	 * @return  array  $navi  Array with several navigation params
	 *
	 * @since   1.1
	 */
	static function getNavigation(&$params, $mid) {
	    /* default <!--! Font Awesome Free 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2022 Fonticons, Inc. -->
	     images */
	    $doc = Factory::getDocument ();
	    $theme = $params->get('theme', 'default');
	    $nav_buttons_style = $params->get('nav_buttons_style');
	    switch ($nav_buttons_style == '1')
	    {
	        case 0:
	        break; 
	        case 2: {
	            $prev= JPATH_ROOT . '/media/mod_wsacarousel/images/prev.png';
	            $next= JPATH_ROOT . '/media/mod_wsacarousel/images/next.png';
	            $pause= JPATH_ROOT . '/media/mod_wsacarousel/images/pause.png';
	            $play= JPATH_ROOT . '/media/mod_wsacarousel/images/play.png';
	        }
	        break;
	        case 1: {
	        $prev = $params->get('left_arrow');
	        $next = $params->get('right_arrow');
	        $play = $params->get('play_button');
	        $pause = $params->get('pause_button');
	        if(empty($prev) || !file_exists(JPATH_ROOT.DS.$prev)) $prev = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="white" stroke="#010101" stroke-width="2" stroke-opacity="0.5"  class="bi bi-chevron-left" viewBox="0 0 320 512">
		  <path d="M224 480c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25l192-192c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25L77.25 256l169.4 169.4c12.5 12.5 12.5 32.75 0 45.25C240.4 476.9 232.2 480 224 480z"/>
          </svg>');
	        if(empty($next) || !file_exists(JPATH_ROOT.DS.$next)) $next = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="white" stroke="#010101" stroke-width="2" stroke-opacity="0.5"  class="bi bi-chevron-right" viewBox="0 0 320 512">
		  <path d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"/>
		  </svg>');
	        }
	       break;  
	    }
	    if(empty($play) || !file_exists(JPATH_ROOT.DS.$play)) $play = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="white" stroke="#010101" stroke-width="2" stroke-opacity="0.5"  class="bi bi-play-fill" viewBox="0 0 384 512">
		<path d="M361 215C375.3 223.8 384 239.3 384 256C384 272.7 375.3 288.2 361 296.1L73.03 472.1C58.21 482 39.66 482.4 24.52 473.9C9.377 465.4 0 449.4 0 432V80C0 62.64 9.377 46.63 24.52 38.13C39.66 29.64 58.21 29.99 73.03 39.04L361 215z"/>
		</svg>');
	    if(empty($pause) || !file_exists(JPATH_ROOT.DS.$pause)) $pause = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="white" stroke="#010101" stroke-width="2" stroke-opacity="0.5" class="bi bi-play-fill" viewBox="0 0 320 512">
		<path d="M272 63.1l-32 0c-26.51 0-48 21.49-48 47.1v288c0 26.51 21.49 48 48 48L272 448c26.51 0 48-21.49 48-48v-288C320 85.49 298.5 63.1 272 63.1zM80 63.1l-32 0c-26.51 0-48 21.49-48 48v288C0 426.5 21.49 448 48 448l32 0c26.51 0 48-21.49 48-48v-288C128 85.49 106.5 63.1 80 63.1z"/>
		</svg>');
	    $navi = (object) array('nav_buttons_style'=>$nav_buttons_style , 'prev'=>$prev,'next'=>$next,'play'=>$play,'pause'=>$pause);
	    
	    return $navi;
	}
	/**
	 * Gets styles for some carousel elements
	 *
	 * @param   mixed  &$params  The parameters set in the administrator section
	 * @param   int    $slidecnt  The number of slides found mximize number to display
	 *
	 * @return  array  $navi  Array with several navigation params
	 *
	 * @since   1.1
	 */
	static function getStyles(&$params, $slidecnt) {
		if(!is_numeric($slide_width = $params->get('image_width'))) $slide_width = 240;
		if(!is_numeric($slide_height = $params->get('image_height'))) $slide_height = 180;
		if(!is_numeric($max = $params->get('max_images'))) $max = 20;
		if(!is_numeric($vicnt = $params->get('visible_images'))) $vicnt = 2;
		if(!is_numeric($spacing = $params->get('space_between_images'))) $spacing = 0;
		if($vicnt>$slidecnt) $vicnt = $slidecnt;
		if($vicnt<1) $vicnt = 1;
		if($vicnt>$max) $vicnt = $max;
		
		
		$desc_width = $params->get('desc_width', $slide_width);
		if(strstr($desc_width, '%') == false && $desc_width > $slide_width) $desc_width = $slide_width;
		$desc_bottom = $params->get('desc_bottom', 0);
		$desc_left = $params->get('desc_horizontal', 0);
		$arrows_top = $params->get('arrows_top', '50%');
		$arrows_horizontal = $params->get('arrows_horizontal', 5);
		
//		switch($params->get('slider_type',0)){ always horizontal 
				$slider_width = $slide_width * $vicnt + $spacing * ($vicnt - 1);
				$slider_height = $slide_height;
				$image_width = 'width: 100%';
				$image_height = 'height: 100%';
				$padding_right = $spacing;
				$padding_bottom = 0;

		
		if(strstr($desc_width, '%') == false) $desc_width = (($desc_width / $slide_width) * 100) .'%';
		if(strstr($desc_left, '%') == false) $desc_left = (($desc_left / $slide_width) * 100) .'%';
		if(strstr($desc_bottom, '%') == false) $desc_bottom = (($desc_bottom / $slide_height) * 100) .'%';
		if(strstr($arrows_top, '%') == false) $arrows_top = (($arrows_top / $slider_height) * 100) .'%';
		if(strstr($arrows_horizontal, '%') == false) $arrows_horizontal = (($arrows_horizontal / $slider_width) * 100) .'%';
		
		if($params->get('fit_to')==1) {
			$image_width = 'width: 100%';
			$image_height = 'height: auto';
		} else if($params->get('fit_to')==2) {
			$image_width = 'width: auto';
			$image_height = 'height: 100%';
		}
		
		$style = array();
		$style['slrwidth'] = ($params->get('full_width', 0) )? '100%' : $slider_width .'px';
		$style['sldwidth'] = ($params->get('full_width', 0) )? '100%' : $slide_width .'px';
		$style['sldheight'] = ($params->get('full_width', 0) )? 'auto' :$slide_height . 'px';
		$style['image'] = $image_width.'; '.$image_height.'; object-fit: contain; ' . 
		                  (($params->get('image_centering', 0))? '' :'object-position: 50% top;');
		$style['aspectratio'] =  $slide_width  /  $slide_height; 
		$style['vicnt'] = $vicnt;
		
		$style['navi'] = 'top: '.$arrows_top.'; margin: 0 '.$arrows_horizontal.';';
		$style['desc'] = 'bottom: '.$desc_bottom.'; left: '.$desc_left.'; width: '.$desc_width.';';
		if($params->get('direction') == 'rtl') {
			$style['slide'] = 'margin: 0 0 '.$padding_bottom.'px '.$padding_right.'px !important; height: '.$slide_height.'px; width: '.$slide_width.'px;';
			$style['marginr'] = 0;
			$style['marginb'] = $padding_bottom . 'px';
			$style['marginl'] = $padding_right .'px';
		} else {
			$style['slide'] = 'margin: 0 '.$padding_right.'px '.$padding_bottom.'px 0 !important; height: '.$slide_height.'px; width: '.$slide_width.'px;';
			$style['marginr'] = $padding_right .'px';
			$style['marginb'] = $padding_bottom . 'px';
			$style['marginl'] = 0;
			
		}
		
		return $style;
	}

}
