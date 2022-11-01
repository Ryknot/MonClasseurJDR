<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Création de la BDD
 */
final class Version20221101140110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        //Création de la BDD
        $this->addSql('CREATE TABLE carte_mj (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(50) NOT NULL, type VARCHAR(50) NOT NULL, filtre VARCHAR(50) DEFAULT NULL, pv INT NOT NULL, note VARCHAR(255) DEFAULT NULL, image VARCHAR(255) NOT NULL, on_board TINYINT(1) DEFAULT NULL, qty_on_board INT DEFAULT NULL, INDEX IDX_4A1326B5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE champs (id INT AUTO_INCREMENT NOT NULL, type_info_id INT NOT NULL, fiche_perso_id INT NOT NULL, label VARCHAR(50) NOT NULL, valeur_texte VARCHAR(50) DEFAULT NULL, type_champ VARCHAR(255) NOT NULL, valeur_area VARCHAR(255) DEFAULT NULL, sort INT NOT NULL, INDEX IDX_B34671BE75AAAE0D (type_info_id), INDEX IDX_B34671BE743FDA88 (fiche_perso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fiche_perso (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, pseudo VARCHAR(100) NOT NULL, image VARCHAR(255) DEFAULT NULL, nb_champs1 INT NOT NULL, nb_champs2 INT NOT NULL, nb_champs3 INT NOT NULL, nb_champs4 INT NOT NULL, nb_champs5 INT NOT NULL, nb_champs6 INT NOT NULL, nb_champs7 INT NOT NULL, nb_ressource INT NOT NULL, INDEX IDX_C7E2101EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, message VARCHAR(255) NOT NULL, date DATETIME NOT NULL, type VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(30) NOT NULL, date DATETIME NOT NULL, content VARCHAR(255) NOT NULL, user VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, fiche_perso_id INT NOT NULL, label VARCHAR(50) NOT NULL, range_max INT NOT NULL, sort INT NOT NULL, valeur_glissante INT NOT NULL, INDEX IDX_939F4544743FDA88 (fiche_perso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_info (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, date_register DATETIME NOT NULL, date_connection DATETIME NOT NULL, validated TINYINT(1) NOT NULL, code_validation VARCHAR(6) NOT NULL, last_code_validation VARCHAR(6) DEFAULT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carte_mj ADD CONSTRAINT FK_4A1326B5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE champs ADD CONSTRAINT FK_B34671BE75AAAE0D FOREIGN KEY (type_info_id) REFERENCES type_info (id)');
        $this->addSql('ALTER TABLE champs ADD CONSTRAINT FK_B34671BE743FDA88 FOREIGN KEY (fiche_perso_id) REFERENCES fiche_perso (id)');
        $this->addSql('ALTER TABLE fiche_perso ADD CONSTRAINT FK_C7E2101EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F4544743FDA88 FOREIGN KEY (fiche_perso_id) REFERENCES fiche_perso (id)');

        //Insertion des "type-info" dans la BDD
        $this->addSql("INSERT INTO `type_info` (`id`, `label`) VALUES (NULL, 'identité')");
        $this->addSql("INSERT INTO `type_info` (`id`, `label`) VALUES (NULL, 'statistiques')");
        $this->addSql("INSERT INTO `type_info` (`id`, `label`) VALUES (NULL, 'compétences')");
        $this->addSql("INSERT INTO `type_info` (`id`, `label`) VALUES (NULL, 'équipements')");
        $this->addSql("INSERT INTO `type_info` (`id`, `label`) VALUES (NULL, 'inventaires')");
        $this->addSql("INSERT INTO `type_info` (`id`, `label`) VALUES (NULL, 'notes')");
        $this->addSql("INSERT INTO `type_info` (`id`, `label`) VALUES (NULL, 'autres')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE champs DROP FOREIGN KEY FK_B34671BE743FDA88');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F4544743FDA88');
        $this->addSql('ALTER TABLE champs DROP FOREIGN KEY FK_B34671BE75AAAE0D');
        $this->addSql('ALTER TABLE carte_mj DROP FOREIGN KEY FK_4A1326B5A76ED395');
        $this->addSql('ALTER TABLE fiche_perso DROP FOREIGN KEY FK_C7E2101EA76ED395');
        $this->addSql('DROP TABLE carte_mj');
        $this->addSql('DROP TABLE champs');
        $this->addSql('DROP TABLE fiche_perso');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('DROP TABLE type_info');
        $this->addSql('DROP TABLE user');
    }
}
