<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260314155359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loan_iteam (id BINARY(16) NOT NULL, loan_id BINARY(16) NOT NULL, book_id BINARY(16) NOT NULL, INDEX IDX_E01D837DCE73868F (loan_id), INDEX IDX_E01D837D16A2B381 (book_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE loan_iteam ADD CONSTRAINT FK_E01D837DCE73868F FOREIGN KEY (loan_id) REFERENCES loan (id)');
        $this->addSql('ALTER TABLE loan_iteam ADD CONSTRAINT FK_E01D837D16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD school_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('CREATE INDEX IDX_169E6FB9C32A47EE ON course (school_id)');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY `FK_C5D30D0316A2B381`');
        $this->addSql('DROP INDEX IDX_C5D30D0316A2B381 ON loan');
        $this->addSql('ALTER TABLE loan DROP book_id');
        $this->addSql('ALTER TABLE student DROP INDEX UNIQ_B723AF33591CC992, ADD INDEX IDX_B723AF33591CC992 (course_id)');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY `FK_B723AF33C32A47EE`');
        $this->addSql('DROP INDEX UNIQ_B723AF33C32A47EE ON student');
        $this->addSql('ALTER TABLE student DROP email, DROP school_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan_iteam DROP FOREIGN KEY FK_E01D837DCE73868F');
        $this->addSql('ALTER TABLE loan_iteam DROP FOREIGN KEY FK_E01D837D16A2B381');
        $this->addSql('DROP TABLE loan_iteam');
        $this->addSql('ALTER TABLE book DROP image');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9C32A47EE');
        $this->addSql('DROP INDEX IDX_169E6FB9C32A47EE ON course');
        $this->addSql('ALTER TABLE course DROP school_id');
        $this->addSql('ALTER TABLE loan ADD book_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT `FK_C5D30D0316A2B381` FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_C5D30D0316A2B381 ON loan (book_id)');
        $this->addSql('ALTER TABLE student DROP INDEX IDX_B723AF33591CC992, ADD UNIQUE INDEX UNIQ_B723AF33591CC992 (course_id)');
        $this->addSql('ALTER TABLE student ADD email VARCHAR(150) DEFAULT NULL, ADD school_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT `FK_B723AF33C32A47EE` FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF33C32A47EE ON student (school_id)');
    }
}
