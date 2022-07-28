<?php
/**
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
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Powerspace extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'powerspace';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
        $this->author = 'Powerspace';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Powerspace - Page de confirmation');
        $this->description = $this->l('Monetize your order confirmation page in one click, and earn money !');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('POWERSPACE_LIVE_MODE', true);

        include(dirname(__FILE__).'/classes/PowerspaceApi.php');

        // Call the API for get the ID
        $response = PowerspaceApi::client()->post(PowerspaceApi::URL_API . 'config', [
            'json' => [
                'domain' => $this->context->shop->getBaseURL(),
                'email' => Configuration::get('PS_SHOP_EMAIL'),
                'address' => Configuration::get('PS_SHOP_ADDR1') . ' ' .  Configuration::get('PS_SHOP_ZIPCODE') . Configuration::get('PS_SHOP_CITY'),
                'company' => Configuration::get('PS_SHOP_NAME'),
                'website' => $this->context->shop->getBaseURL(),
            ]
        ]);

        foreach(['company', 'address', 'website', 'email', 'iban', 'bic', 'bank_name'] as $field) {
            Configuration::updateValue('POWERSPACE_' . strtoupper($field), '');
        }

        $data = json_decode($response->getBody()->getContents(), true);
        if(isset($data['data'])) {
            foreach(['uuid', 'company', 'address', 'website'] as $field) {
                if(isset($data['data'][$field])) {
                    Configuration::updateValue('POWERSPACE_' . strtoupper($field), $data['data'][$field]);
                }
            }
        }

        if(!isset($uuid)) {
            throw new Exception('Powerspace: Unable to get the ID');
        }

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayOrderConfirmation');
    }

    public function uninstall()
    {
        Configuration::deleteByName('POWERSPACE_LIVE_MODE');

        include(dirname(__FILE__).'/classes/PowerspaceApi.php');
        PowerspaceApi::client()->delete(PowerspaceApi::URL_API . 'config/' . Configuration::get('POWERSPACE_UUID'));

        Configuration::deleteByName('POWERSPACE_UUID');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitPowerspaceModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        include(dirname(__FILE__).'/classes/PowerspaceApi.php');

        $response = PowerspaceApi::client()->get(PowerspaceApi::URL_API . 'stats/' . Configuration::get('POWERSPACE_UUID'));
        $data = json_decode($response->getBody()->getContents(), true);

        $this->context->smarty->assign('precMonth', $data['data']['precMonth']);
        $this->context->smarty->assign('month', $data['data']['month']);
        $this->context->smarty->assign('position', Configuration::get('POWERSPACE_DATA_POS'));

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPowerspaceModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'POWERSPACE_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'POWERSPACE_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'POWERSPACE_BANK_NAME',
                        'label' => $this->l('Nom de votre banque'),
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'name' => 'POWERSPACE_IBAN',
                        'label' => $this->l('IBAN'),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'POWERSPACE_BIC',
                        'label' => $this->l('BIC'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'POWERSPACE_LIVE_MODE' => Configuration::get('POWERSPACE_LIVE_MODE', true),
            'POWERSPACE_EMAIL' => Configuration::get('POWERSPACE_EMAIL', 'contact@prestashop.com'),
            'POWERSPACE_IBAN' => Configuration::get('POWERSPACE_IBAN', null),
            'POWERSPACE_BIC' => Configuration::get('POWERSPACE_BIC', null),
            'POWERSPACE_BANK_NAME' => Configuration::get('POWERSPACE_BANK_NAME', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        // Envoi à l'API
        include(dirname(__FILE__).'/classes/PowerspaceApi.php');

        // Call the API for get the ID
        PowerspaceApi::client()->put(PowerspaceApi::URL_API . 'config/' . Configuration::get('POWERSPACE_UUID'), [
            'json' => [
                'email' => Configuration::get('POWERSPACE_EMAIL'),
                'bank_name' => Configuration::get('POWERSPACE_BANK_NAME'),
                'iban' => Configuration::get('POWERSPACE_IBAN'),
                'bic' => Configuration::get('POWERSPACE_BIC'),
            ]
        ]);
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookDisplayOrderConfirmation()
    {
        if(!Configuration::hasKey('POWERSPACE_DATA_POS') && Configuration::hasKey('POWERSPACE_UUID')) {
            include(dirname(__FILE__).'/classes/PowerspaceApi.php');

            // Call the API for get the ID
            $response = PowerspaceApi::client()->get(PowerspaceApi::URL_API . 'config/' . Configuration::get('POWERSPACE_UUID'));
            $data = json_decode($response->getBody()->getContents(), true);
            if(isset($data['data']) && isset($data['data']['position'])) {
                Configuration::updateValue('POWERSPACE_DATA_POS', $data['data']['position']);
            }
        }

        if(Configuration::get('POWERSPACE_DATA_POS') && Configuration::get('POWERSPACE_LIVE_MODE') == 'true') {
            $this->context->smarty->assign('POWERSPACE_DATA_POS', Configuration::get('POWERSPACE_DATA_POS'));
            return $this->display(__FILE__, 'views/templates/hook/powerspace.tpl');
        }
    }
}
