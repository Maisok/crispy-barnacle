<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PayController extends Controller
{

    public function showPayForm()
    {
        return view('pay_form');
    }
    
    public function pay(Request $request)
    {
        // Проверка, что сумма была передана и является числом
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);
    
        // Индификатор терминала.
        $TerminalKey = '1684504766185DEMO';
        
        // Сумма в рублях, полученная из запроса.
        $sum = $request->input('amount');
        
        // Номер заказа.
        $order_id = uniqid();
        
        $data = array(
            "TerminalKey" => $TerminalKey,
            "Amount" => $sum * 100, // Сумма в копейках
            "OrderId" => $order_id,
            "SuccessURL" => route('payment.success'), // Указываем маршрут для страницы успеха
            "PayType" => 'O',
        );
                                
        $ch = curl_init('https://securepay.tinkoff.ru/v2/Init');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        
        $res = json_decode($res, true);
    
        if (!empty($res['PaymentURL'])) {
            // Сохраняем сумму платежа в сессии перед редиректом
            $request->session()->put('payment_amount', $sum);
    
            // Отладочная информация
            \Log::info('Сумма платежа сохранена в сессии: ' . $sum);
    
            // Редирект в платёжную систему.
            return redirect($res['PaymentURL']);
        } else {
            // Обработка ошибки
            return back()->withErrors(['payment' => 'Ошибка инициализации платежа']);
        }
    }
    
    public function handlePaymentSuccess(Request $request)
    {
        // Получаем текущего пользователя
        $user = Auth::user();
    
        // Проверяем, что пользователь авторизован
        if ($user) {
            // Получаем сумму платежа из сессии
            $amount = $request->session()->get('payment_amount', 0);
    
            // Отладочная информация
            \Log::info('Сумма платежа извлечена из сессии: ' . $amount);
    
            // Проверяем, что сумма платежа была сохранена в сессии
            if ($amount > 0) {
                // Обновляем баланс пользователя
                $user->balance += $amount;
                $user->save();
    
                // Отладочная информация
                \Log::info('Баланс пользователя обновлен: ' . $user->balance);
    
                // Очищаем сумму платежа из сессии
                $request->session()->forget('payment_amount');
    
                // Возвращаем представление с сообщением об успешном платеже
                return view('payment_success', ['amount' => $amount]);
            } else {
                // Возвращаем ошибку, если сумма платежа не была сохранена в сессии
                return redirect()->route('home')->withErrors(['payment' => 'Сумма платежа не была найдена']);
            }
        } else {
            // Возвращаем ошибку, если пользователь не авторизован
            return redirect()->route('login')->withErrors(['payment' => 'Пожалуйста, авторизуйтесь для завершения платежа']);
        }
    }




    
   
 
        // public function pay2(Request $request)
        // {
        //     $curl = curl_init();
    
        //     curl_setopt_array($curl, array(
        //         CURLOPT_URL => 'https://business.tbank.ru/openapi/api/v1/invoice/send',
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => '',
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 0,
        //         CURLOPT_FOLLOWLOCATION => true,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => 'POST',
        //         CURLOPT_POSTFIELDS => '{"invoiceNumber":"string","dueDate":"2024-11-01","invoiceDate":"2024-11-01","accountNumber":"string","payer":{"name":"string","inn":"string","kpp":"string"},"items":[{"name":"string","price":0,"unit":"string","vat":"None","amount":0}],"contacts":[{"email":"string"}],"contactPhone":"string","comment":"string"}',
        //         CURLOPT_HTTPHEADER => array(
        //             'Content-Type: application/json',
        //             'Accept: application/json',
        //             'Authorization: Bearer <TOKEN>'
        //         ),
        //     ));
    
        //     $response = curl_exec($curl);
    
        //     if ($response === false) {
        //         // Обработка ошибки cURL
        //         $error = curl_error($curl);
        //         curl_close($curl);
        //         return view('pay', ['error' => 'Ошибка при выполнении запроса: ' . $error]);
        //     }
    
        //     curl_close($curl);
    
        //     // Преобразуем JSON-ответ в массив
        //     $invoiceData = json_decode($response, true);
    
        //     if (json_last_error() !== JSON_ERROR_NONE) {
        //         // Обработка ошибки декодирования JSON
        //         return view('pay', ['error' => 'Ошибка при декодировании JSON: ' . json_last_error_msg()]);
        //     }
    
        //     // Передаем данные в представление
        //     return view('pay', ['invoiceData' => $invoiceData]);
        // }
    }
    

