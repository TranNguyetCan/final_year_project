<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241007061541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, ingredient_id INT DEFAULT NULL, material_id INT DEFAULT NULL, material_name VARCHAR(255) NOT NULL, quantity VARCHAR(255) NOT NULL, unit VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, inventory VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_6BAF7870933FE08C (ingredient_id), INDEX IDX_6BAF7870E308AC6F (material_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF7870933FE08C FOREIGN KEY (ingredient_id) REFERENCES material (id)');
        // $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF7870E308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        // $this->addSql('ALTER TABLE `order` ADD vouchers_id INT NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        // $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993983A546BF7 FOREIGN KEY (vouchers_id) REFERENCES voucher (id)');
        // $this->addSql('CREATE INDEX IDX_F52993983A546BF7 ON `order` (vouchers_id)');
        // $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F46CFFE9AD6');
        // $this->addSql('DROP INDEX IDX_ED896F46CFFE9AD6 ON order_detail');
        // $this->addSql('ALTER TABLE order_detail CHANGE orders_id order_id INT NOT NULL');
        // $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F468D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        // $this->addSql('CREATE INDEX IDX_ED896F468D9F6D38 ON order_detail (order_id)');
        // $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE avatar avatar VARCHAR(255) NOT NULL');
        // $this->addSql('ALTER TABLE voucher ADD percentage INT NOT NULL');
        // $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A314584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        // $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF7870933FE08C');
        // $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF7870E308AC6F');
        // $this->addSql('DROP TABLE ingredient');
        // $this->addSql('DROP TABLE material');
        // $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993983A546BF7');
        // $this->addSql('DROP INDEX IDX_F52993983A546BF7 ON `order`');
        // $this->addSql('ALTER TABLE `order` DROP vouchers_id, CHANGE status status TINYINT(1) NOT NULL');
        // $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F468D9F6D38');
        // $this->addSql('DROP INDEX IDX_ED896F468D9F6D38 ON order_detail');
        // $this->addSql('ALTER TABLE order_detail CHANGE order_id orders_id INT NOT NULL');
        // $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F46CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
        // $this->addSql('CREATE INDEX IDX_ED896F46CFFE9AD6 ON order_detail (orders_id)');
        // $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE avatar avatar VARCHAR(255) DEFAULT NULL');
        // $this->addSql('ALTER TABLE voucher DROP percentage');
        // $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A314584665A');
        // $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31A76ED395');
    }
}
