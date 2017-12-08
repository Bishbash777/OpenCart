<?php

class ControllerPaymentCardstream extends Controller {

    private $error = array();

    static private $url;
    static private $curi;
    static private $token;

    public function __construct($registry) {
        parent::__construct($registry);
        $module = strtolower(basename(__FILE__, '.php'));
        self::$url = 'payment/' . $module;
        self::$curi = $module;
        self::$token = 'token=' . $this->session->data['token'];
    }

    public function index() {

        $this->load->language(self::$url);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting(self::$curi, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link(
                'extension/payment',
                self::$token . '&type=payment',
                true
            ));

        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');

        // place holder texts
        $data['text_merchantid'] = $this->language->get('text_merchantid');
        $data['text_merchantsecret'] = $this->language->get('text_merchantsecret');
        $data['text_currencycode'] = $this->language->get('text_currencycode');
        $data['text_countrycode'] = $this->language->get('text_countrycode');

        $data['text_enabled']    = $this->language->get('text_enabled');
        $data['text_disabled']   = $this->language->get('text_disabled');
        $data['text_all_zones']  = $this->language->get('text_all_zones');
        $data['text_yes']        = $this->language->get('text_yes');
        $data['text_no']         = $this->language->get('text_no');
        $data['text_live']       = $this->language->get('text_live');
        $data['text_successful'] = $this->language->get('text_successful');
        $data['text_fail']       = $this->language->get('text_fail');

        $data['entry_merchantid']      = $this->language->get('entry_merchantid');
        $data['entry_merchantsecret']  = $this->language->get('entry_merchantsecret');
        $data['entry_order_status']    = $this->language->get('entry_order_status');
        $data['entry_geo_zone']        = $this->language->get('entry_geo_zone');
        $data['entry_form_responsive'] = $this->language->get('entry_form_responsive');
        $data['entry_status']          = $this->language->get('entry_status');
        $data['entry_sort_order']      = $this->language->get('entry_sort_order');

        $data['entry_currencycode'] = $this->language->get('entry_currencycode');
        $data['entry_countrycode']  = $this->language->get('entry_countrycode');

        $data['button_save']   = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        // module type selection
        $data['entry_module_type']   = $this->language->get('entry_module_type');
        $data['entry_module_hosted'] = $this->language->get('entry_module_hosted');
        $data['entry_module_iframe'] = $this->language->get('entry_module_iframe');

        // $is_secure = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') || (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https'));
        // if ($is_secure) {
        //  $data['entry_module_direct'] = $this->language->get('entry_module_direct');
        // }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', self::$token, true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', self::$token, true),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link(self::$url, self::$token, true),
            'separator' => ' :: '
        );


        $data['action'] =
            $this->url->link(self::$url, self::$token, true);

        $data['cancel'] =
            $this->url->link('extension/payment', self::$token, true);

        $fields = array(
            'warning',
            'permissions',
            'merchantid',
            'merchantsecret',
            'countrycode',
            'currencycode',
            'status_id',
            'form_responsive',
            'sort_order',
            'module_type',
            'status',
            'order_status_id',
            'geo_zone_id'
        );

        foreach ($fields as $i=>$field) {
            if (isset($this->error[$field]) && !empty($field)) {
                $data['error_' . $field] = $this->error[$field];
            }

            $config = self::$curi . '_' . $field;

            if (isset($this->request->post[$config])) {
                $data[$config] = $this->request->post[$config];
            } else if ($this->config->has($config)) {
                $data[$config] = $this->config->get($config);
            }

            $data['inputname_' . $field] = self::$curi . '_' . $field;
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->template = self::$url . '.tpl';

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view($this->template, $data), $data);

    }

    private function validate() {
        if (!$this->user->hasPermission('modify', self::$url)) {
            $this->error['warning'] = $this->language->get('error_permission');
            return !$this->error;
        }

        if (
            !(isset($this->request->post[self::$curi . '_merchantid']) &&
            !empty($this->request->post[self::$curi . '_merchantid']))
        ) {
            $this->error['merchantid'] = $this->language->get('error_merchantid');
        }

        if (
            !(isset($this->request->post[self::$curi . '_merchantsecret']) &&
            !empty($this->request->post[self::$curi . '_merchantsecret']))
        ) {
            $this->error['merchantsecret'] = $this->language->get('error_merchantsecret');
        }

        if (
            !(isset($this->request->post[self::$curi . '_currencycode']) &&
            is_numeric($this->request->post[self::$curi . '_currencycode']))
        ) {
            $this->error['currencycode'] = $this->language->get('error_currencycode');
        }

        if (
            !(isset($this->request->post[self::$curi . '_countrycode']) &&
            is_numeric($this->request->post[self::$curi . '_countrycode']))
        ) {
            $this->error['countrycode'] = $this->language->get('error_countrycode');
        }
        if (!empty($this->error)) {
            $this->error['warning'] = $this->language->get('error_data');
        }

        return !$this->error;

    }

}
