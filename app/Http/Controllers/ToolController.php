<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

/**
 * STAGING: Free SEO tools (VIN utilities + calculators).
 * EN (dev handoff): single /tools/{tool} route resolves against this registry.
 * To add a tool: add one entry below + a matching blade in resources/views/tools/.
 * The footer "Free Services Tools" column and the /tools hub are both driven by
 * this array, so a new entry appears in the menu automatically.
 */
class ToolController extends Controller
{
    private const NHTSA_DECODE = 'https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValues/';
    private const NHTSA_RECALLS = 'https://api.nhtsa.gov/recalls/recallsByVehicle';

    /** slug => [view, meta title, meta description, short label for menus] */
    public const TOOLS = [
        'vin-decoder' => [
            'view'        => 'tools.vin-decoder',
            'title'       => 'Free VIN Decoder',
            'description' => 'Decode any 17-character VIN into make, model, year, body, engine and more using the official NHTSA database. Free, instant, no sign-up.',
            'label'       => 'VIN Decoder',
        ],
        'vin-validator' => [
            'view'        => 'tools.vin-validator',
            'title'       => 'Free VIN Check-Digit Validator',
            'description' => 'Instantly check whether a 17-character VIN is valid using the official ISO 3779 check-digit formula. Free, no sign-up.',
            'label'       => 'VIN Validator',
        ],
        'recall-checker' => [
            'view'        => 'tools.recall-checker',
            'title'       => 'Free Recall Check by VIN',
            'description' => 'Check open safety recalls for any car by VIN, using the official NHTSA recalls database. Free, instant, no sign-up.',
            'label'       => 'Recall Check by VIN',
        ],
        'model-year-decoder' => [
            'view'        => 'tools.model-year-decoder',
            'title'       => 'Free VIN Model Year Decoder',
            'description' => 'Find a vehicle model year from the 10th character of its VIN, with the 7th character used to tell 1980s/2010s apart. Free and instant.',
            'label'       => 'Model Year Decoder',
        ],
        'car-payment-calculator' => [
            'view'        => 'tools.car-payment-calculator',
            'title'       => 'Free Car Payment Calculator',
            'description' => 'Estimate your monthly auto-loan payment from price, down payment, trade-in, APR and term. Free, instant, no sign-up.',
            'label'       => 'Car Payment Calculator',
        ],
        'fuel-cost-calculator' => [
            'view'        => 'tools.fuel-cost-calculator',
            'title'       => 'Free Fuel Cost Calculator',
            'description' => 'Estimate the fuel cost of any trip or your yearly driving from distance, fuel economy (MPG) and gas price. Free and instant.',
            'label'       => 'Fuel Cost Calculator',
        ],
        'car-affordability-calculator' => [
            'view'        => 'tools.car-affordability-calculator',
            'title'       => 'Free Car Affordability Calculator',
            'description' => 'Find out how much car you can afford from your monthly budget, down payment, APR and loan term. Free and instant.',
            'label'       => 'Car Affordability Calculator',
        ],
        'depreciation-calculator' => [
            'view'        => 'tools.depreciation-calculator',
            'title'       => 'Free Car Depreciation Calculator',
            'description' => 'Estimate how much a car will be worth in the years ahead and how much value it loses to depreciation. Free and instant.',
            'label'       => 'Depreciation Calculator',
        ],
        'lease-vs-buy-calculator' => [
            'view'        => 'tools.lease-vs-buy-calculator',
            'title'       => 'Free Lease vs Buy Calculator',
            'description' => 'Compare the total cost of leasing versus buying a car over the same period and see which is cheaper. Free and instant.',
            'label'       => 'Lease vs Buy Calculator',
        ],
        'cost-of-ownership-calculator' => [
            'view'        => 'tools.cost-of-ownership-calculator',
            'title'       => 'Free Cost of Ownership Calculator',
            'description' => 'Estimate the true cost of owning a car — depreciation, fuel, insurance, maintenance and fees — per year and over time. Free.',
            'label'       => 'Cost of Ownership Calculator',
        ],
    ];

