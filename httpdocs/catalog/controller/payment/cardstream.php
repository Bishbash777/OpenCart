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

    static private $url;
    static private $curi;
    static private $token;

    public function __construct($obj) {
        parent::__construct($obj);
        $module = strtolower(basename(__FILE__, '.php'));
        self::$url = 'payment/' . $module;
        self::$curi = $module;
        self::$token = (isset($this->session->data['token']) ? '&token=' . $this->session->data['token'] : '');
    }

    public function index() {

        // Only load where the confirm action is asking us to show the form!
        if ($_REQUEST['route'] == 'checkout/confirm') {
            $this->load->language(self::$url);
            if ($this->config->get(self::$curi . '_module_type') == 'hosted') {
                return $this->createHostedForm(false);
            }
            if ($this->config->get(self::$curi . '_module_type') == 'direct') {
                return $this->createDirectForm();
            }
            if ($this->config->get(self::$curi . '_module_type') == 'iframe') {
                return $this->createHostedForm(true);
            }
        } else {
            return new \Exception('Unauthorized!');
        }

    }

    public function createHostedForm($iframe = false) {
        $data['loadIframe'] = $iframe;

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['form_hosted_url'] = $this->language->get('form_hosted_url');


        $merchant_id     = $this->config->get(self::$curi . '_merchantid');
        $merchant_secret = $this->config->get(self::$curi . '_merchantsecret');
        $country_code    = $this->config->get(self::$curi . '_countrycode');
        $currency_code   = $this->config->get(self::$curi . '_currencycode');
        $form_responsive = $this->config->get(self::$curi . '_form_responsive');
        $this->load->model('checkout/order');


        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $amount = intval(bcmul(round($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false), 2), 100, 0));

        $trans_id = $this->session->data['order_id'];
        $callback = $this->url->link(self::$url . '/callback', '', true);
        $bill_name = $order['payment_firstname'] . ' ' . $order['payment_lastname'];

        $bill_addr = "";
        $addressFields = [
            'payment_address_1',
            'payment_address_2',
            'payment_city',
            'payment_zone',
            'payment_country'
        ];
        foreach ($addressFields as $item) {
            $bill_addr .= $order[$item] . ($item == 'payment_country' ? "" : ",\n");
        }

        $formdata = array(
            "merchantID"        => $merchant_id,
            "amount"            => $amount,
            "action"            => "SALE",
            "type"              => 1,
            "countryCode"       => $country_code,
            "currencyCode"      => $currency_code,
            "transactionUnique" => $trans_id,
            "orderRef"          => "Order " . $trans_id,
            "redirectURL"       => $callback,
            "callbackURL"       => $callback,
            "formResponsive"    => $form_responsive,
            "customerName"      => $bill_name,
            "customerAddress"   => $bill_addr,
            "customerPostCode"  => $order['payment_postcode'],
            "customerEmail"     => $order['email'],
            "customerPhone"     => @$order['telephone'],
            "item1Description"  => "Order " . $trans_id,
            "item1Quantity"     => 1,
            "item1GrossValue"   => $amount
        );

        ksort( $formdata );

        $signature = http_build_query($formdata, '', '&') . $merchant_secret;

        $formdata['signature'] = hash('SHA512', $signature);

        $data['formdata'] = $formdata;

        return $this->load->view(self::$url . '_hosted.tpl', $data);

    }

    public function createDirectForm() {

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

        return $this->load->view(self::$url . '_direct.tpl', $data);
    }

    /**
     * callback is used with the hosted form integration after a payment attempt to further process the order
     */
    public function callback() {

        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $isIframe = $this->config->get(self::$curi . '_module_type') == 'iframe';
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
            $amountExpected = bcmul(round($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false), 2), 100, 0);

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
                $url = $this->url->link('checkout/success', '', true) . self::$token;
                if ($isIframe) {
                    $this->response->setOutput("<script>top.location = '$url';</script>");
                } else {
                    $this->response->redirect($url);
                }

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
            $url = $this->url->link('checkout/failure', '', true) . self::$token;
            if ($isIframe) {
                $this->response->setOutput("<script>top.location = '$url';</script>");
            } else {
                $this->response->redirect($url);
            }
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
