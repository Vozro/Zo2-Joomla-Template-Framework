<?php

/**
 * Zo2 (http://www.zootemplate.com/zo2)
 * A powerful Joomla template framework
 *
 * @link        http://www.zootemplate.com/zo2
 * @link        https://github.com/cleversoft/zo2
 * @author      ZooTemplate <http://zootemplate.com>
 * @copyright   Copyright (c) 2014 CleverSoft (http://cleversoft.co/)
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class exists checking
 */
if (!class_exists('Zo2Assets'))
{

    /**
     * Zo2 assets management class
     */
    class Zo2Assets
    {

        /**
         * Array of added css files
         * @var array
         */
        private $_stylesheets = array();

        /**
         * Array of added custom stylesheet scripts
         * @var array
         */
        private $_stylesheetDeclarations = array();

        /**
         * Array of added js files
         * @var array
         */
        private $_javascripts = array();

        /**
         * Array of added custom javascripts
         * @var array
         */
        private $_javascriptDeclarations = array();

        /**
         * Get instance of Zo2Assets
         * @return \Zo2Assets
         */
        public static function getInstance()
        {
            static $instance;
            if (!isset($instance))
            {
                $instance = new Zo2Assets();
                Zo2Framework::log('Init assets');
            }
            if (isset($instance))
            {
                return $instance;
            }
        }

        /**
         * Load assets file
         * @param type $assets
         */
        public function load($assets)
        {
            // Load css
            if (isset($assets->css))
            {
                foreach ($assets->css as $css)
                {
                    $this->addStyleSheet($css);
                }
            }
            // Load js
            if (isset($assets->js))
            {
                foreach ($assets->js as $js)
                {
                    $this->addScript($js);
                }
            }
        }

        /**
         * 
         * @param string $key
         * @return \Zo2Assets
         */
        public function addStyleSheet($key)
        {
            $assetFile = Zo2Path::getInstance()->getPath($key);
            if ($assetFile)
            {
                $this->_stylesheets[$assetFile] = Zo2Path::getInstance()->getUrl($key);
            }
            return $this;
        }

        /**
         * 
         * @param string $style
         * @return \Zo2Assets
         */
        public function addStyleSheetDeclaration($style)
        {
            $this->_stylesheetDeclarations[] = $style;
            return $this;
        }

        /**
         * 
         * @param string $key
         * @return \Zo2Assets
         */
        public function addScript($key)
        {
            $assetFile = Zo2Path::getInstance()->getPath($key);
            if ($assetFile)
            {
                $this->_javascripts[$assetFile] = Zo2Path::getInstance()->getUrl($key);
            }
            return $this;
        }

        /**
         *
         * @param string $script
         * @return \Zo2Assets
         */
        public function addScriptDeclaration($script)
        {
            $this->_javascriptDeclarations[] = $script;
            return $this;
        }

        /**
         * Build assets
         */
        public function build()
        {
            $model = new Zo2ModelAssets();
            $model->build();
        }

        /**
         * 
         */
        private function _prepareRender()
        {

            // CSS optimize
            switch (Zo2Framework::getParam('enable_combine_css'))
            {
                // Combine to one css file
                case 'file':
                    $model = new Zo2ModelAssets();
                    $cssFileName = $model->combineCss($this->_stylesheets);
                    if ($cssFileName)
                    {
                        // Reset all old stylesheet and replace by combined file 
                        $this->_stylesheets = array();
                        $this->_stylesheets[] = ZO2URL_CACHE . '/' . $cssFileName;
                    }
                    break;
                case 'gzip':
                    $model = new Zo2ModelAssets();
                    $cssFileName = $model->combineCss($this->_stylesheets);
                    // Reset all old stylesheet and replace by combined file 
                    if ($cssFileName)
                    {
                        $this->_stylesheets = array();
                        $this->_stylesheets[] = ZO2URL_ROOT . '/framework/assets/css/gzip.php?cssFile=' . $cssFileName;
                    }
                    break;
            }

            // JS optimize
            switch (Zo2Framework::getParam('enable_combine_js'))
            {
                case 'file':

                    $model = new Zo2ModelAssets();
                    $jsFileName = $model->combineJs($this->_javascripts);
                    if ($jsFileName)
                    {
                        // Reset all old scripts and replace by combined file 
                        $this->_javascripts = array();
                        $this->_javascripts[] = ZO2URL_CACHE . '/' . $jsFileName;
                    }
                    break;
                case 'gzip':
                    $model = new Zo2ModelAssets();
                    $jsFileName = $model->combineJs($this->_javascripts);
                    // Reset all old stylesheet and replace by combined file 
                    if ($jsFileName)
                    {
                        $this->_javascripts = array();
                        $this->_javascripts[] = ZO2URL_ROOT . '/framework/assets/js/gzip.php?jsFile=' . $jsFileName;
                    }
                    break;
            }
        }

        /**
         * Generate assets code
         * @return type
         */
        public function render()
        {
            $this->_prepareRender();
            {
                Zo2Framework::log('Render assets');
                foreach ($this->_stylesheets as $key => $url)
                {
                    // Use min version
                    // For enabled combine css we already minify it than no need to replace min.css here
                    if (Zo2Framework::getParam('enable_minify_css') && (!Zo2Framework::getParam('enable_combine_css')))
                    {
                        if (strpos($url, '.min.css') === false)
                        {
                            $key = str_replace('.css', '.min.css', $key);
                            if (JFile::exists($key))
                            {
                                $url = str_replace('.css', '.min.css', $url);
                            }
                        }
                    }
                    Zo2Framework::log('Render asset', $url);
                    $assets[] = '<link rel="stylesheet" href="' . $url . '" type="text/css" />';
                }
                foreach ($this->_javascripts as $url)
                {
                    // Use min version                   
                    if (Zo2Framework::getParam('enable_minify_js') && (!Zo2Framework::getParam('enable_combine_js')))
                    {
                        if (strpos($url, '.min.js') === false)
                        {
                            $key = str_replace('.js', '.min.js', $key);
                            if (JFile::exists($key))
                            {
                                $url = str_replace('.js', '.min.js', $url);
                            }
                        }
                    }
                    Zo2Framework::log('Render asset', $url);
                    $assets[] = '<script src="' . $url . '" type="text/javascript"></script>';
                }
                // Style declaration
                $styleSheetDeclaration = implode(PHP_EOL, $this->_stylesheetDeclarations);
                if (Zo2Framework::getParam('enable_minify_css'))
                {
                    $styleSheetDeclaration = Zo2HelperMinify::css($styleSheetDeclaration);
                }
                $assets[] = '<style>' . $styleSheetDeclaration . '</style>';
                // Script declaration
                $javascriptDeclaration = implode(PHP_EOL, $this->_javascriptDeclarations);
                if (Zo2Framework::getParam('enable_minify_js'))
                {
                    $styleSheetDeclaration = Zo2HelperMinify::js($javascriptDeclaration);
                }
                $assets[] = '<script>' . $styleSheetDeclaration . '</script>';
                // Fonts generate
                $fonts = Zo2Fonts::getInstance();
                $assets[] = $fonts->render();
                return implode(PHP_EOL, $assets);
            }
        }

    }

}    