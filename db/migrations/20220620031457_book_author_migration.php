<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class BookAuthorMigration extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $bookAuthors = $this->table('book_authors');
        $bookAuthors->addColumn('author_id', 'integer', [
            'null' => false,
        ]);
        $bookAuthors->addColumn('book_id', 'integer', [
            'null' => false,
        ]);
        $bookAuthors->addForeignKey('author_id', 'authors', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE']);
        $bookAuthors->addForeignKey('book_id', 'books', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE']);
        $bookAuthors->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('book_authors');
    }
}
