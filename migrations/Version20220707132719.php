<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220707132719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO `user` (`id`, `email`, `roles`, `password`, `pseudo`, `date_register`, `date_connection`, `validated`, `code_validation`, `last_code_validation`, `active`) VALUES (NULL, 'seg_jes@hotmail.com', '[\"ROLE_ADMIN\"]', 'admin', 'Ryknot', '2022-07-07 15:22:11', '2022-07-07 15:22:11', '1', '999999', NULL, '1')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
