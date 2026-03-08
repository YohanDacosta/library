<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260308111622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('CREATE INDEX IDX_169E6FB9C32A47EE ON course (school_id)');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY `FK_B723AF33C32A47EE`');
        $this->addSql('DROP INDEX IDX_B723AF33C32A47EE ON student');
        $this->addSql('ALTER TABLE student DROP email, DROP school_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9C32A47EE');
        $this->addSql('DROP INDEX IDX_169E6FB9C32A47EE ON course');
        $this->addSql('ALTER TABLE student ADD email VARCHAR(150) DEFAULT NULL, ADD school_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT `FK_B723AF33C32A47EE` FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('CREATE INDEX IDX_B723AF33C32A47EE ON student (school_id)');
    }
}
