<?php

use Phinx\Migration\AbstractMigration;

class AddFieldPictureInUsersTable extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table("users")
            ->addColumn('picture', 'string', ['null' => true, 'after' => "id"]);

        $table->update();
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
            $column_exist = $table->hasColumn("picture");

            if ($column_exist)
            {
                $table->removeColumn("picture");
                $table->save();
            }
        }
    }
}
