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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA

*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\Module\findomestic_payments\Service;

use PrestaShop\Module\findomestic_payments\Factory\FPurlFactory;

class FPUrlGenerator
{
    private $configuration;

    public function __construct($factory)
    {
        $this->configuration = $factory->getConfiguration();
    }

    public function getWebAppUrl($amount, $simulator = false, $cart_id = null, $user_data = null, $urlRedirect = null, $callBackUrl = null)
    {
        $array = [];
        $amount = $amount*100;
        if ($simulator) {
            $array['importo'] = $amount;
            $array['versione'] = 'L';
        } else {
            $array = $user_data;
            $array['importo'] = $amount;
            $array['cartId'] = $cart_id;
            $array['urlRedirect'] = $urlRedirect;
            $array['callBackUrl'] = $callBackUrl;
        }
        return $this->configuration->getUrl() . $this->urlArgsToString($array); // GET url should return something like https://b2ctest.ecredit.it/clienti/pmcrs/eprice/mcommerce/pages/simulatore.html?tvei=100394259&prf=7079&importo=200&versione=L
    }


    /**
     * @param $args
     * @return false|string
     *
     * CONVERTS ARRAY TO URL ARGUMENTS (no leading "?")
     */
    public function urlArgsToString($args)
    {
        if (count($args)  == 0) {
            return false;
        }
        $string = '';
        foreach ($args as $key => $value) {
            $string .= '&';

            $string.= $key.'='.$value;
        }

        return $string;
    }
}
