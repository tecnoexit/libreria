<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Console\Command;
use ZipArchive;

class ImportProducts extends Command
{
    protected $signature = 'products:import {file? : Ruta del XLSX}';
    protected $description = 'Importa productos desde la hoja Lista Principal del XLSX de precios';

    public function handle(): int
    {
        $file = $this->argument('file') ?: base_path('LA BRUJULA SRL lista de precios.xlsx');
        if (!is_file($file)) { $this->error("No existe el archivo: $file"); return self::FAILURE; }
        $zip = new ZipArchive(); $zip->open($file);
        $sharedXml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
        $strings = [];
        foreach ($sharedXml->si as $item) {
            $text = isset($item->t) ? (string)$item->t : '';
            if (!$text && isset($item->r)) foreach ($item->r as $run) $text .= (string)$run->t;
            $strings[] = trim($text);
        }
        $sheet = simplexml_load_string($zip->getFromName('xl/worksheets/sheet2.xml'));
        $sheet->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $factor = (float)(Setting::where('key', 'monedas')->value('value') ?: 1);
        $count = 0; $category = null;
        foreach ($sheet->xpath('//x:sheetData/x:row') as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $ref = (string)$cell['r']; $column = preg_replace('/\d+/', '', $ref); $value = (string)$cell->v;
                if ((string)$cell['t'] === 's') $value = $strings[(int)$value] ?? '';
                if ($value !== '') $values[$column] = trim($value);
            }
            if (!empty($values['B']) && empty($values['D']) && empty($values['E']) && !is_numeric($values['B'])) $category = $values['B'];
            if (empty($values['B']) || empty($values['D']) || !isset($values['E']) || !is_numeric($values['E'])) continue;
            $name = $values['B']; $sku = $values['D']; $cost = (float)$values['E'];
            if (mb_strlen($name) < 2 || mb_strlen($sku) < 2 || $cost <= 0) continue;
            Product::updateOrCreate(['sku' => $sku], ['name' => $name, 'category' => $category, 'cost' => $cost, 'price' => $cost * $factor, 'active' => true]);
            $count++;
        }
        $zip->close(); $this->info("Productos importados: $count"); return self::SUCCESS;
    }
}
