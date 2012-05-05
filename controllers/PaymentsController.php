<?php
    namespace app\controllers;

    use app\extensions\action\Controller;
    use app\models\Payments;

    class PaymentsController extends Controller
    {
        public function index()
        {
            $payments = Payments::all();

            return compact('payments');
        }
    }