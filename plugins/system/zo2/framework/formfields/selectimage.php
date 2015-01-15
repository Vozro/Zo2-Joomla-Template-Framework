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
//no direct accees
defined('JPATH_BASE') or die;

/**
 * Class exists checking
 */
if (!class_exists('JFormFieldSelectimage'))
{
    jimport('joomla.form.formfield');

    /**
     * Zo2 backend entrypoint field
     * @since 2.0.0
     * @link http://docs.joomla.org/Creating_a_custom_form_field_type
     */
    class JFormFieldSelectimage extends JFormField
    {

        /**
         * Get the html for input
         *
         * @return string
         */
        public function getInput()
        {
            $html = new Zo2Html();
            $html->set('form', $this);
            return $html->fetch('Zo2://html/admin/layout/selectimage.php');
        }

    }

}