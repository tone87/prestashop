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
 * findomestic_paymentsrespondModuleFrontController
 * controller to handle the endpoint for updating the order
 */
class findomestic_paymentsRespondModuleFrontController extends AbstractRestController
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
        $result = Tools::getValue('esito');
        $encoded = Tools::getValue('encoded');
        $unencoded = FPEncodeDecode::unencode($encoded);

        if ($unencoded == false) {
            echo 'no data available to be processed';
            exit;
        }


        if ($result != 'OK') {
            $this->setTemplate('module:findomestic_payments/views/templates/front/order-confirmation-failed-17.tpl'); // VERSION 17 is currently the only compatible version
            $this->context->smarty->assign(array(
                'stripe_order_url' => $this->context->link->getPageLink('order')
            ));

            return;
        }


        $langs = Language::getLanguages(true, $this->context->shop->id);
        $url_lang = '';
        if (count($langs) > 1) {
            $url_lang = '/' . $this->context->language->iso_code;
        }

        $redirect_url = _PS_BASE_URL_ . $url_lang . '/order-confirmation?id_cart=' . $unencoded['id_cart'] . '&id_module=' . $unencoded['id_module'] . '&id_order=' . $unencoded['id_order'] . '&key=' . $unencoded['key'];

        Tools::redirect($redirect_url);

        exit;
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
