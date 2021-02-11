<?php

use yii\db\Migration;

/**
 * Class m210211_080415_apples
 */

/**
 * Description of Apple
 *
 * @author Virus
 * @property integer $id
 * @property integer $user_id
 * @property string  $color
 * @property integer $dateOfAppearance
 * @property integer $dateOfFall
 * @property integer $status
 * @property double  $size
 */

class m210211_080415_apples extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%apples}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'color' => $this->string()->notNull(),
            'dateOfAppearance' => $this->integer()->notNull(),
            'dateOfFall' => $this->integer(),
            'status' => $this->integer()->notNull(),
            'size' => $this->double()->defaultValue(1)
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apples}}');
    }

}
