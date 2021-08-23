<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\ForgeModel;
use Illuminate\Support\Facades\DB;


class TekkinExport implements FromCollection,WithHeadings,WithMultipleSheets,WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $item_id;
    private $sheetCount;
    private $exportData;

    public function __construct($item_id,$sheetCount,$exportData)
    {
        $this->item_id = $item_id;
        $this->sheetCount = $sheetCount;
        $this->exportData = $exportData;
    }

    public function collection()
    {
        
        return collect($this->exportData);//need to change array to collect type when using type FromCollection
    }
    
    public function headings(): array
    {
        if($this->sheetCount == 0)
           return ['element_id','B','H','カット長','level',
           '始端 上主筋 太径','始端 上主筋 1段筋太筋本数','始端 上主筋 2段筋太筋本数',
           '始端 下主筋 太径','始端 下主筋 1段筋太筋本数','始端 下主筋 2段筋太筋本数',
           '始端 肋筋径','始端 肋筋本数','始端 肋筋ピッチ',
           '中央 上主筋 太径','中央 上主筋 1段筋太筋本数','中央 上主筋 2段筋太筋本数',
           '中央 下主筋 太径','中央 下主筋 1段筋太筋本数','中央 下主筋 2段筋太筋本数',
           '中央 肋筋径','中央 肋筋本数','中央 肋筋ピッチ',
           '終端 上主筋 太径','終端 上主筋 1段筋太筋本数','終端 上主筋 2段筋太筋本数',
           '終端 下主筋 太径','終端 下主筋 1段筋太筋本数','終端 下主筋 2段筋太筋本数',
           '終端 肋筋径','終端 肋筋本数','終端 肋筋ピッチ'];
        else if($this->sheetCount == 1)
            return ['element_id','W','D','volume','level',
            '柱頭 主筋太径','柱頭 主筋X方向1段太筋本数','柱頭 主筋X方向2段太筋本数','柱頭 主筋Y方向1段太筋本数','柱頭 主筋Y方向2段太筋本数','柱頭 帯筋径','柱頭 帯筋ピッチ',
            '柱脚 主筋太径','柱脚 主筋X方向1段太筋本数','柱脚 主筋X方向2段太筋本数','柱脚 主筋Y方向1段太筋本数','柱脚 主筋Y方向2段太筋本数','柱脚 帯筋径','柱頭 帯筋ピッチ'];
        else
            return['element_id','D','H','W','level',
            '上端筋_X方向_鉄筋径','上端筋_X方向_鉄筋本数','上端筋_Y方向_鉄筋径','上端筋_Y方向_鉄筋本数',
            '下端筋_X方向_鉄筋径','下端筋_X方向_鉄筋本数','下端筋_Y方向_鉄筋径','下端筋_Y方向_鉄筋本数'];
        
    }
    
    /**
     * @return array
     */
    public function sheets(): array
    {
        $forge = new ForgeModel();
        $tekkin_list = $forge->GetTekkinExcelData($this->item_id); 
    
        $sheets = [];
        for ($count = 0; $count <= 2; $count++) {
            $data;
            if($count == 0)
               $data = $tekkin_list['beam_tekkin_data'];
            else if($count == 1)
               $data = $tekkin_list['column_tekkin_data'];
            else
                $data = $tekkin_list['foundation_tekkin_data'];
                
            $sheets[$count] = new TekkinExport($this->item_id,$count,$data);
        }

        return $sheets;
    }
    
     /**
     * @return string
     */
    public function title(): string
    {
        if($this->sheetCount == 0)
            return '構造梁';
        else if($this->sheetCount == 1)
            return "構造柱";
        else
            return"構造基礎";
    }
}
