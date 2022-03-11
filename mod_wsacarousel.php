<?php
/**
 * @version $Id: mod_wsacarousel.php 
 * @package wsacarousel
 * @subpackage wsacarousel Module
 * @copyright Copyright (C) 2018 - 2022 AHC Waasdorp, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: https://www.waasdorpsoekhan.nl
 * @author email contact@waasdorpsoekhan.nl
 * @developer AHC Waasdorp
 *
 *
 * WsaCarousel is free software: you can redistribute it and/or modify
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
 * 7-2-2018 added J3.8 J4.0 namespaces deleted mootools and refs to JoomlaVersion < 3.0
 * 0.0.9
 * 26-1-2019 popper v 1.14.6 for compatibility with bootstrap 4.2.1
 * 0.1.0
 * 0.2.0 15-2-2019
 * 1.0.6 20-2-2022 adjustments for J4
 * 1.1.0 adjustments for bootstrap 5
 *   magnific popup 
 *   adjustments to comply with Joomla namespaced model: copied WsacarouselHelper.php from helper.php 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use WaasdorpSoekhan\Module\Wsacarousel\Site\Helper\WsacarouselHelper;

// Include the syndicate functions it thea are not autoloaded only once
if (!class_exists('WaasdorpSoekhan\Module\Wsacarousel\Site\Helper\WsacarouselHelper')) {
//   echo '<!-- class WsacarouselHelper not autoloaded -->';  
   require_once (dirname(__FILE__).DS.'src'.DS.'Helper'.DS.'WsacarouselHelper.php');
   class_alias('WaasdorpSoekhan\Module\Wsacarousel\Site\Helper\WsacarouselHelper', 'WsacarouselHelper');
}
$app = Factory::getApplication();
$document = Factory::getDocument();
$mid = $module->id;
$direction = $document->direction;
$asset_dir = Uri::root(true)."/modules/mod_wsacarousel/assets/";

// taking the slides from the source
if($params->get('slider_source')==1) {
	if(!ComponentHelper::isEnabled('com_wsacarousel', true)){
		$app->enqueueMessage(Text::_('MOD_WSACAROUSEL_NO_COMPONENT'),'notice');
		return;
	}
	$slides = WsacarouselHelper::getImagesFromWsaCarousel($params);
	if($slides==null) {
		$app->enqueueMessage(Text::_('MOD_WSACAROUSEL_NO_CATEGORY_OR_ITEMS'),'notice');
		return;
	}
} else {
	$slides = WsacarouselHelper::getImagesFromFolder($params);
	if($slides==null) {
		$app->enqueueMessage(Text::_('MOD_WSACAROUSEL_NO_CATALOG_OR_FILES'),'notice');
		return;
	}
}
$slidecnt = count($slides);
//TODO only used in Helper.
//$slider_type = $params->get('slider_type',0);

$css = $asset_dir.'css/wsacarousel.css'; // module css
//$css = Uri::root(true).'/templates/'.$app->getTemplate().'/css/wsacarousel.css'; //TODO do we need template css ???
if( File::exists(JPATH_ROOT . DS . $css) ) {
   $document->addStyleSheet($css);
}
if($direction == 'rtl') { // load rtl css if exists 
	$css_rtl = File::stripExt($css).'_rtl.css';
	if(File::exists(JPATH_ROOT . DS . $css_rtl)) {
		$document->addStyleSheet($css_rtl);
	}
}

$carousel_class = 'carousel';
switch ($params->get('twbs_version',4)) {
    case "3" : {
        if ($params->get('include_twbs_css') == "1") {
            $document->addStyleSheet($asset_dir . "css/wsacarousel_bootstrap3.3.7.css", array('version'=>'3.3.7'),
                array('id'=>'wsacarousel_bootstrap.css',));
        }
        if ($params->get('include_twbs_js') == "1") {
            HTMLHelper::_('jquery.framework');  // to be sure that jquery is loaded before dependent javascripts
            $document->addScript($asset_dir . "js/wsacarousel_bootstrap3.3.7.js", array('version'=>'3.3.7'),
                array('id'=>'wsacarousel_bootstrap.js', 'defer'=>'defer')); // defer .
        }
    }
    break;
    case "5" : {
        if ($params->get('include_twbs_css') == "1") {
            $document->addStyleSheet($asset_dir . "css/wsacarousel_bootstrap5.1.css", array('version'=>'5.1.3'),
                array('id'=>'wsacarousel_bootstrap.css',));
        }
        if ($params->get('include_twbs_js') == "1") {
            $carousel_class = 'wsacarousel';
            $document->addScript($asset_dir . "js/wsacarousel_bootstrap5.1.js", array('version'=>'5.1.3'),
                array('id'=>'wsacarousel_bootstrap.js', 'defer'=>'defer')); // defer .
        }
    }
    break; 
    case "4" :
    default  : {
            if ($params->get('include_twbs_css') == "1") {
                $document->addStyleSheet($asset_dir . "css/wsacarousel_bootstrap4.0.css", array('version'=>'4.3.1'),
                    array('id'=>'wsacarousel_bootstrap.css',));
            }
            if ($params->get('include_twbs_js') == "1") {
                $carousel_class = 'wsacarousel';
                HTMLHelper::_('jquery.framework');  // to be sure that jquery is loaded before dependent javascripts
                $document->addScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js', array('version'=>'1.14.6'),
                    array('id'=>'popper.js', 'integrity' => 'sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut',   'crossorigin' => 'anonymous'));
                $document->addScript($asset_dir . "js/wsacarousel_bootstrap4.3.js", array('version'=>'4.3.1'),
                    array('id'=>'wsacarousel_bootstrap.js', 'defer'=>'defer')); // defer .
            }
        }
}
$link_image = $params->get('link_image',1);
if($link_image > 1 && $params->get('include_magnific',0) == 1) {
    $document->addScript($asset_dir.'magnific/magnificpopupv1-1-0.js' , array('version'=>'1.1.0'),  array('id'=>'MagnificPopupV1-1-0.js' , 'defer'=>'defer'));
    $document->addStyleSheet($asset_dir.'magnific/magnific.css' , array('version'=>'1.1.0'),  array('id'=>'MagnificPopupV1-1-0.css'));
    $document->addScript($asset_dir.'js/magnific-init.js', array('version'=>'1.1.0') ,  array('id'=>'magnific-init.js', 'defer'=>'defer'));
}
$caption_overlay = ($params->get('caption_overlay', 1)  ? 'absolute':'relative');
$show_desc = $params->get('show_desc', 1);
$show_readmore = $params->get('show_readmore', 0);
$readmore_text = ($params->get('readmore_text', 0) ? $params->get('readmore_text') : Text::_('MOD_WSACAROUSEL_READMORE'));
$link_title = $params->get('link_title', 1);
$link_desc = $params->get('link_desc', 0);
// $limit_desc = $params->get('limit_desc'); //only used in Helper.
// $full_width = $params->get('full_width', 0)
// $fit_to = $params->get('fit_to', 0);
// $sort_by = $params->get('sort_by', 1);
// tricky but value of assignment == value of assigned variable.
//if(!is_numeric($width = $params->get('image_width'))) $width = 240; // only used in helper
//if(!is_numeric($height = $params->get('image_height'))) $height = 180;
//if(!is_numeric($max = $params->get('max_images'))) $max = 20;
//if(!is_numeric($vicnt = $params->get('visible_images'))) $vicnt = 3;
//if(!is_numeric($spacing = $params->get('space_between_images'))) $spacing = 10;
//if(!is_numeric($preload = $params->get('preload'))) $preload = 800;
//if($vicnt>$slidecnt) $vicnt = $slidecnt;
//if($vicnt<1) $vicnt = 1;
//if($vicnt>$max) $vicnt = $max;
$image_centering = $params->get('image_centering', 0);
if(!is_numeric($duration = $params->get('duration'))) $duration = 600;
if(!is_numeric($delay = $params->get('delay'))) $delay = 3000;
$interval = ($params->get('autoplay', 1)) ? $delay + $duration : 'false';
$wrap = $params->get('looponce', 0) ? 'false': 'true';
$show_buttons = $params->get('show_buttons',1);
$show_arrows = $params->get('show_arrows',1);
$show_idx = $params->get('show_custom_nav', 0);
$idx_style = $params->get('idx_style', 0);

$animationOptions = WsacarouselHelper::getAnimationOptions($params);
$style = WsacarouselHelper::getStyles($params, $slidecnt);
$navigation = WsacarouselHelper::getNavigation($params, $mid);

require ModuleHelper::getLayoutPath('mod_wsacarousel', $params->get('layout','default'));
