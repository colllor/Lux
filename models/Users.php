<?php

namespace app\models;

use yii\db\ActiveRecord; 
use yii\web\IdentityInterface; 
use Yii;
/**
 * This is the model class for table "users".
 *
 * @property int $id_user
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property string|null $token
 * @property string $role
 *
 * @property Reservations[] $carts
 */
class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone', 'email', 'password'], 'required'],
            ['password', 'match', 'pattern' => '/^.{6,}+$/iu','message'=>'Пароль должен состоять не менее чем из 6 символов'],
            ['first_name', 'match', 'pattern' => '/^[а-яё\s]+$/iu', 'message'=>'Имя пользователя может состоять только из кириллицы и пробела'],
            ['last_name', 'match', 'pattern' => '/^[а-яё\s]+$/iu', 'message'=>'Фамилия пользователя может состоять только из кириллицы и пробела'],
            ['phone', 'match', 'pattern' => '/^\+7\(\d{3,}\)\d{3,}-\d{2,}-\d{2,}$/', 'message'=>'формат номера телефона: +7(999)999-99-99'],
            ['email', 'email', 'message'=>'Неверный формат E-mail'],
            [['email'], 'unique', 'message'=>'Данный e-mail уже используется'],
            [['role'], 'string'],
            [['first_name', 'last_name', 'email'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 20],
            [['token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */


    public function attributeLabels()
    {
        return [
            'id_user' => 'Id User',
            'first_name' => 'First name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'password' => 'Password',
            'token' => 'Token',
            'role' => 'Role',
        ];
    }

    /**
     * Gets query for [[Reservations]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getReservations()
    {
        return $this->hasMany(Reservations::class, ['user_id' => 'id_user']);
    }

    public static function findIdentity($id_user)
    {
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id_user;
    }

    public function getAuthKey()
    {
    
    }

    public function validateAuthKey($authKey)
    {
        
    }

    public function isAdmin()
    {
    return $this->role ==='admin';
    }
    public function isAuthorized() {
        
        $token = str_replace('Bearer ', '', Yii::$app->request->headers->get('Authorization'));
        if (!$token||$token != $this->token) {
            return false;
        }
        return true; 
    }

    public static function getToken(){
        return self::findOne(['token'=>str_replace('Bearer ', '', Yii::$app->request->headers->get('Authorization'))]);
    }
}