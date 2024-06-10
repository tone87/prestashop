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

require_once dirname(__FILE__) . '/vendor/autoload.php';


use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

//use PrestaShop\Module\findomestic_payments\Service\FPUrlGenerator;


class findomestic_payments extends PaymentModule
{
    public const FP_ACTIVE = 'FINDOMESTIC_ACTIVE';
    public const MODE = 'FINDOMESTIC_MODE';
    public const TVEI = 'FINDOMESTIC_TVEI';
    public const TEST_TVEI = 'FINDOMESTIC_TEST_TVEI';
    public const PRF = 'FINDOMESTIC_PRF';
    public const TEST_PRF = 'FINDOMESTIC_TEST_PRF';
    public const COD_FIN = 'FINDOMESTIC_COD_FIN';
    public const TEST_COD_FIN = 'FINDOMESTIC_TEST_COD_FIN';
    public const URL = 'FINDOMESTIC_URL';
    public const TEST_URL = 'FINDOMESTIC_TEST_URL';
    public const MIN = 'FINDOMESTIC_MIN';
    public const PRODUCT_BUTTON = 'FINDOMESTIC_PRODUCT_BUTTON';
    public const CART_BUTTON = 'FINDOMESTIC_CART_BUTTON';

    public const LOGO_COLOR = 'FINDOMESTIC_LOGO_COLOR';
    public const ICON_COLOR = 'FINDOMESTIC_ICON_COLOR';

    public const LEGAL_TEXT = 'FINDOMESTIC_LEGAL_TEXT';

    public const RETURN_PATH = 'fp/return';
    public const UPDATE_PATH = 'fp/update';

    public const FP_WAITING = 'FP_WAITING_STATUS';
    public const FP_PREACCEPTED = 'FP_PREACCEPTED_STATUS';
    public const FP_DENIED = 'FP_DENIED_STATUS';
    public const FP_ACCEPTED = 'FP_ACCEPTED_STATUS';


    // INIT

    /**
     * findomestic_payments constructor.
     *
     * needed for the module to be recognized by the backend functionalities
     */
    public function __construct()
    {
        $this->name = 'findomestic_payments';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.2';
        $this->author = 'Doing';

        //$this->controllers = array('confirmation');

        parent::__construct();
        $this->displayName = $this->trans('Findomestic Payments', [], 'Modules.findomestic_payments.Admin');
        $this->description = $this->trans('Adds a payment method.', [], 'Modules.findomestic_payments.Admin');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }


    /**
     * @return bool
     *
     * default named module install function, registers hooks, new order states and sets a few basic configuration values
     *  it is automatically called when a module is installed from backend
     */
    public function install()
    {
        $hook = [
            'displayProductAdditionalInfo',
            'displayExpressCheckout',
            'paymentOptions',
            'displayOrderConfirmation',
            'moduleRoutes',
            'displayHeader',
            'displayFooterAfter'
        ];
        if (parent::install()
            && $this->registerHook($hook)
            && $this->installOrderState()
            && $this->isConfigured()
        ) {
            return true;
        } else {
            $this->_errors[] = $this->l('Si è verificato un errore durante l\'installazione del modulo.');

            return false;
        }
    }


    /**
     * @return bool
     *
     * function called during installation
     * sets defaults value to mode (sadbox) and to the minimum amount valid to use this payment methos (0)
     */
    public function isConfigured()
    {

        if (
               !Configuration::updateValue(self::FP_ACTIVE, 0)
            || !Configuration::updateValue(self::MODE, 1)
            || !Configuration::updateValue(self::MIN, 0)
            || !Configuration::updateValue(self::CART_BUTTON, 1)
            || !Configuration::updateValue(self::PRODUCT_BUTTON, 1)
            || !Configuration::updateValue(self::LOGO_COLOR, 0) || !Configuration::updateValue(self::ICON_COLOR, 0)
        ) {
            $this->_errors[] = $this->l('Si è verificato un errore durante l\'installazione del modulo.');
            return false;
        }
        return true;
    }

    public function hookDisplayFooterAfter($params){
        if (Configuration::get(self::LEGAL_TEXT) == '' || Configuration::get(self::FP_ACTIVE) != 1) {
            return '';
        }

        if ($this->checkSettings()) {
            return $this->footerLegalDisclaimer();
        }
    }

