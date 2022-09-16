<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708042754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
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

    }
}
