<?php

import('extensions::htmlheader::biz::filter', 'JsCssInclusionFilterChain');

/**
 *  <!--
 *  This file is part of the adventure php framework (APF) published under
 *  http://adventure-php-framework.org.
 *
 *  The APF is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The APF is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 *  -->
 */

/**
 *  @package extensions::htmlheader::biz
 *  @class JsCssPackager
 *
 *  A packager which can deliver multiple css and js files to client.
 *  Caching and shrinking is supported, but must be configured.
 *
 *  @author Ralf Schubert <ralf.schubert@the-screeze.de>
 *  @version
 *  Version 1.0, 18.03.2010<br />
 */
class JsCssPackager extends APFObject {

    /**
     * @return Configuration The current package configuration.
     */
    private function getPackageConfiguration() {
        return $this->getConfiguration('extensions::htmlheader::biz', 'JsCssPackager.ini');
    }

    /**
     * Loads the content of all files, included in the package with the given name.
     *
     * @param String $name The package name.
     * @param Bool $gzip Return package compressed with gzip
     *
     * @return String The complete package.
     *
     * @author Ralf Schubert
     * @version
     * Version 1.0, 18.03.2010<br />
     */
    public function getPackage($name, $gzip = false) {
        $cfgPack = $this->getPackageConfiguration()->getSection($name);
        
        if($cfgPack === null) {
            throw new InvalidArgumentException('Package with the given name was not found!');
        }

        $ServerCacheMinutes = $cfgPack->getValue('ServerCacheMinutes');
        if ($ServerCacheMinutes === null) {
            $ServerCacheMinutes = 0;
        }

        /* If ServerCacheMinutes is not 0, we use a filecache */
        if ((int) $ServerCacheMinutes !== 0) {
            $cMF = &$this->getServiceObject('tools::cache', 'CacheManagerFabric');
            $cM = &$cMF->getCacheManager('jscsspackager_cache');

            $cacheKey = $name;
            if ($gzip === true) {
                $cacheKey .= '_gzip';
            }

            $cacheContent = $cM->getFromCache(new SimpleCacheKey($cacheKey));
            /* If package is already in cache, we check if it is not expired and return the cache content */
            if ($cacheContent !== null) {
                $cacheExpires = substr($cacheContent, -10);
                if ($cacheExpires >= time()) {
                    return substr($cacheContent, 0, -10);
                } else {
                    /* Cache is expired, delete it */
                    $cM->clearCache(new SimpleCacheKey($name));
                    $cM->clearCache(new SimpleCacheKey($name . '_gzip'));
                }
            }
            /* Package was not in cache or was expired, we generate a new one, cache and deliver it. */
            $newPackage = $this->generatePackage($cfgPack, $name);
            $cacheExpires = time() + ($ServerCacheMinutes * 60);
            $newPackageGzip = gzencode($newPackage, 9);

            $cM->writeToCache(new SimpleCacheKey($name), $newPackage . $cacheExpires);
            $cM->writeToCache(new SimpleCacheKey($name . '_gzip'), $newPackageGzip . $cacheExpires);

            return $gzip ? $newPackageGzip : $newPackage;
        }

        /* We generate the package new, because we don't use a cache */
        $pack = $this->generatePackage($cfgPack, $name);
        return $gzip ? gzencode($pack, 9) : $pack;
    }

    /**
     * Generates a package from it's single files.
     * Will Shrink output, if enabled.
     *
     * @param array $cfgPack The package configuration
     * @param string $name The package name
     * @return string All files put together to one string.
     *
     * @author Ralf Schubert
     * @version
     * Version 1.0, 18.03.2010<br />
     * Version 1.1, 05.11.2010 (Fixed Bug, caused by incompatible use of new configuration methods)<br />
     */
    protected function generatePackage(Configuration $cfgPack, $name) {
        $output = '';
        $Files = $cfgPack->getSection('Files');
        $FileSectionNames = $Files->getSectionNames();
        foreach ($FileSectionNames as $FileSectionName) {
            $file = $Files->getSection($FileSectionName);
            $output .= $this->loadSingleFile($file->getValue('Namespace'), $file->getValue('Filename'), $cfgPack->getValue('PackageType'), $name);
        }

        if ($cfgPack->getValue('EnableShrinking') === 'true') {
            switch ($cfgPack->getValue('PackageType')) {
                case 'js':
                    $output = $this->shrinkJs($output);
                    break;
                case 'css':
                    $output = $this->shrinkCSS($output);
                    break;
            }
        }
        
        $this->initFilterChain($cfgPack->getValue('PackageType'));
        $output = JsCssInclusionFilterChain::getInstance()->filter($output);

        return $output;
    }

