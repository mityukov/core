<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 * 
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage Model
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Model;

/**
 * Module
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 * @Entity (repositoryClass="XLite\Model\Repo\Module")
 * @Table (name="modules")
 */
class Module extends \XLite\Model\AEntity
{
    /**
     * Installed statuses
     */

    const NOT_INSTALLED     = 0;
    const INSTALLED         = 1;
    const INSTALLED_WO_SQL  = 2;
    const INSTALLED_WO_PHP  = 3;
    const INSTALLED_WO_CTRL = 4;


    /**
     * Module id 
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer")
     */
    protected $module_id;

    /**
     * Name 
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * @Column (type="string", length="64")
     */
    protected $name = '';

    /**
     * Enabled 
     * 
     * @var    boolean
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * @Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * Dependencies 
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * @Column (type="string", length="1024")
     */
    protected $dependencies = '';

    /**
     * Mutual modules list
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * @Column (type="string", length="1024")
     */
    protected $mutual_modules = '';

    /**
     * Type 
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * @Column (type="integer")
     */
    protected $type = \XLite\Module\AModule::MODULE_GENERAL;

    /**
     * Installed status
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * @Column (type="integer")
     */
    protected $installed = self::NOT_INSTALLED;

    /**
     * Version
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     * @Column (type="string", length="12")
     */
    protected $version = '1.0';

    /**
     * Main class 
     * 
     * @var    \Xite\Module\AModule
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $mainClass = null; 

    /**
     * Set enabled status
     * 
     * @param boolean $enabled Enabled status
     *  
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setEnabled($enabled)
    {
        $result = false;

        if (!$enabled || $this->canEnable()) {
            $this->enabled = $enabled;
            $result = true;
        }

        return $result;
    }

    /**
     * Get inverted dependencies 
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getInvertedDependencies()
    {
        return $this->getRepository()->findAllByDepend($this->getName());
    }

    /**
     * Disable depended modules
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function disableDepended()
    {
        foreach ($this->getInvertedDependencies() as $module) {
            if ($module->getEnabled()) {
                $module->setEnabled(false);
                \XLite\Core\Database::getEM()->persist($module);
                \XLite\Core\Database::getEM()->flush();
                $module->disableDepended();
            }
        }
    }

    /**
     * Return link to settings form
     *
     * @return string
     * @access public
     * @since  1.0
     */
    public function getSettingsFormLink()
    {
        $link = $this->__call('getSettingsForm');

        return is_null($link)
            ? \XLite\Core\Converter::buildURL('module', '', array('page' => $this->getName()), 'admin.php')
            : $link;
    }

