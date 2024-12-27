<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\UserQuery;
use Illuminate\Http\Request;
use App\Models\BaseAvto;
use App\Models\Part;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
use App\Models\Favorite;
use Illuminate\Support\Facades\Log;

class AdvertsController extends Controller
{
    // Показать все объявления со статусом "activ"
    public function index(Request $request)
    {
        // Получаем объявления со статусом "activ"
        $query = Advert::where('status_ad', 'activ');
        //->where('status_pay', '!=', 'not_pay');

        // Фильтрация по городу, если параметр передан
        if ($request->has('city') && $request->input('city') !== '') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('city', $request->input('city'));
            });
        }
    
        // Пагинация объявлений
        $adverts = $query->paginate(20);
    
        // Получаем список городов для выпадающего списка
        $cities = User::distinct()->pluck('city')->toArray(); // Получаем уникальные города из модели User
    
        return view('adverts.index', compact('adverts', 'cities'));
    }

    // Показать форму для создания нового объявления
    public function create()
    {
        return view('adverts.create');
    }

    public function store(Request $request)
    {
        // Валидация данных
        $validatedData = $request->validate([
            'art_number' => 'required',
            'product_name' => 'required',
            'brand' => 'required',
            'price' => 'required|numeric|min:0',
        ]);
    
        // Создание объявления
        $advert = new Advert();
        $advert->user_id = auth()->id(); // Предполагается, что пользователь авторизован
        $advert->art_number = $validatedData['art_number'];
        $advert->product_name = $validatedData['product_name'];
        $advert->brand = $validatedData['brand'];
        $advert->price = $validatedData['price'];
  
        // Присвоение необязательных полей, если они присутствуют в запросе
        $optionalFields = [
            'number','model', 'new_used', 'year', 'body', 'engine', 'L_R', 'F_R', 'U_D', 
            'color', 'applicability', 'quantity', 'availability', 'main_photo_url', 
            'additional_photo_url_1', 'additional_photo_url_2', 'additional_photo_url_3'
        ];

        foreach ($optionalFields as $field) {
            if ($request->has($field)) {
                $advert->$field = $request->input($field);
            }
        }
        $advert->save();
    
        return redirect()->route('adverts.index')->with('success', 'Объявление успешно создано.');
    }

    // страница объявления
    public function show($id)
    {
        $advert = Advert::findOrFail($id);
    
        $currentArray = json_decode(request()->cookie('viewed', '[]'), true);
        $currentArray[$id] = 1;
        Cookie::queue('viewed', json_encode($currentArray), 9999);
    
        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = Favorite::where('user_id', auth()->id())
                ->where('advert_id', $id)
                ->exists();
        }
    
        $userAddress = $advert->user->userAddress->address_line;
        $product_name = $advert->product_name;
        $main_photo_url = $advert->main_photo_url;
        $address_line = $userAddress;
    
        return view('adverts.show', compact(
            'advert', 'userAddress', 'product_name', 'main_photo_url', 'address_line', 'isFavorite'
        ));
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
    // Проверяем, что $relatedQueries не пустая
    if ($relatedQueries->isEmpty()) {
        return collect(); // Возвращаем пустую коллекцию, если нет связанных запросов
    }

    // Получаем уникальные id_car из связанных запросов
    $carIds = $relatedQueries->pluck('id_car')->unique();

    // Возвращаем запрос Eloquent для получения данных из BaseAvto
    return BaseAvto::whereIn('id_modification', $carIds);
}
    
    // Обновить данные объявления в базе данных
    public function update(Request $request)
    {
        $advert = Advert::find($request->id);
    
        // Обновление текстовых полей
        if ($request->art_number !== $request->old_art_number) {
            $advert->art_number = $request->art_number;
        }
        if ($request->product_name !== $request->old_product_name) {
            $advert->product_name = $request->product_name;
        }
        if ($request->number !== $request->old_number) {
            $advert->number = $request->number;
        }
        if ($request->new_used !== $request->old_new_used) {
            $advert->new_used = $request->new_used;
        }
        if ($request->brand !== $request->old_brand) {
            $advert->brand = $request->brand;
        }
        if ($request->model !== $request->old_model) {
            $advert->model = $request->model;
        }
        if ($request->year !== $request->old_year) {
            $advert->year = $request->year;
        }
        if ($request->body !== $request->old_body) {
            $advert->body = $request->body;
        }
        if ($request->engine !== $request->old_engine) {
            $advert->engine = $request->engine;
        }
        if ($request->L_R !== $request->old_L_R) {
            $advert->L_R = $request->L_R;
        }
        if ($request->F_R !== $request->old_F_R) {
            $advert->F_R = $request->F_R;
        }
        if ($request->U_D !== $request->old_U_D) {
            $advert->U_D = $request->U_D;
        }
        if ($request->color !== $request->old_color) {
            $advert->color = $request->color;
        }
        if ($request->applicability !== $request->old_applicability) {
            $advert->applicability = $request->applicability;
        }
        if ($request->quantity !== $request->old_quantity) {
            $advert->quantity = $request->quantity;
        }
        if ($request->price !== $request->old_price) {
            $advert->price = $request->price;
        }
        if ($request->availability !== $request->old_availability) {
            $advert->availability = $request->availability;
        }
    
        // Обновление URL фотографий
        if ($request->main_photo_url !== $request->old_main_photo_url) {
            $advert->main_photo_url = $request->main_photo_url;
        }
        if ($request->additional_photo_url_1 !== $request->old_additional_photo_url_1) {
            $advert->additional_photo_url_1 = $request->additional_photo_url_1;
        }
        if ($request->additional_photo_url_2 !== $request->old_additional_photo_url_2) {
            $advert->additional_photo_url_2 = $request->additional_photo_url_2;
        }
        if ($request->additional_photo_url_3 !== $request->old_additional_photo_url_3) {
            $advert->additional_photo_url_3 = $request->additional_photo_url_3;
        }
    
        $advert->save();
    
        return redirect()->route('adverts.my_adverts')->with('success', 'Объявление успешно обновлено');
    }

    // получить все активные объявления текущего пользователя
    public function myAdverts(Request $request)
    {
        $userId = auth()->id(); // Получаем ID текущего пользователя

        // Получаем все активные объявления текущего пользователя
        $query = Advert::where('user_id', $userId)
                       ->where('status_ad', 'activ');

        // Поиск по product_name и number
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%");
            });
        }

        // Получение списка марок для выпадающего списка, только для объявлений текущего пользователя
        $brands = Advert::where('user_id', $userId)
                        ->where('status_ad', 'activ')
                        ->select('brand')
                        ->distinct()
                        ->pluck('brand');

        // Фильтрация по выбранной марке
        if ($request->filled('brand')) {
            $brand = strtolower($request->input('brand')); // Приводим к нижнему регистру
            $query->whereRaw('LOWER(brand) = ?', [$brand]); // Фильтруем по марке
        }

        // Получение отфильтрованных объявлений
        $adverts = $query->paginate(100);

        return view('adverts.my_adverts', compact('adverts', 'brands'));
    }

    // Удалить объявление из базы данных
    public function destroy($id)
    {
        $advert = Advert::findOrFail($id);
        $advert->delete();
        return redirect()->route('adverts.my_adverts')->with('success', 'Объявление удалено успешно.');
    }

    public function destroyMultiple(Request $request)
{
    $ids = $request->input('ids');
    Advert::whereIn('id', $ids)->delete();

    return response()->json(['success' => true]);
}
    
   public function viewed(Request $request)
{
    // Получаем данные из куки и преобразуем в массив
    $testData = json_decode($request->cookie('viewed', '[]'), true);

    $adverts = []; // Инициализируем массив для хранения объявлений

    foreach ($testData as $id => $value) {
        $advert = Advert::find($id);
        if ($advert) {
            $adverts[] = $advert; // Добавляем объявление в массив
        }
    }

    // Преобразуем массив в коллекцию Laravel
    $adverts = collect($adverts);

    // Передаем данные в представление
    return view('adverts.viewed', compact('adverts'));
}

    public function viewAdvert(Request $request, $advertId)
    {
        // Получаем данные из куки
        $viewedAdverts = json_decode($request->cookie('viewed_adverts', '[]'), true);

        // Добавляем новый товар в список просмотренных
        if (!in_array($advertId, $viewedAdverts)) {
            $viewedAdverts[] = $advertId;
        }

        // Логируем данные перед сохранением в куки
        Log::info('Сохраняем в куки: ' . json_encode($viewedAdverts));

        // Сохраняем обновленный список в куки
        $cookie = Cookie::make('viewed_adverts', json_encode($viewedAdverts), 60 * 24 * 7); // 1 неделя

        // Проверяем, что куки создается корректно
        if ($cookie) {
            Log::info('Куки создана: ' . $cookie->getValue());
        } else {
            Log::error('Ошибка при создании куки');
        }

        $value = Cookie::get('test-cookie-2');

        echo $value;

        //return redirect()->back()->withCookie($cookie);
        return $value;
    }

       public function favorites(Request $request)
    {
        // Получаем текущего авторизованного пользователя
        $user = auth()->user();
    
        // Получаем избранные товары пользователя через связь
        $favorites = $user->favorites()->with('advert')->get();
    
        // Передаем данные в представление
        return view('adverts.favorites', compact('favorites'));
    }

