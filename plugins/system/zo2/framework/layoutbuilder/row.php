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

if (!class_exists('Zo2LayoutbuilderRow'))
{

    /**
     * Class object for each row
     */
    class Zo2LayoutbuilderRow extends Zo2LayoutbuilderAbstract
    {

        private $_bsVars = array(
            '3' => array(
                'grid' => 'col'
            ),
            '2' => array(
                'grid' => 'span'
            )
        );
        private $_controls;

        public function __construct($properties = null)
        {
            parent::__construct($properties);
            $this->addControl('move', 'arrows', 'move', array('id' => 'move-row'));
            $this->addControl('Add New Col', 'columns', 'add-row', array('onClick' => 'zo2.layoutbuilder.addRow(this);'));
            $this->addControl('Add New Row', 'align-justify', 'add-col', array('onClick' => 'zo2.layoutbuilder.addCol(this);'));
            $this->addControl('Settings', 'cog', 'settings', array('onClick' => 'zo2.layoutbuilder.showSettingModal(this);'));
            $this->addControl('remove', 'remove', 'delete', array('onClick' => 'zo2.layoutbuilder.showDeleteModal(this);'));
            // Default name
            $this->def('name', 'unknown');
            if ($this->get('jdoc'))
            {
                $this->set('jdoc', new JObject($this->get('jdoc')));
            } else
            {
                $this->set('jdoc', new JObject());
            }
        }

        public function setRoot($isRoot)
        {
            $this->set('root', $isRoot);
        }

        public function isRoot()
        {
            return $this->get('root');
        }

        public function addControl($name, $icon, $class, $data = array())
        {
            $control = new Zo2LayoutbuilderControl();
            $control->setName($name);
            $control->setIcon($icon);
            $control->addClass($class);
            $control->set('data', $data);
            $this->_controls[] = $control;
        }

        /**
         * Render layout of this row
         * @return string
         */
        public function render()
        {
            // Only render row if this row have data
            if ($this->hasData())
            {
                $html = new Zo2Html();
                $html->set('row', $this);
                $ret[] = '<!-- BEGIN row: ' . $this->get('name') . ' -->';
                $ret[] = $html->fetch('Zo2://html/site/layout/row.php');
                $ret[] = '<!-- END row: ' . $this->get('name') . ' -->';
                return implode(PHP_EOL, $ret);
            }
        }

        /**
         *
         * @return type
         */
        public function hasData()
        {
            $jDoc = $this->getJdoc();
            /* Check if this row using jDoc and this jDoc has data */
            $hasData = $this->_hasData($jDoc);
            return $hasData;
        }

        /**
         * Render children rows
         * @return string
         */
        public function renderChildren()
        {
            $children = $this->get('children', array());
            return $this->_render($children);
        }

        /**
         * Check if this row has children to render
         * @return boolean
         */
        public function hasChildren()
        {
            $children = $this->get('children', array());
            foreach ($children as $child)
            {
                $child = new Zo2LayoutbuilderRow($child);
                $jDoc = $child->getJdoc();
                return $this->_hasData($jDoc);
            }
            return false;
        }

        /**
         *
         * @param type $jDoc
         * @return boolean
         */
        protected function _hasData($jDoc)
        {
            switch ($jDoc->get('type'))
            {
                // Check if modules position have any modules to render
                case 'modules':
                    $hasChildren = $this->countModules($jDoc->get('name')) > 0;
                    return $hasChildren;
                // Check if this module avaiable to render
                case 'module':
                    return $this->isModuleAvaiable($jDoc->get('name'));
                // For anything else by default is TRUE
                case 'message':
                case 'component':
                    return true;
                default :
                    return true;
            }
        }

        /**
         * @link https://docs.joomla.org/Jdoc_statements
         * @return \JObject
         */
        public function getJdoc()
        {
            return new JObject($this->get('jdoc'));
        }

        public function getWidth($name = 'md')
        {
            $grid = new JObject($this->get('grid'));
            $width = $grid->get($name);
            return empty($width) ? 12 : $width;
        }

        /**
         *
         * @return type
         */
        public function getGridClass()
        {
            $grid = new JObject($this->get('grid'));
            $grid->def('xs', 12);
            $grid->def('sm', 12);
            $grid->def('md', 12);
            $grid->def('lg', 12);
            $gridProperties = $grid->getProperties();
            $class = array();
            // For frontpage we generate grid by use BS3
            if (JFactory::getApplication()->isSite())
            {
                foreach ($gridProperties as $key => $value)
                {

                    $gridClass = $this->_getBootstrapVar('3', 'grid', 'col');
                    $class [] = $gridClass . '-' . $key . '-' . $value;
                }
            } else // For backend we generate span md by use BS2
            {
                $gridClass = $this->_getBootstrapVar('2', 'grid', 'span');
                $class [] = $gridClass . $grid->get('md');
            }

            return trim(implode(' ', $class));
        }

        /**
         *
         * @param type $default
         * @return string
         */
        public function getClass($default = array())
        {
            $class = $default;
            $class [] = '';
            if ($this->get('class') != '')
                $class [] = $this->get('class');
            $class [] = $this->getGridClass();
            return implode(' ', $class);
        }

        /**
         *
         * @param array $controls
         * @return string
         */
        public function getControls($controls = array())
        {

            $html = array();
            foreach ($this->_controls as $control)
            {
                $html [] = $control->getHtml();
            }
            return implode(PHP_EOL, $html);
        }

        private function _getBootstrapVar($version, $name, $default = null)
        {
            if (isset($this->_bsVars[$version]))
            {
                if (isset($this->_bsVars[$version][$name]))
                {
                    return $this->_bsVars[$version][$name];
                }
            }
            return $default;
        }

    }

}