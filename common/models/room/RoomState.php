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
}
