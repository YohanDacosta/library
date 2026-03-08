<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260314155550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tutor_school DROP FOREIGN KEY `FK_DC209D69208F64F1`');
        $this->addSql('ALTER TABLE tutor_school DROP FOREIGN KEY `FK_DC209D69C32A47EE`');
        $this->addSql('DROP TABLE tutor_school');
        $this->addSql('ALTER TABLE tutor ADD user_id BINARY(16) NOT NULL, DROP first_name, DROP last_name, DROP email');
        $this->addSql('ALTER TABLE tutor ADD CONSTRAINT FK_99074648A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99074648A76ED395 ON tutor (user_id)');
        $this->addSql('ALTER TABLE user ADD pin_code VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tutor_school (tutor_id BINARY(16) NOT NULL, school_id BINARY(16) NOT NULL, INDEX IDX_DC209D69208F64F1 (tutor_id), INDEX IDX_DC209D69C32A47EE (school_id), PRIMARY KEY (tutor_id, school_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tutor_school ADD CONSTRAINT `FK_DC209D69208F64F1` FOREIGN KEY (tutor_id) REFERENCES tutor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutor_school ADD CONSTRAINT `FK_DC209D69C32A47EE` FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutor DROP FOREIGN KEY FK_99074648A76ED395');
        $this->addSql('DROP INDEX UNIQ_99074648A76ED395 ON tutor');
        $this->addSql('ALTER TABLE tutor ADD first_name VARCHAR(100) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, DROP user_id');
        $this->addSql('ALTER TABLE user DROP pin_code');
    }
}
