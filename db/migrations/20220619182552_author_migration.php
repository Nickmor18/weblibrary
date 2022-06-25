<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AuthorMigration extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $authors = $this->table('authors');
        $authors
            ->addColumn('name', 'string', ['limit' => 255])
            ->addIndex(['name'], ['unique' => true])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('authors');
    }
}
