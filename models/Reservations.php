<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reservations".
 *
 * @property int $id_reservation
 * @property int $user_id
 * @property int $room_id
 * @property int $count_people
 * @property int|null $contract_id
 *
 * @property Contracts $contract
 * @property Rooms $rooms
 * @property Users $user
 */
class Reservations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reservations';
    }

    /**
    * {@inheritdoc}
    */

    public function rules()
    {
        return [
            [['user_id', 'room_id', 'count_people'], 'required'],
            [['user_id', 'room_id', 'count_people', 'contract_id'], 'integer'],
            ['count_people', 'match', 'pattern' => '/[0-9]+$/iu', 'message' =>'Только цифры'],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rooms::class, 'targetAttribute' => ['room_id' => 'id_room']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id_user']],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contracts::class, 'targetAttribute' => ['contract_id' => 'id_contract']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_reservation' => 'Id Cart',
            'user_id' => 'User ID',
            'room_id' => 'Room ID',
            'count_people' => 'Count People',
            'contract_id' => 'Contract ID',
        ];
    }

    /**
     * Gets query for [[Contracts]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getContract()
    {
        return $this->hasOne(Contracts::class, ['id_contract' => 'contract_id']);
    }

    /**
     * Gets query for [[Rooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRooms()
    {
        return $this->hasOne(Rooms::class, ['id_room' => 'room_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id_user' => 'user_id']);
    }
}
