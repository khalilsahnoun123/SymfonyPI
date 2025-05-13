# Stratospheres-
1. Clone the Main Branch
First, switch to the main branch where all feature branches (user, transport-public, reclamation, covoiturage, vélo, taxi) have been merged:


git clone https://github.com/khalilsahnoun123/SymphonyPI.git
cd YourRepo
git checkout main
2. Install PHP Dependencies
Make sure you have Composer installed on your system. From the project root, install all required PHP packages and Symfony bundles (including QR code generation, PDF/Excel export, etc.):

composer install

3. Configure Your Environment
Copy the example environment file and update any database or mailer settings as needed:
for example if u want to config the mailer sender with ur account juste put ur email in the appropriate field in env
cp .env.example .env
# Then edit .env to match your local configuration


4. Initialize the Database
Create your local database and run migrations to set up the schema:

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate


6. Start the Symfony Server
Finally, launch the built-in Symfony web server:


symfony serve
Visit http://127.0.0.1:8000/login in your browser to verify everything is working.
