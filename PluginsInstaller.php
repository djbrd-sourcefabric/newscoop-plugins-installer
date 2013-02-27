<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Composer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;
use Symfony\Component\Finder\Finder;

class PluginsInstaller extends LibraryInstaller
{
    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $targetDir = $package->getTargetDir();
        return 'plugins'. ($targetDir ? '/'.$targetDir : '');
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'newscoop-plugin' === $packageType;
    }


    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);

        $this->findAllPlugins();
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::uninstall($repo, $package);

        $this->findAllPlugins();
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $package, $target);

        $this->findAllPlugins();
    }

    public function findAllPlugins()
    {
        $plugins = array();
        $finder = new Finder();
        $pluginsDirectory = $this->vendorDir. '/../plugins';
        $cacheDirectory = $this->vendorDir. '/../cache';
        $elements = $finder->depth('== 0')->in($pluginsDirectory);
        foreach ($elements as $element) {
            if (count($elements) > 0) {
                $vendorName = $element->getFileName();
                $secondFinder = new Finder();
                $directories = $secondFinder->depth('== 0')->in($element->getPathName());
                foreach ($directories as $directory) {
                    $pluginName = $directory->getFileName();
                    $className = $vendorName . '\\' .$pluginName . '\\' . $vendorName . $pluginName;
                    $pos = strpos($className, 'Bundle');
                    if ($pos !== false) {
                        $plugins[] = $className;
                    }
                }
            }
        }

        file_put_contents($cacheDirectory.'/avaiable_plugins.json', json_encode($plugins));
    }
}