<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222213820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student DROP INDEX UNIQ_B723AF33591CC992, ADD INDEX IDX_B723AF33591CC992 (course_id)');
        $this->addSql('ALTER TABLE student DROP INDEX UNIQ_B723AF33C32A47EE, ADD INDEX IDX_B723AF33C32A47EE (school_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student DROP INDEX IDX_B723AF33591CC992, ADD UNIQUE INDEX UNIQ_B723AF33591CC992 (course_id)');
        $this->addSql('ALTER TABLE student DROP INDEX IDX_B723AF33C32A47EE, ADD UNIQUE INDEX UNIQ_B723AF33C32A47EE (school_id)');
    }
}
