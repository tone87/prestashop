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

/*
* TO NAME THE CLASS USE THIS PATTERN "[MODULE_NAME][FILE_NAME]ModuleFrontController"
*/


/**
 * findomestic_paymentsupdateModuleFrontController
 * controller to handle the endpoint for updating the order
 */
class findomestic_paymentsUpdateModuleFrontController extends AbstractRestController
{
    /**
     * Method processGetRequest
     * at the moment used only to check if the service is responding by calling the enpoint on browser and to do some testing on objects
     * TODO:: Clean this up to return a neutral or void response (or implement the method to return other kinds of results)
     *
     * @return void
     */
    protected function processGetRequest()
    {
        $result = Tools::getValue('codiceEsitoFindomestic');
        $encoded = Tools::getValue('encoded');
        $unencoded = FPEncodeDecode::unencode($encoded);

        if ($unencoded == false) {
            echo 0;
            exit;
        }

        PrestaShopLogger::addLog('FINDOMESTIC CALLBAK - PARAMETERS: ' . json_encode($_GET));
        if ($result == 0) {
            PrestaShopLogger::addLog('FINDOMESTIC CALLBAK - order pre accepted:  ' . $unencoded['id_order']);
            $this->updateOrder($unencoded['id_order'], Configuration::get(findomestic_payments::FP_PREACCEPTED));
            echo 1;
            exit;
        }
        PrestaShopLogger::addLog('FINDOMESTIC CALLBAK - order error:  ' . $unencoded['id_order']);
        $this->updateOrder($unencoded['id_order'], Configuration::get(findomestic_payments::FP_DENIED)); // 8 is the default error order state for PRESTASHOP
        echo 1;
        exit;
    }


    private function updateOrder($order_id, $status)
    {
        $objOrder = new Order($order_id); //order with id=1
        $history = new OrderHistory();
        $history->id_order = (int)$objOrder->id;
        $history->changeIdOrderState($status, (int)($objOrder->id)); //order status=3
        $history->save();
    }


    /**
     * Method processPostRequest
     * handles the post request s
     *
     * @return void
     */
    protected function processPostRequest()
    {
        $this->ajaxRender(json_encode([
            'success' => true,
            'operation' => 'post',
        ]));
        exit;
    }

    protected function processPutRequest()
    {
        // do something then output the result
        $this->ajaxRender(json_encode([
            'success' => true,
            'operation' => 'put'
        ]));
    }

    /**
     * Method processDeleteRequest
     * handles the delete request, at the moment does nothing
     *
     * @return void
     */
    protected function processDeleteRequest()
    {
        // do something then output the result
        $this->ajaxRender(json_encode([
            'success' => true,
            'operation' => 'delete'
        ]));
        exit;
    }

    /**
     * Method error
     * simple error method
     * @return void
     */
    protected function error()
    {
        // do something then output the result
        $this->ajaxRender(json_encode([
            'success' => false,
            'operation' => 'ERROR'
        ]));
        exit;
    }
}
