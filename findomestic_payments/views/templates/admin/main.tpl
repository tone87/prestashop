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

<div class="tabs">
  {*
	<div class="sidebar navigation col-md-2">

		<nav class="list-group categorieList">
			<a class="list-group-item migration-tab" href="#findomestic_step_1">
			  	<i class="icon-power-off pstab-icon"></i>
			  	{l s='Connessione' mod='findomestic_payments'}
			</a>
			<a class="list-group-item migration-tab" href="#findomestic_step_2">
			  	<i class="icon-ticket pstab-icon"></i>
			  	{l s='Altro' mod='findomestic_payments'}
			</a>
		</nav>
	</div>
	*}

	<div class="col-md-10">
		<div class="content-wrap panel">
			<section id="section-shape-1">
				{include file="./_partials/configuration.tpl"}
			</section>
      {*
			<section id="section-shape-2">
				{include file="./_partials/other.tpl"}
			</section>
*}
		</div>
	</div>

</div>
