<?php

class ControllerPaymentCardstream extends Controller
{

    public function index()
    {

        $this->load->language('payment/cardstream');

        if ($this->config->get('cardstream_module_type') == 'hosted') {
            return $this->hosted_form();
        }

        if ($this->config->get('cardstream_module_type') == 'direct') {
            return $this->direct_form();
        }

        if ($this->config->get('cardstream_module_type') == 'iframe') {
            return $this->iframe_form();
        }


    }

    public function hosted_form()
    {

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success');


        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['merchantid'] = $this->config->get('cardstream_merchantid');
        $data['merchantsecret'] = $this->config->get('cardstream_merchantsecret');
        $data['amount'] =
            (int)round(($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) *
                100));


        $data['countrycode'] = $this->config->get('cardstream_countrycode');

        switch ($order_info['currency_code']) {
            case 'EUR':
                $data['currencycode'] = 978;
                break;
            case 'USD':
                $data['currencycode'] = 840;
                break;
            case 'GBP':
                $data['currencycode'] = 826;
                break;
            default :
                $data['currencycode'] = $this->config->get('cardstream_currencycode');
                break;
        }

        $data['trans_id'] = $this->session->data['order_id'];
        $data['callback'] = $this->url->link('payment/cardstream/callback', '', 'SSL');
        $data['bill_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

        $data['bill_addr'] = "";

        if (isset($order_info['payment_address_1']) && ($order_info['payment_address_1'] != "")) {

            $data['bill_addr'] .= $order_info['payment_address_1'] . ",\n";

        }

        if (isset($order_info['payment_address_2']) && ($order_info['payment_address_2'] != "")) {

            $data['bill_addr'] .= $order_info['payment_address_2'] . ",\n";

        }

        if (isset($order_info['payment_city']) && ($order_info['payment_city'] != "")) {

            $data['bill_addr'] .= $order_info['payment_city'] . ",\n";

        }

        if (isset($order_info['payment_zone']) && ($order_info['payment_zone'] != "")) {

            $data['bill_addr'] .= $order_info['payment_zone'] . ",\n";

        }

        if (isset($order_info['payment_country']) && ($order_info['payment_country'] != "")) {

            $data['bill_addr'] .= $order_info['payment_country'] . ",\n";

        }

        if (strlen($data['bill_addr']) > 1) {

            $data['bill_addr'] = substr(trim($data['bill_addr']), 0, -1);

        }

        $data['bill_post_code'] = $order_info['payment_postcode'];
        $data['bill_email'] = $order_info['email'];
        $data['bill_tel'] = $order_info['telephone'];

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cardstream_hosted.tpl')
        ) {

            $template = $this->config->get('config_template') . '/template/payment/cardstream_hosted.tpl';

        } else {

            $template = 'default/template/payment/cardstream_hosted.tpl';

        }

