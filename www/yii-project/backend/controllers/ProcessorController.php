<?php

namespace backend\controllers;

use console\models\Request;
use Yii;

class ProcessorController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $all_requests = Request::find()->all();
        return $this->asJson($all_requests);
    }

    public function actionHandle($delay)
    {
        $applications = Yii::$app->db->createCommand(
            '
        SELECT * 
        FROM request req
        WHERE req.status = :pending 
        AND req.customer_id NOT IN (
            SELECT customer_id 
            FROM request 
            WHERE status = :approved
        )
    '
        )
            ->bindValue(':pending', 'pending')
            ->bindValue(':approved', 'approved')
            ->queryAll();


        foreach ($applications as $one_request) {
            sleep($delay);

            $transaction = Yii::$app->db->beginTransaction();
            try {

                $decision = rand(1, 100) <= 10 ? 'approved' : 'declined';

                Yii::$app->db->createCommand()
                    ->update('request', ['status' => $decision], ['id' => $one_request['id']])
                    ->execute();

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error(
                    sprintf(
                        'Error during updating Request with ID %d. Errors: %s',
                        $one_request['id'],
                        json_encode($one_request->getErrors())
                    ),
                    __METHOD__
                );
                throw $e;
            }
        }


        return $this->asJson(['result' => true]);
    }
}
