<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Description of Apple
 *
 * @author Virus
 */
class Apple extends Model
{
    // Блок констант для статуса яблока
    const ON_TREE = 1;      // Яблоко на дереве
    const ON_GROUND = 2;    // Яблоко упало и может быть съедено
    const SPOILED = 3;      // Яблоко сгнило

    // Массив строк для статуса яблока
    const STATUS_STRINGS = [
        ON_TREE     => 'Яблоко на дереве',
        ON_GROUND   => 'Яблоко на земле',
        SPOILED     => 'Яблоко сгнило'
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

    // Константа аккуратности вывода размера яблока (количество цифр после
    // запятой)
    const SIZE_ACCURACY = 2;

    // Цвет яблока
    private $color;
    // Дата появления
    private $dateOfAppearance;
    // Дата падения с дерева
    private $dateOfFall;
    // Статус яблока
    private $status;
    // Размер яблока (1 - целое, 0 - съедено)
    private $size;

    public function __construct($color = null) {
        parent::__construct();
        if (!is_null($color)) {
            $this->color = $color;
        } else {
            $this->color = COLORS[rand(0,5)];
        }
        $this->dateOfAppearance = time();
        $this->status = ON_TREE;
        $this->size = 1;
    }

    public function getColor() {
        return $this->color;
    }

    public function getDateOfAppearance($timeFormat = 'Y-m-d H:i:s') {
        return date($timeFormat, $this->dateOfAppearance);
    }

    public function getDateOfFall($timeFormat = 'Y-m-d H:i:s') {
        return date($timeFormat, $this->dateOfFall);
    }

    public function fallToGround() {
        if ($this->status != ON_TREE) {
            throw new InvalidValueException(
                    'Данное яблоко уже упало или сгнило.');
        }
        $this->status = ON_GROUND;
        $this->dateOfFall = time();
    }

    public function getStatus() {
        return self::STATUS_STRINGS[$this->status];
    }

    public function getSize() {
        return number_format($this->size, SIZE_ACCURACY);
    }

    public function eat($percent) {
        if ($this->status == ON_GROUND) {
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
    
}
