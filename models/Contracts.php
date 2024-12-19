<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contracts".
 *
 * @property int $id_contract
 * @property string $phone
 * @property string $date_checkin
 * @property string $payment_type
 * @property string $comments
 * @property string $date_created
 *
 * @property Reservations[] $reservations
 */
class Contracts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contracts';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['phone', 'date_checkin'], 'required'],
            [['date_checkin', 'date_createddate_created'], 'safe'],
            [['payment_type', 'comments'], 'string'],
            [['phone'], 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^\+7\(\d{3,}\)\d{3,}-\d{2,}-\d{2,}$/', 'message'=>'формат номера телефона: +7(999)999-99-99'],
            ['payment_type', 'match', 'pattern' => '/^[а-я\s]+$/iu', 'message'=>'наличными, картой или онлайн'],
            ['date_checkin', 'match', 'pattern' => '/^\d{4}\-\d{1,2}\-\d{1,2}+$/iu', 'message'=>'в формате 2024-12-20'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_contract' => 'Id Contract',
            'phone' => 'Phone',
            'date_checkin' => 'Date Checkin',
            'payment_type' => 'Payment Type',
            'comments' => 'Comments',
            'date_created' => 'Date Created',
        ];
    }

    /**
     * Gets query for [[Contracts]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getReservations()
    {
        return $this->hasMany(Cart::class, ['contract_id' => 'id_contract']);
    }
}
