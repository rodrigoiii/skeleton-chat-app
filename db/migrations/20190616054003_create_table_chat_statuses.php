<?php

use Phinx\Migration\AbstractMigration;

class CreateTableChatStatuses extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table("chat_statuses")
            ->addColumn("status", "enum", ['values' => ["online", "offline"]])
            ->addColumn("user_id", "integer")
            ->addTimestamps();

        $table->create();

        $table->addForeignKey('user_id', "users", "id")->save();
    }

    /**
     * [down description]
     *
     * @return void
     */
    public function down()
    {
        $table_exist = $this->hasTable("chat_statuses");
        if ($table_exist)
        {
            $table = $this->table("chat_statuses");

            $table->dropForeignKey("user_id")
                ->drop("chat_statuses")
                ->save();
        }
    }
}
