<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\findomestic_payments\Configuration\FPUrlConfiguration;

class FPUrlConfigurationTest extends TestCase
{
    protected function setUp()
    {
        define('_PS_ROOT_DIR_', dirname(__FILE__) . '/../../../../../..');
        define('MODULE_ROOT_DIR', dirname(__FILE__) . '/../../../..');

        require_once MODULE_ROOT_DIR . '/vendor/autoload.php';
    }

    /**
     * @covers FPUrlConfiguration::getUrl
     */
    public function tesGetUrl()
    {

        //require _PS_ROOT_DIR_ . '/modules/findomestic_payments/findomestic_payments.php';
        //require _PS_ROOT_DIR_ . '/modules/findomestic_payments/controllers/front/redirect.php';

        // MOCK CONFIGURATION
        $configuration = new FPUrlConfiguration('http://testUrl/live', '123456', '123');

        $expected = 'http://testUrl/live?tvei=123456&prf=123';

        $this->assertEquals(
            $expected,
            //'?aaa=bbb&ccc=ddd'
            $configuration->getUrl()
        );
    }
}
