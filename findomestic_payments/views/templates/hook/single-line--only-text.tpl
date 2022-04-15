{*
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
*}
<!--</div>  closing div to exit the current block TODO:: another hook should be found -->

<div class="findomestic-simulator--single-line--only-text findomestic-info-{$color}" >
  <div class="text">
    {l s='Pagamento a rate con'  mod='findomestic_payments'} <span><img id="eps" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/{$color}-logo.svg" alt="{l s='Findomestic' mod='findomestic_payments'}" /></span>
  </div>
  <div class="link">
    <a class="findomestic-view-simulator" href="{$info.url}" target="_blank">
      {l s='Calcola la rata'  mod='findomestic_payments'}
    </a>
  </div>
</div>

<!-- <div>  opening a div again to avoid errors in html -->
