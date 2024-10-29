<?php

namespace App\Console\Commands;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * From docker bash run php artisan make:preloaded-data
 */
class MakePreloadedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:preloaded-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will print an associative array of preloaded data';

    private string $fName = 'app/Services/Application/Form/PreloadedData/PreloadedData.php';

    public function handle()
    {
        $content = "<?php\nnamespace App\Services\Application\Form\PreloadedData;\n";
        $content .= "use App\Services\Application\Form\Fields\McqOption;\n";
        $content .= "class PreloadedData\n{\n\t";
        $content .= "public static function getData()\n\t{\n\t\treturn [\n";
        $content .= $this->getAddressContent().$this->getBanksBranchesContent().$this->tab(2)."];\n\t}\n}";
        if (file_exists($this->fName)) {
            unlink($this->fName);
        }
        File::put($this->fName, $content);
        $this->info('success');
    }

    private function tab($count): string
    {
        $i = 0;
        $tab = '';
        while ($i < $count) {
            $tab .= "\t";
            $i++;
        }

        return $tab;
    }

    /**
     * @return string
     *
     * @throws InvalidFormatException
     */
    private function getAddressContent(): string
    {
        $dir = 'app/Console/Commands/PreloadedData';
        $divisions = $this->readFromJson($dir.'/divisions.json');
        $districts = $this->readFromJson($dir.'/districts.json');
        $thanas = $this->readFromJson($dir.'/thanas.json');

        if (empty($divisions) || empty($districts) || empty($thanas)) {
            throw new InvalidFormatException('unable to parse json');
        }

        $outputDiv = $this->tab(3)."'divisions' => [\n";
        $outputDis = $this->tab(3)."'districts' => [\n";
        $outputThana = $this->tab(3)."'thanas' => [\n";
        foreach ($divisions['divisions'] as $division) {
            $outputDiv .= $this->tab(4).$this->buildFirstGenMcqOption($division, null);
            $divValue = $this->nameForValue($division['name']);
            foreach ($districts['districts'] as $district) {
                if ($division['id'] == $district['division_id']) {
                    $outputDis .= $this->tab(4).$this->buildSecondGenMcqOption($district, $division, $divValue);
                    $disValue = $divValue.'_'.$this->nameForValue($district['name']);
                    foreach ($thanas['thanas'] as $thana) {
                        if ($district['id'] == $thana['district_id']) {
                            $outputThana .= $this->tab(4).$this->buildSecondGenMcqOption($thana, $district, $disValue);
                        }
                    }
                }
            }
        }

        return $outputDiv.$this->tab(3)."],\n".$outputDis.$this->tab(3)."],\n"
            .$outputThana.$this->tab(3)."],\n";
    }

    /**
     * @return string
     *
     * @throws InvalidFormatException
     */
    private function getBanksBranchesContent(): string
    {
        $dir = 'app/Console/Commands/PreloadedData';
        $banks = $this->readFromJson($dir.'/banks.json');

        if (empty($banks)) {
            throw new InvalidFormatException('unable to parse json');
        }
        $outputBanks = $this->tab(3)."'banks' => [\n";
        $outputDis = $this->tab(3)."'banks_districts' => [\n";
        $outputBranch = $this->tab(3)."'banks_branches' => [\n";
        foreach ($banks['banks'] as $bank) {
            $outputBanks .= $this->tab(4).$this->buildFirstGenMcqOption($bank, null);
            $bankValue = $this->nameForValue($bank['name']);
            foreach ($bank['districts'] as $district) {
                $outputDis .= $this->tab(4).$this->buildSecondGenMcqOption($district, $bank, $bankValue);
                $disValue = $bankValue.'_'.$this->nameForValue($district['name']);
                foreach ($district['branches'] as $branch) {
                    $outputBranch .= $this->tab(4).$this->buildSecondGenMcqOption($branch, $district, $disValue);
                }
            }
        }

        return $outputBanks.$this->tab(3)."],\n".$outputDis.$this->tab(3)."],\n"
            .$outputBranch.$this->tab(3)."],\n";
    }

    private function readFromJson($fName): bool|string|array|null
    {
        $json = file_get_contents($fName);
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            exit('Error decoding JSON file: '.json_last_error_msg());
        }

        return $data;
    }

    private function nameForValue($s): array|string
    {
        $s = strtolower($s);
        $s = str_replace(' ', '', $s);

        return str_replace("'", '', $s);
    }

    private function nameForLabel($s): array|string
    {
        return str_replace("'", "\'", $s);
    }

    /**
     * @param  mixed  $from
     * @param  mixed  $parent
     * @param  string  $parentValue
     * @return string
     */
    private function buildSecondGenMcqOption(mixed $from, mixed $parent, string $parentValue): string
    {
        return "[...(new McqOption('"
            .$this->nameForLabel($from['name_bn'])."', '"
            .$parentValue.'_'.$this->nameForValue($from['name'])."'))->toArrayForApi(),\n"
            .$this->tab(5)."'parent'".' => '.$this->buildFirstGenMcqOption($parent, $parentValue)
            .$this->tab(4)."],\n";
    }

    /**
     * @param  mixed  $from
     * @param  string|null  $value
     * @return string
     */
    private function buildFirstGenMcqOption(mixed $from, ?string $value): string
    {
        $contentValue = $value ?? $this->nameForValue($from['name']);

        return "[...(new McqOption('"
            .$this->nameForLabel($from['name_bn'])."', '"
            .$contentValue."'))->toArrayForApi()],\n";
    }
}