    /**
     * @param $cart
     * @return false|string
     * @throws SmartyException
     *
     * this function recovers the link to the findomestic simulator with the full cart price.
     */
    public function footerLegalDisclaimer()
    {
        $this->context->smarty->assign('text', Configuration::get(self::LEGAL_TEXT));

        return $this->context->smarty->fetch('module:findomestic_payments/views/templates/hook/footer-legal-text.tpl');
    }


    // STYLING
    public function hookDisplayHeader($params)
    {
        // Only on product page
        /*
        if ('product' === $this->context->controller->php_self) {
            $this->context->controller->registerStylesheet(
                'module-modulename-style',
                'modules/'.$this->name.'/css/modulename.css',
                [
                    'media' => 'all',
                    'priority' => 200,
                ]
            );

            $this->context->controller->registerJavascript(
                'module-modulename-simple-lib',
                'modules/'.$this->name.'/js/lib/simple-lib.js',
                [
                    'priority' => 200,
                    'attribute' => 'async',
                ]
            );
        }
        */

        // On every pages
        $this->context->controller->registerStylesheet(
            'findomestic_payments-css',
            'modules/' . $this->name . '/views/css/fp.css',
            [
                'media' => 'all',
                'priority' => 200,
            ]
        );
    }


    // SHOW SIMULATOR LINK

    /**
     * @param $params
     * @return false|string
     *
     * hook for simulator link in cart
     */
    public function hookDisplayExpressCheckout($params)
    {
        if (Configuration::get(self::CART_BUTTON) == 0 || Configuration::get(self::FP_ACTIVE) != 1) {
            return '';
        }

        if ($this->checkSettings()) {
            return $this->cartSimulatorLink($params['cart'], Configuration::get(self::CART_BUTTON));
        }
    }

    /**
     * @param $cart
     * @return false|string
     * @throws SmartyException
     *
     * this function recovers the link to the findomestic simulator with the full cart price.
     */
    public function cartSimulatorLink($cart, $template)
    {
        $price = $cart->getordertotal();
        $min = Configuration::get(self::MIN);
        $color = Configuration::get(self::LOGO_COLOR);
        if ($price < $min) {
            return; // exit if product price is too low
        }

        $service = $this->context->controller->getContainer()->get('findomestic_payments_url_generator');
        $info = array('url' => $service->getWebAppUrl($cart->getordertotal(), true));


        $this->context->smarty->assign('info', $info);
        $this->context->smarty->assign('color', $color);
        $this->context->smarty->assign('module_dir', _MODULE_DIR_ . '/findomestic_payments/');

        switch ($template) {
            case 1:
                return $this->context->smarty->fetch('module:findomestic_payments/views/templates/hook/single-line--only-text.tpl');
                break;
            case 2:
                return $this->context->smarty->fetch('module:findomestic_payments/views/templates/hook/multiple-lines--only-text.tpl');
                break;
            default:
                return $this->context->smarty->fetch('module:findomestic_payments/views/templates/hook/single-line--only-text.tpl');
                break;
        }
    }

    /**
     * @param $params
     * @return false|string
     *
     * hook for simulator link in product near price, not usable
     */
    public function hookDisplayProductPriceBlock ($params)
    {
        // NOT USABLE, applies to multiple points
        //return $this->hookDisplayProductAdditionalInfo($params);
    }
    /**
     * @param $params
     * @return false|string
     *
     * hook for simulator link in product
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        if (Configuration::get(self::PRODUCT_BUTTON) == 0 || Configuration::get(self::FP_ACTIVE) != 1) {
            return '';
        }
        if ($this->checkSettings()) {
            return $this->productSimulatorLink($params['product'], Configuration::get(self::PRODUCT_BUTTON));
        }
    }

    /**
     * @param $product
     * @return false|string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     *
     * this function recovers the link to the findomestic simulator with the single product price.
     */
    private function productSimulatorLink($product, $template)
    {
        $product_obj = new Product($product->getId());
        $price = $product_obj->getPrice();
        $min = Configuration::get(self::MIN);
        $color = Configuration::get(self::LOGO_COLOR);

        if ($price < $min) {
            return; // exit if product price is too low
        }

        $service = $this->context->controller->getContainer()->get('findomestic_payments_url_generator');

        $info = array('url' => $service->getWebAppUrl($price, true));

        $this->context->smarty->assign('info', $info);
        $this->context->smarty->assign('color', $color);
        $this->context->smarty->assign('module_dir', _MODULE_DIR_ . '/findomestic_payments/');
        switch ($template) {
            case 1:
                return $this->context->smarty->fetch('module:findomestic_payments/views/templates/hook/single-line--only-text.tpl');
                break;
            case 2:
                return $this->context->smarty->fetch('module:findomestic_payments/views/templates/hook/multiple-lines--only-text.tpl');
                break;
            default:
                return $this->context->smarty->fetch('module:findomestic_payments/views/templates/hook/single-line--only-text.tpl');
                break;
        }

    }




