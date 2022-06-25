<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserBookMigration extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $userBook = $this->table('user_book');
        $userBook->addColumn('user_id', 'integer', [
            'null' => false,
        ]);
        $userBook->addColumn('book_id', 'integer', [
            'null' => false,
        ]);
        $userBook->addColumn('favorite', 'boolean', [
            'default' => false,
        ]);
        $userBook->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        $userBook->addForeignKey('book_id', 'books', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        $userBook->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('user_book');
    }
}
