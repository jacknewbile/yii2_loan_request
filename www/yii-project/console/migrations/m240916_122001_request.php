<?php
use yii\db\Migration;

/**
 * Class m240916_122001_request
 */
class m240916_122001_request extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		// Create 'request' table
		$this->createTable('{{%request}}', [
			'id' => $this->primaryKey(),
			'customer_id' => $this->integer()->notNull(),
			'amount' => $this->integer()->notNull(),
			'term' => $this->integer()->notNull(), // term in days
			'status' => $this->string()->notNull()->defaultValue('pending'),
			'date_created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),  // Automatically set on insert
			'date_updated' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),  // Will be updated via trigger
		]);

		// Create index for column 'customer_id'
		$this->createIndex(
			'idx-request-customer_id',
			'{{%request}}',
			'customer_id'
		);

		// Add foreign key for table 'user'
		$this->addForeignKey(
			'fk-request-customer_id',
			'{{%request}}',
			'customer_id',
			'{{%customer}}',
			'id',
			'CASCADE'
		);

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
            CREATE TRIGGER update_request_timestamp
            BEFORE UPDATE ON {{%request}}
            FOR EACH ROW
            EXECUTE FUNCTION update_timestamp_column();
        ");
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		// Drop foreign key
		$this->dropForeignKey(
			'fk-request-customer_id',
			'{{%request}}'
		);

		// Drop index
		$this->dropIndex(
			'idx-request-customer_id',
			'{{%request}}'
		);

		// Drop trigger and function before dropping table
		$this->execute("DROP TRIGGER IF EXISTS update_customer_timestamp ON {{%customer}};");
		$this->execute("DROP FUNCTION IF EXISTS update_timestamp_column;");
		// Drop table
		$this->dropTable('{{%request}}');
	}
}


