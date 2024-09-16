<?php

namespace console\models;

use Yii;
use yii\web\Response;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $amount
 * @property int $term
 * @property string $status
 * @property string|null $date_created
 * @property string|null $date_updated
 *
 * @property Customer $customer
 */
class Request extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'amount', 'term'], 'required'],
            [['customer_id', 'amount', 'term'], 'default', 'value' => null],
            [['customer_id', 'amount', 'term'], 'integer'],
            [['date_created', 'date_updated'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'amount' => 'Amount',
            'term' => 'Term',
            'status' => 'Status',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
        ];
    }


    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