    // PAYMENT HANDLING

    /**
     * @param $params
     * @return PaymentOption[]|void
     * @throws Exception
     *
     * prestashop hook to add a payment method, first checks if the module is inactive or if there are missing settings (checkSettings)
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active || !$this->checkSettings() || Configuration::get(self::FP_ACTIVE) != 1) {
            return;
        }
        $min = Configuration::get(self::MIN);
        $cart = $this->context->cart;
        if ($cart->getordertotal() < $min) {
            return; // exit if not enough value in cart
        }

        $payment_options = [
            $this->getFindomesticPaymentOption($cart),
        ];

        return $payment_options;
    }


    /**
     * @param $cart
     * @return PaymentOption
     * @throws SmartyException
     *
     * returns the payment option data (most of the code is standard for this prestashop functionality) including the relative html
     */
    private function getFindomesticPaymentOption($cart)
    {
        $externalOption = new PaymentOption();

        $service = $this->context->controller->getContainer()->get('findomestic_payments_url_generator');
        $info = array('url' => $service->getWebAppUrl($cart->getordertotal(), true));
        $color = Configuration::get(self::ICON_COLOR);
        $logo = $color.'-icon.svg';


        $this->context->smarty->assign('info', $info);
        $this->context->smarty->assign('color', $color);

        $externalOption->setCallToActionText($this->l('Finanziamento Findomestic'))
            ->setAction($this->context->link->getModuleLink($this->name, 'redirect', array(), true))
            ->setModuleName('findomestic')
            ->setInputs([
                'token' => [
                    'name' => 'module',
                    'type' => 'hidden',
                    'value' => 'findomestic_payments',
                ],
            ])
            ->setAdditionalInformation($this->context->smarty->fetch('module:findomestic_payments/views/templates/front/payment_infos.tpl'))
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/'. $logo));

        return $externalOption;
    }


    /**
     * @param $params
     * @return string
     *
     * returns the custom text/content in the order confirmation page
     */
    public function hookDisplayOrderConfirmation($params)
    {
        if ($params['order']->module == 'findomestic_payments') {
            return Module::fetch('module:findomestic_payments/views/templates/hook/payment_return.tpl');
        }
    }


    // HANDLE Requests

    /**
     * @return void
     *
     * this hook implementation creates the endpoints
     * the endpoints are handled by the controllers under controllers/front
     *
     */
    public function hookModuleRoutes()
    {
        //$head = Configuration::get('your_config', $this->language->id);

        $routes = [
            'module-findomestic_payments-update' => [ //module-[MOCULE_NAME]-[CONTROLLER_FILE_NAME]
                'rule' => self::UPDATE_PATH, // any path
                'keywords' => [],
                'controller' => 'update', // [CONTROLLER_FILE_NAME]
                'params' => [
                    'fc' => 'module', //mandatory
                    'module' => 'findomestic_payments', // [MOCULE_NAME] mandatory
                ]
            ],
            'module-findomestic_payments-respond' => [ //module-[MOCULE_NAME]-[CONTROLLER_FILE_NAME]
                //'rule' => self::RETURN_PATH, // any path
                'rule' => self::RETURN_PATH, // any path
                'keywords' => [],
                'controller' => 'respond', // [CONTROLLER_FILE_NAME]
                'params' => [
                    'fc' => 'module', //mandatory
                    'module' => 'findomestic_payments', // [MOCULE_NAME] mandatory
                ]
            ],
        ];

        return $routes;
    }



