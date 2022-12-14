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

<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-12 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/logo.png" class="img" height="60" />
		</div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p>
					<h4>{l s='Configuration de votre compte' mod='powerspace'}</h4>
					<p>Pour recevoir vos paiements, merci de remplir vos informations bancaires en bas de page.</p>
					<p>Pour toute demande d'informations, vous pouvez nous contacter par email : <a href="mailto:prestashop@powerspace.com">prestashop@powerspace.com</a></p>
				</p>
				{if empty($position)}
					<br />
					<p>
						<b>Votre compte n'est pas encore activé dans la plateforme Powerspace, et les pubs ne peuvent pas encore être affichée. Veuillez patienter un à deux jours ouvrés.</b>
					</p>
				{/if}

				<br />
				<p>
					{if isset($precMonth)}
						Le mois précédent, vous avez générez : <strong>{$precMonth|escape:'html':'UTF-8'} €</strong>. Vous recevrez votre virement sous 60 jours.<br><br>
					{/if}
					{if isset($month)}
						Ce mois ci, vous avez générez : <strong>{$month|escape:'html':'UTF-8'} €</strong>.
					{/if}
				</p>
			</div>
		</div>
	</div>
</div>
