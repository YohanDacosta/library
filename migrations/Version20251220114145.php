<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251220114145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan ADD student_id BINARY(16) NOT NULL, ADD tutor_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D03CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D03208F64F1 FOREIGN KEY (tutor_id) REFERENCES tutor (id)');
        $this->addSql('CREATE INDEX IDX_C5D30D03CB944F1A ON loan (student_id)');
        $this->addSql('CREATE INDEX IDX_C5D30D03208F64F1 ON loan (tutor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D03CB944F1A');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D03208F64F1');
        $this->addSql('DROP INDEX IDX_C5D30D03CB944F1A ON loan');
        $this->addSql('DROP INDEX IDX_C5D30D03208F64F1 ON loan');
        $this->addSql('ALTER TABLE loan DROP student_id, DROP tutor_id');
    }
}
