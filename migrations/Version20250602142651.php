<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602142651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, news_id INT DEFAULT NULL, content LONGTEXT NOT NULL, insert_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_9474526CB5A459A0 (news_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, content LONGTEXT NOT NULL, insert_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE news_category (news_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_4F72BA90B5A459A0 (news_id), INDEX IDX_4F72BA9012469DE2 (category_id), PRIMARY KEY(news_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE news_view (id INT AUTO_INCREMENT NOT NULL, news_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_2B205D05B5A459A0 (news_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526CB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news_category ADD CONSTRAINT FK_4F72BA90B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news_category ADD CONSTRAINT FK_4F72BA9012469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news_view ADD CONSTRAINT FK_2B205D05B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB5A459A0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news_category DROP FOREIGN KEY FK_4F72BA90B5A459A0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news_category DROP FOREIGN KEY FK_4F72BA9012469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news_view DROP FOREIGN KEY FK_2B205D05B5A459A0
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE news
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE news_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE news_view
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