    public function getFile($path, $file, $type, $gzip = false) {
        
        $libBasePath = Registry::retrieve('apf::core', 'LibPath');
        $filePath = $libBasePath . '/' . $path . '/' . $file . '.' . $type;

        if (!file_exists($filePath)) {
            throw new IncludeException('[JsCssPackager::getFile()] The requested file "' . $file . '.'
                    . $type . '" cannot be found in namespace "' . str_replace('_', '::', $path) . '". Please '
                    . 'check your taglib definition for tag &lt;htmlheader:add* /&gt;!',
                    E_USER_ERROR);
        }

        $this->initFilterChain($type);
        $filteredContent = JsCssInclusionFilterChain::getInstance()->filter(file_get_contents($filePath));
        
        $config = $this->getConfiguration('extensions::htmlheader::biz', 'JsCssInclusion.ini');

        $sectionGeneral = $config->getSection('General');
        if($sectionGeneral !== null) {
            if ($sectionGeneral->getValue('EnableShrinking') === 'true') {
                switch ($type) {
                    case 'js':
                        $filteredContent = $this->shrinkJs($filteredContent);
                        break;
                    case 'css':
                        $filteredContent = $this->shrinkCSS($filteredContent);
                        break;
                }
            }
        }
        
        return $gzip ? gzencode($filteredContent, 9) : $filteredContent;
    }

    /**
     * Shrinks a string containing javascript.
     *
     * @param string $input The javascript which should be shrinked.
     * @return string The minified javascript.
     *
     * @author Ralf Schubert
     * @version
     * Version 1.0, 18.03.2010<br />
     */
    protected function shrinkJs($input) {
        import('extensions::htmlheader::biz', 'JSMin');
        return JSMin::minify($input);
    }

    /**
     * Shrinks a string containing css
     *
     * @param string $input The css which should be shrinked.
     * @return string The minified css.
     *
     * @author Ralf Schubert
     * @version
     * Version 1.0, 18.03.2010<br />
     */
    protected function shrinkCSS($input) {
        $input = preg_replace('#\s+#', ' ', $input);
        $input = preg_replace('#/\*.*?\*/#s', '', $input);
        $input = str_replace('; ', ';', $input);
        $input = str_replace(': ', ':', $input);
        $input = str_replace(' {', '{', $input);
        $input = str_replace('{ ', '{', $input);
        $input = str_replace(', ', ',', $input);
        $input = str_replace('} ', '}', $input);
        $input = str_replace(';}', '}', $input);
        return trim($input);
    }

    /**
     * Loads the period (in days) the package should be cached by client.
     * Default is 0 (no client caching)
     *
     * @param String $name The package name.
     * @return int The period the package should be cached by client in days.
     *
     * @author Ralf Schubert
     * @version
     * Version 1.0, 18.03.2010<br />
     */
    public function getClientCachePeriod($name) {
        if (($CCP = $this->getPackageConfiguration()->getSection($name)->getValue('ClientCacheDays')) !== null) {
            return (int) $CCP;
        }
        return 0;
    }

    /**
     * Loads the content of a file.
     *
     * @param string $namespace The namespace of the file.
     * @param string $file The name of the file.
     * @param string $ext The extension of the file.
     * @param string $packageName The name of the package, which contains the file.
     * @return string The content of the file.
     *
     * @author Ralf Schubert
     * @version
     * Version 1.0, 18.03.2010<br />
     */
    protected function loadSingleFile($namespace, $file, $ext, $packageName) {
        $namespace = str_replace('::', '/', $namespace);
        $libBasePath = Registry::retrieve('apf::core', 'LibPath');
        $filePath = $libBasePath . '/' . $namespace . '/' . $file . '.' . $ext;

        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }
        throw new IncludeException('[JsCssPackager::loadSingleFile()] The requested file "' . $file . '.' . $ext
                . '" cannot be found in namespace "' . str_replace('/', '::', $namespace) . '". Please
                    check the configuration of package "' . $packageName . '"!');
    }

    /**
     * @param $fileType The current file extension (js or css).
     */
    protected function initFilterChain($fileType) {

        try {
            $config = $this->getConfiguration('extensions::htmlheader::biz', 'JsCssInclusion.ini');
        } catch (ConfigurationException $e) {
            return;
        }

        $sectionName = ($fileType === 'css') ? 'CssFilter' : 'JsFilter';
        $section = $config->getSection($sectionName);

        if ($section !== null) {
            foreach ($section->getSectionNames() as $key) {
                $FilterInformation = $section->getSection($key);

                $namespace = $FilterInformation->getValue('Namespace');
                $name = $FilterInformation->getValue('Name');

                import($namespace, $name);
                JsCssInclusionFilterChain::getInstance()->appendFilter(new $name());
            }
        }
    }

}

?>