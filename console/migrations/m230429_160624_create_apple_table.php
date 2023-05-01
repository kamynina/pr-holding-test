<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apple}}`.
 */
class m230429_160624_create_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'color' => $this->string(16)->notNull(),
            'birthdate' => $this->timestamp()->defaultValue(null),
            'fall_date' => $this->timestamp()->defaultValue(null),
            'status' => $this->tinyInteger()->null(),
            'percent_eaten' => $this->tinyInteger()->defaultValue(0)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }
}
