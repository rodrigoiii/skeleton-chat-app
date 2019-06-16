<?php

use Phinx\Migration\AbstractMigration;

class CreateTableContacts extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table("contacts")
            ->addColumn("contact_id", "integer", ['comment' => "Contact"])
            ->addColumn("user_id", "integer", ['comment' => "Owner"])
            ->addTimestamps();

        $table->create();

        $table->addForeignKey("contact_id", "users", "id")
            ->addForeignKey("user_id", "users", "id")
            ->save();
    }

    /**
     * [down description]
     *
     * @return void
     */
    public function down()
    {
        $table_exist = $this->hasTable("contacts");
        if ($table_exist)
        {
            $table = $this->table("contacts");

            $table->dropForeignKey(["contact_id", "user_id"])
                ->drop("contacts")
                ->save();
        }
    }
}
