<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240629193641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE impost (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, percentatge INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE element_factura CHANGE impost impost_id INT NOT NULL');
        $this->addSql('ALTER TABLE element_factura ADD CONSTRAINT FK_E00E05D8BC24A69 FOREIGN KEY (impost_id) REFERENCES impost (id)');
        $this->addSql('CREATE INDEX IDX_E00E05D8BC24A69 ON element_factura (impost_id)');
        $this->addSql('ALTER TABLE elements ADD impost_id INT NOT NULL, DROP impost');
        $this->addSql('ALTER TABLE elements ADD CONSTRAINT FK_444A075D8BC24A69 FOREIGN KEY (impost_id) REFERENCES impost (id)');
        $this->addSql('CREATE INDEX IDX_444A075D8BC24A69 ON elements (impost_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_factura DROP FOREIGN KEY FK_E00E05D8BC24A69');
        $this->addSql('ALTER TABLE elements DROP FOREIGN KEY FK_444A075D8BC24A69');
        $this->addSql('DROP TABLE impost');
        $this->addSql('DROP INDEX IDX_E00E05D8BC24A69 ON element_factura');
        $this->addSql('ALTER TABLE element_factura CHANGE impost_id impost INT NOT NULL');
        $this->addSql('DROP INDEX IDX_444A075D8BC24A69 ON elements');
        $this->addSql('ALTER TABLE elements ADD impost DOUBLE PRECISION NOT NULL, DROP impost_id');
    }
}
