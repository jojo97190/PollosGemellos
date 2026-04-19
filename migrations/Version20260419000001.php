<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260419000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change roles column from JSON to VARCHAR(255)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE utilisateur ALTER COLUMN roles TYPE VARCHAR(255) USING 'ROLE_USER'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE utilisateur ALTER COLUMN roles TYPE JSON USING to_json(roles)");
    }
}
