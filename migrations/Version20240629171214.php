<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240629171214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_factura ADD factura_id INT NOT NULL, ADD elements_id INT NOT NULL');
        $this->addSql('ALTER TABLE element_factura ADD CONSTRAINT FK_E00E05DF04F795F FOREIGN KEY (factura_id) REFERENCES factura (id)');
        $this->addSql('ALTER TABLE element_factura ADD CONSTRAINT FK_E00E05DDB6D46DE FOREIGN KEY (elements_id) REFERENCES elements (id)');
        $this->addSql('CREATE INDEX IDX_E00E05DF04F795F ON element_factura (factura_id)');
        $this->addSql('CREATE INDEX IDX_E00E05DDB6D46DE ON element_factura (elements_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_factura DROP FOREIGN KEY FK_E00E05DF04F795F');
        $this->addSql('ALTER TABLE element_factura DROP FOREIGN KEY FK_E00E05DDB6D46DE');
        $this->addSql('DROP INDEX IDX_E00E05DF04F795F ON element_factura');
        $this->addSql('DROP INDEX IDX_E00E05DDB6D46DE ON element_factura');
        $this->addSql('ALTER TABLE element_factura DROP factura_id, DROP elements_id');
    }
}
