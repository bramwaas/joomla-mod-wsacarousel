<?php

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
        echo '<p>' . Text::sprintf('MOD_WSACAROUSEL_PREFLIGHT_TEXT', $adapter->getManifest()->xpath('/extension/version')) . '</p>';
        $paths = ['/media/wsacarousel', '/modules/mod_wsacarousel/css', '/modules/mod_wsacarousel/js', '/modules/mod_wsacarousel/themes'];
        foreach($paths as $path) {
        echo Path::clean(JPATH_ROOT . '/media/wsacarousel');

         if (Folder::exists(JPATH_ROOT . $path)) {
//         Folder::delete('/media/wsacarousel');
         echo '<p>', $path, ' bestaat </p>';
         } else echo '<p>', $path, ' niet gevonden </p>'; 

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
        
//        echo '<p>' . Text::sprintf('MOD_WSACAROUSEL_UPDATE_TEXT', $adapter->getManifest()->attributes()->type) . '</p>';
        echo '<p>' . Text::sprintf('MOD_WSACAROUSEL_UPDATE_TEXT', (string) $adapter->getManifest()) . '</p>';
        
        
        
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