    // CONFIGURATION

    /**
     * @return string
     *
     * backend configuration function, builds and saves the configuration form, can be extended
     */
    public function getContent()
    {
        if (Tools::isSubmit(self::MODE)) {
            
            Configuration::updateValue(self::FP_ACTIVE, Tools::getValue(self::FP_ACTIVE));
            Configuration::updateValue(self::MODE, Tools::getValue(self::MODE));
            Configuration::updateValue(self::TVEI, Tools::getValue(self::TVEI));
            Configuration::updateValue(self::TEST_TVEI, Tools::getValue(self::TEST_TVEI));
            Configuration::updateValue(self::PRF, Tools::getValue(self::PRF));
            Configuration::updateValue(self::TEST_PRF, Tools::getValue(self::TEST_PRF));
            Configuration::updateValue(self::COD_FIN, Tools::getValue(self::COD_FIN));
            Configuration::updateValue(self::TEST_COD_FIN, Tools::getValue(self::TEST_COD_FIN));
            Configuration::updateValue(self::URL, Tools::getValue(self::URL));
            Configuration::updateValue(self::TEST_URL, Tools::getValue(self::TEST_URL));
            Configuration::updateValue(self::MIN, Tools::getValue(self::MIN));
            Configuration::updateValue(self::CART_BUTTON, Tools::getValue(self::CART_BUTTON));
            Configuration::updateValue(self::PRODUCT_BUTTON, Tools::getValue(self::PRODUCT_BUTTON));
            Configuration::updateValue(self::LOGO_COLOR, Tools::getValue(self::LOGO_COLOR));
            Configuration::updateValue(self::ICON_COLOR, Tools::getValue(self::ICON_COLOR));
            Configuration::updateValue(self::LEGAL_TEXT, Tools::getValue(self::LEGAL_TEXT), true);

            //$this->_clearBlockcategoriesCache();

            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&conf=6');
        }
        if (Tools::usingSecureMode()) {
            $domain = Tools::getShopDomainSsl(true, true);
        } else {
            $domain = Tools::getShopDomain(true, true);
        }

        //$this->context->controller->addCSS($this->_path.'/views/css/admin.css');
        $this->context->controller->addJS($this->_path . '/views/js/config.js');

        $this->context->smarty->assign(
            array(
                'findomestic_active' => Configuration::get(self::FP_ACTIVE),
                'findomestic_mode' => Configuration::get(self::MODE),
                'findomestic_tvei' => Configuration::get(self::TVEI),
                'findomestic_test_tvei' => Configuration::get(self::TEST_TVEI),
                'findomestic_prf' => Configuration::get(self::PRF),
                'findomestic_test_prf' => Configuration::get(self::TEST_PRF),
                'findomestic_cod_fin' => Configuration::get(self::COD_FIN),
                'findomestic_test_cod_fin' => Configuration::get(self::TEST_COD_FIN),
                'findomestic_url' => Configuration::get(self::URL),
                'findomestic_test_url' => Configuration::get(self::TEST_URL),
                'findomestic_min' => Configuration::get(self::MIN),
                'findomestic_product_button' => Configuration::get(self::PRODUCT_BUTTON),
                'findomestic_cart_button' => Configuration::get(self::CART_BUTTON),
                'findomestic_logo_color' => Configuration::get(self::LOGO_COLOR),
                'findomestic_icon_color' => Configuration::get(self::ICON_COLOR),
                'findomestic_legal_text' => Configuration::get(self::LEGAL_TEXT),
            )
        );

        return $this->display($this->_path, 'views/templates/admin/main.tpl');
    }

