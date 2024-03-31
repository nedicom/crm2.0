<?php

namespace App\Services\GenerateDocuments\CertCompletion;

use App\Models\ClientsModel;
use App\Models\Tasks;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Element\TextRun;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/** Класс по генерации документа 'Акта выполненных работ' клиента */
class CertCompletionService
{
    private string|null $fileTemplate = null;
    private TemplateProcessor $adapter;
    private Table $table;

    public function __construct()
    {
        $tableStyle = [
            'borderSize' => 3,
            'width' => Converter::cmToTwip(16.51),
            'borderColor' => '000000',
            'cellMargin' => 0,
            'cellSpacing' => 0,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP,
        ];
        $this->table = new Table($tableStyle);
    }

    /**
     * @param string $templateFile
     * @param ClientsModel $client
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     * @throws FileNotFoundException
     */
    public function __invoke(string $templateFile, ClientsModel $client)
    {
        $this->setFileTemplate($templateFile);
        $this->makeAdapter();

        // Generate Date
        $this->generateText('dateGenerate', Carbon::now()->format('d.m.Y'), ['name' => 'Times New Roman', 'bold' => true, 'size' => 10]);
        $this->dateContacts($client);
        // Generate Table
        $this->adapter->setComplexBlock('table', $this->makeTable($client));
        // Generate client info
        $this->generateText('fullname',(!empty($client->name)) ? $client->name : '', ['name' => 'Times New Roman', 'bold' => true, 'size' => 9]);
        $this->generateText('fullname_sec',(!empty($client->name)) ? $client->name : '', ['name' => 'Times New Roman', 'bold' => true, 'size' => 9]);
        $this->generateText('address',(!empty($client->address)) ? $client->address : '', ['name' => 'Times New Roman', 'bold' => false, 'size' => 9]);
        $this->generateText('phone',(!empty($client->phone)) ? $client->phone : '', ['name' => 'Times New Roman', 'bold' => false, 'size' => 9]);

        return $this->adapter->save('php://output');
    }

    /**
     * Генерация текста
     * @param string $variable
     * @param string $value
     * @param array $fontStyles
     * @return void
     */
    public function generateText(string $variable, string $value, array $fontStyles)
    {
        $inline = new TextRun();
        $inline->addText($value, $fontStyles);
        $this->adapter->setComplexValue($variable, $inline);
    }

    /**
     * Генерация даты Договора
     * @param ClientsModel $client
     * @return void
     */
    private function dateContacts(ClientsModel $client): void
    {
        $dateContract = (!empty($client->contracts()))
            ? $client->contracts()->select('date')->orderByDesc('id')->limit(1)->value('date')
            : '';
        $inline = new TextRun();
        $inline->addText(Carbon::parse($dateContract)->format('d.m.Y'), ['name' => 'Times New Roman', 'bold' => true, 'size' => 11]);
        $this->adapter->setComplexValue('dateContract', $inline);
    }

    /**
     *  Создание таблицы
     *  @param  ClientsModel $client
     *  @return Table
     */
    private function makeTable(ClientsModel $client): Table
    {
        // Header table
        $headerColumns = require_once __DIR__ . '/params/header_columns.php';
        $this->generateRow(0.70);
        foreach ($headerColumns as $column) {
            $this->generateHeaderColumns($column['width'], $column['text']);
        }

        // Body table
        $resultPrice = 0;
        $i = 1;
        $tasks = $client->tasksFunc()->orderByDesc('id')->get();
        /** @var Tasks $task */
        foreach ($tasks as $task) {
            $this->generateRow(0.70);
            $taskPrice = ($task->service) ? $task->service->price : 0;
            $columnValues = [
                $i,
                ($task->service) ? $task->service->name : $task->name,
                1,
                ($task->service) ? \App\Helpers\TaskHelper::transformDuration($task->duration, $task->type_duration)['hours'] : 0,
                $task->type,
                $taskPrice,
                $taskPrice,
            ];
            $resultPrice += $taskPrice;

            for ($j = 0; $j < count($headerColumns); $j++) {
                $this->generateBodyColumns($headerColumns[$j]['width'], $columnValues[$j]);
            }
            $i++;
        }

        // Footer table
        $textF = "В том числе:";
        $textS = "НДС (18%)";
        $footerColumns = require __DIR__ . '/params/footer_columns.php';
        $this->generateRow(0.70);
        foreach ($footerColumns as $column) {
            $this->generateFooterColumns($column['column'], $column['text']);
        }
        $totalPrice = $resultPrice;
        $textF = "Итого:";
        $textS = "(с учетом НДС)";
        $footerColumns = require __DIR__ . '/params/footer_columns.php';
        $this->generateRow(0.70);
        foreach ($footerColumns as $column) {
            $this->generateFooterColumns($column['column'], $column['text']);
        }

        return $this->table;
    }

    /**
     * Генерация строки таблицы
     * @param float $width ширина в см.
     * @return void
     */
    private function generateRow(float $width)
    {
        $this->table->addRow(Converter::cmToTwip($width));
    }

    /**
     * Генерация колонок таблицы в Header
     * @param float $width ширина в см.
     * @param string|null $text
     * @return void
     */
    private function generateHeaderColumns(float $width, string $text = null): void
    {
        $style = require __DIR__ . '/params/header_styles_columns.php';
        $this->table->addCell(Converter::cmToTwip($width), $style['columnStyle'])->addText($text, $style['fontStyle'], $style['textStyle']);
    }

    /**
     * Генерация колонок таблицы в Body
     * @param float $width ширина в см.
     * @param string|null $text
     * @return void
     */
    private function generateBodyColumns(float $width, string $text = null)
    {
        $style = require __DIR__ . '/params/body_styles_columns.php';
        $this->table->addCell(Converter::cmToTwip($width), $style['columnStyle'])->addText($text, $style['fontStyle'], $style['textStyle']);
    }

    /**
     * Генерация колонок таблицы в Footer
     * @param array $column
     * @param string|array|null $text
     * @return void
     */
    private function generateFooterColumns(array $column, string|array $text = null)
    {
        $style = require __DIR__ . '/params/footer_styles_columns.php';

        if ($column['style'] !== null) {
            $cell = $this->table->addCell(Converter::cmToTwip($column['width']), $column['style']);
        } else {
            $cell = $this->table->addCell(Converter::cmToTwip($column['width']), $style['columnStyle']);
        }

        if (is_string($text)) {
            $cell->addText($text, $style['fontStyle'], $style['textStyle']);
        } else if (is_array($text)) {
            foreach ($text as $txt) {
                if ($txt !== null) {
                    $cell->addText(
                        $txt['value'],
                        (!empty($txt['fontStyle'])) ? $txt['fontStyle'] : $style['fontStyle'],
                        (!empty($txt['prStyle'])) ? $txt['prStyle'] : $style['textStyle']
                    );
                }
            }
        }
    }

    public function setFileTemplate(string $file): void
    {
        $this->fileTemplate = $file;
    }

    public function getFileTemplate(): string
    {
        return $this->fileTemplate;
    }

    /** Подключение нужного адаптера */
    private function makeAdapter(): void
    {
        $fileTemplate = $this->getFileTemplate();
        if (File::exists($fileTemplate)) {
            $this->adapter = new TemplateProcessor($fileTemplate);
        } else {
            throw new FileNotFoundException('Указанный файл не найден.');
        }
    }
}
