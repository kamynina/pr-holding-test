<?php

namespace app\models;

use Yii;

enum Color: string {
    case Green = 'green';
    case Red = 'red';
    case Yellow = 'yellow';
}

enum Status: int {
    case HANGING = 1; // висит на дереве
    case FELL = 2; // упало
}

enum State: int {
    case HANGING = 1; // висит на дереве
    case FELL = 2; // упало
    case ROTTEN = 3; // гнилое
}
/**
 * This is the model class for table "apple".
 *
 * @property int $id
 * @property string|null $color
 * @property string|null $birthdate
 * @property string|null $fall_date
 * @property int|null $status
 * @property int|null $percent_eaten
 * @property float $size
 * @property State $state - readonly значение статуса, вычисляется на основании других
 */
class Apple extends \yii\db\ActiveRecord
{

    const SCENARIO_DELETE = 'delete';
    const SCENARIO_FALL_TO_GROUND = 'fall_to_ground';
    const SCENARIO_EAT = 'eat';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['birthdate', 'fall_date'], 'safe'],
            [['status', 'percent_eaten'], 'integer'],
            [['color'], 'string', 'max' => 16],
            [['status'],  'isEatable', 'on' => self::SCENARIO_EAT ],
            [['status'],  'isFallable', 'on' => self::SCENARIO_FALL_TO_GROUND ],
            [['status'],  'isDeletable', 'on' => self::SCENARIO_DELETE ],
            [['color'], 'in', 'range' => array_column(Color::cases(), 'value')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color' => 'Color',
            'birthdate' => 'Birthdate',
            'fall_date' => 'Fall Date',
            'status' => 'Status',
            'percent_eaten' => 'Percent Eaten',
        ];
    }

    public function getSize()
    {
        return round($this->percent_eaten / 100, 2);
    }

    public function getState(): ?State
    {
        switch ($this->status) {
            case Status::HANGING->value:
                return State::HANGING;
            case Status::FELL->value:
                if (strtotime("now") - strtotime($this->fall_date) >= 5 * 3600 ) {
                    return State::ROTTEN;
                }
                return State::FELL;
            case null:
                return null;
            default:
                throw new \RuntimeException(sprintf('incorrect value of status: %s', $this->status));
        }
    }

    public static function create()
    {
        $apple = new Apple();
        $color = Color::cases()[rand(0, count(State::cases()) - 1)];
        $apple->color = $color->value;
        $apple->status = State::HANGING->value;
        $apple->birthdate = date('Y-m-d H:i:s', rand(strtotime("2023-01-01"), strtotime("now")));
        $apple->percent_eaten = 0;
        $apple->save();
    }

    public function eat(int $piece)
    {
        $this->scenario = self::SCENARIO_EAT;
        $result = $this->validate();
        if (!$result) {
            throw new \RuntimeException(implode("; ", $this->getErrorSummary(true)));
        }
        $this->percent_eaten = $this->percent_eaten + $piece;
        if ($this->percent_eaten >= 100) {
            $this->percent_eaten = 100;
            $this->delete();
            return;
        }
        $this->save();
    }

    public function fallToGround()
    {
        $this->scenario = self::SCENARIO_FALL_TO_GROUND;
        $this->status = Status::FELL->value;
        $this->fall_date = date('Y-m-d H:i:s');
        $result = $this->validate();
        if (!$result) {
            throw new \RuntimeException(implode("; ", $this->getErrorSummary(true)));
        }
        $this->save();
    }

    public function delete()
    {
        $this->scenario = self::SCENARIO_DELETE;
        $result = $this->validate();
        if (!$result) {
            throw new \RuntimeException(implode("; ", $this->getErrorSummary(true)));
        }
        parent::delete();
    }

    public function isEatable()
    {
        if ($this->state == State::HANGING) {
            $this->addError('status', 'Съесть нельзя, яблоко на дереве');
            return;
        }

        if ($this->state == State::ROTTEN) {
            $this->addError('status', 'Съесть нельзя, яблоко испорчено');
            return;
        }
    }

    public function isFallable()
    {
        if ($this->getOldAttribute('status') != Status::HANGING->value) {
            $this->addError('status', 'Только висящее яблоко может упасть');
        }
    }

    public function isDeletable()
    {

    }
}
