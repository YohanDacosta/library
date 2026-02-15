<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215092747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loan_iteam (id INT AUTO_INCREMENT NOT NULL, loan_id BINARY(16) NOT NULL, book_id BINARY(16) NOT NULL, INDEX IDX_E01D837DCE73868F (loan_id), INDEX IDX_E01D837D16A2B381 (book_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE loan_iteam ADD CONSTRAINT FK_E01D837DCE73868F FOREIGN KEY (loan_id) REFERENCES loan (id)');
        $this->addSql('ALTER TABLE loan_iteam ADD CONSTRAINT FK_E01D837D16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY `FK_C5D30D0316A2B381`');
        $this->addSql('DROP INDEX IDX_C5D30D0316A2B381 ON loan');
        $this->addSql('ALTER TABLE loan DROP book_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan_iteam DROP FOREIGN KEY FK_E01D837DCE73868F');
        $this->addSql('ALTER TABLE loan_iteam DROP FOREIGN KEY FK_E01D837D16A2B381');
        $this->addSql('DROP TABLE loan_iteam');
        $this->addSql('ALTER TABLE loan ADD book_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT `FK_C5D30D0316A2B381` FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_C5D30D0316A2B381 ON loan (book_id)');
    }
}
