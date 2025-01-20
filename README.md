# Assessment in Symfony with a Pokemon TCG App

## How to test

### Requirements

1. Docker / Docker Compose
2. Port 8000 free
3. Internet connection

### Installation

1. Clone this project from Github repository
2. Build the container and start it: `docker-compose up -d --build`
3. Enter in container bash: `docker exec -ti assessment-symfony-pokemontcg-app bash`
4. Install the packages: `composer install`
5. Run the migrations: `php bin/console doctrine:migration:migrate`
6. Run manually the command for the first import of cards database: `php bin/console app:import-cards-api`
    - This command has 2 option inputs:
        1. 'skipSetsUpdate': It skips Sets update process, jumping directly to Card update one: `php bin/console app:import-cards-api --skipSetsUpdate`
        2. 'set:<set_id>': Passing the id of Set, it only update cards for that specific set.: `php bin/console app:import-cards-api --set=swsh4`
7. Open http://localhost:8000

### Final Considerations

1. The application was designed to store API data locally periodically, thus avoiding exceeding the API's request limit.
2. Pages display data that is stored in the database
3. A command was created to import data from the API. It can be executed manually or aged via scheduler
4. Unfortunately, it was not possible to implement all unit and integration tests as stipulated.

### Contact

1. Email: thalesbitencourt@gmail.com
2. Github: https://github.com/tbitencourt
