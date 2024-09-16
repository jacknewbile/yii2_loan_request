<?php

namespace backend\controllers;

use console\models\Customer;
use console\models\Request;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class RequestController extends \yii\rest\ActiveController
{
	public $modelClass = 'console\models\Request';
	public $enableCsrfValidation = false;
	public function actions()
	{
		return [
			'requests' => 'backend\controllers\RequestController::actionAddnew'

		];
	}
    public function actionIndex()
    {
        return $this->render('index');
    }

	/**
	 * @throws BadRequestHttpException
	 */
	public function actionAddnew()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$customerId = Yii::$app->request->post('customer_id');
		$amount = Yii::$app->request->post('amount');
		$term = Yii::$app->request->post('term');

        $customer = Customer::findOne($customerId);

        if (!$customer) {
            $customer = new Customer();
            $customer->id = $customerId;
            $customer->status = 'active';


            if (!$customer->save()) {
                Yii::error(
                    sprintf(
                        'Error during creating customer. Errors: %s',
                        json_encode($customer->getErrors())
                    ),
                    __METHOD__
                );
                return $this->errorResponse('Error creating customer');
            }
        }

		$approvedExists = Request::find()
								 ->where(['customer_id' => $customerId, 'status' => 'approved'])
								 ->exists();

		if ($approvedExists) {

			Yii::error(
				sprintf(
					'Customer with ID %d already has an approved Request, but trying to process another Request',
					$customerId,
				),
				__METHOD__
			);
			Yii::$app->response->statusCode = 400;
			return [
				'result' => false
			];
		}

		$load_request = new Request();
		$load_request->customer_id = $customerId;
		$load_request->amount = $amount;
		$load_request->term = $term;
		$load_request->status = 'pending';

		if ($load_request->save()) {
			return [
				'result' => true,
				'id' => $load_request->id
			];
		} else {
			Yii::error(
				sprintf(
					'Error during saving Request with ID %d. Errors: %s',
					$customerId,
					json_encode($load_request->getErrors())
				),
				__METHOD__
			);
			return [
				'result' => false,
				'error' => 'Error during saving Request',
			];
		}
	}

	private function errorResponse($message) {

		// set response code to 400
		Yii::$app->response->statusCode = 400;

		return $this->asJson(['error' => $message]);
	}

}
