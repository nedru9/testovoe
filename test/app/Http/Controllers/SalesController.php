<?php
namespace App\Http\Controllers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
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
            $response = $client->request('GET', 'http://89.108.115.241:6969/api/sales', [
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
            foreach ($data as $oneSale) {
                Sale::updateOrCreate([
                    'g_number' => $oneSale['g_number'],
                    'date' => $oneSale['date'],
                    'last_change_date' => $oneSale['last_change_date'],
                    'supplier_article' => $oneSale['supplier_article'],
                    'tech_size' => $oneSale['tech_size'],
                    'barcode' => $oneSale['barcode'],
                    'total_price' => $oneSale['total_price'],
                    'discount_percent' => $oneSale['discount_percent'],
                    'is_supply' => $oneSale['is_supply'],
                    'is_realization' => $oneSale['is_realization'],
                    'promo_code_discount' => $oneSale['promo_code_discount'],
                    'warehouse_name' => $oneSale['warehouse_name'],
                    'country_name' => $oneSale['country_name'],
                    'oblast_okrug_name' => $oneSale['oblast_okrug_name'],
                    'region_name' => $oneSale['region_name'],
                    'income_id' => $oneSale['income_id'],
                    'sale_id' => $oneSale['sale_id'],
                    'odid' => $oneSale['odid'],
                    'spp' => $oneSale['spp'],
                    'for_pay' => $oneSale['for_pay'],
                    'finished_price' => $oneSale['finished_price'],
                    'price_with_disc' => $oneSale['price_with_disc'],
                    'nm_id' => $oneSale['nm_id'],
                    'subject' => $oneSale['subject'],
                    'category' => $oneSale['category'],
                    'brand' => $oneSale['brand'],
                    'is_storno' => $oneSale['is_storno']
                ]);
            } 
            return 'Записи добавлены в таблицу sales';
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
