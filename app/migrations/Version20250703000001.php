<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250703000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users, friendships and posts tables for FocusDaily';
    }

    public function up(Schema $schema): void
    {
        // Users table
        $this->addSql('CREATE TABLE users (
            id CHAR(36) NOT NULL PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            hashed_password VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Friendships table
        $this->addSql('CREATE TABLE friendships (
            id CHAR(36) NOT NULL PRIMARY KEY,
            requester_id CHAR(36) NOT NULL,
            addressee_id CHAR(36) NOT NULL,
            status ENUM(\'pending\', \'accepted\', \'rejected\') NOT NULL DEFAULT \'pending\',
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE KEY friendship_unique (requester_id, addressee_id),
            INDEX requester_idx (requester_id),
            INDEX addressee_idx (addressee_id),
            INDEX status_idx (status)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Posts table
        $this->addSql('CREATE TABLE posts (
            id CHAR(36) NOT NULL PRIMARY KEY,
            author_id CHAR(36) NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            archived_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX author_created_idx (author_id, created_at),
            INDEX archived_idx (archived_at)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Foreign keys
        $this->addSql('ALTER TABLE friendships ADD CONSTRAINT FK_friendships_requester FOREIGN KEY (requester_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE friendships ADD CONSTRAINT FK_friendships_addressee FOREIGN KEY (addressee_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_posts_author FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE friendships');
        $this->addSql('DROP TABLE users');
    }
}
