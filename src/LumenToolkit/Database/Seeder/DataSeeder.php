<?php

namespace LumenToolkit\Database\Seeder;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use LumenToolkit\Helpers\Ip;
use Symfony\Component\Console\Output\ConsoleOutput;
use function array_slice;
use function count;
use function fgetcsv;
use function fopen;
use function str_getcsv;
use function array_combine;
use function feof;
use function fgets;
use function trim;

abstract class DataSeeder extends Seeder
{
    protected string $relative_path = '/../../../database/seeds/csv/';

    /**
     * The name of the folder containing the seed data .CSV files
     *
     * Overload this property when extending the class
     *
     * @var string
     */
    protected string $data_folder;

    /**
     * An array of table names that we're going to seed, in the order we're going to seed them
     *
     * Overload this property when extending the class
     *
     * @var array
     */
    protected array $seed_tables;

    /**
     * @var ConsoleOutput
     */
    protected ConsoleOutput $output;

    /**
     * Run the database seeds.
     *
     * CLI command: php artisan db:seed --class=TestDataSeeder
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $this->output = new ConsoleOutput();

        $this->verifyTestDataIsBeingSeeded();

        /**
         * Seed models first, as order is important due to foreign key relations;
         * meta tables should already be seeded at this point through the InitialSeeder
         */
        foreach ($this->seed_tables as $table) {
            $this->seed($table);
        }
    }

    /**
     * @throws Exception
     */
    private function verifyTestDataIsBeingSeeded()
    {
        $test_data_tables = $this->getExistingTestDataTables();

        foreach ($test_data_tables as $test_data_table) {
            if (!in_array($test_data_table, $this->seed_tables)) {
                throw new Exception("test data .csv exists for table '" . $test_data_table
                                    . "' but is not listed in the 'seed_tables' array (found in the " . get_called_class() . " class)");
            }
        }

        foreach ($this->seed_tables as $seed_data_table) {
            if (!in_array($seed_data_table, $test_data_tables)) {
                throw new Exception("test data .csv does not exist for table '" . $seed_data_table
                                    . "' but is listed in the 'seed_tables' array (found in the " . get_called_class() . " class)");
            }
        }
    }

    /**
     * @return array
     */
    private function getExistingTestDataTables(): array
    {
        $files = scandir(realpath(__DIR__ . env('SEED_DIRECTORY') . $this->data_folder));

        $test_data_tables = [];
        foreach ($files as $filename) {
            // strip out extensions
            $filename_without_extension = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
            if ($filename_without_extension == "." || $filename_without_extension == "..") {
                continue;
            }
            $test_data_tables[] = $filename_without_extension;
        }

        return $test_data_tables;
    }

    /**
     * @param string $table_name
     *
     * @throws Exception
     */
    private function seed(string $table_name)
    {
        // determine buffer spacing for output
        $buffer = $this->getMaxTableNameLength() + 5;

        $data             = $this->getTestData($table_name);
        $count            = count($data);
        $database_default = config('database.default');
        $database         = config("database.connections.$database_default.database");
        $spaces           = str_repeat("-", $buffer - strlen($table_name) - strlen($count));
        $this->output->writeln("<fg=green>Seeding table:</> $database.$table_name <fg=blue>$spaces($count rows)</>");

        // disable foreign key checks for truncating
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table($table_name)->truncate();

        // re-enable foreign key checks for inserts
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table($table_name)->insert($data);
    }

    /**
     * @return int
     */
    private function getMaxTableNameLength(): int
    {
        $max_length = 0;
        foreach ($this->seed_tables as $table_name) {
            $table_name_length = strlen($table_name);
            if ($table_name_length > $max_length) {
                $max_length = $table_name_length;
            }
        }

        return $max_length;
    }

    /**
     * @param string $table
     *
     * @return array
     * @throws Exception
     */
    private function getTestData(string $table): array
    {
        $relative_path = __DIR__ . $this->relative_path . $this->data_folder . '/' . $table . '.csv';
        $csv_path      = realpath($relative_path);
        if (empty($csv_path)) {
            throw new Exception ("Relative path $relative_path does not exist");
        }
        return $this->getCsvData($csv_path);
    }

    /**
     * @param string $csv_path
     *
     * @return array
     */
    private function getCsvData(string $csv_path): array
    {
        $fileResource = fopen($csv_path, 'r');
        $headers      = fgetcsv($fileResource);
        $data         = [];

        while (!feof($fileResource)) {
            $line = trim(fgets($fileResource));

            // ignore commented lines
            if (empty($line) || $line[0] == '#') {
                continue;
            }

            try {
                // ignore rows after headers; anything after is a comment by design
                $row    = array_combine($headers, array_slice(str_getcsv($line), 0, count($headers)));
                $data[] = $this->parseRow($row);
            } catch (Exception $e) {
                $this->command->error("Skipped line in $csv_path, too few columns");
            }
        }
        return $data;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function parseRow(array $row): array
    {
        $row_data = [];
        foreach ($row as $column_header => $column_value) {
            $column_header = trim($column_header);
            $column_value  = trim($column_value);

            // created_at fields are raw SQL derived from a string with `CURDATE()` or 'NOW()'
            if ($column_header == 'created_at'
                || strpos($column_value, 'NOW()') !== false
                || strpos($column_value, 'CURDATE()') !== false
            ) {
                $row_data[$column_header] = DB::raw($column_value);
                continue;
            }

            // null values are derived from a string with 'NULL'
            if ($column_value == 'NULL') {
                $row_data[$column_header] = null;
                continue;
            }

            // false as 'FALSE'
            if (strtoupper($column_value) == 'FALSE') {
                $row_data[$column_header] = 0;
                continue;
            }

            // true as 'TRUE'
            if (strtoupper($column_value) == 'TRUE') {
                $row_data[$column_header] = 1;
                continue;
            }

            // IP address values are converted into VARBINARY(16)
            if (Ip::isIpv4($column_value)
                || Ip::isIpv6($column_value)
            ) {
                $row_data[$column_header] = inet_pton($column_value);
                continue;
            }

            // date string values in the 'Y-m-d G:i:s' format return a DateTime object
            $date_time = DateTime::createFromFormat('Y-m-d G:i:s', $column_value);
            if ($date_time !== false) {
                $row_data[$column_header] = $date_time;
                continue;
            }

            // date string values in the 'Y-m-d G:i:s' format return a DateTime object
            $date = DateTime::createFromFormat('Y-m-d', $column_value);
            if ($date !== false) {
                $row_data[$column_header] = $date;
                continue;
            }

            // data string is a referenced json file
            $json_path = __DIR__ . $this->relative_path . '../json/email/' . $column_value;
            if(in_array(pathinfo($json_path, PATHINFO_EXTENSION), ['json'])
                && file_exists($json_path)
            ) {
                $row_data[$column_header] = file_get_contents($json_path);
                continue;
            }

            // otherwise, just return the raw value
            $row_data[$column_header] = $column_value;
        }
        return $row_data;
    }
}
