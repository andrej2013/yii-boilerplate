<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\widget\export;

use Google\Spreadsheet\Worksheet;
use andrej2013\yiiboilerplate\models\GoogleSpreadsheet;
use yii\base\Exception;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use yii\helpers\Html;
use kartik\grid\DataColumn;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;

class ExportMenu extends \kartik\export\ExportMenu
{
    const FORMAT_GOOGLE = 'GoogleSpreadsheet';

    /**
     * @var Worksheet
     */
    protected $googleWorksheet;
    /**
     * @var \Google_Service_Sheets_Spreadsheet
     */
    protected $googleSpreadsheet;

    /**
     * @var \Google_Service_Sheets
     */
    protected $service;

    protected $spreadSheetName;

    /**
     *
     */
    public function initExport()
    {
        parent::initExport();
        if ($this->isGoogleEnabled()) {
            $this->exportConfig = array_merge($this->exportConfig, [
                self::FORMAT_GOOGLE => [
                    'label' => \Yii::t('kvexport', 'Google Spreadsheet'),
                    'icon' => 'google',
                    'iconOptions' => ['class' => 'text-success'],
                    'linkOptions' => [],
                    'options' => ['title' => \Yii::t('kvexport', 'Google Spreadsheet')],
                    'alertMsg' => \Yii::t('kvexport', 'The Google Spreadsheet file will be generated.'),
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'extension' => 'xlsx',
                    'writer' => 'Excel2007',
                ],
            ]);
            $this->styleOptions = array_merge($this->styleOptions, [
                self::FORMAT_GOOGLE => [
                    'font' => [
                        'bold' => true,
                        'color' => [
                            'argb' => 'FFFFFFFF',
                        ],
                    ],
                    'fill' => [
                        'color' => [
                            'argb' => '00000000',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     *
     */
    public function initPHPExcel()
    {
        if ($this->isGoogle()) {
            $this->registerGoogleSpreadSheet();
        }
        parent::initPHPExcel();
    }

    /**
     *
     */
    public function setHttpHeaders()
    {
        if ($this->_exportType === self::FORMAT_GOOGLE) {
            echo Html::a('Link', $this->googleSpreadsheet->getSpreadsheetUrl(), ['target' => '_blank']);
            exit();
        } else {
            parent::setHttpHeaders();
        }
    }

    /**
     *
     */
    protected function registerGoogleSpreadSheet()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . \Yii::getAlias(\Yii::$app->params['GoogleCredentials']));
        $client = new \Google_Client();

        $client->useApplicationDefaultCredentials();
        $client->setScopes([
            'https://www.googleapis.com/auth/spreadsheets',
            'https://www.googleapis.com/auth/drive'
        ]);

        if ($client->isAccessTokenExpired()) {
            $client->refreshTokenWithAssertion();
        }

        $accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
        $serviceRequest = new DefaultServiceRequest($accessToken);
        // On windows, we need to disable ssl verification for the script to "properly" work.
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $serviceRequest->setSslVerifyPeer(false);
        }
        ServiceRequestFactory::setInstance($serviceRequest);


        $service = new \Google_Service_Sheets($client);
        $this->service = $service;
        $drive = new \Google_Service_Drive($client);

        $s = new \Google_Service_Sheets_Spreadsheet();

        $properities = new \Google_Service_Sheets_SpreadsheetProperties();
        $properities->setTitle($this->generateTitle());

        $s->setProperties($properities);

        $ss = $service->spreadsheets->create($s);

        $newPermission = new \Google_Service_Drive_Permission();
        $newPermission->setType('anyone');
        $newPermission->setRole('writer');
        $optParams = array('sendNotificationEmail' => false);

        $drive->permissions->create($ss->spreadsheetId, $newPermission, $optParams);


        $spreadsheetService = new \Google\Spreadsheet\SpreadsheetService();
        $spreadsheetFeed = $spreadsheetService->getSpreadsheetFeed();

        $spreadsheet = $spreadsheetFeed->getByTitle($s->getProperties()->getTitle());

        $worksheetFeed = $spreadsheet->getWorksheetFeed();

        $this->googleWorksheet = $worksheetFeed->getByTitle('Sheet1');
        $this->googleSpreadsheet = $ss;
        $model = new GoogleSpreadsheet();
        $model->user_id = \Yii::$app->user->id;
        $model->title = $s->getProperties()->getTitle();
        $model->url = $ss->getSpreadsheetUrl();
        $model->type = GoogleSpreadsheet::TYPE_EXPORT;
        $model->save();
    }

    public function generateHeader()
    {
        if ($this->isGoogle()) {
            $columns = $this->getVisibleColumns();
            if (count($columns) == 0) {
                return;
            }
//            $colFirst = self::columnName(1);
            $colFirst = 1;
            $this->_endCol = 0;
            $cell = $this->googleWorksheet->getCellFeed();
            foreach ($this->getVisibleColumns() as $column) {
                $this->_endCol++;
                /**
                 * @var DataColumn $column
                 */
                $head = ($column instanceof DataColumn) ? $this->getColumnHeader($column) : $column->header;
                $cell->editCell($this->_beginRow, $this->_endCol, $head);
                $this->applyStyleToGoogleCell(
                    $this->_beginRow,
                    $this->_endCol,
                    ArrayHelper::getValue($this->styleOptions, self::FORMAT_GOOGLE)
                );
            }
            for ($i = $this->_headerBeginRow; $i < ($this->_beginRow); $i++) {
//                $sheet->mergeCells($colFirst . $i . ":" . self::columnName($this->_endCol) . $i);
                $this->mergeGoogleCells($colFirst, $i, $this->_endCol, $i);
            }
        } else {
            parent::generateHeader();
        }
    }

    public function generateBody()
    {
        if ($this->isGoogle()) {
            $this->_endRow = 0;
            $columns = $this->getVisibleColumns();
            $models = array_values($this->_provider->getModels());
            $cellFeed = $this->googleWorksheet->getCellFeed();
            if (count($columns) == 0) {
                $cellFeed->editCell(1, 1, $this->emptyText);
                $cell = $cellFeed->getCell(1, 1);
                $model = reset($models);
                $this->raiseEvent('onRenderDataCell', [$cell, $this->emptyText, $model, null, 0, $this]);
                return 0;
            }
            // do not execute multiple COUNT(*) queries
            $totalCount = $this->_provider->getTotalCount();
            $this->findGroupedColumn();
            while (count($models) > 0) {
                $keys = $this->_provider->getKeys();
                foreach ($models as $index => $model) {
                    $key = $keys[$index];
                    $this->generateRow($model, $key, $this->_endRow);
                    $this->_endRow++;
                    if ($index === $totalCount) {
                        //a little hack to generate last grouped footer
                        $this->checkGroupedRow($model, $models[0], $key, $this->_endRow);
                    } elseif (isset($models[$index + 1])) {
                        $this->checkGroupedRow($model, $models[$index + 1], $key, $this->_endRow);
                    }
                    if (!is_null($this->_groupedRow)) {
                        $this->_endRow++;
                        $this->spreadsheetFromArray($this->_groupedRow, null, 'A' . ($this->_endRow + 1), true);
//                        $cell = "A" . ($this->_endRow + 1) . ":" . self::columnName(count($columns)) . ($this->_endRow + 1);
//                        $this->_objPHPExcelSheet->getStyle($cell)->applyFromArray($this->groupedRowStyle);
                        $this->_groupedRow = null;
                    }
                }
                if ($this->_provider->pagination) {
                    $this->_provider->pagination->page++;
                    $this->_provider->refresh();
                    $this->_provider->setTotalCount($totalCount);
                    $models = $this->_provider->getModels();
                } else {
                    $models = [];
                }
            }

            // Set autofilter on
            $this->setGoogleAutoFilter(1, $this->_beginRow, $this->_endCol, $this->_endRow);
            return $this->_endRow;
        } else {
            return parent::generateBody();
        }
    }

    public function generateRow($model, $key, $index)
    {

        if ($this->isGoogle()) {
            /**
             * @var Column $column
             */
            $this->_endCol = 0;
            $cellFeed = $this->googleWorksheet->getCellFeed();
            foreach ($this->getVisibleColumns() as $column) {
                if ($column instanceof SerialColumn) {
                    $value = $column->renderDataCell($model, $key, $index);
                } elseif ($column instanceof ActionColumn) {
                    $value = '';
                } else {
                    $format = $this->enableFormatter && isset($column->format) ? $column->format : 'raw';
                    $value = ($column->content === null) ? (method_exists($column, 'getDataCellValue') ?
                        $this->formatter->format($column->getDataCellValue($model, $key, $index), $format) :
                        $column->renderDataCell($model, $key, $index)) :
                        call_user_func($column->content, $model, $key, $index, $column);
                }
                if (empty($value) && !empty($column->attribute) && $column->attribute !== null) {
                    $value = ArrayHelper::getValue($model, $column->attribute, '');
                }
                $this->_endCol++;
                $cellFeed->editCell(
                    $index + $this->_beginRow + 1,
                    $this->_endCol,
                    empty($value) && !strlen($value) ? '' : strip_tags($value)
                );
                $cell = $cellFeed->getCell($index + $this->_beginRow + 1, $this->_endCol);
                $this->raiseEvent('onRenderDataCell', [$cell, $value, $model, $key, $index, $this]);
            }
        } else {
            parent::generateRow($model, $key, $index);
        }
    }

    /**
     * Generate a grouped row
     *
     * @param array $groupFooter footer row
     * @param integer $groupedCol the zero-based index of grouped column
     */
    protected function generateGroupedRow($groupFooter, $groupedCol)
    {
        $endGroupedCol = 0;
        $this->_groupedRow = [];
        $fLine = ArrayHelper::getValue($this->_groupedColumn[$groupedCol], 'firstLine', -1);
        $fLine = ($fLine == $this->_beginRow) ? $this->_beginRow + 1 : ($fLine + 3);
        $firstLine = ($this->_endRow == ($this->_beginRow + 3) && $fLine == 2) ? $this->_beginRow + 3 : $fLine;
        $endLine = $this->_endRow + 1;
        list($endLine, $firstLine) = ($endLine > $firstLine) ? [$endLine, $firstLine] : [$firstLine, $endLine];
        foreach ($this->getVisibleColumns() as $key => $column) {
            $value = isset($groupFooter[$key]) ? $groupFooter[$key] : '';
            $endGroupedCol++;
            $groupedRange = self::columnName($key + 1) . $firstLine . ":" . self::columnName($key + 1) . $endLine;
            //$lastCell = self::columnName($key + 1) . $endLine - 1;
            if (isset($column->group) && $column->group) {
                $this->mergeGoogleCells(self::columnName($key + 1), $firstLine, self::columnName($key + 1), $endLine);
//                $this->_objPHPExcelSheet->mergeCells($groupedRange);
            }
            switch ($value) {
                case self::F_SUM:
                    $value = "=sum($groupedRange)";
                    break;
                case self::F_COUNT:
                    $value = '=countif(' . $groupedRange . ',"*")';
                    break;
                case self::F_AVG:
                    $value = "=AVERAGE($groupedRange)";
                    break;
                case self::F_MAX:
                    $value = "=max($groupedRange)";
                    break;
                case self::F_MIN:
                    $value = "=min($groupedRange)";
                    break;
            }
            if ($value instanceof \Closure) {
                $value = call_user_func($value, $groupedRange, $this);
            }
            $this->_groupedRow[] = empty($value) ? '' : strip_tags($value);
        }
    }

    protected function isGoogle()
    {
        return $this->_exportType === self::FORMAT_GOOGLE;
    }

    protected function spreadsheetFromArray(
        $source = null,
        $nullValue = null,
        $startCell = 'A1',
        $strictNullComparison = false
    )
    {
        if (is_array($source)) {
            //    Convert a 1-D array to 2-D (for ease of looping)
            if (!is_array(end($source))) {
                $source = array($source);
            }

            // start coordinate
            list ($startColumn, $startRow) = \PHPExcel_Cell::coordinateFromString($startCell);

            // Loop through $source
            $cellFeed = $this->googleWorksheet->getCellFeed();
            foreach ($source as $rowData) {
                $currentColumn = $startColumn;
                foreach ($rowData as $cellValue) {
                    if ($strictNullComparison) {
                        if ($cellValue !== $nullValue) {
                            // Set cell value
                            $cellFeed->editCell($startRow, $currentColumn, $cellValue);
                        }
                    } else {
                        if ($cellValue != $nullValue) {
                            // Set cell value
                            $cellFeed->editCell($startRow, $currentColumn, $cellValue);
                        }
                    }
                    ++$currentColumn;
                }
                ++$startRow;
            }
        } else {
            throw new Exception("Parameter \$source should be an array.");
        }
        return $this;
    }

    /**
     *
     */
    protected function sendRequest($request)
    {
        $busr = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest();
        $busr->setRequests([$request]);
        return $this->service->spreadsheets->batchUpdate($this->googleSpreadsheet->spreadsheetId, $busr, []);
    }

    /**
     *
     */
    protected function applyStyleToGoogleCell($row, $column, $style)
    {
//        $style = ArrayHelper::getValue($this->styleOptions, self::FORMAT_GOOGLE);
        $color = $style['fill']['color']['argb'];
        $color = $this->hexToRgb($color);
        $font = $style['font'];
        $fontColor = $this->hexToRgb($font['color']['argb']);
        $request = new \Google_Service_Sheets_Request(array(
            'updateCells' => array(
                'start' => array(
                    'sheetId' => 0,
                    'rowIndex' => $row - 1,
                    'columnIndex' => $column - 1,
                ),
                'rows' => array(
                    array(
                        'values' => array(
                            array(
                                'userEnteredFormat' => [
                                    'textFormat' => [
                                        'bold' => isset($font['bold']),
                                        'italic' => isset($font['italic']),
                                        'foregroundColor' => [
                                            'red' => $fontColor['r'],
                                            'green' => $fontColor['g'],
                                            'blue' => $fontColor['b'],
                                        ],
                                    ],
                                    'backgroundColor' => [
                                        'red' => $color['r'],
                                        'green' => $color['g'],
                                        'blue' => $color['b'],
                                    ],
                                ],
                            ),
                        )
                    )
                ),
//                'fields' => '*',
                'fields' => 'userEnteredFormat.backgroundColor, userEnteredFormat.textFormat',
            )
        ));
        $this->sendRequest($request);
    }

    protected function setGoogleAutoFilter($startColumnIndex, $startRowIndex, $endColumnIndex, $endRowIndex)
    {
        $request = new \Google_Service_Sheets_Request([
            'setBasicFilter' => [
                'filter' => [
                    'range' => [
                        'endColumnIndex' => $endColumnIndex,
                        'endRowIndex' => $endRowIndex + 1,
                        'startColumnIndex' => $startColumnIndex - 1,
                        'startRowIndex' => $startRowIndex - 1,
                        'sheetId' => 0,
                    ],
                ],
            ],
        ]);
        $this->sendRequest($request);
    }

    protected function mergeGoogleCells($startColumnIndex, $startRowIndex, $endColumnIndex, $endRowIndex)
    {
        $request = new \Google_Service_Sheets_MergeCellsRequest([
            'range' => [
                'endColumnIndex' => $endColumnIndex,
                'endRowIndex' => $endRowIndex + 1,
                'startColumnIndex' => $startColumnIndex - 1,
                'startRowIndex' => $startRowIndex - 1,
                'sheetId' => 0,
            ],
        ]);
        $this->sendRequest($request);
    }

    /**
     *
     */
    private function hexToRgb($hex, $alpha = false)
    {
        $hex = str_replace("#", "", $hex);
        $length = strlen($hex);
        if ($length == 8) {
            $hex = substr($hex, 2, 6);
        }

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array('r' => floatval($r / 255), 'g' => floatval($g / 255), 'b' => floatval($b / 255));
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    /**
     *
     */
    protected function generateTitle()
    {
        $reflection = new \ReflectionClass($this->_provider->getModels()[0]);
        return $reflection->getShortName() . ' - ' . date('Y-m-d h:i:s', time());
    }

    /**
     *
     */
    public function isGoogleEnabled()
    {
        return file_exists(\Yii::getAlias(\Yii::$app->params['GoogleCredentials']));
    }

    /**
     * Generates the before content at the top of the exported sheet
     *
     * @return void
     */
    public function generateBeforeContent()
    {
        if ($this->isGoogle()) {
            $cellFeed = $this->googleWorksheet->getCellFeed();
            foreach ($this->contentBefore as $contentBefore) {
                $cellFeed->editCell($this->_beginRow, 1, $contentBefore['value']);
                $this->_beginRow += 1;
            }
        } else {
            parent::generateBeforeContent();
        }
    }

    /**
     * Generates the output footer row after a specific row number
     *
     * @param int $row the row number after which the footer is to be generated
     */
    public function generateFooter()
    {
        if ($this->isGoogle()) {
            $row = $this->_endRow + $this->_beginRow;
            $footerExists = false;
            $columns = $this->getVisibleColumns();
            if (count($columns) == 0) {
                return;
            }
            $this->_endCol = 0;
            $cellFeed = $this->googleWorksheet->getCellFeed();
            foreach ($this->getVisibleColumns() as $n => $column) {
                $this->_endCol = $this->_endCol + 1;
                if ($column->footer) {
                    $footerExists = true;
                    $footer = trim($column->footer) !== '' ? $column->footer : $column->grid->blankDisplay;
                    $cellFeed->editCell($row + 1, $this->_endCol, $footer);
                }
            }
            if ($footerExists) $row++;
            return $row;
        } else {
            return parent::generateFooter();
        }
    }

    /**
     * Generates the after content at the bottom of the exported sheet
     *
     * @return void
     */
    public function generateAfterContent($row)
    {
        if ($this->isGoogle()) {
            $row++;
            $afterContentBeginRow = $row;
            $cellFeed = $this->googleWorksheet->getCellFeed();
            foreach ($this->contentAfter as $contentAfter) {
                $cellFeed->editCell($row, 1, $contentAfter['value']);
                $row += 1;
            }
            for ($i = $afterContentBeginRow; $i < $row; $i++) {
                $this->mergeGoogleCells(1, $i, $this->_endCol, $i);
            }
        } else {
            parent::generateAfterContent($row);
        }
    }

}