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
            ->addColumn("from_id", "integer", ['comment' => "Message sender"])
            ->addColumn("to_id", "integer", ['comment' => "Message receiver"])
            ->addTimestamps();

        $table->create();

        $table->addForeignKey("from_id", "users", "id")
            ->addForeignKey("to_id", "users", "id")
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

            $table->dropForeignKey(["from_id", "to_id"])
                ->drop("messages")
                ->save();
        }
    }
}
