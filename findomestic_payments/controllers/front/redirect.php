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

//namespace Modules\findomestic_payments\findomestic_paymentsRedirectModuleFront;

class findomestic_paymentsRedirectModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if ($this->context->cart->id_customer == 0 || $this->context->cart->id_address_delivery == 0 || $this->context->cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'findomestic_payments') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('Questo metodo di pagamento non è disponibile.', 'validation'));
        }


        if ($this->validateOrder()) {
            $amount = $this->context->cart->getordertotal();
            $redirect_url = $this->getRedirectUrl();
            $callback_url = $this->getCallBackUrl();
            $user_data = $this->getUserData();

            $service = $this->context->controller->getContainer()->get('findomestic_payments_url_generator');
            $findomestic_url = $service->getWebAppUrl($amount, false, $this->context->cart->id, $user_data, $redirect_url, $callback_url);

            // https://b2ctest.ecredit.it/clienti/pmcrs/eprice/mcommerce/pages/simulatore.html?tvei=123123&prf=7079&nomeCliente=TEST&cognomeCliente=Doing&emailCliente=francesco.mura@doing.com&importo=38940&cartId=6&urlRedirect=http://localhost/en/fp/return?encoded=key:9209b2cb631d5115828591fecde44c65-id_order:6-id_cart:6-id_module:70&callBackUrl=http://localhost/en/fp/update?encoded=key:9209b2cb631d5115828591fecde44c65-id_order:6
            Tools::redirect($findomestic_url);
        } else {
            // TODO:: HANDLE ERROR
            die($this->module->l('Si è verificato un errore nella gestione del tuo ordine.', 'validation'));
        }


        exit;
    }


    /**
     * @return array
     *
     * recovers the userData to be sent at Findomestic
     */
    public function getUserData()
    {
        $array = [];

        $array['nomeCliente'] = $this->context->customer->firstname;
        $array['cognomeCliente'] = $this->context->customer->lastname;
        $array['emailCliente'] = $this->context->customer->email;

        return $array;
    }


    /**
     * @return string
     *
     * builds the callback url to pass to the findomestic web app
     */
    public function getCallBackUrl()
    {
        $return_array = [];

        $return_array['key'] = $this->context->customer->secure_key;
        $return_array['id_order'] = $this->id_order;

        $langs = Language::getLanguages(true, $this->context->shop->id);

        $url_lang = '';
        if (count($langs) > 1) {
            $url_lang = '/' . $this->context->language->iso_code;
        }

        $base_url = _PS_BASE_URL_ . $url_lang . '/' . findomestic_payments::UPDATE_PATH;
        return $base_url . '?encoded=' . FPEncodeDecode::encodeUrlArgs($return_array);
    }

    /**
     * @return string
     *
     * builds the return url to pass to the findomestic web app
     */
    public function getRedirectUrl()
    {
        $return_array = [];

        $return_array['key'] = $this->context->customer->secure_key;
        $return_array['id_order'] = $this->id_order;
        $return_array['id_cart'] = $this->context->cart->id;
        $return_array['id_module'] = $this->module->id;


        $langs = Language::getLanguages(true, $this->context->shop->id);
        $url_lang = '';
        if (count($langs) > 1) {
            $url_lang = '/' . $this->context->language->iso_code;
        }

        $base_url = _PS_BASE_URL_ . $url_lang . '/' . findomestic_payments::RETURN_PATH;
        return $base_url . '?encoded=' . FPEncodeDecode::encodeUrlArgs($return_array);
    }

    /**
     * @return bool
     * @throws Exception
     *
     * changes the order's status to awaiting findomestic payment
     */
    public function validateOrder()
    {
        if (!isset($this->context->cart)) {
            return false;
        }

        $order_status = (int)Configuration::get(findomestic_payments::FP_WAITING);

        $order_total = $this->context->cart->getOrderTotal(true);


        if ($this->module->validateOrder($this->context->cart->id, $order_status, $order_total, "Findomestic", null, array(), null, false, $this->context->cart->secure_key)) {
            $this->id_order = Order::getIdByCartId($this->context->cart->id);

            return true;
        }

        return false;
    }
}
