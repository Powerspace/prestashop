{*
* 2007-2022 PrestaShop
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
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Core Powerspace Ad Tag -->
<!-- Put the following div where you want to display the ad -->
<div class="pws-pos" data-pos="{$POWERSPACE_DATA_POS|escape:'htmlall':'UTF-8'}}" ></div>
<!--This code has to be inject inside your HTML pages of your website, just before the html closing tag. -->
<script>document.addEventListener('DOMContentLoaded',function(){window.PWS=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],v=window.PWS||{};if(d.getElementById(id))return v;js=d.createElement(s);js.id = id;js.src="//cdn.powerspace.com/pws.js?t="+((new Date()).getTime()/36e5).toFixed();fjs.parentNode.insertBefore(js, fjs);return v;}(document,"script","pws-js"));});</script>