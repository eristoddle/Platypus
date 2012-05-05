<?php
    namespace app\controllers;

    use lithium\action\Controller;
    use app\util\Config;
    use app\util\Paypal;
    use app\models\Payments;
    use app\models\ShoppingCarts;
    use app\models\CartItems;
    use lithium\analysis\Logger;

    class PaypalController extends Controller
    {
        public function ipn()
        {
            if ($this->request->data) {

                $paypal_txn_id = $this->request->data['txn_id'];
                $payment = Payments::first(array('conditions' => array(array('txn_id' => $paypal_txn_id))));

                if (!isset($payment)) {
                    Logger::debug('New transaction #' . $paypal_txn_id);
                    # Put the payment in the DB
                    $payment = Payments::create($this->request->data);
                    if ($payment->invoice) {
                        # Map invoices to cart_ids, right now this is direct
                        $payment->shopping_cart_id = $payment->invoice;    
                    }

                    $payment->save();
                } else {
                    Logger::debug('Transaction play-back (txn #' . $paypal_txn_id . ')');
                }


                Logger::debug('$payment->_id = ' . $payment->_id);

                $cart = ShoppingCarts::find($payment->invoice);

                Logger::debug('$cart->_id = ' . $cart->_id);

                $items = $cart->getItems();

                if (strtolower($payment->payment_status) == 'pending' and strtolower($payment->pending_reason) == 'authorization') {
                    $captureAmount = 0;
                    $remainder = 0;

                    Logger::debug('authorization transaction');

                    foreach ($items as $ci) {
                        Logger::debug('.....cart_item ' . $ci->_id);

                        $refObj = $ci->getReference();

                        if ($ci->isValid()) {
                            Logger::debug('..........valid purchase -- auto-capture');
                            $ci->save(array('status' => CartItems::STATUS_CAPT));

                            // Pass payment status down to referenced object
                            $refObj->save(array('payment_status' => CartItems::STATUS_CAPT));


                            $captureAmount += $ci->price;
                        } else {
                            Logger::debug('..........not valid, hold as pending');
                            $ci->save(array('status' => CartItems::STATUS_AUTH));

                            // Pass payment status down to referenced object
                            $refObj->save(array('payment_status' => CartItems::STATUS_AUTH));

                            $remainder += $ci->price;
                        }

                        $cart->save(array('is_authorized' => true));
                    }

                    if ($captureAmount > 0) {
                        Logger::debug('Capturing $' . $captureAmount);

                        $result = Paypal::doCapture($payment->auth_id, $captureAmount, $payment->mc_currency, ($remainder == 0));
                        Logger::debug(print_r($result, true));

                        // Log NVP transaction result to the payment
                        $query = array('$push' => array('nvp' => $result));
                        $conditions = array('_id' => $payment->_id);
                        Payments::update($query, $conditions);

                        Logger::debug('Captured!');
                    }
                } else if (strtolower($payment->payment_status) == 'completed') {
                    $unpaid_items = 0;

                    foreach ($items as $ci) {
                        Logger::debug('.....cart_item ' . $ci->_id);

                        $refObj = $ci->getReference();

                        if ($ci->status == CartItems::STATUS_CAPT) {
                            Logger::debug('..........PAID!');
                            $ci->save(array('status' => CartItems::STATUS_PAID));

                            // Pass payment status down to referenced object
                            $refObj->save(array(
                                'paid' => true,
                                'payment_status' => CartItems::STATUS_PAID,
                                // TODO: this stuff should really be handled by the registration object
                                'status' => 'active'
                            ));
                        } else if  ($ci->status != CartItems::STATUS_PAID) {
                            $unpaid_items++;
                        }
                    }

                    if ($unpaid_items == 0) {
                        $cart->save(array('status' => 'closed'));
                    }
                }
            }
            
            return $this->render(array('layout' => false));
        }

        public function doCapture()
        {
            $result = 'payment not found';
            $payment = Payments::find($this->request->id);

            if ($payment) {
                $result = Paypal::doCapture($payment->auth_id, $payment->auth_amount, 'USD', true);
            }

            return compact('result');
        }
    }