    public function index(): View
    {
        return view('tools.index', [
            'tools'           => self::TOOLS,
            'metaTitle'       => __('tools.hub.meta_title'),
            'metaDescription' => __('tools.hub.meta_desc'),
        ]);
    }

    public function show(string $tool): View
    {
        // STAGING: локализованный роут /{locale}/... передаёт {locale} первым позиционным
        // параметром, поэтому берём нужный параметр по имени из роута (работает и для /en, и для локалей).
        $tool = request()->route('tool');
        abort_unless(isset(self::TOOLS[$tool]), 404);

        $config = self::TOOLS[$tool];

        return view($config['view'], [
            'metaTitle'       => __("tools.items.$tool.title"),
            'metaDescription' => __("tools.items.$tool.description"),
            'toolSlug'        => $tool,
        ]);
    }

    /** Decode a VIN via NHTSA vPIC (cached a day). Returns the Results[0] array. */
    private function decodeVinCached(string $vin): array
    {
        return Cache::remember('vin_decode_'.$vin, now()->addDay(), function () use ($vin) {
            return Http::timeout(12)
                ->get(self::NHTSA_DECODE.$vin, ['format' => 'json'])
                ->json('Results.0', []) ?: [];
        });
    }

    /**
     * Server-side proxy for the free VIN Decoder tool.
     * EN (dev handoff): queries NHTSA vPIC server-side (avoids browser CORS/network
     * issues), caches per VIN for a day, returns a trimmed result.
     */
    public function decodeVin(Request $request): JsonResponse
    {
        $vin = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $request->query('vin', '')));

        if (strlen($vin) !== 17) {
            return response()->json(['error' => 'A VIN must be exactly 17 characters.'], 422);
        }

        try {
            $result = $this->decodeVinCached($vin);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Could not reach the VIN service. Please try again in a moment.'], 502);
        }

        return response()->json(['result' => $result]);
    }

    /**
     * Server-side proxy for the free Recall Check tool.
     * EN (dev handoff): decodes the VIN to make/model/year, then queries the NHTSA
     * recalls API for that vehicle. Cached per VIN for a day.
     */
    public function recallsByVin(Request $request): JsonResponse
    {
        $vin = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $request->query('vin', '')));

        if (strlen($vin) !== 17) {
            return response()->json(['error' => 'A VIN must be exactly 17 characters.'], 422);
        }

        try {
            $data = Cache::remember('vin_recalls_'.$vin, now()->addDay(), function () use ($vin) {
                $decode = $this->decodeVinCached($vin);
                $make  = $decode['Make'] ?? '';
                $model = $decode['Model'] ?? '';
                $year  = $decode['ModelYear'] ?? '';

                if (! $make || ! $model || ! $year) {
                    return ['vehicle' => '', 'count' => 0, 'recalls' => []];
                }

                $results = Http::timeout(12)->get(self::NHTSA_RECALLS, [
                    'make' => $make, 'model' => $model, 'modelYear' => $year,
                ])->json('results', []);

                $recalls = array_map(function ($r) {
                    return [
                        'campaign'    => $r['NHTSACampaignNumber'] ?? '',
                        'component'   => $r['Component'] ?? '',
                        'summary'     => $r['Summary'] ?? '',
                        'consequence' => $r['Consequence'] ?? '',
                        'remedy'      => $r['Remedy'] ?? '',
                        'date'        => $r['ReportReceivedDate'] ?? '',
                    ];
                }, is_array($results) ? $results : []);

                return [
                    'vehicle' => trim($year.' '.$make.' '.$model),
                    'count'   => count($recalls),
                    'recalls' => $recalls,
                ];
            });
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Could not reach the recall service. Please try again in a moment.'], 502);
        }

        return response()->json($data);
    }
}
