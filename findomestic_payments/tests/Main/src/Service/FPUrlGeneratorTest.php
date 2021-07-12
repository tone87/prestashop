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

//namespace LegacyTests\Modules\findomestic_payments\Redirect;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\findomestic_payments\Service\FPUrlGenerator;
use PrestaShop\Module\findomestic_payments\Configuration\FPUrlConfiguration;
use PrestaShop\Module\findomestic_payments\Factory\FPUrlFactory;

class FPUrlGeneratorTest extends TestCase
{
    protected function setUp()
    {
        define('_PS_ROOT_DIR_', dirname(__FILE__) . '/../../../../../..');
        define('MODULE_ROOT_DIR', dirname(__FILE__) . '/../../../..');

        require_once MODULE_ROOT_DIR . '/vendor/autoload.php';
        /*
        require_once _PS_ROOT_DIR_ . '/config/defines.inc.php';
        require_once _PS_ROOT_DIR_ . '/classes/PrestaShopAutoload.php';
        $autoload = PrestaShopAutoload::getInstance();
        $autoload->load('ModuleFrontController');
        $autoload->load('ModuleInterface');
        $autoload->load('Module');
        */
    }

    /**
     * @covers FPUrlGenerator::getWebAppUrl
     */
    public function testgetWebAppUrl()
    {
        $this->simulatorUrl();
        $this->paymentUrl();
    }


    public function simulatorUrl()
    {
        //require _PS_ROOT_DIR_ . '/modules/findomestic_payments/findomestic_payments.php';
        //require _PS_ROOT_DIR_ . '/modules/findomestic_payments/controllers/front/redirect.php';

        // MOCK CONFIGURATION
        $configuration = $this->getMockBuilder(FPUrlConfiguration::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUrl'])
            ->getMock();
        $configuration->method('getUrl')
            ->will($this->returnValue('http://testUrl.it/live?prf=123&tvei=123456'));


        // MOCK FACTORY
        $factory = $this->getMockBuilder(FPUrlFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConfiguration'])
            ->getMock();

        $factory->method('getConfiguration')
            ->will($this->returnValue($configuration));


        // TEST URL
        $service = new FPUrlGenerator($factory);

        $this->assertEquals(
            'http://testUrl.it/live?prf=123&tvei=123456&importo=10000&versione=L',
            //'?aaa=bbb&ccc=ddd'
            $service->getWebAppUrl(100, true)
        );
    }


    public function paymentUrl()
    {

        //require _PS_ROOT_DIR_ . '/modules/findomestic_payments/findomestic_payments.php';
        //require _PS_ROOT_DIR_ . '/modules/findomestic_payments/controllers/front/redirect.php';

        // MOCK CONFIGURATION
        $configuration = $this->getMockBuilder(FPUrlConfiguration::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUrl'])
            ->getMock();
        $configuration->method('getUrl')
            ->will($this->returnValue('http://testUrl.it/live?prf=123&tvei=123456'));


        // MOCK FACTORY
        $factory = $this->getMockBuilder(FPUrlFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConfiguration'])
            ->getMock();

        $factory->method('getConfiguration')
            ->will($this->returnValue($configuration));


        // TEST URL
        $service = new FPUrlGenerator($factory);
        $user_data = [
            'nomeCliente' => 'Mario',
            'cognomeCliente' => 'Rossi',
            'emailCliente' => 'mario.rossi@email.com',
        ];

        $expected = 'http://testUrl.it/live' .
            '?prf=123' .
            '&tvei=123456' .
            '&nomeCliente=' . $user_data['nomeCliente'] .
            '&cognomeCliente=' . $user_data['cognomeCliente'] .
            '&emailCliente=' . $user_data['emailCliente'] .
            '&importo=10000' .
            '&cartId=10' .
            '&urlRedirect=http://urlRedirect.com?encoded=aaa:bbb-ccc:ddd' .
            '&callBackUrl=http://urlCallBack.com?encoded=eee:fff-ggg:hhh';


        $this->assertEquals(
            $expected,
            //'?aaa=bbb&ccc=ddd'
            $service->getWebAppUrl(100, false, 10, $user_data, 'http://urlRedirect.com?encoded=aaa:bbb-ccc:ddd', 'http://urlCallBack.com?encoded=eee:fff-ggg:hhh')
        );
    }
}
