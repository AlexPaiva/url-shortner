<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240514133225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create short_url table and drop url table if it exists';
    }

    public function up(Schema $schema): void
    {
        // Check if the 'url' table exists before attempting to drop it
        if ($schema->hasTable('url')) {
            $this->addSql('DROP TABLE url');
        }

        // Create the 'short_url' table
        $this->addSql('CREATE TABLE short_url (id INT AUTO_INCREMENT NOT NULL, original_url LONGTEXT NOT NULL, short_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Drop the 'short_url' table if it exists
        if ($schema->hasTable('short_url')) {
            $this->addSql('DROP TABLE short_url');
        }

        // Create the 'url' table
        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, original_url VARCHAR(2048) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, short_code VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }
}
