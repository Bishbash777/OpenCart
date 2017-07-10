<?php

class ControllerPaymentCardstream extends Controller {

    private $error = array();

    public function index() {

        $this->load->language( 'payment/cardstream' );

        $this->document->setTitle( $this->language->get( 'heading_title' ) );

        $this->load->model( 'setting/setting' );

        if ( ( $this->request->server['REQUEST_METHOD'] == 'POST' ) && $this->validate() ) {

            $this->model_setting_setting->editSetting( 'cardstream', $this->request->post );

            $this->session->data['success'] = $this->language->get( 'text_success' );

            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));

        }

        $data['heading_title'] = $this->language->get( 'heading_title' );

        $data['text_edit'] = $this->language->get('text_edit');

        // place holder texts
        $data['text_merchantid'] = $this->language->get('text_merchantid');
        $data['text_merchantsecret'] = $this->language->get('text_merchantsecret');
        $data['text_currencycode'] = $this->language->get('text_currencycode');
        $data['text_countrycode'] = $this->language->get('text_countrycode');

        $data['text_enabled']    = $this->language->get( 'text_enabled' );
        $data['text_disabled']   = $this->language->get( 'text_disabled' );
        $data['text_all_zones']  = $this->language->get( 'text_all_zones' );
        $data['text_yes']        = $this->language->get( 'text_yes' );
        $data['text_no']         = $this->language->get( 'text_no' );
        $data['text_live']       = $this->language->get( 'text_live' );
        $data['text_successful'] = $this->language->get( 'text_successful' );
        $data['text_fail']       = $this->language->get( 'text_fail' );

        $data['entry_merchantid']      = $this->language->get( 'entry_merchantid' );
        $data['entry_merchantsecret']  = $this->language->get( 'entry_merchantsecret' );
        $data['entry_order_status']    = $this->language->get( 'entry_order_status' );
        $data['entry_geo_zone']        = $this->language->get( 'entry_geo_zone' );
        $data['entry_form_responsive'] = $this->language->get( 'entry_form_responsive' );
        $data['entry_status']          = $this->language->get( 'entry_status' );
        $data['entry_sort_order']      = $this->language->get( 'entry_sort_order' );

        $data['entry_currencycode'] = $this->language->get( 'entry_currencycode' );
        $data['entry_countrycode']  = $this->language->get( 'entry_countrycode' );

        $data['button_save']   = $this->language->get( 'button_save' );
        $data['button_cancel'] = $this->language->get( 'button_cancel' );

        // cardstream module type selection
        $data['entry_module_type']   = $this->language->get( 'entry_module_type' );
        $data['entry_module_hosted'] = $this->language->get( 'entry_module_hosted' );
        $data['entry_module_iframe'] = $this->language->get( 'entry_module_iframe' );
        //$data['entry_module_direct'] = $this->language->get( 'entry_module_direct' );


        if ( isset( $this->error['warning'] ) ) {
            $data['error_warning'] = $this->error['warning'];
        }

        if ( isset( $this->error['merchantid'] ) ) {
            $data['error_merchantid'] = $this->error['merchantid'];
        }

        if ( isset( $this->error['merchantsecret'] ) ) {

            $data['error_merchantsecret'] = $this->error['merchantsecret'];

        }

        if ( isset( $this->error['currencycode'] ) ) {

            $data['error_currencycode'] = $this->error['currencycode'];

        }

        if ( isset( $this->error['countrycode'] ) ) {

            $data['error_countrycode'] = $this->error['countrycode'];

        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get( 'text_home' ),
            'href'      => $this->url->link( 'common/home', 'token=' . $this->session->data['token'], 'SSL' ),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get( 'text_payment' ),
            'href'      => $this->url->link( 'extension/payment', 'token=' . $this->session->data['token'], 'SSL' ),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get( 'heading_title' ),
            'href'      => $this->url->link( 'payment/cardstream', 'token=' . $this->session->data['token'], 'SSL' ),
            'separator' => ' :: '
        );


        $data['action'] =
            $this->url->link( 'payment/cardstream', 'token=' . $this->session->data['token'], 'SSL' );

        $data['cancel'] =
            $this->url->link( 'extension/payment', 'token=' . $this->session->data['token'], 'SSL' );

        if ( isset( $this->request->post['cardstream_merchantid'] ) ) {

            $data['cardstream_merchantid'] = $this->request->post['cardstream_merchantid'];

        } else {

            $data['cardstream_merchantid'] = $this->config->get( 'cardstream_merchantid' );

        }

        if ( isset( $this->request->post['cardstream_merchantsecret'] ) ) {

            $data['cardstream_merchantsecret'] = $this->request->post['cardstream_merchantsecret'];

        } else {

            $data['cardstream_merchantsecret'] = $this->config->get( 'cardstream_merchantsecret' );

        }

        if ( isset( $this->request->post['cardstream_order_status_id'] ) ) {

            $data['cardstream_order_status_id'] = $this->request->post['cardstream_order_status_id'];

        } else {

            $data['cardstream_order_status_id'] = $this->config->get( 'cardstream_order_status_id' );

        }

        $this->load->model( 'localisation/order_status' );

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if ( isset( $this->request->post['cardstream_geo_zone_id'] ) ) {

            $data['cardstream_geo_zone_id'] = $this->request->post['cardstream_geo_zone_id'];

        } else {

            $data['cardstream_geo_zone_id'] = $this->config->get( 'cardstream_geo_zone_id' );

        }

        $this->load->model( 'localisation/geo_zone' );

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if ( isset( $this->request->post['cardstream_status'] ) ) {

            $data['cardstream_status'] = $this->request->post['cardstream_status'];

        } else {

            $data['cardstream_status'] = $this->config->get( 'cardstream_status' );

        }

        if ( isset( $this->request->post['cardstream_form_responsive'] ) ) {

          $data['cardstream_form_responsive'] = $this->request->post['cardstream_form_responsive'];

        } else {

          $data['cardstream_form_responsive'] = $this->config->get( 'cardstream_form_responsive' );

        }

        if ( isset( $this->request->post['cardstream_sort_order'] ) ) {

            $data['cardstream_sort_order'] = $this->request->post['cardstream_sort_order'];

        } else {

            $data['cardstream_sort_order'] = $this->config->get( 'cardstream_sort_order' );

        }

        if ( isset( $this->request->post['cardstream_currencycode'] ) ) {

            $data['cardstream_currencycode'] = $this->request->post['cardstream_currencycode'];

        } else {

            $data['cardstream_currencycode'] = $this->config->get( 'cardstream_currencycode' );

        }

        if ( isset( $this->request->post['cardstream_countrycode'] ) ) {

            $data['cardstream_countrycode'] = $this->request->post['cardstream_countrycode'];

        } else {

            $data['cardstream_countrycode'] = $this->config->get( 'cardstream_countrycode' );

        }

        // cardstream module type selection
        if ( isset( $this->request->post['cardstream_module_type'] ) ) {

            $data['cardstream_module_type'] = $this->request->post['cardstream_module_type'];

        } else {

            $data['cardstream_module_type'] = $this->config->get( 'cardstream_module_type' );

        }

        $this->template = 'payment/cardstream.tpl';

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput( $this->load->view($this->template, $data), $data );

    }

    private function validate() {

        if ( !$this->user->hasPermission( 'modify', 'payment/cardstream' ) ) {

            $this->error['warning'] = $this->language->get( 'error_permission' );

        }

        if ( !$this->request->post['cardstream_merchantid'] ) {

            $this->error['merchantid'] = $this->language->get( 'error_merchantid' );
            $this->error['warning'] = $this->language->get( 'error_data' );

        }

        if ( !$this->request->post['cardstream_merchantsecret'] ) {

            $this->error['merchantsecret'] = $this->language->get( 'error_merchantsecret' );
            $this->error['warning'] = $this->language->get( 'error_data' );
        }

        if ( ( !$this->request->post['cardstream_currencycode'] ) ||
            ( !is_numeric( $this->request->post['cardstream_currencycode'] ) )
        ) {

            $this->error['currencycode'] = $this->language->get( 'error_currencycode' );
            $this->error['warning'] = $this->language->get( 'error_data' );
        }

        if ( ( !$this->request->post['cardstream_countrycode'] ) ||
            ( !is_numeric( $this->request->post['cardstream_countrycode'] ) )
        ) {

            $this->error['countrycode'] = $this->language->get( 'error_countrycode' );
            $this->error['warning'] = $this->language->get( 'error_data' );
        }

        if ( !$this->error ) {

            return true;

        } else {

            return false;

        }

    }

}
