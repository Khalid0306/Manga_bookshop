<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130145837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C17B6461');
        $this->addSql('DROP INDEX IDX_64C19C17B6461 ON category');
        $this->addSql('ALTER TABLE category DROP manga_id');
        $this->addSql('ALTER TABLE manga ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E0312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_765A9E0312469DE2 ON manga (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD manga_id INT NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C17B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_64C19C17B6461 ON category (manga_id)');
        $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E0312469DE2');
        $this->addSql('DROP INDEX IDX_765A9E0312469DE2 ON manga');
        $this->addSql('ALTER TABLE manga DROP category_id');
    }
}
