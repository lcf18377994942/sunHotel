<?php

namespace common\models\room;
use common\models\BaseModel;

/**
 * This is the model class for table "{{%room_type}}".
 *
 * @property int $type_id 类型编号
 * @property string $type_name 类型名称
 * @property float $price 价格
 */
class RoomType extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%room_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['type_name'], 'string', 'max' => 20],
            [['type_name','price'],'required','on'=>'Reg'],
            [['type_name'],'unique','on'=>'Reg'],
            [['type_name','price'],'required','on'=>'Edit'],
            [['type_name'],'unique','on'=>'Edit'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'type_id' => '类型编号',
            'type_name' => '类型名称',
            'price' => '价格',
        ];
    }

    /*
        获取所有房间类型
    */
    public static function getRoomTypeAll()
    {
        return self::find()->select(['type_id','type_name'])->indexBy('type_id')->asArray()->all();
    }

    /*
        通过id获取房间类型
    */
    public static function getRoomTypeById($typeId,$field=['*'])
    {
        if(!$typeId) return false;
        return self::find()->select($field)->where(['type_id' => $typeId])->asArray()->one();
    }
}