    /**
     * Get module Main class name 
     * 
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getMainClassName()
    {
        return '\XLite\Module\\' . $this->getName() . '\Main';
    }

    /**
     * Get module Main class
     * 
     * @return \XLite\Module\AModule
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getMainClass()
    {
        if (!isset($this->mainClass) && $this->includeMainClass()) {
            $class = $this->getMainClassName();
            $this->mainClass = new $class;

            if (!is_subclass_of($this->mainClass, '\XLite\Module\AModule')) {
                $this->mainClass = null;
            }
        }

        return $this->mainClass;
    }

    /**
     * Include module Main class 
     * 
     * @return boolean
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function includeMainClass()
    {
        $class = $this->getMainClassName();

        if (
            !\XLite\Core\Operator::isClassExists($class)
            && file_exists(LC_CLASSES_DIR . str_replace('\\', LC_DS, $class) . '.php')
        ) {
            require_once LC_CLASSES_DIR . str_replace('\\', LC_DS, $class) . '.php';
        }

        return \XLite\Core\Operator::isClassExists($class);
    }

    /**
     * Get mutual modules 
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getMutualModules()
    {
        return explode(',', $this->mutual_modules);
    }

    /**
     * Set mutual modules list
     * 
     * @param mixed $modules Modules list (string or array)
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setMutualModules($modules)
    {
        $this->mutual_modules = is_string($modules)
            ? $modules
            : implode(',', $modules);
    }

    /**
     * Get dependencies modules 
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getDependencies()
    { 
        return $this->dependencies
            ? explode(',', $this->dependencies)
            : array();
    }

    /**
     * Set dependencies modules list
     * 
     * @param mixed $modules Modules list (string or array)
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function setDependencies($modules)
    {
        $this->dependencies = is_string($modules)
            ? $modules
            : implode(',', $modules);
    }

    /**
     * Get dependencies modules 
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getDependenciesModules()
    {
        return $this->getDependencies()
            ? $this->getRepository()->findAllByNames($this->getDependencies())
            : array();
    }

    /**
     * Check - can module enable or not
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function canEnable()
    {
        $status = true;

        // Check installed status
        if (self::INSTALLED != $this->getInstalled()) {
            $status = false;
        }

        // Check dependencies
        if ($status && $this->getDependencies()) {
            foreach ($this->getDependenciesModules() as $module) {
                if (!$module->getEnabled()) {
                    $status = false;
                    break;
                }
            }
        }

        // Check internal enviroment checker
        if ($status) {
            $status = $this->getMainClass()->check();
        }

        return $status;
    }

    /**
     * Get module hash 
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getHash()
    {
        $class = $this->getMainClassName();

        $path = LC_CLASSES_DIR . $this->getName() . LC_DS;
        $iterator = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        $list = array();
        foreach ($iterator as $f) {
            $list[] = $f->getRealPath();
        }

        sort($list);

        foreach ($list as $k => $path) {
            $list[$k] = hash_file('sha256', $path);
        }

        return hash('sh1512', implode('', $list));
    }

    /**
     * Create module 
     * 
     * @param string $name Name
     *  
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function create($name)
    {
        // Seet common properties
        $this->setName($name);
        $this-setInstalled(self::NOT_INSTALLED);
        $this-setEnabled(false);

        $status = self::INSTALLED;

        $mainClass = $this->getMainClass();

        if ($mainClass) {

            // Set properties
            $this->setMutualModules($mainClass->getMutualModulesList());
            $this->setDependencies($mainClass->getDependenciesList());
            $this->setType($mainClass->getModuleType());
            $this->setVersion($mainClass->getVersion());

            // Install SQL dump
            $installSQLPath = LC_MODULES_DIR . $name . LC_DS . 'install.sql';

            if (file_exists($installSQLPath)) {
                try {
                    \XLite\Core\Database::getInstance()->importSQLFromFile($installSQLPath);

                } catch (\InvalidArgumentException $exception) {

                    \XLite\Logger::getInstance()->log($exception->getMessage(), PEAR_LOG_ERR);
                    $status = self::INSTALLED_WO_SQL;

                } catch (\PDOException $exception) {

                    \XLite\Logger::getInstance()->log($exception->getMessage(), PEAR_LOG_ERR);
                    $status = self::INSTALLED_WO_SQL;
                }
            }

            // Run custom install code
            if (false === $mainClass->installModule($this)) {
                \XLite\Logger::getInstance()->log(
                    sprintf('\'%s\' module custom installation error', $name),
                    PEAR_LOG_ERR
                );
                $status = self::INSTALLED_WO_PHP;
            }

        } else {
            $status = self::INSTALLED_WO_CTRL;
        }

        switch ($status) {
            case self::INSTALLED:
                \XLite\Logger::getInstance()->log(
                    \XLite\Core\Translation::lbl('The X module has been installed successfully', array('module' => $name)),
                    PEAR_LOG_ERR
                );
                break;

            case self::INSTALLED_WO_SQL:
                \XLite\Logger::getInstance()->log(
                    \XLite\Core\Translation::lbl(
                        'The X module has been installed with errors: the DB has not been modified correctly',
                        array('module' => $name)
                    ),
                    PEAR_LOG_ERR
                );
                break;

            case self::INSTALLED_WO_PHP:
                \XLite\Logger::getInstance()->log(
                    \XLite\Core\Translation::lbl(
                        'The X module has been installed incorrectly. Please see the logs for more information',
                        array('module' => $name)
                    ),
                    PEAR_LOG_ERR
                );
                break;

            case self::INSTALLED_WO_CTRL:
                \XLite\Logger::getInstance()->log(
                    \XLite\Core\Translation::lbl(
                        'The X module has been installed, but the module has a wrong module control class',
                        array('module' => $name)
                    ),
                    PEAR_LOG_ERR
                );
                break;



        }

        $module->setInstalled($status);
        \XLite\Core\Database::getEM()->persist($module);
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Uninstall module
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function uninstall()
    {
        $status = true;

        // Uninstall SQL
        $installSQLPath = LC_MODULES_DIR . $this->getName() . LC_DS . 'uninstall.sql';
        if (file_exists($installSQLPath)) {
            try {
                \XLite\Core\Database::getInstance()->importSQLFromFile($installSQLPath);

            } catch (\InvalidArgumentException $exception) {

                \XLite\Logger::getInstance()->log($exception->getMessage(), PEAR_LOG_ERR);
                $status = false;

            } catch (\PDOException $exception) {

                \XLite\Logger::getInstance()->log($exception->getMessage(), PEAR_LOG_ERR);
                $status = false;
            }
        }

        // Run custom uninstall code
        if (false === $mainClass->uninstallModule($this)) {
            \XLite\Logger::getInstance()->log(
                sprintf('\'%s\' module custom deinstallation error', $name),
                PEAR_LOG_ERR
            );
            $status = false;
        }

        // Remove repository (if needed)
        return $status && \Includes\Utils\FileManager::unlinkRecursive(LC_MODULES_DIR . $this->getName());
    }

    /**
     * It's possible to call methods of certain module directly
     * 
     * @param string $method method name
     * @param array  $args   call arguments
     *  
     * @return mixed
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function __call($method, array $args = array())
    {
        return method_exists($this->getMainClass(), $method)
            ? call_user_func_array(array($this->getMainClass(), $method), $args)
            : parent::__call($method, $args);

    }

    /**
     * FIXME - this method is required for Decorator
     * TODO - find a more convinient way to avoid the fatal error
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getName()
    {
        return $this->name;
    }
}
