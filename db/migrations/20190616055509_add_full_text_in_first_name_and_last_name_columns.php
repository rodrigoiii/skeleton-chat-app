<?php

use Phinx\Migration\AbstractMigration;

class AddFullTextInFirstNameAndLastNameColumns extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table("users")
            ->addIndex(["first_name", "last_name"], ['type' => "fulltext"]);

        $table->save();
    }

    /**
     * [down description]
     *
     * @return void
     */
    public function down()
    {
        // $table_exist = $this->hasTable("users");
        // if ($table_exist)
        // {
        //     $table = $this->table("users");

        //     if ($table->hasColumn("first_name"))
        //     {
        //         $table->removeIndex("first_name");
        //         $table->save();
        //     }

        //     if ($table->hasColumn("last_name"))
        //     {
        //         $table->removeIndex("last_name");
        //         $table->save();
        //     }
        // }
    }
}
