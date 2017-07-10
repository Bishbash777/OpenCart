<?php
/**
 * NOTE: OpenCart uses camelCase for functions/variables and
 *       snake_case for database fields (e.g. order_status_id).
 *
 *       Retrieved model data simply returns field name and their
 *       value in snake_case rather than camelCase otherwise used
 *       in OpenCart's PHP coding standards
 *
 *       Loaded models also use snake_case
 */
class ControllerPaymentCardstream extends Controller
{
    public function __construct($obj) {
        parent::__construct($obj);
        $this->load->language('payment/cardstream');
    }

    public function index() {

        $this->load->language('payment/cardstream');

        if ($this->config->get('cardstream_module_type') == 'hosted') {
            return $this->createHostedForm();
        }

        if ($this->config->get('cardstream_module_type') == 'direct') {
            return $this->createDirectForm();
        }

        if ($this->config->get('cardstream_module_type') == 'iframe') {
            return $this->createEmbeddedForm();
        }

    }

    public function createHostedForm() {

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success');
        $data['merchantid']     = $this->config->get('cardstream_merchantid');
        $data['merchantsecret'] = $this->config->get('cardstream_merchantsecret');
        $data['countrycode'] = $this->config->get('cardstream_countrycode');
        $data['currencycode'] = $this->config->get('cardstream_currencycode');
        $data['form_responsive'] = $this->config->get('cardstream_form_responsive');

        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['amount'] = (int)$this->currency->format(
            $order['total'],
            $order['currency_code'],
            $order['currency_value'],
            false
        ) * 100;

        $data['trans_id'] = $this->session->data['order_id'];
        $data['callback'] = $this->url->link('/payment/cardstream/callback', '', 'SSL');
        $data['bill_name'] = $order['payment_firstname'] . ' ' . $order['payment_lastname'];

        $data['bill_addr'] = "";
        $addressFields = [
            'payment_address_1',
            'payment_address_2',
            'payment_city',
            'payment_zone',
            'payment_country'
        ];
        foreach ($addressFields as $item) {
            $data['bill_addr'] .= $order[$item] . ($item == 'payment_country' ? "" : ",\n");
        }

        $data['bill_post_code'] = $order['payment_postcode'];
        $data['bill_email'] = $order['email'];
        $data['bill_tel'] = $order['telephone'];

        return $this->load->view('payment/cardstream_hosted', $data);

    }

    public function createEmbeddedForm() {

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['merchantid']     = $this->config->get('cardstream_merchantid');
        $data['merchantsecret'] = $this->config->get('cardstream_merchantsecret');
        $data['countrycode'] = $this->config->get('cardstream_countrycode');
        $data['currencycode'] = $this->config->get('cardstream_currencycode');
        $data['form_responsive'] = $this->config->get('cardstream_form_responsive');

        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['amount'] = (int)$this->currency->format(
            $order['total'],
            $order['currency_code'],
            $order['currency_value'],
            false
        ) * 100;


        $data['trans_id'] = $this->session->data['order_id'];
        $data['callback'] = $this->url->link('/payment/cardstream/callback', '', 'SSL');
        $data['bill_name'] = $order['payment_firstname'] . ' ' . $order['payment_lastname'];

        $data['bill_addr'] = "";
        $addressFields = [
            'payment_address_1',
            'payment_address_2',
            'payment_city',
            'payment_zone',
            'payment_country'
        ];
        foreach ($addressFields as $item) {
            $data['bill_addr'] .= $order[$item] . ($item == 'payment_country' ? "" : ",\n");
        }

        $data['bill_post_code'] = $order['payment_postcode'];
        $data['bill_email'] = $order['email'];
        $data['bill_tel'] = $order['telephone'];

        return $this->load->view('payment/cardstream_iframe', $data);
    }

    public function createDirectForm() {

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

        return $this->load->view('payment/cardstream_direct', $data);
    }

    /**
     * callback is used with the hosted form integration after a payment attempt to further process the order
     */
    public function callback() {

        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $data['base']              = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $data['language']          = $this->language->get('code');
        $data['direction']         = $this->language->get('direction');
        //Page titles
        $data['title']             = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $data['heading_title']     = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $data['text_response']     = $this->language->get('text_response');
        //Success text
        $data['text_success']      = $this->language->get('text_success');
        $data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
        //Error text
        $data['text_failure']      = $this->language->get('text_failure');
        $data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
        //Mismatch
        $data['text_mismatch']     = $this->language->get('text_mismatch');
        //Start processing response data
        $data = $this->request->post;
        $error = false;
        //Make sure it's a valid request
        if ($this->hasKeys($data, $this->getResponseTemplate())) {
            $this->load->model('checkout/order');
            $orderId = $data['transactionUnique'];
            $order = $this->model_checkout_order->getOrder($orderId);
            $amountExpected = (int)$this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100;

            if (intval($data['responseCode']) == 0 && $data['amountReceived'] == $amountExpected) {
                //Only update if the order id has not been properly set yet
                if ($order['order_status_id'] == 0) {
                    $this->model_checkout_order->addOrderHistory(
                        $data['transactionUnique'], // order ID
                        $this->config->get('config_order_status_id'), // order status ID
                        $this->buildMessage(), // Comment to status
                        true //Send notification
                    );
                }
                $this->response->redirect($this->url->link('checkout/success', $this->session->data['token'], 'SSL'));
            } else {
                if ($order) {
                    try {
                        $this->model_checkout_order->deleteOrder($orderId);
                    } catch (Exception $e) {
                        //Order was not present
                    }
                }
                $error = true;
            }
        } else {
            //Don't try to delete an order here,
            //since it could be a fraudulent request!
            $error = true;
        }
        if ($error) {
            $this->response->redirect($this->url->link('checkout/failure', $this->session->data['token'], 'SSL'));
        }

    }
    public function getResponseTemplate(){
        return array(
            'orderRef',
            'signature',
            'responseCode',
            'transactionUnique',
            'responseMessage',
            'action'
        );
    }
    public function hasKeys($array, $keys) {
        $checkKeys = array_keys($array);
        $str = '';
        foreach ($keys as $key){
            if(!array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
    }
    private function buildMessage() {
        $data = $this->request->post;

        $msg = "Payment " . ($data['responseCode'] == 0 ? "Successful" : "Unsuccessful") . "<br/><br/>" .
                "Amount Received: " . (isset($data['amountReceived']) ? floatval($data['amountReceived']) / 100 : 'Unknown') . "<br/>" .
                "Message: \"" . ucfirst($data['responseMessage']) . "\"</br>" .
                "Xref: " . $data['xref'] . "<br/>" .
                (isset($data['cv2Check']) ? "CV2 Check: " . ucfirst($data['cv2Check']) . "</br>": '') .
                (isset($data['addressCheck']) ? "Address Check: " . ucfirst($data['addressCheck']) . "</br>": '') .
                (isset($data['postcodeCheck']) ? "Postcode Check: " . ucfirst($data['postcodeCheck']) . "</br>": '');

        if (isset($data['threeDSEnrolled'])) {
            switch ($data['threeDSEnrolled']) {
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
            $msg .= "<br />3D Secure enrolled check outcome: \"" . $enrolledtext . "\"";
        }

        if (isset($data['threeDSAuthenticated'])) {
            switch ($data['threeDSAuthenticated']) {
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
            $msg .= "<br />3D Secure authenticated check outcome: \"" . $authenticatedtext . "\"";
        }
        return $msg;
    }

}
