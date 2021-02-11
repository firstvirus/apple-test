<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\InvalidValueException;

/**
 * Description of Apple
 *
 * @author Virus
 * @property integer $id
 * @property integer $user_id           Хозяин яблока
 * @property string  $color             Цвет яблока
 * @property integer $dateOfAppearance  Дата появления
 * @property integer $dateOfFall        Дата падения с дерева
 * @property integer $status            Статус яблока
 * @property double  $size              Размер яблока (1 - целое, 0 - съедено)
 */

class Apple extends ActiveRecord
{
    // Блок констант для статуса яблока
    const ON_TREE = 1;      // Яблоко на дереве
    const ON_GROUND = 2;    // Яблоко упало и может быть съедено
    const SPOILED = 3;      // Яблоко сгнило

    // Массив строк для статуса яблока
    const STATUS_STRINGS = [
        self::ON_TREE     => 'Яблоко на дереве',
        self::ON_GROUND   => 'Яблоко на земле',
        self::SPOILED     => 'Яблоко сгнило'
    ];

    // Массив первоначальных цветов яблока
    const COLORS = [
        'Зеленое',
        'Желтое',
        'Оранжевое',
        'Красное',
        'Синее',
        'Черное'
    ];

    // Константа времени гниения
    const TIME_SPOIL = 18000;

    public function __construct($color = null) {
        parent::__construct();
        if (!is_null($color) && !empty($color)) {
            $this->color = $color;
        } else {
            $this->color = self::COLORS[rand(0,5)];
        }
        $this->dateOfAppearance = time();
        $this->status = self::ON_TREE;
        $this->size = 1;
        $this->user_id = Yii::$app->user->id;
    }

    public static function tableName() {
        return '{{%apples}}';
    }

    public function getColor() {
        return $this->color;
    }

    public function getDateOfAppearance($timeFormat = 'Y-m-d H:i:s') {
        return date($timeFormat, $this->dateOfAppearance);
    }

    public function getDateOfFall($timeFormat = 'Y-m-d H:i:s') {
        if (!is_null($this->dateOfFall)) {
            return date($timeFormat, $this->dateOfFall);
        } else {
            return 'Еще не упало.';
        }
    }

    public function fallToGround() {
        if ($this->status != self::ON_TREE) {
            throw new InvalidValueException(
                    'Данное яблоко уже упало или сгнило.');
        }
        $this->status = self::ON_GROUND;
        $this->dateOfFall = time();
    }

    public function getStatus() {
        return self::STATUS_STRINGS[$this->status];
    }

    public function getStatusInt() {
        return $this->status;
    }

    public function getSize() {
//        return number_format($this->size, self::SIZE_ACCURACY);
        return strval(round($this->size * 100)) . '%';
    }

    public function eat($percent) {
        if ($this->status == self::ON_GROUND) {
            if ($percent < 0 || $percent > 100) {
                throw new InvalidValueException(
                        'Неверное количество процентов съедения яблока');
            }
            $this->size -= $percent / 100;
            if ($this->size < 0) {
                $this->size = 0;
            }
        } else {
            throw new InvalidValueException(
                    'Яблоко еще не упало или оно уже сгнило.');
        }
    }

    public function clean() {
        if ($this->getSize() == 0) {
            $this->delete();
            return true;
        }
        return false;
    }

    public function checkSpoiled() {
        if ($this->status == self::ON_GROUND) {
            if (($this->dateOfFall + self::TIME_SPOIL) <= time()) {
                $this->status = self::SPOILED;
                $this->save();
                return true;
            } else {
                return false;
            }
        }
    }

}
