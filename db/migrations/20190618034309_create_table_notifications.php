<?php

use Phinx\Migration\AbstractMigration;

class CreateTableNotifications extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table("notifications")
            ->addColumn("by_id", "integer")
            ->addColumn("to_id", "integer")
            ->addColumn("is_read", "boolean", ['default' => 0])
            ->addColumn("message", "string")
            ->addTimestamps();

        $table->create();
    }

    /**
     * [down description]
     *
     * @return void
     */
    public function down()
    {
        $table_exist = $this->hasTable("notifications");
        if ($table_exist)
        {
            $table = $this->table("notifications");
            $table->drop()->save();
        }
    }
}
