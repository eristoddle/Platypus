<?php
    namespace app\controllers;

    use app\extensions\action\Controller;
    use app\util\Config;

    class CartsController extends Controller
    {
        public function index()
        {
            if (!isset($this->CURRENT_USER)) {
                $this->flashMessage('You must log in to visit your shopping cart.', array('alertType' => 'error'));
                return $this->redirect('/');
            }

            $cart = $this->CURRENT_USER->getShoppingCart();

            if (!isset($cart)) {
                $this->flashMessage('No shopping cart found.', array('alertType' => 'error'));
                return $this->redirect('/');
            }

            $cart_items = $cart->getItems();
            
            $paypal_config = Config::get('paypal');

            return compact('cart', 'cart_items', 'paypal_config');
        }
    }