<?php

namespace common\models\room;
use common\models\BaseModel;

/**
 * This is the model class for table "{{%room_state}}".
 *
 * @property int $state_id 状态编号
 * @property string $state_name 状态名称
 */
class RoomState extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%room_state}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state_name'], 'string', 'max' => 20],
            [['state_name'],'unique','on'=>'Reg'],
            [['state_name'],'unique','on'=>'Edit'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'state_id' => '状态编号',
            'state_name' => '状态名称',
        ];
    }

    /*
        获取所有房间状态
    */
    public static function getRoomStateAll()
    {
        return self::find()->select(['state_id','state_name'])->asArray()->all();
    }

    /*
        通过id获取房间状态
    */
    public static function getRoomStateById($stateId,$field=['*'])
    {
        if(!$stateId) return false;
        return self::find()->select($field)->where(['state_id' => $stateId])->asArray()->one();
    }

    /*
        通过状态名获取未入住状态ID
    */
    public static function getRoomStateId($state_name = '未入住')
    {
        return self::find()->select('state_id')->where(['state_name' => $state_name])->asArray()->one()['state_id'];
    }
}
