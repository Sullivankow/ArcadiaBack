<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020152827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_habitat (image_id INT NOT NULL, habitat_id INT NOT NULL, INDEX IDX_AE27E5343DA5256D (image_id), INDEX IDX_AE27E534AFFE2D26 (habitat_id), PRIMARY KEY(image_id, habitat_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_habitat ADD CONSTRAINT FK_AE27E5343DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_habitat ADD CONSTRAINT FK_AE27E534AFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_habitat DROP FOREIGN KEY FK_AE27E5343DA5256D');
        $this->addSql('ALTER TABLE image_habitat DROP FOREIGN KEY FK_AE27E534AFFE2D26');
        $this->addSql('DROP TABLE image_habitat');
    }
}
