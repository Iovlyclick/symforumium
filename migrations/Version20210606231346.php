<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210606231346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_topic (user_id INT NOT NULL, topic_id INT NOT NULL, INDEX IDX_7F822543A76ED395 (user_id), INDEX IDX_7F8225431F55203D (topic_id), PRIMARY KEY(user_id, topic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_post (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_200B2044A76ED395 (user_id), INDEX IDX_200B20444B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_comment (user_id INT NOT NULL, comment_id INT NOT NULL, INDEX IDX_CC794C66A76ED395 (user_id), INDEX IDX_CC794C66F8697D13 (comment_id), PRIMARY KEY(user_id, comment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_topic ADD CONSTRAINT FK_7F822543A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_topic ADD CONSTRAINT FK_7F8225431F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post ADD CONSTRAINT FK_200B2044A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post ADD CONSTRAINT FK_200B20444B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_comment ADD CONSTRAINT FK_CC794C66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_comment ADD CONSTRAINT FK_CC794C66F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD author_id INT NOT NULL, ADD reported_by_id INT DEFAULT NULL, DROP user_id, DROP report_amount');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C71CE806 FOREIGN KEY (reported_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C71CE806 ON comment (reported_by_id)');
        $this->addSql('ALTER TABLE post ADD author_id INT NOT NULL, ADD reported_by_id INT DEFAULT NULL, DROP user_id, DROP report_amount');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D71CE806 FOREIGN KEY (reported_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D71CE806 ON post (reported_by_id)');
        $this->addSql('ALTER TABLE topic ADD author_id INT NOT NULL, ADD reported_by_id INT DEFAULT NULL, DROP user_id, DROP report_amount');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B71CE806 FOREIGN KEY (reported_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9D40DE1BF675F31B ON topic (author_id)');
        $this->addSql('CREATE INDEX IDX_9D40DE1B71CE806 ON topic (reported_by_id)');
        $this->addSql('ALTER TABLE user DROP liked_topic, DROP liked_post, DROP liked_comment');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_topic');
        $this->addSql('DROP TABLE user_post');
        $this->addSql('DROP TABLE user_comment');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C71CE806');
        $this->addSql('DROP INDEX IDX_9474526CF675F31B ON comment');
        $this->addSql('DROP INDEX IDX_9474526C71CE806 ON comment');
        $this->addSql('ALTER TABLE comment ADD report_amount INT NOT NULL, DROP reported_by_id, CHANGE author_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D71CE806');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF675F31B ON post');
        $this->addSql('DROP INDEX IDX_5A8A6C8D71CE806 ON post');
        $this->addSql('ALTER TABLE post ADD report_amount INT NOT NULL, DROP reported_by_id, CHANGE author_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BF675F31B');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B71CE806');
        $this->addSql('DROP INDEX IDX_9D40DE1BF675F31B ON topic');
        $this->addSql('DROP INDEX IDX_9D40DE1B71CE806 ON topic');
        $this->addSql('ALTER TABLE topic ADD report_amount INT NOT NULL, DROP reported_by_id, CHANGE author_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD liked_topic VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD liked_post VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD liked_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
