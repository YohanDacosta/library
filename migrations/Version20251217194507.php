<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251217194507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE course (id BINARY(16) NOT NULL, name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE school (id BINARY(16) NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tutor (id BINARY(16) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE student ADD course_id BINARY(16) NOT NULL, ADD school_id BINARY(16) NOT NULL, DROP grade');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF33591CC992 ON student (course_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF33C32A47EE ON student (school_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE tutor');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33591CC992');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33C32A47EE');
        $this->addSql('DROP INDEX UNIQ_B723AF33591CC992 ON student');
        $this->addSql('DROP INDEX UNIQ_B723AF33C32A47EE ON student');
        $this->addSql('ALTER TABLE student ADD grade VARCHAR(50) NOT NULL, DROP course_id, DROP school_id');
    }
}
