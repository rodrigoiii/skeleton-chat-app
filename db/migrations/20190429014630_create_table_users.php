<?php

use Phinx\Migration\AbstractMigration;

class CreateTableUsers extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table("users")
            ->addColumn("first_name", "string", ['limit' => 50])
            ->addColumn("last_name", "string", ['limit' => 50])
            ->addColumn("email", "string", ['limit' => 50])
            ->addColumn("password", "string", ['limit' => 60])
            ->addIndex("email", ['unique' => true])
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
        $table_exist = $this->hasTable("users");
        if ($table_exist)
        {
            $table = $this->table("users");
            $table->drop()->save();
        }
    }
}
