<?php

use Illuminate\Database\Seeder as IlluminateSeeder;
use Symfony\Component\Console\Output\ConsoleOutput;
use LumenToolkit\Database\Seeder;

class DatabaseSeeder extends IlluminateSeeder
{
    /** Overload this property by extending the class with what you want to call */
    protected array $seeder_class_names = [
        // overload (e.g., DataSeederExtended::class)
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            foreach ($this->seeder_class_names as $seeder_class_name) {
                $this->call($seeder_class_name);
            }
        } catch (PDOException $e) {
            $output = new ConsoleOutput();
            if (stripos($e->getMessage(), 'duplicate')) {
                $output->writeln(" - Seed data may already exist! \n<fg=red>{$e->getMessage()}</>");
            } else {
                $output->writeln("<fg=red>{$e->getMessage()}</>");
            }
        }
    }
}
