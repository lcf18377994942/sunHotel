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
}