public function search(Request $request)
{
    // Получаем данные из запроса
    $searchQuery = $request->input('search_query');
    $brand = $request->input('brand');
    $model = $request->input('model');
    $year = $request->input('year');
    $selectedEngines = $request->input('engines', []);

    // Начинаем запрос к базе данных
    $query = Advert::query();

    // Если введен поисковый запрос
    if ($searchQuery) {
        // Логика поиска по тексту
        $query->where(function ($q) use ($searchQuery) {
            $q->where('product_name', 'like', '%' . $searchQuery . '%')
              ->orWhere('number', 'like', '%' . $searchQuery . '%');
        });
    }

    // Добавляем условия поиска по марке, модели и году
    if ($brand) {
        $query->where('brand', $brand);
    }

    if ($model) {
        $query->where('model', $model);
    }

    if ($year) {
        $query->where('year', $year);
    }

    // Фильтрация по модификациям
    if ($brand && $model && $year) {
        $generation = BaseAvto::where('brand', $brand)
            ->where('model', $model)
            ->where('year_from', '<=', $year)
            ->where('year_before', '>=', $year)
            ->first();

        if ($generation) {
            $query->whereBetween('year', [$generation->year_from, $generation->year_before]);
        } else {
            return back()->withErrors(['message' => 'Для указанного года не найдено подходящего поколения модели.']);
        }
    }

    // Фильтрация по параметру engine
    if (!empty($selectedEngines)) {
        $query->whereIn('engine', $selectedEngines);
    }

    // Выполняем начальный запрос и получаем результаты
    $initialAdverts = $query->get();

    // Добавляем функцию поиска по совместимости
    $compatibilityAdverts = collect(); // Коллекция для объявлений по совместимости

    if ($searchQuery && $brand && $model && $year) {
        // Разбиваем searchQuery на слова
        $words = explode(' ', $searchQuery);
        $firstWord = $words[0]; // Первое слово для поиска

        // Ищем все part_id в таблице parts_list, где part_name начинается с первого слова
        $parts = Part::where('part_name', 'like', $firstWord . '%')->get();

        if ($parts->isNotEmpty()) {
            foreach ($parts as $part) {
                $partId = $part->part_id;

                // Ищем id_modification по марке, модели и году
                $modifications = BaseAvto::where('brand', $brand)
                    ->where('model', $model)
                    ->where('year_from', '<=', $year)
                    ->where('year_before', '>=', $year)
                    ->pluck('id_modification')
                    ->toArray();

                if (!empty($modifications)) {
                    // Ищем id_queri в таблице users_queries по id_part и id_car
                    $userQueries = UserQuery::where('id_part', $partId)
                        ->whereIn('id_car', $modifications)
                        ->pluck('id_queri')
                        ->toArray();

                    if (!empty($userQueries)) {
                        // Ищем id_car в таблице users_queries по id_queri
                        $idCars = UserQuery::whereIn('id_queri', $userQueries)
                            ->pluck('id_car')
                            ->toArray();

                        if (!empty($idCars)) {
                            // Ищем данные в таблице base_avto по id_modification = id_car
                            $baseAvtos = BaseAvto::whereIn('id_modification', $idCars)
                                ->get(['brand', 'model', 'year_from', 'year_before']);

                            if ($baseAvtos->isNotEmpty()) {
                                // Логируем найденные данные
                                foreach ($baseAvtos as $baseAvto) {
                                    // Ищем объявления в таблице adverts по brand, model и year
                                    $adverts = Advert::where('brand', $baseAvto->brand)
                                        ->where('model', $baseAvto->model)
                                        ->whereBetween('year', [$baseAvto->year_from, $baseAvto->year_before])
                                        ->get();

                                    // Добавляем найденные объявления в коллекцию
                                    $compatibilityAdverts = $compatibilityAdverts->merge($adverts);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Поиск по номеру запчасти
    $advertsByNumber = collect(); // Коллекция для объявлений, найденных по номеру запчасти
    if ($searchQuery) {
        // Ищем все id_car, у которых id_query равен номеру запчасти
        $idCars = UserQuery::where('id_queri', $searchQuery)->pluck('id_car')->toArray();

        if (!empty($idCars)) {
            // Ищем данные в таблице base_avto по id_modification (совпадает с id_car)
            $baseAvtos = BaseAvto::whereIn('id_modification', $idCars)->get();

            if ($baseAvtos->isNotEmpty()) {
                // Добавляем условия поиска в таблице adverts
                foreach ($baseAvtos as $baseAvto) {
                    $query->orWhere(function ($q) use ($baseAvto) {
                        $q->where('brand', $baseAvto->brand)
                          ->where('model', $baseAvto->model)
                          ->whereBetween('year', [$baseAvto->year_from, $baseAvto->year_before]);
                    });
                }
            } 
        }

        // Ищем объявления, где номер запчасти совпадает с searchQuery в таблице adverts
        $advertsByNumber = Advert::where('number', $searchQuery)->get();
    }

    // Объединяем результаты начального поиска и поиска по совместимости
    $allAdverts = $initialAdverts->merge($compatibilityAdverts)->unique('id');

    // Фильтруем объявления: product_name должен содержать хотя бы одно слово из searchQuery
    if ($searchQuery) {
        $words = explode(' ', $searchQuery); // Разбиваем searchQuery на слова
        $allAdverts = $allAdverts->filter(function ($advert) use ($words, $advertsByNumber) {
            // Если объявление найдено по номеру запчасти, пропускаем фильтрацию по product_name
            if ($advertsByNumber->contains('id', $advert->id)) {
                return true;
            }
            // Для остальных объявлений проверяем product_name
            foreach ($words as $word) {
                if (stripos($advert->product_name, $word) !== false) {
                    return true; // Хотя бы одно слово найдено
                }
            }
            return false; // Ни одно слово не найдено
        });
    }

    // Сортируем объявления по релевантности
    if ($searchQuery) {
        $allAdverts = $allAdverts->sortByDesc(function ($advert) use ($words, $advertsByNumber) {
            // Если объявление найдено по номеру запчасти, оно имеет максимальную релевантность
            if ($advertsByNumber->contains('id', $advert->id)) {
                return PHP_INT_MAX; // Максимальное значение для сортировки
            }
            // Для остальных объявлений считаем релевантность по product_name
            $relevance = 0;
            foreach ($words as $word) {
                if (stripos($advert->product_name, $word) !== false) {
                    $relevance++; // Увеличиваем релевантность за каждое найденное слово
                }
            }
            return $relevance;
        });
    }

    // Получаем все уникальные значения для engine из найденных объявлений
    $engines = $allAdverts->pluck('engine')->unique()->filter()->values();

    // Получаем массив адресов для карты
    $addresses = $allAdverts->map(function ($advert) {
        return $advert->user->userAddress->city ?? 'Не указан';
    })->toArray();

    // Получаем другие данные для карты
    $prod_name = $allAdverts->pluck('product_name')->toArray();
    $image_prod = $allAdverts->pluck('main_photo_url')->toArray();
    $advert_ids = $allAdverts->pluck('id')->toArray();

    // Используем пагинацию
    $allAdverts = new LengthAwarePaginator(
        $allAdverts->forPage(request('page', 1), 10), // Текущая страница
        $allAdverts->count(), // Общее количество элементов
        10, // Количество элементов на странице
        request('page', 1), // Текущая страница
        ['path' => request()->url(), 'query' => request()->query()] // URL и параметры запроса
    );

    // Возвращаем представление с результатами
    return view('adverts.search', compact(
        'allAdverts',
        'searchQuery',
        'brand',
        'model',
        'year',
        'engines',
        'addresses',
        'prod_name',
        'image_prod',
        'advert_ids'
    ));
}
}