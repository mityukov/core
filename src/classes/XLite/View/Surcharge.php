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
 * PHP version 5.3.0
 *
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\View;

/**
 * Common surcharge
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Surcharge extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_SURCHARGE = 'surcharge';
    const PARAM_CURRENCY  = 'currency';


    /**
     * Register CSS files
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'common/surcharge.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultTemplate()
    {
        return 'common/surcharge.tpl';
    }

    /**
     * Return surcharge
     *
     * @return float
     * @see    ____func_see____
     * @since  1.0.2
     */
    protected function getSurcharge()
    {
        return $this->getParam(self::PARAM_SURCHARGE);
    }

    /**
     * Return currency
     *
     * @return \XLite\Model\Currency
     * @see    ____func_see____
     * @since  1.0.2
     */
    protected function getCurrency()
    {
        return $this->getParam(self::PARAM_CURRENCY);
    }

    /**
     * Define widget parameters
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_SURCHARGE => new \XLite\Model\WidgetParam\Float('Surcharge', null),
            self::PARAM_CURRENCY  => new \XLite\Model\WidgetParam\Object('Currency', \XLite::getInstance()->getCUrrency(), false, 'XLite\Model\Currency'),
        );
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isVisible()
    {
        return parent::isVisible() && !is_null($this->getParam(self::PARAM_SURCHARGE));
    }
}