        return $this->load->view($template, $data);

    }

    public function iframe_form()
    {

        $data['button_confirm'] = $this->language->get('button_confirm');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['merchantid'] = $this->config->get('cardstream_merchantid');
        $data['merchantsecret'] = $this->config->get('cardstream_merchantsecret');
        $data['amount'] =
            (int)round(($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) *
                100));


        $data['countrycode'] = $this->config->get('cardstream_countrycode');

        switch ($order_info['currency_code']) {
            case 'EUR':
                $data['currencycode'] = 978;
                break;
            case 'USD':
                $data['currencycode'] = 840;
                break;
            case 'GBP':
                $data['currencycode'] = 826;
                break;
            default :
                $data['currencycode'] = $this->config->get('cardstream_currencycode');
                break;
        }

        $data['trans_id'] = $this->session->data['order_id'];
        $data['callback'] = $this->url->link('payment/cardstream/callback', '', 'SSL');
        $data['bill_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

        $data['bill_addr'] = "";

        if (isset($order_info['payment_address_1']) && ($order_info['payment_address_1'] != "")) {

            $data['bill_addr'] .= $order_info['payment_address_1'] . ",\n";

        }

        if (isset($order_info['payment_address_2']) && ($order_info['payment_address_2'] != "")) {

            $data['bill_addr'] .= $order_info['payment_address_2'] . ",\n";

        }

        if (isset($order_info['payment_city']) && ($order_info['payment_city'] != "")) {

            $data['bill_addr'] .= $order_info['payment_city'] . ",\n";

        }

        if (isset($order_info['payment_zone']) && ($order_info['payment_zone'] != "")) {

            $data['bill_addr'] .= $order_info['payment_zone'] . ",\n";

        }

        if (isset($order_info['payment_country']) && ($order_info['payment_country'] != "")) {

            $data['bill_addr'] .= $order_info['payment_country'] . ",\n";

        }

        if (strlen($data['bill_addr']) > 1) {

            $data['bill_addr'] = substr(trim($data['bill_addr']), 0, -1);

        }

        $data['bill_post_code'] = $order_info['payment_postcode'];
        $data['bill_email'] = $order_info['email'];
        $data['bill_tel'] = $order_info['telephone'];

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cardstream_iframe.tpl')
        ) {

            $template = $this->config->get('config_template') . '/template/payment/cardstream_iframe.tpl';

        } else {

            $template = 'default/template/payment/cardstream_iframe.tpl';

        }

        return $this->load->view($template, $data);
    }

    public function direct_form()
    {

        $data['cards'] = array();

        $data['cards'][] = array(
            'text' => 'Visa',
            'value' => 'VISA'
        );

        $data['cards'][] = array(
            'text' => 'MasterCard',
            'value' => 'MC'
        );

        $data['cards'][] = array(
            'text' => 'Visa Delta/Debit',
            'value' => 'DELTA'
        );

        $data['cards'][] = array(
            'text' => 'Solo',
            'value' => 'SOLO'
        );

        $data['cards'][] = array(
            'text' => 'Maestro',
            'value' => 'MAESTRO'
        );

        $data['cards'][] = array(
            'text' => 'Visa Electron UK Debit',
            'value' => 'UKE'
        );

        $data['cards'][] = array(
            'text' => 'American Express',
            'value' => 'AMEX'
        );

        $data['cards'][] = array(
            'text' => 'Diners Club',
            'value' => 'DC'
        );

        $data['cards'][] = array(
            'text' => 'Japan Credit Bureau',
            'value' => 'JCB'
        );

        $data['cc_cardholder_name'] = $this->language->get('cc_cardholder_name');
        $data['cc_card_number'] = $this->language->get('cc_card_number');
        $data['cc_card_start_date'] = $this->language->get('cc_card_start_date');
        $data['cc_card_start_date_help'] = $this->language->get('cc_card_start_date_help');
        $data['cc_card_expiry_date'] = $this->language->get('cc_card_expiry_date');
        $data['cc_card_cvv'] = $this->language->get('cc_card_cvv');
        $data['cc_card_type'] = $this->language->get('cc_card_type');
        $data['cc_card_issue'] = $this->language->get('cc_card_issue');
        $data['cc_card_issue_help'] = $this->language->get('cc_card_issue_help');
        $data['text_credit_card'] = $this->language->get('text_credit_card');
        $data['button_confirm'] = $this->language->get('button_confirm');


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cardstream_direct.tpl')
        ) {

            $template = $this->config->get('config_template') . '/template/payment/cardstream_direct.tpl';

        } else {

            $template = 'default/template/payment/cardstream_direct.tpl';

        }

        return $this->load->view($template, $data);
    }

    /**
     * callback is used with the hosted form integration after a payment attempt to further process the order
     */
    public function callback()
    {

        if (isset($this->request->post['transactionUnique'])) {

            $order_id = $this->request->post['transactionUnique'];

        } else {

            $order_id = 0;

        }

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($order_id);

        if ($order_info) {

            $this->language->load('payment/cardstream');

            $data['title'] =
                sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

            if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {

                $data['base'] = HTTP_SERVER;

            } else {

                $data['base'] = HTTPS_SERVER;

            }

            $data['language'] = $this->language->get('code');
            $data['direction'] = $this->language->get('direction');

            $data['heading_title'] =
                sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

            $data['text_response'] = $this->language->get('text_response');
            $data['text_success'] = $this->language->get('text_success');
            $data['text_success_wait'] =
                sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
            $data['text_failure'] = $this->language->get('text_failure');
            $data['text_failure_wait'] =
                sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
            $data['text_mismatch'] = $this->language->get('text_mismatch');

            if (isset($this->request->post['responseCode']) && $this->request->post['responseCode'] === "0") {
                //var_dump((int) round( ( $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100 ) ),$this->request->post['amount'], $order_info['total']*100, $order_info,$this->request );
                if ((int)$this->request->post['amount'] ===
                    (int)round(($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) *
                        100))
                ) {
                    //Amount paid matches the amount paid.

                    $this->load->model('checkout/order');

                    $message = $this->buildordermessage();

                    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cardstream_order_status_id'), $message);

                    $data['continue'] = $this->url->link('checkout/success');

                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') .
                        '/template/payment/cardstream_success.tpl')
                    ) {

                        $template = $this->config->get('config_template') . '/template/payment/cardstream_success.tpl';

                    } else {

                        $template = 'default/template/payment/cardstream_success.tpl';

                    }

                    $this->response->setOutput($this->load->view($template, $data));

                } else {
                    //Amount paid doesn't match the amount required.
                    $data['continue'] = $this->url->link('checkout/cart');

                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') .
                        '/template/payment/cardstream_mismatch.tpl')
                    ) {
                        $template =
                            $this->config->get('config_template') . '/template/payment/cardstream_mismatch.tpl';

                    } else {
                        $template = 'default/template/payment/cardstream_mismatch.tpl';
                    }

                    $this->response->setOutput($this->load->view($template, $data));

                }

            } else {

                $data['continue'] = $this->url->link('checkout/cart');

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') .
                    '/template/payment/cardstream_failure.tpl')
                ) {

                    $template =
                        $this->config->get('config_template') . '/template/payment/cardstream_failure.tpl';

                } else {

                    $template = 'default/template/payment/cardstream_failure.tpl';

                }

                $this->response->setOutput($this->load->view($template, $data));

            }

        }

    }

    private function buildordermessage()
    {

        if ($this->request->post['responseCode'] === "0") {

            $paymentoutcome = "Payment Successful";

        } else {

            $paymentoutcome = "Payment Unsuccessful";

        }

        if (isset($this->request->post['amountReceived'])) {

            $amountreceived = number_format(round(($this->request->post['amountReceived'] / 100), 2), 2);

        } else {

            $amountreceived = "Unknown";

        }

        $ordermessage = $paymentoutcome . "<br /><br />Amount Received: " . $amountreceived . "<br />Message: \"" .
            ucfirst($this->request->post['responseMessage']) .
            "\"<br />Xref: " . $this->request->post['xref'];

        if (isset($this->request->post['cv2Check'])) {

            $ordermessage .= "<br />CV2 Check Result: " . ucfirst($this->request->post['cv2Check']);

        }

        if (isset($this->request->post['addressCheck'])) {

            $ordermessage .= "<br />Address Check Result: " . ucfirst($this->request->post['addressCheck']);

        }

        if (isset($this->request->post['postcodeCheck'])) {

            $ordermessage .= "<br />Postcode Check Result: " . ucfirst($this->request->post['postcodeCheck']);

        }

        if (isset($this->request->post['threeDSEnrolled'])) {

            switch ($this->request->post['threeDSEnrolled']) {
                case "Y":
                    $enrolledtext = "Enrolled.";
                    break;
                case "N":
                    $enrolledtext = "Not Enrolled.";
                    break;
                case "U";
                    $enrolledtext = "Unable To Verify.";
                    break;
                case "E":
                    $enrolledtext = "Error Verifying Enrolment.";
                    break;
                default:
                    $enrolledtext = "Integration unable to determine enrolment status.";
                    break;
            }

            $ordermessage .= "<br />3D Secure enrolled check outcome: \"" . $enrolledtext . "\"";

        }

        if (isset($this->request->post['threeDSAuthenticated'])) {

            switch ($this->request->post['threeDSAuthenticated']) {

                case "Y":
                    $authenticatedtext = "Authentication Successful";
                    break;
                case "N":
                    $authenticatedtext = "Not Authenticated";
                    break;
                case "U";
                    $authenticatedtext = "Unable To Authenticate";
                    break;
                case "A":
                    $authenticatedtext = "Attempted Authentication";
                    break;
                case "E":
                    $authenticatedtext = "Error Checking Authentication";
                    break;
                default:
                    $authenticatedtext = "Integration unable to determine authentication status.";
                    break;

            }

            $ordermessage .= "<br />3D Secure authenticated check outcome: \"" . $authenticatedtext . "\"";

        }

        return $ordermessage;

    }

}
