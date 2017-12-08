<?php

class ControllerPaymentCardstream extends Controller {

	protected function index() {

		$this->load->language( 'payment/cardstream' );

		if ( $this->config->get( 'cardstream_module_type' ) == 'hosted' ) {
			$this->hosted_form();
		}

		if ( $this->config->get( 'cardstream_module_type' ) == 'direct' ) {
			$this->direct_form();
		}

		if($this->config->get('cardstream_module_type') == 'iframe'){
			$this->iframe_form();
		}

		$this->render();

	}

	public function hosted_form() {

		$this->data['button_confirm'] = $this->language->get( 'button_confirm' );

		$this->load->model( 'checkout/order' );

		$order_info = $this->model_checkout_order->getOrder( $this->session->data['order_id'] );

		$this->data['merchantid']     = $this->config->get( 'cardstream_merchantid' );
		$this->data['merchantsecret'] = $this->config->get( 'cardstream_merchantsecret' );
		$this->data['amount']         = intval(bcmul(round($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false), 2), 100, 0));


		$this->data['countrycode'] = $this->config->get( 'cardstream_countrycode' );

		switch ( $order_info['currency_code'] ) {
			case 'EUR':
				$this->data['currencycode'] = 978;
				break;
			case 'USD':
				$this->data['currencycode'] = 840;
				break;
			case 'GBP':
				$this->data['currencycode'] = 826;
				break;
			default :
				$this->data['currencycode'] = $this->config->get( 'cardstream_currencycode' );
				break;
		}

