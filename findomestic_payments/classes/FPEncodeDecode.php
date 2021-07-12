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

class FPEncodeDecode
{
    public static function unencode($encoded)
    {


        if ($encoded == null || Tools::strlen($encoded) == 0) {
            return false;
        }

        $args = [];
        $exploded = explode('-', $encoded);

        if (count($exploded) == 0) {
            return false;
        }

        foreach ($exploded as $argument) {
            $data = explode(':', $argument);
            $args[$data[0]] = $data[1];
        }

        return $args;
    }


    /**
     * @param $args
     * @return false|string
     *
     * encode the return args that will be handled by the endpoint (maybe move to the module class)
     */
    public static function encodeUrlArgs($args)
    {
        if (count($args) == 0) {
            return false;
        }
        $string = '';
        foreach ($args as $key => $value) {
            if ($string != '') {
                $string .= '-';
            }
            $string .= $key . ':' . $value;
        }

        return $string;
    }
}
