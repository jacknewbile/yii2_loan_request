<?php

use yii\db\Migration;

/**
 * Class m240916_122000_customer
 */
class m240916_122000_customer extends Migration
{
	public function safeUp()
	{
		// Create 'customer' table
		$this->createTable('{{%customer}}', [
			'id' => $this->primaryKey(),
			'status' => $this->string()->notNull(),
			'date_created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),  // Automatically set on insert
			'date_updated' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),  // Will be updated via trigger
		]);

		// Create trigger to automatically update `date_updated` field on update
		$this->execute("
            CREATE OR REPLACE FUNCTION update_timestamp_column() 
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.date_updated = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

		$this->execute("
            CREATE TRIGGER update_customer_timestamp
            BEFORE UPDATE ON {{%customer}}
            FOR EACH ROW
            EXECUTE FUNCTION update_timestamp_column();
        ");
	}

	public function safeDown()
	{
		// Drop trigger and function before dropping table
		$this->execute("DROP TRIGGER IF EXISTS update_customer_timestamp ON {{%customer}};");
		$this->execute("DROP FUNCTION IF EXISTS update_timestamp_column;");

		// Drop table
		$this->dropTable('{{%customer}}');
	}

}
