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
            ->addColumn("from_id", "integer")
            ->addColumn("to_id", "integer")
            ->addColumn("is_accepted", "boolean", ['default' => 0])
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
        $table_exist = $this->hasTable("contact_requests");
        if ($table_exist)
        {
            $table = $this->table("contact_requests");

            $table->dropForeignKey(["from_id", "to_id"])
                ->drop("contact_requests")
                ->save();
        }
    }
}
