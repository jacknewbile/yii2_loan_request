<?php

use Faker\Factory;
use yii\db\Migration;

/**
 * Class m240916_122004_seed_customers_table
 */
class m240916_122004_seed_customers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->insertFakeMembers();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240916_122004_seed_customers_table cannot be reverted.\n";

        return false;
    }

	private function insertFakeMembers()
	{
		$faker = Factory::create();
		$customers = [];

		// Insert 10 fake customers into customer table
		for ($i = 0; $i < 10; $i++) {
			$customers[] = [
				$faker->randomElement(['active', 'inactive']),  // status
				$faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),  // date_created
				$faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),  // date_updated
			];
		}
		// Insert generated customer data
		$this->batchInsert('{{%customer}}', ['status', 'date_created', 'date_updated'], $customers);

		// Fetch the IDs of newly inserted customers
		$customerIds = (new \yii\db\Query())
			->select('id')
			->from('{{%customer}}')
			->column();

		$requests = [];
		// Insert 20 fake requests, linking them to random customer IDs
		for ($i = 0; $i < 20; $i++) {
			$requests[] = [
				$faker->randomElement($customerIds),  // customer_id
				$faker->numberBetween(100, 5000),  // amount
				$faker->numberBetween(10, 60),  // term in days
				$faker->randomElement(['approved', 'declined']),  // status
				$faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),  // date_created
				$faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),  // date_updated
			];
		}
		// Insert generated request data
		$this->batchInsert('{{%request}}', ['customer_id', 'amount', 'term', 'status', 'date_created', 'date_updated'], $requests);
	}
}
