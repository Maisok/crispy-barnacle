<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advert;
use App\Models\UserQuery;
use App\Models\BaseAvto;
use App\Models\Part;

class AdvertTableController extends Controller
{
    public function getTableData(Request $request, $advertId)
{
    $advert = Advert::findOrFail($advertId);
    $filterType = $request->query('filterType');
    $filterValue = $request->query('filterValue');

    // Логика для получения данных таблицы
    $parts = $this->findPartsByProductName($advert->product_name);

    $foundPartId = null;
    if ($parts->isNotEmpty()) {
        $foundPartId = $parts->first()->part_id;
    }

    $modificationId = $this->findModificationId($advert);

    $userQueries = UserQuery::where('id_part', $foundPartId)
        ->where('id_car', $modificationId)
        ->get();

    $queryIds = $userQueries->pluck('id_queri')->toArray();

    $relatedQueries = UserQuery::whereIn('id_queri', $queryIds)->get();

    if ($relatedQueries->isEmpty()) {
        $adverts = collect();
    } else {
        $query = $this->getRelatedCars($relatedQueries);

        // Применяем фильтр, если он задан
        if ($filterType && $filterValue) {
            $query->where($filterType, $filterValue);
        }

        $adverts = $query->paginate(10); // 10 элементов на страницу
    }

    return response()->json([
        'adverts' => $adverts->items(),
        'pagination' => [
            'current_page' => $adverts->currentPage(),
            'last_page' => $adverts->lastPage(),
            'next_page_url' => $adverts->nextPageUrl(),
            'prev_page_url' => $adverts->previousPageUrl(),
        ]
    ]);
}

    public function getBrandsAndModels()
    {
        $brands = BaseAvto::distinct()->pluck('brand');
        $models = BaseAvto::distinct()->pluck('model');

        return response()->json([
            'brands' => $brands,
            'models' => $models,
        ]);
    }

    private function findPartsByProductName($productName)
    {
        return Part::where(Part::raw("'{$productName}'"), 'LIKE', Part::raw("CONCAT('%', part_name, '%')"))->get();
    }

    private function findModificationId($advert)
    {
        $query = BaseAvto::where('brand', $advert->brand)
            ->where('model', $advert->model);
    
        if ($advert->year !== null) {
            $query->where('year_from', '<=', $advert->year)
                  ->where('year_before', '>=', $advert->year);
        }
    
        $baseAvto = $query->first();
    
        return $baseAvto ? $baseAvto->id_modification : null;
    }

    private function getRelatedCars($relatedQueries)
    {
        if ($relatedQueries->isEmpty()) {
            return collect();
        }
    
        $carIds = $relatedQueries->pluck('id_car')->unique();
    
        return BaseAvto::whereIn('id_modification', $carIds);
    }
}