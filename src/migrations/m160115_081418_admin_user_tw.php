<?php

use yii\db\Schema;
use yii\db\Migration;

class m160115_081418_admin_user_tw extends Migration
{
    public function up()
    {
        $this->execute("INSERT INTO {{%user}} (`id`, `username`, `email`, `password_hash`, `auth_key`, `confirmed_at`, `unconfirmed_email`, `blocked_at`, `registration_ip`, `created_at`, `updated_at`, `flags`) VALUES
(1, 'admin', 'info@dukisoft.com', '\$2y\$10\$aUcoJriuMdENOgK9jtwkYubOwcZsPnaKA81pqsS5/bD/H37SenTEe', '18ejHbm8lkBwuG50knS2IXWNzqQRN7G8', 1434121729, NULL, NULL, NULL, 1434121725, 1434121725, 0);
");
    }

    public function down()
    {
        echo "m160115_081418_admin_user_tw cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
