<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207160233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', city_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', federation_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', municipality VARCHAR(50) DEFAULT NULL, neighborhood VARCHAR(50) DEFAULT NULL, avenue VARCHAR(50) NOT NULL, number VARCHAR(10) NOT NULL, INDEX IDX_D4E6F818BAC62AF (city_id), INDEX IDX_D4E6F816A03EFC5 (federation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', province_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(50) NOT NULL, INDEX IDX_2D5B0234E946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE community (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', sector_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(50) NOT NULL, INDEX IDX_1B604033DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE federation (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', city_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(50) NOT NULL, INDEX IDX_AD241BCD8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `member` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', member_category_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', province_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', territory_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', sector_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', community_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', address_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', firstname VARCHAR(30) NOT NULL, lastname VARCHAR(30) NOT NULL, gender VARCHAR(6) NOT NULL, email VARCHAR(50) DEFAULT NULL, reference VARCHAR(50) NOT NULL, date_of_birth DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', place_of_birth VARCHAR(50) NOT NULL, name VARCHAR(30) NOT NULL, country VARCHAR(50) NOT NULL, phone VARCHAR(20) DEFAULT NULL, education VARCHAR(30) DEFAULT NULL, affiliation VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_70E4FA78E7927C74 (email), UNIQUE INDEX UNIQ_70E4FA78AEA34913 (reference), INDEX IDX_70E4FA78CB7B8920 (member_category_id), INDEX IDX_70E4FA78E946114A (province_id), INDEX IDX_70E4FA7873F74AD4 (territory_id), INDEX IDX_70E4FA78DE95C867 (sector_id), INDEX IDX_70E4FA78FDA7B0BF (community_id), INDEX IDX_70E4FA78F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_category (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(25) NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(25) NOT NULL, code VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sector (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', territory_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(50) NOT NULL, INDEX IDX_4BA3D9E873F74AD4 (territory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE territory (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', province_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(50) NOT NULL, INDEX IDX_E9743966E946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F816A03EFC5 FOREIGN KEY (federation_id) REFERENCES federation (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE community ADD CONSTRAINT FK_1B604033DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE federation ADD CONSTRAINT FK_AD241BCD8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78CB7B8920 FOREIGN KEY (member_category_id) REFERENCES member_category (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA7873F74AD4 FOREIGN KEY (territory_id) REFERENCES territory (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78FDA7B0BF FOREIGN KEY (community_id) REFERENCES community (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E873F74AD4 FOREIGN KEY (territory_id) REFERENCES territory (id)');
        $this->addSql('ALTER TABLE territory ADD CONSTRAINT FK_E9743966E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F816A03EFC5');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234E946114A');
        $this->addSql('ALTER TABLE community DROP FOREIGN KEY FK_1B604033DE95C867');
        $this->addSql('ALTER TABLE federation DROP FOREIGN KEY FK_AD241BCD8BAC62AF');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78CB7B8920');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78E946114A');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA7873F74AD4');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78DE95C867');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78FDA7B0BF');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78F5B7AF75');
        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E873F74AD4');
        $this->addSql('ALTER TABLE territory DROP FOREIGN KEY FK_E9743966E946114A');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE community');
        $this->addSql('DROP TABLE federation');
        $this->addSql('DROP TABLE `member`');
        $this->addSql('DROP TABLE member_category');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE sector');
        $this->addSql('DROP TABLE territory');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
