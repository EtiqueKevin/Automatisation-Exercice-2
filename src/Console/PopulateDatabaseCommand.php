<?php

namespace App\Console;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Illuminate\Support\Facades\Schema;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Factory;

class PopulateDatabaseCommand extends Command
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database');
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int
    {
        $faker = Factory::create();
        $output->writeln('Populate database...');

        /**
 * @var \Illuminate\Database\Capsule\Manager $db 
*/
        $db = $this->app->getContainer()->get('db');

        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $db->getConnection()->statement("TRUNCATE `employees`");
        $db->getConnection()->statement("TRUNCATE `offices`");
        $db->getConnection()->statement("TRUNCATE `companies`");


        for ($i = 1; $i < 5; $i++) {
            $company = addslashes($faker->company);
            $phoneNumber = addslashes($faker->phoneNumber());
            $companyEmail = addslashes($faker->companyEmail);
            $url = addslashes($faker->url);
            $db->getConnection()->statement(
                "INSERT INTO `companies` VALUES 
            ($i, '$company', '$phoneNumber', '$companyEmail', '$url', 'https://picsum.photos/800/400' , now(), now(), null)"
            );

            for ($i = 1; $i < 4; $i++) {
                $city = addslashes($faker->city);
                $address = addslashes($faker->address);
                $postcode = addslashes($faker->postcode);
                $country = addslashes($faker->country);
                $email = addslashes($faker->email);
                $number = $faker->numberBetween(1, 2);
                $db->getConnection()->statement(
                    "INSERT INTO `offices` VALUES 
                ($i, 'bureau de $city', '$address', '$city', '$postcode', '$country', '$email', NULL, $number , now(), now())"
                );

                for ($i = 1; $i < 11; $i++) {
                    $firstName = addslashes($faker->firstName);
                    $lastName = addslashes($faker->lastName);
                    $email = addslashes($faker->email);
                    $jobTitle = addslashes($faker->jobTitle);
                    $number = $faker->numberBetween(1, 4);
                    $db->getConnection()->statement(
                        "INSERT INTO `employees` VALUES 
                    ($i, '$firstName', '$lastName', $number , '$email', NULL, '$jobTitle', now(), now())"
                    );
                }
            }
        }

        

       




        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");
        $db->getConnection()->statement("update companies set head_office_id = 1 where id = 1;");
        $db->getConnection()->statement("update companies set head_office_id = 3 where id = 2;");

        $output->writeln('Database created successfully!');
        return 0;
    }
}
