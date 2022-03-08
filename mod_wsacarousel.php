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
//jimport('joomla.filesystem.file');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ModuleHelper;
use  Joomla\CMS\Filesystem\File;
use WaasdorpSoekhan\Module\Wsacarousel\Site\Helper\WsaCarouselHelper;

// Include the syndicate functions only once
// require_once (dirname(__FILE__).DS.'helper.php');
$app = Factory::getApplication();
$document = Factory::getDocument();

// taking the slides from the source
if($params->get('slider_source')==1) {
//	jimport('joomla.application.component.helper');
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
$direction = $document->direction;
// direction integration with joomla monster templates
if ($app->input->get('direction') == 'rtl'){
	$direction = 'rtl';
} else if ($app->input->get('direction') == 'ltr') {
	$direction = 'ltr';
} else {
	if (isset($_COOKIE['jmfdirection'])) {
		$direction = $_COOKIE['jmfdirection'];
	} else {
		$direction = $app->input->get('jmfdirection', $direction);
	}
}
$params->set('direction', $direction);

$theme = $params->get('theme', 'default');

if($theme != '_override' ) {
	$css = 'modules/mod_wsacarousel/themes/'.$theme.'/css/wsacarousel.css';
} else {
	$theme = 'override';
	$css = 'templates/'.$app->getTemplate().'/css/wsacarousel.css';
}
// add only if theme file exists
if($theme != 'default' AND File::exists(JPATH_ROOT . DS . $css) ) {
   $document->addStyleSheet(Uri::root(true).'/'.$css);
}
if($direction == 'rtl') { // load rtl css if exists in theme or joomla template
	$css_rtl = File::stripExt($css).'_rtl.css';
	if(File::exists(JPATH_ROOT . DS . $css_rtl)) {
		$document->addStyleSheet(Uri::root(true).'/'.$css_rtl);
	}
}

$jquery = version_compare(JVERSION, '3.8.0', '>=');

$db = Factory::getDBO();
$db->setQuery("SELECT manifest_cache FROM #__extensions WHERE element='mod_wsacarousel' LIMIT 1");
$ver = json_decode($db->loadResult());
$ver = $ver->version;


HTMLHelper::_('jquery.framework');  // to be sure that jquery is loaded before dependent javascripts
$carousel_class = 'carousel';

switch ($params->get('twbs_version',4)) {
    case "3" : {
        if ($params->get('include_twbs_css') == "1") {
            $document->addStyleSheet(Uri::root(true)."/modules/mod_wsacarousel/assets/css/wsacarousel_bootstrap3.3.7.css", array('version'=>''),
                array('id'=>'wsacarousel_bootstrap.css',));
        }
        if ($params->get('include_twbs_js') == "1") {
            $document->addScript(Uri::root(true)."/modules/mod_wsacarousel/assets/js/wsacarousel_bootstrap3.3.7.js", array('version'=>''),
                array('id'=>'wsacarousel_bootstrap.js', 'defer'=>'defer')); // defer .
        }
    }
    break;
    case "5" :         {
        if ($params->get('include_twbs_css') == "1") {
            $document->addStyleSheet(Uri::root(true)."/modules/mod_wsacarousel/assets/css/wsacarousel_bootstrap5.1.css", array('version'=>''),
                array('id'=>'wsacarousel_bootstrap.css',));
        }
        if ($params->get('include_twbs_js') == "1") {
            $carousel_class = 'wsacarousel';
//TODO do we need popper ???
//            $document->addScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js', array('version'=>'1.14.6'),
//                array('id'=>'popper.js', 'integrity' => 'sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut',   'crossorigin' => 'anonymous'));
            $document->addScript(Uri::root(true)."/modules/mod_wsacarousel/assets/js/wsacarousel_bootstrap5.1.js", array('version'=>''),
                array('id'=>'wsacarousel_bootstrap.js', 'defer'=>'defer')); // defer .
                //	    $document->addCustomTag('<script src="'. Uri::root(true) . '/modules/mod_wsacarousel/assets/js/wsacarousel_bootstrap4.0.js" id="wsacarousel_bootstrap.js" defer></script>'); // after all other js
        }
        
    }
    break; 
    case "4" :
    default  : 
        {
            if ($params->get('include_twbs_css') == "1") {
                $document->addStyleSheet(Uri::root(true)."/modules/mod_wsacarousel/assets/css/wsacarousel_bootstrap4.0.css", array('version'=>''),
                    array('id'=>'wsacarousel_bootstrap.css',));
            }
            if ($params->get('include_twbs_js') == "1") {
                $carousel_class = 'wsacarousel';
                $document->addScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js', array('version'=>'1.14.6'),
                    array('id'=>'popper.js', 'integrity' => 'sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut',   'crossorigin' => 'anonymous'));
                // javascript to CustomTag, to order it as latest	makes no difference so back in old way and using other class to b e sure tu use this script in stead of order
                $document->addScript(Uri::root(true)."/modules/mod_wsacarousel/assets/js/wsacarousel_bootstrap4.3.js", array('version'=>''),
                    array('id'=>'wsacarousel_bootstrap.js', 'defer'=>'defer')); // defer .
                    //	    $document->addCustomTag('<script src="'. Uri::root(true) . '/modules/mod_wsacarousel/assets/js/wsacarousel_bootstrap4.0.js" id="wsacarousel_bootstrap.js" defer></script>'); // after all other js
            }
            
        }
        
}


