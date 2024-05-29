<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        $page = $request->input('page');
        $limit = $request->input('limit');
        $key = $request->input('key');
        $client = new Client();
        try {
            $response = $client->request('GET', 'http://89.108.115.241:6969/api/orders', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'key' => $key
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            
            if (empty($body['data'])) {
                return 'API не вернуло записи';
            }
            $data = $body['data'];
            foreach ($data as $oneOrder) {
                Order::updateOrCreate([
                    'g_number' => $oneOrder['g_number'],
                    'date' => $oneOrder['date'],
                    'last_change_date' => $oneOrder['last_change_date'],
                    'supplier_article' => $oneOrder['supplier_article'],
                    'tech_size' => $oneOrder['tech_size'],
                    'barcode' => $oneOrder['barcode'],
                    'total_price' => $oneOrder['total_price'],
                    'discount_percent' => $oneOrder['discount_percent'],
                    'warehouse_name' => $oneOrder['warehouse_name'],
                    'oblast' => $oneOrder['oblast'],
                    'income_id' => $oneOrder['income_id'],
                    'odid' => $oneOrder['odid'],
                    'nm_id' => $oneOrder['nm_id'],
                    'subject' => $oneOrder['subject'],
                    'category' => $oneOrder['category'],
                    'brand' => $oneOrder['brand'],
                    'is_cancel' => $oneOrder['is_cancel'],
                    'cancel_dt' => $oneOrder['cancel_dt'],
                   
                ]);
            } 
            return 'Записи добавлены в таблицу orders';
        } 
        catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorMessage = $e->getResponse()->getBody()->getContents();
            } else {
                $errorMessage = "Произошла ошибка при выполнении запроса: " . $e->getMessage();
            }
            return $errorMessage;
        }
    }
}
