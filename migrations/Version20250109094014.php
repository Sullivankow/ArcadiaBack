<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109094014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal (id INT AUTO_INCREMENT NOT NULL, habitat_id INT DEFAULT NULL, race_id INT DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, etat VARCHAR(50) DEFAULT NULL, INDEX IDX_6AAB231FAFFE2D26 (habitat_id), INDEX IDX_6AAB231F6E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE habitat (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, description VARCHAR(155) NOT NULL, commentaire_habitat VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE habitat_image (habitat_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_9AD7E031AFFE2D26 (habitat_id), INDEX IDX_9AD7E0313DA5256D (image_id), PRIMARY KEY(habitat_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, image_data LONGBLOB DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rapport_veterinaire (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, animal_id INT DEFAULT NULL, date DATE NOT NULL, detail VARCHAR(255) DEFAULT NULL, INDEX IDX_CE729CDEA76ED395 (user_id), INDEX IDX_CE729CDE8E962C16 (animal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, api_token VARCHAR(255) NOT NULL, INDEX IDX_8D93D649D60322AC (role_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FAFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id)');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F6E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE habitat_image ADD CONSTRAINT FK_9AD7E031AFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE habitat_image ADD CONSTRAINT FK_9AD7E0313DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD CONSTRAINT FK_CE729CDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD CONSTRAINT FK_CE729CDE8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FAFFE2D26');
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F6E59D40D');
        $this->addSql('ALTER TABLE habitat_image DROP FOREIGN KEY FK_9AD7E031AFFE2D26');
        $this->addSql('ALTER TABLE habitat_image DROP FOREIGN KEY FK_9AD7E0313DA5256D');
        $this->addSql('ALTER TABLE rapport_veterinaire DROP FOREIGN KEY FK_CE729CDEA76ED395');
        $this->addSql('ALTER TABLE rapport_veterinaire DROP FOREIGN KEY FK_CE729CDE8E962C16');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE habitat');
        $this->addSql('DROP TABLE habitat_image');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE rapport_veterinaire');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE user');
    }
}
