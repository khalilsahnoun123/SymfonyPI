<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250504185900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE ligne (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, prix_vip NUMERIC(10, 0) DEFAULT NULL, prix_premium NUMERIC(10, 0) DEFAULT NULL, prix_economique NUMERIC(10, 0) DEFAULT NULL, region VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, utilisateur_id INT NOT NULL, description LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, status VARCHAR(100) NOT NULL, reponse LONGTEXT DEFAULT NULL, attachment VARCHAR(255) DEFAULT NULL, note INT DEFAULT NULL, commentaire_satisfaction LONGTEXT DEFAULT NULL, INDEX IDX_CE606404C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservations (reservation_id INT AUTO_INCREMENT NOT NULL, vehicule_id INT DEFAULT NULL, depart_station_id INT DEFAULT NULL, fin_station_id INT DEFAULT NULL, travel_date DATE NOT NULL, number_of_seats INT NOT NULL, ticket_category VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, total_price NUMERIC(10, 0) DEFAULT NULL, paid TINYINT(1) NOT NULL, flouci_payment_id VARCHAR(255) DEFAULT NULL, payment_link VARCHAR(255) DEFAULT NULL, INDEX IDX_4DA2394A4A3511 (vehicule_id), INDEX IDX_4DA2394901E5FB (depart_station_id), INDEX IDX_4DA239C4D02159 (fin_station_id), PRIMARY KEY(reservation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservationtaxi (id INT AUTO_INCREMENT NOT NULL, id_vehicule INT DEFAULT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_47ED57E779F41388 (id_vehicule), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE stations (id INT AUTO_INCREMENT NOT NULL, ligne_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, INDEX IDX_A7F775E95A438E76 (ligne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE typereclamation (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, gouvernorat VARCHAR(255) DEFAULT NULL, municipalite VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, bloque VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(255) NOT NULL, modele VARCHAR(255) NOT NULL, immatriculation VARCHAR(255) NOT NULL, numserie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE vehicules (id INT AUTO_INCREMENT NOT NULL, ligne_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, nbr_max_passagers_vip INT NOT NULL, nbr_max_passagers_premium INT NOT NULL, nbr_max_passagers_economy INT NOT NULL, places_disponibles_vip INT NOT NULL, places_disponibles_premium INT NOT NULL, places_disponibles_economy INT NOT NULL, INDEX IDX_78218C2D5A438E76 (ligne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404C54C8C93 FOREIGN KEY (type_id) REFERENCES typereclamation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations ADD CONSTRAINT FK_4DA2394A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicules (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations ADD CONSTRAINT FK_4DA2394901E5FB FOREIGN KEY (depart_station_id) REFERENCES stations (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations ADD CONSTRAINT FK_4DA239C4D02159 FOREIGN KEY (fin_station_id) REFERENCES stations (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservationtaxi ADD CONSTRAINT FK_47ED57E779F41388 FOREIGN KEY (id_vehicule) REFERENCES vehicule (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stations ADD CONSTRAINT FK_A7F775E95A438E76 FOREIGN KEY (ligne_id) REFERENCES ligne (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicules ADD CONSTRAINT FK_78218C2D5A438E76 FOREIGN KEY (ligne_id) REFERENCES ligne (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404C54C8C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2394A4A3511
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2394901E5FB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239C4D02159
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservationtaxi DROP FOREIGN KEY FK_47ED57E779F41388
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stations DROP FOREIGN KEY FK_A7F775E95A438E76
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicules DROP FOREIGN KEY FK_78218C2D5A438E76
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ligne
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservations
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservationtaxi
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE stations
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE typereclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE vehicule
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE vehicules
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
