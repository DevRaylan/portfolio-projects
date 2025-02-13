<?php

/**
 * @class DataExport
 *
 * Esta classe fornece o serviço de exportar dados em arquivos do tipo: xls.
 *
 * @version 1.0
 * @author Mayara Madeira Trevisol <mayara.trevisol@udesc.br>
 */

namespace Geral\Model\Services;

require DIR_SYS . '/../libs/phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DataExport
{
    public static function xlsExport($fileName, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set first row bold
        $sheet->getStyle('1:1')->getFont()->setBold(true);

        // Set header values
        $titles = array_keys($data[0]);
        $row = 1;
        $col = 0;
        foreach ($titles  as $header) {
            $sheet->setCellValueByColumnAndRow($col, $row, ucwords($header));
            $col++;
        }

        // Build rows
        $row = 2;
        foreach ($data as $row_data) {
            $col = 0;
            foreach ($row_data  as $key => $value) {
                // Check if col is an array with inner key 'nome';
                if (is_array($value)) {
                    if (array_key_exists('nome', $value)) {
                        $value = $value['nome'];
                    } else {
                        $value = 'Object';
                    }
                }
                // Put values on cells
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);
    }
}
