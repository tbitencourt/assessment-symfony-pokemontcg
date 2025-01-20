<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120185718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "card_images" (id SERIAL NOT NULL, card_id VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9220ED1E4ACC9A20 ON "card_images" (card_id)');
        $this->addSql('CREATE TABLE "cards" (id VARCHAR(255) NOT NULL, set_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, supertype VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4C258FD10FB0D18 ON "cards" (set_id)');
        $this->addSql('CREATE TABLE pokemon_attack (id SERIAL NOT NULL, card_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, converted_energy_cost INT NOT NULL, damage VARCHAR(255) NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2B29516F4ACC9A20 ON pokemon_attack (card_id)');
        $this->addSql('CREATE TABLE pokemon_attack_cost (id SERIAL NOT NULL, pokemon_attack_id INT NOT NULL, type_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6C3CECBAC4D8BD7F ON pokemon_attack_cost (pokemon_attack_id)');
        $this->addSql('CREATE INDEX IDX_6C3CECBAC54C8C93 ON pokemon_attack_cost (type_id)');
        $this->addSql('CREATE TABLE pokemon_resistance (id SERIAL NOT NULL, type_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DA30E8F5C54C8C93 ON pokemon_resistance (type_id)');
        $this->addSql('CREATE TABLE card_pokemon_resistance (pokemon_resistance_id INT NOT NULL, card_id VARCHAR(255) NOT NULL, PRIMARY KEY(pokemon_resistance_id, card_id))');
        $this->addSql('CREATE INDEX IDX_5B8DDBBD65303D99 ON card_pokemon_resistance (pokemon_resistance_id)');
        $this->addSql('CREATE INDEX IDX_5B8DDBBD4ACC9A20 ON card_pokemon_resistance (card_id)');
        $this->addSql('CREATE TABLE pokemon_weakness (id SERIAL NOT NULL, type_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E8ECF4E7C54C8C93 ON pokemon_weakness (type_id)');
        $this->addSql('CREATE TABLE card_pokemon_weakness (pokemon_weakness_id INT NOT NULL, card_id VARCHAR(255) NOT NULL, PRIMARY KEY(pokemon_weakness_id, card_id))');
        $this->addSql('CREATE INDEX IDX_7B8B2C6513808917 ON card_pokemon_weakness (pokemon_weakness_id)');
        $this->addSql('CREATE INDEX IDX_7B8B2C654ACC9A20 ON card_pokemon_weakness (card_id)');
        $this->addSql('CREATE TABLE "sets" (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, series VARCHAR(255) NOT NULL, ptcgo_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "types" (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE card_type (type_id INT NOT NULL, card_id VARCHAR(255) NOT NULL, PRIMARY KEY(type_id, card_id))');
        $this->addSql('CREATE INDEX IDX_60ED558BC54C8C93 ON card_type (type_id)');
        $this->addSql('CREATE INDEX IDX_60ED558B4ACC9A20 ON card_type (card_id)');
        $this->addSql('CREATE TABLE "users" (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE "card_images" ADD CONSTRAINT FK_9220ED1E4ACC9A20 FOREIGN KEY (card_id) REFERENCES "cards" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "cards" ADD CONSTRAINT FK_4C258FD10FB0D18 FOREIGN KEY (set_id) REFERENCES "sets" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_attack ADD CONSTRAINT FK_2B29516F4ACC9A20 FOREIGN KEY (card_id) REFERENCES "cards" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_attack_cost ADD CONSTRAINT FK_6C3CECBAC4D8BD7F FOREIGN KEY (pokemon_attack_id) REFERENCES pokemon_attack (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_attack_cost ADD CONSTRAINT FK_6C3CECBAC54C8C93 FOREIGN KEY (type_id) REFERENCES "types" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_resistance ADD CONSTRAINT FK_DA30E8F5C54C8C93 FOREIGN KEY (type_id) REFERENCES "types" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE card_pokemon_resistance ADD CONSTRAINT FK_5B8DDBBD65303D99 FOREIGN KEY (pokemon_resistance_id) REFERENCES pokemon_resistance (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE card_pokemon_resistance ADD CONSTRAINT FK_5B8DDBBD4ACC9A20 FOREIGN KEY (card_id) REFERENCES "cards" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_weakness ADD CONSTRAINT FK_E8ECF4E7C54C8C93 FOREIGN KEY (type_id) REFERENCES "types" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE card_pokemon_weakness ADD CONSTRAINT FK_7B8B2C6513808917 FOREIGN KEY (pokemon_weakness_id) REFERENCES pokemon_weakness (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE card_pokemon_weakness ADD CONSTRAINT FK_7B8B2C654ACC9A20 FOREIGN KEY (card_id) REFERENCES "cards" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE card_type ADD CONSTRAINT FK_60ED558BC54C8C93 FOREIGN KEY (type_id) REFERENCES "types" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE card_type ADD CONSTRAINT FK_60ED558B4ACC9A20 FOREIGN KEY (card_id) REFERENCES "cards" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "card_images" DROP CONSTRAINT FK_9220ED1E4ACC9A20');
        $this->addSql('ALTER TABLE "cards" DROP CONSTRAINT FK_4C258FD10FB0D18');
        $this->addSql('ALTER TABLE pokemon_attack DROP CONSTRAINT FK_2B29516F4ACC9A20');
        $this->addSql('ALTER TABLE pokemon_attack_cost DROP CONSTRAINT FK_6C3CECBAC4D8BD7F');
        $this->addSql('ALTER TABLE pokemon_attack_cost DROP CONSTRAINT FK_6C3CECBAC54C8C93');
        $this->addSql('ALTER TABLE pokemon_resistance DROP CONSTRAINT FK_DA30E8F5C54C8C93');
        $this->addSql('ALTER TABLE card_pokemon_resistance DROP CONSTRAINT FK_5B8DDBBD65303D99');
        $this->addSql('ALTER TABLE card_pokemon_resistance DROP CONSTRAINT FK_5B8DDBBD4ACC9A20');
        $this->addSql('ALTER TABLE pokemon_weakness DROP CONSTRAINT FK_E8ECF4E7C54C8C93');
        $this->addSql('ALTER TABLE card_pokemon_weakness DROP CONSTRAINT FK_7B8B2C6513808917');
        $this->addSql('ALTER TABLE card_pokemon_weakness DROP CONSTRAINT FK_7B8B2C654ACC9A20');
        $this->addSql('ALTER TABLE card_type DROP CONSTRAINT FK_60ED558BC54C8C93');
        $this->addSql('ALTER TABLE card_type DROP CONSTRAINT FK_60ED558B4ACC9A20');
        $this->addSql('DROP TABLE "card_images"');
        $this->addSql('DROP TABLE "cards"');
        $this->addSql('DROP TABLE pokemon_attack');
        $this->addSql('DROP TABLE pokemon_attack_cost');
        $this->addSql('DROP TABLE pokemon_resistance');
        $this->addSql('DROP TABLE card_pokemon_resistance');
        $this->addSql('DROP TABLE pokemon_weakness');
        $this->addSql('DROP TABLE card_pokemon_weakness');
        $this->addSql('DROP TABLE "sets"');
        $this->addSql('DROP TABLE "types"');
        $this->addSql('DROP TABLE card_type');
        $this->addSql('DROP TABLE "users"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
