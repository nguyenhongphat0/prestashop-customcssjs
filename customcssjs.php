<?php
/**
 *  @author nguyenhongphat0 <nguyenhongphat28121998@gmail.com>
 *  @copyright 2018 nguyenhongphat0
 *  @license https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Module main class
 */
class CustomCssJs extends Module
{
    public function __construct()
    {
        $this->name = 'customcssjs';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'nguyenhongphat0';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => _PS_VERSION_
        );
        $this->bootstrap = true;
        $this->module_key = '26d140b284bb3ecd72f3f0f09131ab45';
        parent::__construct();

        $this->displayName = $this->l('Custom CSS and JS');
        $this->description = $this->l('This module allow you to insert into everypage custom CSS code in <head> tag and JavaScript code before the closing </body> tag.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install() {
        return parent::install()
            && $this->registerHook([
                'displayHeader',
                'displayBeforeBodyClosingTag',
            ]);
    }
    
    public function hookDisplayHeader(array $params)
    {
        return '<style>'.Configuration::get('css').'</style>';
    }

    public function hookDisplayBeforeBodyClosingTag(array $params)
    {
        return '<script>'.Configuration::get('js').'</script>';
    }

    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit'.$this->name)) {
            $css = $_POST['css'];
            $js = $_POST['js'];

            Configuration::updateValue('css', $css);
            Configuration::updateValue('js', $js);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Custom CSS and JS'),
            ],
            'input' => [
                [
                    'type' => 'textarea',
                    'label' => $this->l('CSS'),
                    'name' => 'css'
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('JS'),
                    'name' => 'js'
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->fields_value['css'] = Configuration::get('css');
        $helper->fields_value['js'] = Configuration::get('js');

        return $helper->generateForm($fieldsForm);
    }
}
