<?php

namespace App\Http\Controllers;
use GuzzleHttp\Exception\RequestException;
use App\Models\Income;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class IncomesController extends Controller
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
            $response = $client->request('GET', 'http://89.108.115.241:6969/api/incomes', [
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
            foreach ($data as $oneIncome) {
                Income::updateOrCreate(['income_id' => $oneIncome['income_id']], [
                    'income_id' => $oneIncome['income_id'],
                    'number' => $oneIncome['number'],
                    'date' => $oneIncome['date'],
                    'last_change_date' => $oneIncome['last_change_date'],
                    'supplier_article' => $oneIncome['supplier_article'],
                    'tech_size' => $oneIncome['tech_size'],
                    'barcode' => $oneIncome['barcode'],
                    'quantity' => $oneIncome['quantity'],
                    'total_price' => $oneIncome['total_price'],
                    'date_close' => $oneIncome['date_close'],
                    'warehouse_name' => $oneIncome['warehouse_name'],
                    'nm_id' => $oneIncome['nm_id'],
                    'status' => $oneIncome['status'],
                ]);
            } 
            return 'Записи добавлены в таблицу incomes';
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
