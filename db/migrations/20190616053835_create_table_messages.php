<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class CreateTableMessages extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table("messages")
            ->addColumn("message", "text", ['limit' => MysqlAdapter::TEXT_TINY])
            ->addColumn("is_read", "boolean", ['default' => 0])
            ->addColumn("sender_id", "integer")
            ->addColumn("receiver_id", "integer")
            ->addTimestamps();

        $table->create();

        $table->addForeignKey("sender_id", "users", "id")
            ->addForeignKey("receiver_id", "users", "id")
            ->save();
    }

    /**
     * [down description]
     *
     * @return void
     */
    public function down()
    {
        $table_exist = $this->hasTable("messages");
        if ($table_exist)
        {
            $table = $this->table("messages");

            $table->dropForeignKey(["sender_id", "receiver_id"])
                ->drop("messages")
                ->save();
        }
    }
}
