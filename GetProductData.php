<?php

namespace app;

use PHPExcel;
use PHPExcel_Writer_Excel5;
use PHPExcel_Style_Alignment;
use phpQuery;

class GetProductData
{
    /**
     * @var array
     */
    public $page;

    /**
     * Метод возвращает массив с данными о товаре (название, код, цена, ссылка на обложку)
     * @param array $page
     * @return array
     */
    public function parseProduct(array $page)
    {
        $this->page = $page;

        foreach ($this->page as $item) {
            $phpQuery = phpQuery::newDocument($item->getBody()->getContents());
            $catalog = $phpQuery->find('.img_big_img');
            $re = '~<td>Артикул: <span.*?">(?<articul>.*?)</span>.*?<td>Наименование: <span.*?">(?<name>.*?)</span>.*?<td>Цена: <span.*?">(?<price>.*?)</span>~s';
            preg_match_all($re, $phpQuery, $matches, PREG_SET_ORDER, 0);
            $imageExtension = pathinfo(pq($catalog)->attr('src'), PATHINFO_EXTENSION );
            for($i = 0; $i < count($matches); $i++){
                $productDataArr[] = [
                    'imageSrc' => pq($catalog)->attr('src'),
                    'imageName' => $matches[$i]['name'],
                    'productPrice' => $matches[$i]['price'],
                    'productArticul' => 'БЧ' . $matches[$i]['articul'],
                    'imageExtension' => $imageExtension,
                ];
            }
        }
        return $productDataArr;
    }

    /**
     * Метод сохраняет обложку в указанную папку и формирует excel каталог
     * @param $imageDir
     * @param array $imageInfo
     */
    public function saveProduct($imageDir, array $imageInfo)
    {

        if (!file_exists('cover/' . $imageDir)) {
            mkdir('cover/' . $imageDir, 0777, true);
        }
        foreach ($imageInfo as $item) {
            copy($item['imageSrc'], 'cover/' . $imageDir . '/' . $item['productArticul'] . '-' . str_replace('/', '-', $item['imageName']) . '.' . $item['imageExtension']);
            sleep(rand(2, 4));
        }
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
        $activeSheet = $objPHPExcel->getActiveSheet();
        $activeSheet->getStyle('A')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $activeSheet->getStyle('B')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $activeSheet->getStyle('C')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $activeSheet->getStyle('D')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        for($i = 0; $i < count($imageInfo); ++$i){
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $imageInfo[$i]['productArticul']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $imageInfo[$i]['imageName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $imageInfo[$i]['productPrice']);
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('cover/' . $imageDir . '/' .$imageDir . '.xls');
    }

}