<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210611114949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, post_id_id INT NOT NULL, content LONGTEXT NOT NULL, like_amount INT NOT NULL, reported TINYINT(1) DEFAULT NULL, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526CE85F12B8 (post_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE like_storage (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, topic_id_id INT DEFAULT NULL, post_id_id INT DEFAULT NULL, comment_id_id INT DEFAULT NULL, INDEX IDX_7F751179D86650F (user_id_id), INDEX IDX_7F75117C4773235 (topic_id_id), INDEX IDX_7F75117E85F12B8 (post_id_id), INDEX IDX_7F75117D6DE06A6 (comment_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, topic_id_id INT NOT NULL, content LONGTEXT NOT NULL, like_amount INT NOT NULL, reported TINYINT(1) DEFAULT NULL, INDEX IDX_5A8A6C8DF675F31B (author_id), INDEX IDX_5A8A6C8DC4773235 (topic_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_storage (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, topic_id_id INT DEFAULT NULL, post_id_id INT DEFAULT NULL, comment_id_id INT DEFAULT NULL, INDEX IDX_34E366349D86650F (user_id_id), INDEX IDX_34E36634C4773235 (topic_id_id), INDEX IDX_34E36634E85F12B8 (post_id_id), INDEX IDX_34E36634D6DE06A6 (comment_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE like_storage ADD CONSTRAINT FK_7F751179D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE like_storage ADD CONSTRAINT FK_7F75117C4773235 FOREIGN KEY (topic_id_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE like_storage ADD CONSTRAINT FK_7F75117E85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE like_storage ADD CONSTRAINT FK_7F75117D6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC4773235 FOREIGN KEY (topic_id_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE report_storage ADD CONSTRAINT FK_34E366349D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report_storage ADD CONSTRAINT FK_34E36634C4773235 FOREIGN KEY (topic_id_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE report_storage ADD CONSTRAINT FK_34E36634E85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE report_storage ADD CONSTRAINT FK_34E36634D6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE topic ADD title VARCHAR(255) NOT NULL, ADD content LONGTEXT NOT NULL, ADD like_amount INT NOT NULL, ADD reported TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE like_storage DROP FOREIGN KEY FK_7F75117D6DE06A6');
        $this->addSql('ALTER TABLE report_storage DROP FOREIGN KEY FK_34E36634D6DE06A6');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE85F12B8');
        $this->addSql('ALTER TABLE like_storage DROP FOREIGN KEY FK_7F75117E85F12B8');
        $this->addSql('ALTER TABLE report_storage DROP FOREIGN KEY FK_34E36634E85F12B8');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE like_storage');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE report_storage');
        $this->addSql('ALTER TABLE topic DROP title, DROP content, DROP like_amount, DROP reported');
    }
}
