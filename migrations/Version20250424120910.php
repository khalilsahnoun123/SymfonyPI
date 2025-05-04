<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424120910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY fk_user_cov');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY reservations_ibfk_3');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY reservations_ibfk_1');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY reservations_ibfk_2');
        $this->addSql('ALTER TABLE reservationtaxi DROP FOREIGN KEY reservationtaxi_ibfk_1');
        $this->addSql('ALTER TABLE reservationtaxi DROP FOREIGN KEY reservationtaxi_ibfk_2');
        $this->addSql('ALTER TABLE reservationvelo DROP FOREIGN KEY reservationvelo_ibfk_1');
        $this->addSql('ALTER TABLE reservationvelo DROP FOREIGN KEY reservationvelo_ibfk_2');
        $this->addSql('ALTER TABLE reservation_cov DROP FOREIGN KEY fk_user_res_cov');
        $this->addSql('ALTER TABLE reservation_cov DROP FOREIGN KEY fk_res_cov');
        $this->addSql('ALTER TABLE stations DROP FOREIGN KEY stations_ibfk_1');
        $this->addSql('ALTER TABLE stationvelo DROP FOREIGN KEY stationvelo_ibfk_1');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY vehicule_ibfk_1');
        $this->addSql('ALTER TABLE velo DROP FOREIGN KEY velo_ibfk_2');
        $this->addSql('ALTER TABLE velo DROP FOREIGN KEY velo_ibfk_1');
        $this->addSql('DROP TABLE covoiturage');
        $this->addSql('DROP TABLE ligne');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE reservationtaxi');
        $this->addSql('DROP TABLE reservationvelo');
        $this->addSql('DROP TABLE reservation_cov');
        $this->addSql('DROP TABLE stations');
        $this->addSql('DROP TABLE stationvelo');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('DROP TABLE vehicules');
        $this->addSql('DROP TABLE velo');
        $this->addSql('DROP TABLE velo_type');
        $this->addSql('ALTER TABLE user ADD google_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE covoiturage (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, point_de_depart VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, point_de_destination VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix DOUBLE PRECISION NOT NULL, nbr_place INT NOT NULL, INDEX fk_user_cov (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ligne (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix_vip DOUBLE PRECISION DEFAULT NULL, prix_premium DOUBLE PRECISION DEFAULT NULL, prix_economique DOUBLE PRECISION DEFAULT NULL, region VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservations (reservation_id INT AUTO_INCREMENT NOT NULL, vehicule_id INT DEFAULT NULL, depart_station_id INT NOT NULL, fin_station_id INT NOT NULL, reservation_date DATE NOT NULL, travel_date DATE NOT NULL, number_of_seats INT NOT NULL, ticket_category VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, total_price DOUBLE PRECISION DEFAULT NULL, INDEX vehicule_id (vehicule_id), INDEX depart_station_id (depart_station_id), INDEX fin_station_id (fin_station_id), PRIMARY KEY(reservation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservationtaxi (id INT AUTO_INCREMENT NOT NULL, id_vehicule INT NOT NULL, id_user INT NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'en attente\' COLLATE `utf8mb4_general_ci`, INDEX id_user (id_user), INDEX id_vehicule (id_vehicule), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservationvelo (id_reservation_velo INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_velo INT NOT NULL, date_debut DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, date_fin DATE NOT NULL, statut VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'booked\' NOT NULL COLLATE `utf8mb4_general_ci`, price NUMERIC(10, 2) DEFAULT NULL, payment_status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'PENDING\' COLLATE `utf8mb4_general_ci`, payment_ref VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, payment_date DATETIME DEFAULT NULL, INDEX id_user (id_user), INDEX id_velo (id_velo), PRIMARY KEY(id_reservation_velo)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservation_cov (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_cov INT NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, nbr_place INT NOT NULL, INDEX fk_user_res_cov (id_user), INDEX fk_res_cov (id_cov), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE stations (id INT AUTO_INCREMENT NOT NULL, ligne_id INT DEFAULT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, INDEX ligne_id (ligne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE stationvelo (id_station INT AUTO_INCREMENT NOT NULL, id_admin INT NOT NULL, name_station VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, gouvernera VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, municapilite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, station_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, INDEX id_admin (id_admin), PRIMARY KEY(id_station)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, marque VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, modele VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, immatriculation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, numserie VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, INDEX id_user (id_user), UNIQUE INDEX immatriculation (immatriculation), UNIQUE INDEX numserie (numserie), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vehicules (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, nbr_max_passagers_vip INT NOT NULL, nbr_max_passagers_premium INT NOT NULL, nbr_max_passagers_economy INT NOT NULL, places_disponibles_vip INT NOT NULL, places_disponibles_premium INT NOT NULL, places_disponibles_economy INT NOT NULL, ligne_id INT NOT NULL, INDEX ligne_id (ligne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE velo (id_velo INT AUTO_INCREMENT NOT NULL, id_station INT NOT NULL, id_type INT NOT NULL, dispo INT NOT NULL, INDEX id_station (id_station), INDEX id_type (id_type), PRIMARY KEY(id_velo)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE velo_type (id_type INT AUTO_INCREMENT NOT NULL, type_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, price NUMERIC(10, 2) NOT NULL, velo_image VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id_type)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT fk_user_cov FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT reservations_ibfk_3 FOREIGN KEY (fin_station_id) REFERENCES stations (id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT reservations_ibfk_1 FOREIGN KEY (vehicule_id) REFERENCES vehicules (id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT reservations_ibfk_2 FOREIGN KEY (depart_station_id) REFERENCES stations (id)');
        $this->addSql('ALTER TABLE reservationtaxi ADD CONSTRAINT reservationtaxi_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservationtaxi ADD CONSTRAINT reservationtaxi_ibfk_2 FOREIGN KEY (id_vehicule) REFERENCES vehicule (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservationvelo ADD CONSTRAINT reservationvelo_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservationvelo ADD CONSTRAINT reservationvelo_ibfk_2 FOREIGN KEY (id_velo) REFERENCES velo (id_velo) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_cov ADD CONSTRAINT fk_user_res_cov FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_cov ADD CONSTRAINT fk_res_cov FOREIGN KEY (id_cov) REFERENCES covoiturage (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stations ADD CONSTRAINT stations_ibfk_1 FOREIGN KEY (ligne_id) REFERENCES ligne (id)');
        $this->addSql('ALTER TABLE stationvelo ADD CONSTRAINT stationvelo_ibfk_1 FOREIGN KEY (id_admin) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT vehicule_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE velo ADD CONSTRAINT velo_ibfk_2 FOREIGN KEY (id_type) REFERENCES velo_type (id_type) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE velo ADD CONSTRAINT velo_ibfk_1 FOREIGN KEY (id_station) REFERENCES stationvelo (id_station) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `user` DROP google_id');
    }
}
