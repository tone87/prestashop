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

$(document).ready(function () {
  // Multistore
  var old = $('.bootstrap.panel');
  $('#content').after(old);
  old.css('margin-left', '12%');

  // Test mode
  toggleTestMode();
  $('#configuration_form input').on('change', function () {
    toggleTestMode();
  });


});


function toggleTestMode() {
  const isTestModeActive = $('input[name="FINDOMESTIC_MODE"]:checked', '#configuration_form').val();

  if (isTestModeActive == '1') {
    $('#tvei').parent().parent().hide();
    $('#prf').parent().parent().hide();
    $('#cod_fin').parent().parent().hide();
    $('#url').parent().parent().hide();
    $('#test_tvei').parent().parent().show();
    $('#test_prf').parent().parent().show();
    $('#test_cod_fin').parent().parent().show();
    $('#test_url').parent().parent().show();
  } else {
    $('#tvei').parent().parent().show();
    $('#prf').parent().parent().show();
    $('#cod_fin').parent().parent().show();
    $('#url').parent().parent().show();
    $('#test_tvei').parent().parent().hide();
    $('#test_prf').parent().parent().hide();
    $('#test_cod_fin').parent().parent().hide();
    $('#test_url').parent().parent().hide();
  }
}