    /**
     * @return bool
     *
     * check if the settings for the current mode (1: sandbox, 0: live) are compiled
     */
    public function checkSettings()
    {
        if (Configuration::get(self::MODE)) {
            if (!$this->checkSetting(self::TEST_TVEI) || !$this->checkSetting(self::TEST_PRF) || !$this->checkSetting(self::TEST_URL)) {
                return false;
            }
        } else {
            if (!$this->checkSetting(self::TVEI) || !$this->checkSetting(self::PRF) || !$this->checkSetting(self::URL)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $setting
     * @return bool
     *
     * checks if a given setting has a value
     */
    public function checkSetting($setting)
    {
        $value = Configuration::get($setting);
        if ($value == '' || $value == null) {
            return false;
        }
        return true;
    }


    // INSTALLATION

    public function addOrderStatus($status, $languages, $color, $images = null)
    {
        if (!Configuration::get($status)
            || !Validate::isLoadedObject(new OrderState(Configuration::get($status)))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                switch (Tools::strtolower($language['iso_code'])) {
                    case 'it':
                        $order_state->name[$language['id_lang']] = $languages['it'];
                        break;
                    default:
                        $order_state->name[$language['id_lang']] = $languages['en'];
                        break;
                }
            }
            $order_state->send_email = false; // MAYBE SEND EMAIL
            $order_state->color = $color;
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;


            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_ . 'findomestic_payments/logo.png';
                $destination = _PS_ROOT_DIR_ . '/img/fp/' . (int)$order_state->id . '.gif';
                copy($source, $destination);
            }

            return Configuration::updateValue($status, (int)$order_state->id);
        }else{
            $order_state = new OrderState(Configuration::get($status));
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                switch (Tools::strtolower($language['iso_code'])) {
                    case 'it':
                        $order_state->name[$language['id_lang']] = $languages['it'];
                        break;
                    default:
                        $order_state->name[$language['id_lang']] = $languages['en'];
                        break;
                }
            }
            $order_state->update();
        }
        return true;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * adds a new order status "Awaiting for Findomestic payment", the code is pretty much mandatory for prestashop
     */
    public function addWaitingOrderStatus()
    {
        $languages = [
            'it' => 'Findomestic Inserimento dati in corso',
            'en' => 'Findomestic Awaiting data compilation',
        ];
        return $this->addOrderStatus(self::FP_WAITING, $languages, '#c9c567');
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * adds a new order status "Pagamento Findomestic pre autorizzato", the code is pretty much mandatory for prestashop
     */
    public function addPreAcceptedStatus()
    {
        $languages = [
            'it' => 'Findomestic Richiesta in valutazione',
            'en' => 'Findomestic request under scrutiny',
        ];
        return $this->addOrderStatus(self::FP_PREACCEPTED, $languages, '#34209E');
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * adds a new order status "Awaiting for Findomestic payment", the code is pretty much mandatory for prestashop
     */
    public function addDeniedOrderStatus()
    {
        $languages = [
            'it' => 'Findomestic Richiesta non accolta',
            'en' => 'Findomestic Request denied',
        ];
        return $this->addOrderStatus(self::FP_DENIED, $languages, '#E74C3C');
    }


    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * adds a new order status "Awaiting for Findomestic payment", the code is pretty much mandatory for prestashop
     */
    public function addAcceptedOrderStatus()
    {
        $languages = [
            'it' => 'Findomestic Richiesta accolta',
            'en' => 'Findomestic Request accepted',
        ];
        return $this->addOrderStatus(self::FP_ACCEPTED, $languages, '#01B887');
    }


    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * prepare the new order statuses
     */
    public function installOrderState()
    {
        if ($this->addPreAcceptedStatus()
            && $this->addWaitingOrderStatus()
            && $this->addAcceptedOrderStatus()
            && $this->addDeniedOrderStatus()

        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     *
     * standard uninstallation call for prestashop, more functionality might be added
     */
    public function uninstall()
    {
        if ((parent::uninstall() == false)) {
            return false;
        }

        if ($this->uninstallOrderStates() == false) {
            return false;
        }

        return true;
    }

    public function uninstallOrderStates()
    {
        /* @var $orderState OrderState */
        $result = true;
        $collection = new PrestaShopCollection('OrderState');
        $collection->where('module_name', '=', $this->name);
        $orderStates = $collection->getResults();

        if ($orderStates == false) {
            return $result;
        }

        foreach ($orderStates as $orderState) {
            $result &= $orderState->delete();
        }

        return $result;
    }
}
