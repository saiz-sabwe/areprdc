<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250208083103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inscription_payment (id INT AUTO_INCREMENT NOT NULL, mode_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', enrollee_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', amount DOUBLE PRECISION NOT NULL, status INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reference VARCHAR(64) NOT NULL, INDEX IDX_44F972E177E5854A (mode_id), INDEX IDX_44F972E1A5BEEC8F (enrollee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscription_payment ADD CONSTRAINT FK_44F972E177E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('ALTER TABLE inscription_payment ADD CONSTRAINT FK_44F972E1A5BEEC8F FOREIGN KEY (enrollee_id) REFERENCES `member` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription_payment DROP FOREIGN KEY FK_44F972E177E5854A');
        $this->addSql('ALTER TABLE inscription_payment DROP FOREIGN KEY FK_44F972E1A5BEEC8F');
        $this->addSql('DROP TABLE inscription_payment');
    }
}
