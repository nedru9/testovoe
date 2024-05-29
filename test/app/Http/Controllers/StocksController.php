<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\Stock;
use Illuminate\Http\Request;

class StocksController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('dateFrom');
        $page = $request->input('page');
        $limit = $request->input('limit');
        $key = $request->input('key');
        $client = new Client();
        try {
            $response = $client->request('GET', 'http://89.108.115.241:6969/api/stocks', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'dateFrom' => $dateFrom,
                    'key' => $key
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            
            if (empty($body['data'])) {
                return 'API не вернуло записи';
            }
            $data = $body['data'];
            foreach ($data as $oneStock) {
                Stock::updateOrCreate([
                    'date' => $oneStock['date'],
                    'last_change_date' => $oneStock['last_change_date'],
                    'last_change_date' => $oneStock['last_change_date'],
                    'supplier_article' => $oneStock['supplier_article'],
                    'tech_size' => $oneStock['tech_size'],
                    'barcode' => $oneStock['barcode'],
                    'quantity' => $oneStock['quantity'],
                    'is_supply' => $oneStock['is_supply'],
                    'is_realization' => $oneStock['is_realization'],
                    'quantity_full' => $oneStock['quantity_full'],
                    'warehouse_name' => $oneStock['warehouse_name'],
                    'in_way_to_client' => $oneStock['in_way_to_client'],
                    'in_way_from_client' => $oneStock['in_way_from_client'],
                    'nm_id' => $oneStock['nm_id'],
                    'subject' => $oneStock['subject'],
                    'category' => $oneStock['category'],
                    'brand' => $oneStock['brand'],
                    'sc_code' => $oneStock['sc_code'],
                    'price' => $oneStock['price'],
                    'discount' => $oneStock['discount'],
                   
                ]);
            } 
            return 'Записи добавлены в таблицу stocks';
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
