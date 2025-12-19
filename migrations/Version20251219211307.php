<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251219211307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tutor_school (tutor_id BINARY(16) NOT NULL, school_id BINARY(16) NOT NULL, INDEX IDX_DC209D69208F64F1 (tutor_id), INDEX IDX_DC209D69C32A47EE (school_id), PRIMARY KEY (tutor_id, school_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tutor_school ADD CONSTRAINT FK_DC209D69208F64F1 FOREIGN KEY (tutor_id) REFERENCES tutor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutor_school ADD CONSTRAINT FK_DC209D69C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutor_course DROP FOREIGN KEY `FK_3320286B208F64F1`');
        $this->addSql('ALTER TABLE tutor_course DROP FOREIGN KEY `FK_3320286B591CC992`');
        $this->addSql('DROP TABLE tutor_course');
        $this->addSql('ALTER TABLE school DROP FOREIGN KEY `FK_F99EDABB208F64F1`');
        $this->addSql('DROP INDEX IDX_F99EDABB208F64F1 ON school');
        $this->addSql('ALTER TABLE school DROP tutor_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tutor_course (tutor_id BINARY(16) NOT NULL, course_id BINARY(16) NOT NULL, INDEX IDX_3320286B208F64F1 (tutor_id), INDEX IDX_3320286B591CC992 (course_id), PRIMARY KEY (tutor_id, course_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tutor_course ADD CONSTRAINT `FK_3320286B208F64F1` FOREIGN KEY (tutor_id) REFERENCES tutor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutor_course ADD CONSTRAINT `FK_3320286B591CC992` FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutor_school DROP FOREIGN KEY FK_DC209D69208F64F1');
        $this->addSql('ALTER TABLE tutor_school DROP FOREIGN KEY FK_DC209D69C32A47EE');
        $this->addSql('DROP TABLE tutor_school');
        $this->addSql('ALTER TABLE school ADD tutor_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT `FK_F99EDABB208F64F1` FOREIGN KEY (tutor_id) REFERENCES tutor (id)');
        $this->addSql('CREATE INDEX IDX_F99EDABB208F64F1 ON school (tutor_id)');
    }
}
