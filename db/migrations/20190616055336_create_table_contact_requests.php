<?php

use Phinx\Migration\AbstractMigration;

class CreateTableContactRequests extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table("contact_requests")
            ->addColumn("by_id", "integer")
            ->addColumn("to_id", "integer")
            ->addColumn("is_read_by", "boolean", ['default' => 0])
            ->addColumn("is_read_to", "boolean", ['default' => 0])
            ->addColumn("is_accepted", "boolean")
            ->addTimestamps();

        $table->create();

        $table->addForeignKey("by_id", "users", "id")
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
        $table_exist = $this->hasTable("contact_requests");
        if ($table_exist)
        {
            $table = $this->table("contact_requests");

            $table->dropForeignKey(["by_id", "to_id"])
                ->drop("contact_requests")
                ->save();
        }
    }
}