if($params->get('link_image',1) > 1 && $params->get('include_magnific',0) == 1) {
	
    $document->addScript(Uri::root(true).'/modules/mod_wsacarousel/assets/magnific/magnificpopupv1-1-0.js' , array('version'=>'1.1.0'),  array('id'=>'MagnificPopupV1-1-0.js' , 'defer'=>'defer'));
    $document->addStyleSheet(Uri::root(true).'/modules/mod_wsacarousel/assets/magnific/magnific.css' , array('version'=>'1.1.0'),  array('id'=>'MagnificPopupV1-1-0.css'));
		$document->addScript(Uri::root(true).'/modules/mod_wsacarousel/assets/js/magnific-init.js', array('version'=>'1.1.0') ,  array('id'=>'magnific-init.js', 'defer'=>'defer'));
	 
}

if(!is_numeric($width = $params->get('image_width'))) $width = 240;
if(!is_numeric($height = $params->get('image_height'))) $height = 180;
if(!is_numeric($max = $params->get('max_images'))) $max = 20;
if(!is_numeric($count = $params->get('visible_images'))) $count = 3;
if(!is_numeric($spacing = $params->get('space_between_images'))) $spacing = 10;
if(!is_numeric($preload = $params->get('preload'))) $preload = 800;
if($count>$slidecnt) $count = $slidecnt;
if($count<1) $count = 1;
if($count>$max) $count = $max;
$mid = $module->id;
$slider_type = $params->get('slider_type',0);
switch($slider_type){
	case 2:
		$slide_size = $width;
		$count = 1;
		break;
	case 1:
		$slide_size = $height + $spacing;
		break;
	case 0:
	default:
		$slide_size = $width + $spacing;
		break;
}

$animationOptions = WsacarouselHelper::getAnimationOptions($params);
$moduleSettings = json_encode(array('id' => $mid, 'slider_type' => $slider_type, 'slide_size' => $slide_size, 'visible_slides' => $count, 'direction' => $direction == 'rtl' ? 'right':'left',
	'show_buttons' => $params->get('show_buttons',1), 'show_arrows' => $params->get('show_arrows',1), 'preload' => $preload, 'css3' => $params->get('css3', 0)
));

$style = WsacarouselHelper::getStyles($params);
$navigation = WsacarouselHelper::getNavigation($params,$mid);
$show = (object) array('arr'=>$params->get('show_arrows'), 'btn'=>$params->get('show_buttons'), 'idx'=>$params->get('show_custom_nav'));

require ModuleHelper::getLayoutPath('mod_wsacarousel', $params->get('layout','default'));
