<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240629134613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE codis (id INT AUTO_INCREMENT NOT NULL, tipus_codi_id INT NOT NULL, empresa_publica_id INT NOT NULL, codi VARCHAR(255) NOT NULL, unitat VARCHAR(255) NOT NULL, INDEX IDX_49188E419BC89A8D (tipus_codi_id), INDEX IDX_49188E415A6AEF57 (empresa_publica_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte_bancari (id INT AUTO_INCREMENT NOT NULL, iban VARCHAR(255) NOT NULL, swift VARCHAR(255) NOT NULL, entitat VARCHAR(255) NOT NULL, referencia VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE element_factura (id INT AUTO_INCREMENT NOT NULL, preu_amb_impostos DOUBLE PRECISION NOT NULL, preu_sense_impostos DOUBLE PRECISION NOT NULL, impost INT NOT NULL, unitats INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE elements (id INT AUTO_INCREMENT NOT NULL, concepte VARCHAR(255) NOT NULL, preu_unitari INT NOT NULL, preu_sense_impostos INT NOT NULL, impost DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE factura (id INT AUTO_INCREMENT NOT NULL, compte_bancari_id INT NOT NULL, emisor_id INT NOT NULL, receptor_id INT NOT NULL, data_emissio DATE NOT NULL, total INT NOT NULL, url_pdf VARCHAR(255) DEFAULT NULL, INDEX IDX_F9EBA009553FD968 (compte_bancari_id), INDEX IDX_F9EBA0096BDF87DF (emisor_id), INDEX IDX_F9EBA009386D8D01 (receptor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persona_empresa (id INT AUTO_INCREMENT NOT NULL, nif VARCHAR(255) NOT NULL, carrer VARCHAR(255) NOT NULL, ciutat VARCHAR(255) NOT NULL, cp VARCHAR(255) NOT NULL, provincia VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tipus_codi (id INT AUTO_INCREMENT NOT NULL, referencia VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE codis ADD CONSTRAINT FK_49188E419BC89A8D FOREIGN KEY (tipus_codi_id) REFERENCES tipus_codi (id)');
        $this->addSql('ALTER TABLE codis ADD CONSTRAINT FK_49188E415A6AEF57 FOREIGN KEY (empresa_publica_id) REFERENCES persona_empresa (id)');
        $this->addSql('ALTER TABLE factura ADD CONSTRAINT FK_F9EBA009553FD968 FOREIGN KEY (compte_bancari_id) REFERENCES compte_bancari (id)');
        $this->addSql('ALTER TABLE factura ADD CONSTRAINT FK_F9EBA0096BDF87DF FOREIGN KEY (emisor_id) REFERENCES persona_empresa (id)');
        $this->addSql('ALTER TABLE factura ADD CONSTRAINT FK_F9EBA009386D8D01 FOREIGN KEY (receptor_id) REFERENCES persona_empresa (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE codis DROP FOREIGN KEY FK_49188E419BC89A8D');
        $this->addSql('ALTER TABLE codis DROP FOREIGN KEY FK_49188E415A6AEF57');
        $this->addSql('ALTER TABLE factura DROP FOREIGN KEY FK_F9EBA009553FD968');
        $this->addSql('ALTER TABLE factura DROP FOREIGN KEY FK_F9EBA0096BDF87DF');
        $this->addSql('ALTER TABLE factura DROP FOREIGN KEY FK_F9EBA009386D8D01');
        $this->addSql('DROP TABLE codis');
        $this->addSql('DROP TABLE compte_bancari');
        $this->addSql('DROP TABLE element_factura');
        $this->addSql('DROP TABLE elements');
        $this->addSql('DROP TABLE factura');
        $this->addSql('DROP TABLE persona_empresa');
        $this->addSql('DROP TABLE tipus_codi');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
