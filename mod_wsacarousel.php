<?php
/**
 * @version $Id: mod_wsacarousel.php 
 * @package wsacarousel
 * @subpackage wsacarousel Module
 * @copyright Copyright (C) 2018 AHC Waasdorp, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://www.waasdorpsoekhan.nl
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
 * along with DJ-ImageSlider. If not, see <http://www.gnu.org/licenses/>.
 * 7-2-2018 added J3.8 J4.0namespaces.
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
jimport('joomla.filesystem.file');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ModuleHelper;

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');
$app = Factory::getApplication();
$document = Factory::getDocument();

// taking the slides from the source
if($params->get('slider_source')==1) {
	jimport('joomla.application.component.helper');
	if(!ComponentHelper::isEnabled('com_djimageslider', true)){
		$app->enqueueMessage(Text::_('MOD_WSACAROUSEL_NO_COMPONENT'),'notice');
		return;
	}
	$slides = modDJImageSliderHelper::getImagesFromDJImageSlider($params);
	if($slides==null) {
		$app->enqueueMessage(Text::_('MOD_WSACAROUSEL_NO_CATEGORY_OR_ITEMS'),'notice');
		return;
	}
} else {
	$slides = modDJImageSliderHelper::getImagesFromFolder($params);
	if($slides==null) {
		$app->enqueueMessage(Text::_('MOD_WSACAROUSEL_NO_CATALOG_OR_FILES'),'notice');
		return;
	}
}

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

if($theme != '_override') {
	$css = 'modules/mod_wsacarousel/themes/'.$theme.'/css/djimageslider.css';
} else {
	$theme = 'override';
	$css = 'templates/'.$app->getTemplate().'/css/djimageslider.css';
}
// add only if theme file exists
if(JFile::exists(JPATH_ROOT . DS . $css)) {
	$document->addStyleSheet(Uri::root(true).'/'.$css);
}
if($direction == 'rtl') { // load rtl css if exists in theme or joomla template
	$css_rtl = JFile::stripExt($css).'_rtl.css';
	if(JFile::exists(JPATH_ROOT . DS . $css_rtl)) {
		$document->addStyleSheet(Uri::root(true).'/'.$css_rtl);
	}
}

$jquery = version_compare(JVERSION, '3.8.0', '>=');
$canDefer = preg_match('/(?i)msie [6-9]/', @$_SERVER['HTTP_USER_AGENT']) ? false : true;

$db = Factory::getDBO();
$db->setQuery("SELECT manifest_cache FROM #__extensions WHERE element='mod_wsacarousel' LIMIT 1");
$ver = json_decode($db->loadResult());
$ver = $ver->version;

if ($jquery) {
	HTMLHelper::_('jquery.framework');
	$document->addScript(Uri::root(true).'/media/djextensions/jquery-easing/jquery.easing.min.js', 'text/javascript', $canDefer);
	if ($params->get('twbs_version',4) == "3") {
	    if ($params->get('include_twbs_css') == "1") {
	   $document->addStyleSheet('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array('version'=>'3.3.7'),
	       array('id'=>'bootstrap.min.css', 'integrity' => 'sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u', 'crossorigin' => 'anonymous'));
	    }
	    if ($params->get('include_twbs_js') == "1") {
//	        $document->addScript("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" , array('version'=>'3.3.7'),
//	            array('id'=>'bootstrap.min.js', 'defer'=>'defer')); // defer .
	        $document->addScript(Uri::root(true)."/modules/mod_wsacarousel/assets/js/wsacarousel_bootstrap3.3.7.js", array('version'=>'3.3.7'),
	            array('id'=>'wsacarousel_bootstrap.js', 'defer'=>'defer')); // defer .
	    }
	}
	else {
	    if ($params->get('include_twbs_css') == "1") {
	        $document->addStyleSheet('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css', array('version'=>'4.0.0'),
	            array('id'=>'bootstrap.min.css', 'integrity' => 'sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm', 'crossorigin' => 'anonymous'));
	    }
	    if ($params->get('include_twbs_js') == "1") {
	        $document->addScript("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js", array('version'=>'1.12.9'),
	            array('id'=>'popper.js', 'integrity'=>'sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q'));
	        $document->addScript("https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" , array('version'=>'4.0.0'), 
	            array('id'=>'bootstrap.min.js', 'integrity' => 'sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl', 'defer'=>'defer')); // defer .
	    }
	    
	}
}

if($params->get('link_image',1) > 1) {
	if($jquery) {
		$document->addScript(Uri::root(true).'/media/djextensions/magnific/magnific.js', 'text/javascript', $canDefer);
		$document->addStyleSheet(Uri::root(true).'/media/djextensions/magnific/magnific.css');
		$document->addScript(Uri::root(true).'/modules/mod_wsacarousel/assets/js/magnific-init.js', 'text/javascript', $canDefer);
	} else {
		$document->addScript(Uri::root(true).'/modules/mod_wsacarousel/assets/slimbox/js/slimbox.js', 'text/javascript', $canDefer);
		$document->addStyleSheet(Uri::root(true).'/modules/mod_wsacarousel/assets/slimbox/css/slimbox.css');
	}
}

if(!is_numeric($width = $params->get('image_width'))) $width = 240;
if(!is_numeric($height = $params->get('image_height'))) $height = 180;
if(!is_numeric($max = $params->get('max_images'))) $max = 20;
if(!is_numeric($count = $params->get('visible_images'))) $count = 3;
if(!is_numeric($spacing = $params->get('space_between_images'))) $spacing = 10;
if(!is_numeric($preload = $params->get('preload'))) $preload = 800;
if($count>count($slides)) $count = count($slides);
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

$animationOptions = modDJImageSliderHelper::getAnimationOptions($params);
$moduleSettings = json_encode(array('id' => $mid, 'slider_type' => $slider_type, 'slide_size' => $slide_size, 'visible_slides' => $count, 'direction' => $direction == 'rtl' ? 'right':'left',
	'show_buttons' => $params->get('show_buttons',1), 'show_arrows' => $params->get('show_arrows',1), 'preload' => $preload, 'css3' => $params->get('css3', 0)
));

$style = modDJImageSliderHelper::getStyles($params);
$navigation = modDJImageSliderHelper::getNavigation($params,$mid);
$show = (object) array('arr'=>$params->get('show_arrows'), 'btn'=>$params->get('show_buttons'), 'idx'=>$params->get('show_custom_nav'));

require ModuleHelper::getLayoutPath('mod_wsacarousel', $params->get('layout','default'));
