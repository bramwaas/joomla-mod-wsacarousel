<?php
/**
 * @version $Id: mod_wsacarousel.script.php
 * @package wsacarousel
 * @subpackage wsacarousel Module
 * @copyright Copyright (C) 2018 - 2022 AHC Waasdorp, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: https://www.waasdorpsoekhan.nl
 * @author email contact@waasdorpsoekhan.nl
 * @developer AHC Waasdorp
 * 2022-01-21 remove unused files and maps.
 */
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;

class mod_wsacarouselInstallerScript
{
    /**
     * Constructor
     *
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     */
    public function __construct(InstallerAdapter $adapter)
    {
    }
    
    /**
     * Called before any type of action
     *
     * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function preflight($route, InstallerAdapter $adapter)
    {
        $first_message = false;
        $paths = ['/media/wsacarousel', '/modules/mod_wsacarousel/css', '/modules/mod_wsacarousel/js', '/modules/mod_wsacarousel/themes'];
        foreach($paths as $path) {

            if (Folder::exists(JPATH_ROOT . $path) && Folder::delete(JPATH_ROOT . $path)) {
                if ($first_message) {
                    echo '<p>' . Text::sprintf('MOD_WSACAROUSEL_PREFLIGHT_TEXT') . '</p>';
                    $first_message = true;
                }
        echo '<p>', $path, ' removed </p>';
         }  

        }
        $paths = ['/modules/mod_wsacarousel/helper.php'];
        foreach($paths as $path) {
            if (File::exists(JPATH_ROOT . $path) && File::delete(JPATH_ROOT . $path)) {
                if ($first_message) {
                    echo '<p>' . Text::sprintf('MOD_WSACAROUSEL_PREFLIGHT_TEXT') . '</p>';
                    $first_message = true;
                }
                echo '<p>', $path, ' removed </p>';
            } 
            
        }
        
        return true;
    }
    
    /**
     * Called after any type of action
     *
     * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function postflight($route, $adapter)
    {
        return true;
    }
    
    /**
     * Called on installation
     *
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function install(InstallerAdapter $adapter)
    {
        return true;
    }
    
    /**
     * Called on update
     *
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function update(InstallerAdapter $adapter)
    {
        
   //     echo '<p>' . Text::sprintf('MOD_WSACAROUSEL_UPDATE_TEXT') . '</p>';
        
        
        
        return true;
    }
    
    /**
     * Called on uninstallation
     *
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     */
    public function uninstall(InstallerAdapter $adapter)
    {
        return true;
    }
}

?>