		$this->data['trans_id']  = $this->session->data['order_id'];
		$this->data['callback']  = $this->url->link( 'payment/cardstream/callback', '', 'SSL' );
		$this->data['bill_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

		$this->data['bill_addr'] = "";

		if ( isset( $order_info['payment_address_1'] ) && ( $order_info['payment_address_1'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_address_1'] . ",\n";

		}

		if ( isset( $order_info['payment_address_2'] ) && ( $order_info['payment_address_2'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_address_2'] . ",\n";

		}

		if ( isset( $order_info['payment_city'] ) && ( $order_info['payment_city'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_city'] . ",\n";

		}

		if ( isset( $order_info['payment_zone'] ) && ( $order_info['payment_zone'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_zone'] . ",\n";

		}

		if ( isset( $order_info['payment_country'] ) && ( $order_info['payment_country'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_country'] . ",\n";

		}

		if ( strlen( $this->data['bill_addr'] ) > 1 ) {

			$this->data['bill_addr'] = substr( trim( $this->data['bill_addr'] ), 0, -1 );

		}

		$this->data['bill_post_code'] = $order_info['payment_postcode'];
		$this->data['bill_email']     = $order_info['email'];
		$this->data['bill_tel']       = $order_info['telephone'];

		if ( file_exists( DIR_TEMPLATE . $this->config->get( 'config_template' ) . '/template/payment/cardstream_hosted.tpl' )
		) {

			$this->template = $this->config->get( 'config_template' ) . '/template/payment/cardstream_hosted.tpl';

		} else {

			$this->template = 'default/template/payment/cardstream_hosted.tpl';

		}

	}

	public function iframe_form() {

		$this->data['button_confirm'] = $this->language->get( 'button_confirm' );

		$this->load->model( 'checkout/order' );

		$order_info = $this->model_checkout_order->getOrder( $this->session->data['order_id'] );

		$this->data['merchantid']     = $this->config->get( 'cardstream_merchantid' );
		$this->data['merchantsecret'] = $this->config->get( 'cardstream_merchantsecret' );
		$this->data['amount']         = intval(bcmul(round($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false), 2), 100, 0));


		$this->data['countrycode'] = $this->config->get( 'cardstream_countrycode' );

		switch ( $order_info['currency_code'] ) {
			case 'EUR':
				$this->data['currencycode'] = 978;
				break;
			case 'USD':
				$this->data['currencycode'] = 840;
				break;
			case 'GBP':
				$this->data['currencycode'] = 826;
				break;
			default :
				$this->data['currencycode'] = $this->config->get( 'cardstream_currencycode' );
				break;
		}

		$this->data['trans_id']  = $this->session->data['order_id'];
		$this->data['callback']  = $this->url->link( 'payment/cardstream/callback', '', 'SSL' );
		$this->data['bill_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

		$this->data['bill_addr'] = "";

		if ( isset( $order_info['payment_address_1'] ) && ( $order_info['payment_address_1'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_address_1'] . ",\n";

		}

		if ( isset( $order_info['payment_address_2'] ) && ( $order_info['payment_address_2'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_address_2'] . ",\n";

		}

		if ( isset( $order_info['payment_city'] ) && ( $order_info['payment_city'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_city'] . ",\n";

		}

		if ( isset( $order_info['payment_zone'] ) && ( $order_info['payment_zone'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_zone'] . ",\n";

		}

		if ( isset( $order_info['payment_country'] ) && ( $order_info['payment_country'] != "" ) ) {

			$this->data['bill_addr'] .= $order_info['payment_country'] . ",\n";

		}

		if ( strlen( $this->data['bill_addr'] ) > 1 ) {

			$this->data['bill_addr'] = substr( trim( $this->data['bill_addr'] ), 0, -1 );

		}

		$this->data['bill_post_code'] = $order_info['payment_postcode'];
		$this->data['bill_email']     = $order_info['email'];
		$this->data['bill_tel']       = $order_info['telephone'];

		if ( file_exists( DIR_TEMPLATE . $this->config->get( 'config_template' ) . '/template/payment/cardstream_iframe.tpl' )
		) {

			$this->template = $this->config->get( 'config_template' ) . '/template/payment/cardstream_iframe.tpl';

		} else {

			$this->template = 'default/template/payment/cardstream_iframe.tpl';

		}

	}

	public function direct_form() {

		$this->data['cards'] = array();

		$this->data['cards'][] = array(
			'text'  => 'Visa',
			'value' => 'VISA'
		);

		$this->data['cards'][] = array(
			'text'  => 'MasterCard',
			'value' => 'MC'
		);

		$this->data['cards'][] = array(
			'text'  => 'Visa Delta/Debit',
			'value' => 'DELTA'
		);

		$this->data['cards'][] = array(
			'text'  => 'Solo',
			'value' => 'SOLO'
		);

		$this->data['cards'][] = array(
			'text'  => 'Maestro',
			'value' => 'MAESTRO'
		);

		$this->data['cards'][] = array(
			'text'  => 'Visa Electron UK Debit',
			'value' => 'UKE'
		);

		$this->data['cards'][] = array(
			'text'  => 'American Express',
			'value' => 'AMEX'
		);

		$this->data['cards'][] = array(
			'text'  => 'Diners Club',
			'value' => 'DC'
		);

		$this->data['cards'][] = array(
			'text'  => 'Japan Credit Bureau',
			'value' => 'JCB'
		);

		$this->data['cc_cardholder_name']  = $this->language->get( 'cc_cardholder_name' );
		$this->data['cc_card_number']      = $this->language->get( 'cc_card_number' );
		$this->data['cc_card_start_date']  = $this->language->get( 'cc_card_start_date' );
		$this->data['cc_card_start_date_help']  = $this->language->get( 'cc_card_start_date_help' );
		$this->data['cc_card_expiry_date'] = $this->language->get( 'cc_card_expiry_date' );
		$this->data['cc_card_cvv']         = $this->language->get( 'cc_card_cvv' );
		$this->data['cc_card_type']         = $this->language->get( 'cc_card_type' );
		$this->data['cc_card_issue']         = $this->language->get( 'cc_card_issue' );
		$this->data['cc_card_issue_help']         = $this->language->get( 'cc_card_issue_help' );
		$this->data['text_credit_card']         = $this->language->get( 'text_credit_card' );
		$this->data['button_confirm']         = $this->language->get( 'button_confirm' );


		if ( file_exists( DIR_TEMPLATE . $this->config->get( 'config_template' ) . '/template/payment/cardstream_direct.tpl' )
		) {

			$this->template = $this->config->get( 'config_template' ) . '/template/payment/cardstream_direct.tpl';

		} else {

			$this->template = 'default/template/payment/cardstream_direct.tpl';

		}

	}

	/**
	 * callback is used with the hosted form integration after a payment attempt to further process the order
	 */
	public function callback() {

		if ( isset( $this->request->post['transactionUnique'] ) ) {

			$order_id = $this->request->post['transactionUnique'];

		} else {

			$order_id = 0;

		}

		$this->load->model( 'checkout/order' );

		$order_info = $this->model_checkout_order->getOrder( $order_id );

		if ( $order_info ) {

			$this->language->load( 'payment/cardstream' );

			$this->data['title'] =
				sprintf( $this->language->get( 'heading_title' ), $this->config->get( 'config_name' ) );

			if ( !isset( $this->request->server['HTTPS'] ) || ( $this->request->server['HTTPS'] != 'on' ) ) {

				$this->data['base'] = HTTP_SERVER;

			} else {

				$this->data['base'] = HTTPS_SERVER;

			}

			$this->data['language']  = $this->language->get( 'code' );
			$this->data['direction'] = $this->language->get( 'direction' );

			$this->data['heading_title'] =
				sprintf( $this->language->get( 'heading_title' ), $this->config->get( 'config_name' ) );

			$this->data['text_response']     = $this->language->get( 'text_response' );
			$this->data['text_success']      = $this->language->get( 'text_success' );
			$this->data['text_success_wait'] =
				sprintf( $this->language->get( 'text_success_wait' ), $this->url->link( 'checkout/success' ) );
			$this->data['text_failure']      = $this->language->get( 'text_failure' );
			$this->data['text_failure_wait'] =
				sprintf( $this->language->get( 'text_failure_wait' ), $this->url->link( 'checkout/cart' ) );
			$this->data['text_mismatch']     = $this->language->get( 'text_mismatch' );

			if ( isset( $this->request->post['responseCode'] ) && $this->request->post['responseCode'] === "0" ) {

				$amountExpected = bcmul(round($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false), 2), 100, 0);

				if ( $this->request->post['amount'] === $amountExpected) {
					//Amount paid matches the amount paid.

					$this->load->model( 'checkout/order' );

					$this->model_checkout_order->confirm( $this->request->post['transactionUnique'], $this->config->get( 'config_order_status_id' ) );

					$message = $this->buildordermessage();

					$this->model_checkout_order->update( $order_id, $this->config->get( 'cardstream_order_status_id' ), $message, false );

					$this->data['continue'] = $this->url->link( 'checkout/success' );

					if ( file_exists( DIR_TEMPLATE . $this->config->get( 'config_template' ) .
									  '/template/payment/cardstream_success.tpl' )
					) {

						$this->template =
							$this->config->get( 'config_template' ) . '/template/payment/cardstream_success.tpl';

					} else {

						$this->template = 'default/template/payment/cardstream_success.tpl';

					}

					$this->children = array(
						'common/column_left',
						'common/column_right',
						'common/content_top',
						'common/content_bottom',
						'common/footer',
						'common/header'
					);

					$this->response->setOutput( $this->render() );


				} else {
					//Amount paid doesn't match the amount required.

					$this->data['continue'] = $this->url->link( 'checkout/cart' );

					if ( file_exists( DIR_TEMPLATE . $this->config->get( 'config_template' ) .
									  '/template/payment/cardstream_mismatch.tpl' )
					) {

						$this->template =
							$this->config->get( 'config_template' ) . '/template/payment/cardstream_mismatch.tpl';

					} else {

						$this->template = 'default/template/payment/cardstream_mismatch.tpl';

					}

					$this->children = array(
						'common/column_left',
						'common/column_right',
						'common/content_top',
						'common/content_bottom',
						'common/footer',
						'common/header'
					);

					$this->response->setOutput( $this->render() );

				}

			} else {

				$this->data['continue'] = $this->url->link( 'checkout/cart' );

				if ( file_exists( DIR_TEMPLATE . $this->config->get( 'config_template' ) .
								  '/template/payment/cardstream_failure.tpl' )
				) {

					$this->template =
						$this->config->get( 'config_template' ) . '/template/payment/cardstream_failure.tpl';

				} else {

					$this->template = 'default/template/payment/cardstream_failure.tpl';

				}

				$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
				);

				$this->response->setOutput( $this->render() );

			}

		}

	}

	private function buildordermessage() {

		if ( $this->request->post['responseCode'] === "0" ) {

			$paymentoutcome = "Payment Successful";

		} else {

			$paymentoutcome = "Payment Unsuccessful";

		}

		if ( isset( $this->request->post['amountReceived'] ) ) {

			$amountreceived = number_format( round( ( $this->request->post['amountReceived'] / 100 ), 2 ), 2 );

		} else {

			$amountreceived = "Unknown";

		}

		$ordermessage = $paymentoutcome . "<br /><br />Amount Received: " . $amountreceived . "<br />Message: \"" .
						ucfirst( $this->request->post['responseMessage'] ) .
						"\"<br />Xref: " . $this->request->post['xref'];

		if ( isset( $this->request->post['cv2Check'] ) ) {

			$ordermessage .= "<br />CV2 Check Result: " . ucfirst( $this->request->post['cv2Check'] );

		}

		if ( isset( $this->request->post['addressCheck'] ) ) {

			$ordermessage .= "<br />Address Check Result: " . ucfirst( $this->request->post['addressCheck'] );

		}

		if ( isset( $this->request->post['postcodeCheck'] ) ) {

			$ordermessage .= "<br />Postcode Check Result: " . ucfirst( $this->request->post['postcodeCheck'] );

		}

		if ( isset( $this->request->post['threeDSEnrolled'] ) ) {

			switch ( $this->request->post['threeDSEnrolled'] ) {
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

		if ( isset( $this->request->post['threeDSAuthenticated'] ) ) {

			switch ( $this->request->post['threeDSAuthenticated'] ) {

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
