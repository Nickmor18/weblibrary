<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class BookMigration extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $books = $this->table('books');
        $books
            ->addColumn('uid','string')
            ->addColumn('title', 'string', ['limit' => 255])
            ->addColumn('description', 'text', ['null' => true])
            ->addIndex(['uid'], ['unique' => true])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('books');
    }
}
