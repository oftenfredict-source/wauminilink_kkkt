<?php

namespace App\Http\Controllers;

use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Pledge;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Member;
use App\Models\Community;
use App\Models\CommunityOffering;
use App\Models\CommunityOfferingItem;
use App\Models\AhadiPledge;
use App\Models\OfferingCollectionSession;
use App\Models\OfferingCollectionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * System-wide reports overview: members and finance at a glance
     * For Church Elders: shows community-specific reports
     */
    public function generalSecretaryReport(Request $request)
    {
        $user = auth()->user();

        // Restrict access to Secretary, Pastor, and Administrator only
        if (!$user->isSecretary() && !$user->isPastor() && !$user->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to view the General Secretary report.');
        }

        $year = $request->get('year', date('Y'));
        $start = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $end = Carbon::createFromDate($year, 12, 31)->endOfDay();

        // 1. Gospel Income (10.xx)
        $gospelIncome = [
            '10.01' => [
                'name' => 'AHADI',
                'description' => 'Ahadi ya Bwana offerings and payments',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.02' => [
                'name' => 'KOLEKTI',
                'description' => 'Sunday Offering',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.03' => [
                'name' => 'S/SCHOOL',
                'description' => 'Sunday School offerings',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.04' => [
                'name' => 'SHUKRANI',
                'description' => 'Thanksgiving offerings',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.05' => [
                'name' => 'CHAKULA CHA BWANA',
                'description' => 'Lord\'s Supper offerings',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.06' => [
                'name' => 'FUNGU LA KUMI',
                'description' => 'Tithes (Zaka)',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.07' => [
                'name' => 'SHUKRANI YA NDOA',
                'description' => 'Wedding Thanksgiving',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.08' => [
                'name' => 'SHUKRANI UBATIZO',
                'description' => 'Baptism Thanksgiving',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.09' => [
                'name' => 'MALIMBUKO',
                'description' => 'First Fruits',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.10' => [
                'name' => 'SADAKA YA KAWAIDA',
                'description' => 'General Offerings',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.12' => [
                'name' => 'MAVUNO',
                'description' => 'Harvest',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.14' => [
                'name' => 'HURUMA',
                'description' => 'Compassion/Mercy',
                'actual' => 0,
                'estimate' => 0
            ],
            '10.15' => [
                'name' => 'MKOPO KUTOKA VIKUNDI',
                'description' => 'Loans from Groups',
                'actual' => 0,
                'estimate' => 0
            ],
        ];

        // 2. Group Income (20.xx)
        $groupIncome = [
            '20.01' => ['name' => 'UMOJA', 'actual' => 0, 'estimate' => 0],
            '20.02' => ['name' => 'OMBENI KWAYA', 'actual' => 0, 'estimate' => 0],
            '20.03' => ['name' => 'ALPHA NA OMEGA KWAYA', 'actual' => 0, 'estimate' => 0],
            '20.04' => ['name' => 'TUMAINI KWAYA', 'actual' => 0, 'estimate' => 0],
            '20.05' => ['name' => 'SIFUNI KWAYA', 'actual' => 0, 'estimate' => 0],
            '20.06' => ['name' => 'JUMUIYA', 'actual' => 0, 'estimate' => 0],
            '20.07' => ['name' => 'WANAWAKE', 'actual' => 0, 'estimate' => 0],
            '20.08' => ['name' => 'MASIFU YA ASUBUHI', 'actual' => 0, 'estimate' => 0],
            '20.09' => ['name' => 'DIAKONIA/BCC', 'actual' => 0, 'estimate' => 0],
            '20.12' => ['name' => 'VIJANA', 'actual' => 0, 'estimate' => 0],
            '20.13' => ['name' => 'PRAISE TEAM', 'actual' => 0, 'estimate' => 0],
            '20.15' => ['name' => 'UAMSHO', 'actual' => 0, 'estimate' => 0],
            '20.16' => ['name' => 'UIMBAJI', 'actual' => 0, 'estimate' => 0],
            '20.17' => ['name' => 'SEMINA/KONGAMANO', 'actual' => 0, 'estimate' => 0],
            '20.21' => ['name' => 'MICHAEL NA WATOTO', 'actual' => 0, 'estimate' => 0],
            '20.23' => ['name' => 'NYUMBA YA MAOMBI', 'actual' => 0, 'estimate' => 0],
            '20.24' => ['name' => 'UKARIMU WAGENI', 'actual' => 0, 'estimate' => 0],
            '20.25' => ['name' => 'KUSTAAFISHA WAZEE', 'actual' => 0, 'estimate' => 0],
            '20.26' => ['name' => 'USCF KWAYA', 'actual' => 0, 'estimate' => 0],
            '20.31' => ['name' => 'KAMBA PORI', 'actual' => 0, 'estimate' => 0],
            '20.32' => ['name' => 'MCHANGO KIPAIMARA', 'actual' => 0, 'estimate' => 0],
            '20.33' => ['name' => 'MCHANGO MAALUMU', 'actual' => 0, 'estimate' => 0],
            '20.34' => ['name' => 'VITI WATOTO S/SCHOOL', 'actual' => 0, 'estimate' => 0],
            '20.35' => ['name' => 'MAPAMBO KANISA', 'actual' => 0, 'estimate' => 0],
        ];

        // 3. Building Income (30.xx)
        $buildingIncome = [
            '30.01' => ['name' => 'JENGO KAWAIDA', 'actual' => 0, 'estimate' => 0, 'opening_balance' => 0],
            '30.02' => ['name' => 'JENGO SENTA', 'actual' => 0, 'estimate' => 0, 'opening_balance' => 0],
            '30.03' => ['name' => 'JENGO KIFUMBU', 'actual' => 0, 'estimate' => 0, 'opening_balance' => 0],
            '30.04' => ['name' => 'JENGO MWEKA', 'actual' => 0, 'estimate' => 0, 'opening_balance' => 0],
            '30.05' => ['name' => 'JENGO CCP', 'actual' => 0, 'estimate' => 0, 'opening_balance' => 0],
            '30.06' => ['name' => 'JENGO S/SCHOOL SENTA', 'actual' => 0, 'estimate' => 0, 'opening_balance' => 0],
            '30.07' => ['name' => 'ALAMA KIPAIMARA', 'actual' => 0, 'estimate' => 0, 'opening_balance' => 0],
        ];

        // 4. Investment Income (40.xx)
        $investmentIncome = [
            '40.01' => ['name' => 'KITEGA UCHUMI', 'actual' => 0, 'estimate' => 0, 'opening_balance' => 0],
        ];

        // 5. Gospel Expenses (60.xx)
        $gospelExpenses = [
            '60.01' => ['name' => 'MISHAHARA', 'actual' => 0, 'estimate' => 0],
            '60.02' => ['name' => 'NAULI NA POSHO', 'actual' => 0, 'estimate' => 0],
            '60.03' => ['name' => 'CHAI NA CHAKULA', 'actual' => 0, 'estimate' => 0],
            '60.04' => ['name' => 'NSSF', 'actual' => 0, 'estimate' => 0],
            '60.05' => ['name' => 'VIFAA VYA USAFI', 'actual' => 0, 'estimate' => 0],
            '60.06' => ['name' => 'VIFAA VYA OFISINI/STAINERY', 'actual' => 0, 'estimate' => 0],
            '60.07' => ['name' => 'WAHUBIRI', 'actual' => 0, 'estimate' => 0],
            '60.08' => ['name' => 'MATIBABU', 'actual' => 0, 'estimate' => 0],
            '60.09' => ['name' => 'UMEME', 'actual' => 0, 'estimate' => 0],
            '60.10' => ['name' => 'MAJI', 'actual' => 0, 'estimate' => 0],
            '60.11' => ['name' => 'CHAKULA CHA BWANA', 'actual' => 0, 'estimate' => 0],
            '60.12' => ['name' => 'MAZINGIRA', 'actual' => 0, 'estimate' => 0],
            '60.13' => ['name' => 'VIKAO NDANI', 'actual' => 0, 'estimate' => 0],
            '60.14' => ['name' => 'VIKAO NJE', 'actual' => 0, 'estimate' => 0],
            '60.16' => ['name' => 'WENZI WA WATUMISHI', 'actual' => 0, 'estimate' => 0],
            '60.20' => ['name' => 'LESEN ZA NDOA WCHG.', 'actual' => 0, 'estimate' => 0],
            '60.21' => ['name' => 'MAVAZI YA HUDUMA WATUMISHI', 'actual' => 0, 'estimate' => 0],
            '60.23' => ['name' => '19% KKKT DAYOSISI', 'actual' => 0, 'estimate' => 0],
            '60.24' => ['name' => '8% JIMBO', 'actual' => 0, 'estimate' => 0],
            '60.26' => ['name' => 'MKUTANO MKUU WA USHARIKA', 'actual' => 0, 'estimate' => 0],
            '60.27' => ['name' => 'UTUNZAJI OFISI', 'actual' => 0, 'estimate' => 0],
            '60.28' => ['name' => 'S/SCHOOL SIKUKUU', 'actual' => 0, 'estimate' => 0],
            '60.29' => ['name' => 'KUSTAAFISHA WAZEE USHARIKA', 'actual' => 0, 'estimate' => 0],
            '60.30' => ['name' => 'MABANGO MAVUNO', 'actual' => 0, 'estimate' => 0],
            '60.31' => ['name' => 'MAWASILIANO', 'actual' => 0, 'estimate' => 0],
            '60.32' => ['name' => 'GHARAMA ZA BENKI', 'actual' => 0, 'estimate' => 0],
            '60.33' => ['name' => 'UKARIMU WAGENI', 'actual' => 0, 'estimate' => 0],
            '60.34' => ['name' => 'DIAKONIA', 'actual' => 0, 'estimate' => 0],
            '60.35' => ['name' => 'MAPAMBO YA KANISA', 'actual' => 0, 'estimate' => 0],
            '60.36' => ['name' => 'MAVUNO', 'actual' => 0, 'estimate' => 0],
            '60.37' => ['name' => 'MAVAZI YA KIPAIMARA', 'actual' => 0, 'estimate' => 0],
            '60.38' => ['name' => 'MATENGENEZO COMPUTER', 'actual' => 0, 'estimate' => 0],
        ];

        // 6. Group/Department Expenses (70.xx)
        $groupExpenses = [
            '70.01' => ['name' => 'BCC MISHAHARA', 'actual' => 0, 'estimate' => 0],
            '70.02' => ['name' => 'NSSF BCC', 'actual' => 0, 'estimate' => 0],
            '70.03' => ['name' => 'BCC CHAKULA', 'actual' => 0, 'estimate' => 0],
            '70.04' => ['name' => 'SIFUNI KWAYA', 'actual' => 0, 'estimate' => 0],
            '70.06' => ['name' => 'OMBENI KWAYA', 'actual' => 0, 'estimate' => 0],
            '70.07' => ['name' => 'TUMAINI KWAYA', 'actual' => 0, 'estimate' => 0],
            '70.09' => ['name' => 'MASIFU YA ASUBUHI', 'actual' => 0, 'estimate' => 0],
            '70.10' => ['name' => 'JUMUIYA', 'actual' => 0, 'estimate' => 0],
            '70.11' => ['name' => 'WANAWAKE', 'actual' => 0, 'estimate' => 0],
            '70.12' => ['name' => 'UMOJA', 'actual' => 0, 'estimate' => 0],
            '70.15' => ['name' => 'VIJANA', 'actual' => 0, 'estimate' => 0],
            '70.16' => ['name' => 'KONGAMANO/SEMINA', 'actual' => 0, 'estimate' => 0],
            '70.18' => ['name' => 'UAMSHO', 'actual' => 0, 'estimate' => 0],
            '70.20' => ['name' => 'MICHAEL NA WATOTO', 'actual' => 0, 'estimate' => 0],
            '70.21' => ['name' => 'DIAKONIA', 'actual' => 0, 'estimate' => 0],
            '70.24' => ['name' => 'NYUMBA YA MAOMBI', 'actual' => 0, 'estimate' => 0],
            '70.25' => ['name' => 'UKARIMU WAGENI', 'actual' => 0, 'estimate' => 0],
            '70.26' => ['name' => 'PRAISE TEAM', 'actual' => 0, 'estimate' => 0],
            '70.27' => ['name' => 'ZAWADI WAZEE', 'actual' => 0, 'estimate' => 0],
            '70.28' => ['name' => 'NURU YA INJILI', 'actual' => 0, 'estimate' => 0],
            '70.29' => ['name' => 'GHARAMA BENKI', 'actual' => 0, 'estimate' => 0],
            '70.30' => ['name' => 'KAMBA PORI', 'actual' => 0, 'estimate' => 0],
            '70.31' => ['name' => 'VITAMBAA MAADHABAHUNI', 'actual' => 0, 'estimate' => 0],
            '70.32' => ['name' => 'VITI WATOTO WA S/SCHOOL CCP', 'actual' => 0, 'estimate' => 0],
            '70.33' => ['name' => 'KIPAIMARA', 'actual' => 0, 'estimate' => 0],
            '70.34' => ['name' => 'MKOPO INJILI', 'actual' => 0, 'estimate' => 0],
        ];

        // 7. Building Expenses (80.xx)
        $buildingExpenses = [
            '80.01' => ['name' => 'MAJENGO KAWAIDA', 'actual' => 0, 'estimate' => 0],
            '80.02' => ['name' => 'JENGO CCP', 'actual' => 0, 'estimate' => 0],
            '80.03' => ['name' => 'JENGO SENTA', 'actual' => 0, 'estimate' => 0],
            '80.04' => ['name' => 'JENGO KIFUMBU', 'actual' => 0, 'estimate' => 0],
            '80.05' => ['name' => 'JENGO MWEKA', 'actual' => 0, 'estimate' => 0],
            '80.06' => ['name' => 'GHARAMA ZA BENKI', 'actual' => 0, 'estimate' => 0],
        ];

        // Fetch Actual Data
        // Ahadi (10.01)
        $gospelIncome['10.01']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->whereIn('offering_type', ['Ahadi ya Bwana', 'sadaka_ahadi', 'ahadi'])
            ->where('approval_status', 'approved')
            ->sum('amount') +
            \App\Models\PledgePayment::whereBetween('payment_date', [$start, $end])
                ->sum('amount') +
            AhadiPledge::whereBetween('fulfillment_date', [$start, $end])
                ->where(function ($q) {
                    $q->where('item_type', 'LIKE', '%Cash%')->orWhere('item_type', 'LIKE', '%Fedha%');
                })->sum('quantity_fulfilled') +
            OfferingCollectionItem::whereHas('session', function ($q) use ($start, $end) {
                $q->whereBetween('collection_date', [$start, $end])->where('status', 'received');
            })->sum('amount_pledge') +
            CommunityOfferingItem::whereHas('offering', function ($q) use ($start, $end) {
                $q->whereBetween('offering_date', [$start, $end])->where('status', 'completed');
            })->sum('amount_ahadi');

        // Kolekti (10.02) - Sunday Offering (Excluding Building)
        $gospelIncome['10.02']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->where('offering_type', 'kolekti') // Only explicit Kolekti
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['KOLEKTI', 'kolekti'])
                ->where('status', 'completed')
                ->sum('amount_other');

        // Building Income (30.01) - Jengo Kawaida
        $buildingIncome['30.01']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->whereIn('offering_type', ['building_fund', 'sadaka_jengo', 'Ahadi ya Jengo'])
            ->where('approval_status', 'approved')
            ->sum('amount') +
            OfferingCollectionItem::whereHas('session', function ($q) use ($start, $end) {
                $q->whereBetween('collection_date', [$start, $end])->where('status', 'received');
            })->sum('amount_building') +
            CommunityOfferingItem::whereHas('offering', function ($q) use ($start, $end) {
                $q->whereBetween('offering_date', [$start, $end])->where('status', 'completed');
            })->sum('amount_jengo');

        // Sunday School (10.03)
        $gospelIncome['10.03']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->where(function ($q) {
                $q->whereIn('offering_type', ['sunday_school', 's/school'])
                    ->orWhere(function ($subQ) {
                        $subQ->where('offering_type', 'general')
                            ->where('service_type', 'children_service');
                    });
            })
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->where(function ($q) {
                    $q->whereIn('offering_type', ['S/SCHOOL', 'sunday_school'])
                        ->orWhere(function ($subQ) {
                            $subQ->where('offering_type', 'general')
                                ->where('service_type', 'children_service');
                        });
                })
                ->where('status', 'completed')
                ->sum('amount_other');

        // Thanksgiving (10.04)
        $gospelIncome['10.04']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->whereIn('offering_type', ['thanksgiving', 'shukrani'])
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['SHUKRANI', 'shukrani', 'thanksgiving'])
                ->where('status', 'completed')
                ->sum('amount_other');

        // Lord's Supper (10.05)
        $gospelIncome['10.05']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->whereIn('offering_type', ['Lord\'s Supper', 'chakula_cha_bwana'])
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['CHAKULA CHA BWANA', 'chakula_cha_bwana'])
                ->where('status', 'completed')
                ->sum('amount_other');

        $gospelIncome['10.06']['actual'] = Tithe::whereBetween('tithe_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['FUNGU LA KUMI', 'tithe'])
                ->where('status', 'completed')
                ->sum('amount_other');

        // Wedding Thanksgiving (10.07)
        $gospelIncome['10.07']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->where('offering_type', 'shukrani_ndoa')
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['SHUKRANI YA NDOA', 'shukrani_ndoa'])
                ->where('status', 'completed')
                ->sum('amount_other');

        // Baptism Thanksgiving (10.08)
        $gospelIncome['10.08']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->where('offering_type', 'shukrani_ubatizo')
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['SHUKRANI UBATIZO', 'shukrani_ubatizo'])
                ->where('status', 'completed')
                ->sum('amount_other');

        // Malimbuko (10.09)
        $gospelIncome['10.09']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->where('offering_type', 'malimbuko')
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['MALIMBUKO', 'malimbuko'])
                ->where('status', 'completed')
                ->sum('amount_other');

        // Sadaka ya Kawaida (10.10)
        $gospelIncome['10.10']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->where('offering_type', 'general')
            ->where('service_type', '!=', 'children_service')
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->where('offering_type', 'general')
                ->where('service_type', '!=', 'children_service')
                ->where('status', 'completed')
                ->sum('amount_other');

        // Mavuno (10.12)
        $gospelIncome['10.12']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->where('offering_type', 'mavuno')
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['MAVUNO', 'mavuno'])
                ->where('status', 'completed')
                ->sum('amount_other');

        // Huruma (10.14)
        $gospelIncome['10.14']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->where('offering_type', 'huruma')
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOffering::whereBetween('offering_date', [$start, $end])
                ->whereIn('offering_type', ['HURUMA', 'huruma'])
                ->where('status', 'completed')
                ->sum('amount_other');

        // Umoja (20.01)
        $groupIncome['20.01']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja', 'umoja'])
            ->where('approval_status', 'approved')
            ->sum('amount') +
            CommunityOfferingItem::whereHas('offering', function ($q) use ($start, $end) {
                $q->whereBetween('offering_date', [$start, $end])
                    ->where('status', 'completed');
            })->sum('amount_umoja');

        // Fetch Group specific data (this might need refinement based on how kwaya/vikundi are tracked)
        // For now, let's map Offering types directly if they match group names
        foreach ($groupIncome as $code => &$data) {
            if ($code == '20.01')
                continue;

            $typeName = strtolower($data['name']);
            $data['actual'] = Offering::whereBetween('offering_date', [$start, $end])
                ->where('offering_type', 'LIKE', '%' . $typeName . '%')
                ->where('approval_status', 'approved')
                ->sum('amount') +
                CommunityOffering::whereBetween('offering_date', [$start, $end])
                    ->where('offering_type', 'LIKE', '%' . $data['name'] . '%')
                    ->where('status', 'completed')
                    ->sum('amount_other');
        }

        // Fetch Investment Data (40.01)
        $investmentIncome['40.01']['actual'] = Offering::whereBetween('offering_date', [$start, $end])
            ->whereIn('offering_type', ['kitega_uchumi', 'investment'])
            ->where('approval_status', 'approved')
            ->sum('amount');

        // Fetch other 30.xx categories if they match offering types
        foreach ($buildingIncome as $code => &$data) {
            if ($code == '30.01')
                continue;

            $typeName = strtolower($data['name']);
            $data['actual'] = Offering::whereBetween('offering_date', [$start, $end])
                ->where('offering_type', 'LIKE', '%' . $typeName . '%')
                ->where('approval_status', 'approved')
                ->sum('amount') +
                CommunityOffering::whereBetween('offering_date', [$start, $end])
                    ->where('offering_type', 'LIKE', '%' . $data['name'] . '%')
                    ->where('status', 'completed')
                    ->sum('amount_other');
        }

        // --- EXPENDITURE AGGREGATION ---

        // Gospel Expenses (60.xx)
        foreach ($gospelExpenses as $code => &$data) {
            $typeName = strtolower($data['name']);
            $data['actual'] = Expense::whereBetween('expense_date', [$start, $end])
                ->where(function ($q) use ($typeName) {
                    $q->where('expense_name', 'LIKE', '%' . $typeName . '%')
                        ->orWhere('expense_category', 'LIKE', '%' . $typeName . '%');
                })
                ->where('approval_status', 'approved')
                ->sum('amount');
        }

        // Group/Department Expenses (70.xx)
        foreach ($groupExpenses as $code => &$data) {
            $typeName = strtolower($data['name']);
            $data['actual'] = Expense::whereBetween('expense_date', [$start, $end])
                ->where(function ($q) use ($typeName) {
                    $q->where('expense_name', 'LIKE', '%' . $typeName . '%')
                        ->orWhere('expense_category', 'LIKE', '%' . $typeName . '%');
                })
                ->where('approval_status', 'approved')
                ->sum('amount');
        }

        // Building Expenses (80.xx)
        foreach ($buildingExpenses as $code => &$data) {
            $typeName = strtolower($data['name']);
            $data['actual'] = Expense::whereBetween('expense_date', [$start, $end])
                ->where(function ($q) use ($typeName) {
                    $q->where('expense_name', 'LIKE', '%' . $typeName . '%')
                        ->orWhere('expense_category', 'LIKE', '%' . $typeName . '%');
                })
                ->where('approval_status', 'approved')
                ->sum('amount');
        }

        // Fetch Estimates from Budget (if available)
        $budgets = Budget::where('fiscal_year', $year)
            ->where('approval_status', 'approved')
            ->with('lineItems')
            ->get();

        foreach ($budgets as $budget) {
            foreach ($budget->lineItems as $item) {
                // Try to match item_name to income names
                foreach ($gospelIncome as &$gospel) {
                    if (stripos($item->item_name, $gospel['name']) !== false) {
                        $gospel['estimate'] += $item->amount;
                    }
                }
                foreach ($groupIncome as &$group) {
                    if (stripos($item->item_name, $group['name']) !== false) {
                        $group['estimate'] += $item->amount;
                    }
                }
                // Match expenses
                foreach ($gospelExpenses as &$exp) {
                    if (stripos($item->item_name, $exp['name']) !== false) {
                        $exp['estimate'] += $item->amount;
                    }
                }
                foreach ($groupExpenses as &$exp) {
                    if (stripos($item->item_name, $exp['name']) !== false) {
                        $exp['estimate'] += $item->amount;
                    }
                }
                foreach ($buildingExpenses as &$exp) {
                    if (stripos($item->item_name, $exp['name']) !== false) {
                        $exp['estimate'] += $item->amount;
                    }
                }
            }
        }

        return view('finance.reports.general-secretary', [
            'year' => $year,
            'gospelIncome' => $gospelIncome,
            'groupIncome' => $groupIncome,
            'buildingIncome' => $buildingIncome,
            'investmentIncome' => $investmentIncome,
            'gospelExpenses' => $gospelExpenses,
            'groupExpenses' => $groupExpenses,
            'buildingExpenses' => $buildingExpenses
        ]);
    }

    /**
     * System-wide reports overview: members and finance at a glance
     * For Church Elders: shows community-specific reports
     */
    public function overview(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date', Carbon::now()->startOfYear());
        $endDate = $request->get('end_date', Carbon::now()->endOfYear());

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Check if user is a church elder and get their communities
        $isChurchElder = $user->isChurchElder();
        $communities = $isChurchElder ? $user->elderCommunities() : collect();
        $communityIds = $communities->pluck('id')->toArray();
        $selectedCommunity = null;

        // If community_id is provided, use it (for church elders)
        if ($request->has('community_id') && $isChurchElder) {
            $selectedCommunity = Community::find($request->community_id);
            if ($selectedCommunity && $communities->contains('id', $selectedCommunity->id)) {
                $communityIds = [$selectedCommunity->id];
            } else {
                $selectedCommunity = $communities->first();
                $communityIds = $selectedCommunity ? [$selectedCommunity->id] : [];
            }
        } elseif ($isChurchElder && $communities->isNotEmpty()) {
            $selectedCommunity = $communities->first();
            $communityIds = [$selectedCommunity->id];
        }

        // Members - filter by community if church elder
        $membersQuery = Member::query();
        if ($isChurchElder && !empty($communityIds)) {
            $membersQuery->whereIn('community_id', $communityIds);
        }
        $totalMembers = $membersQuery->count();
        $newMembers30d = (clone $membersQuery)->where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Contributions (approved only where applicable) - filter by community if church elder
        $tithes = Tithe::whereBetween('tithe_date', [$start, $end])->where('approval_status', 'approved');
        $offerings = Offering::whereBetween('offering_date', [$start, $end])->where('approval_status', 'approved');
        $donations = Donation::whereBetween('donation_date', [$start, $end])->where('approval_status', 'approved');

        // Filter by community members if church elder
        if ($isChurchElder && !empty($communityIds)) {
            $tithes->whereHas('member', function ($query) use ($communityIds) {
                $query->whereIn('community_id', $communityIds);
            });
            $offerings->whereHas('member', function ($query) use ($communityIds) {
                $query->whereIn('community_id', $communityIds);
            });
            $donations->whereHas('member', function ($query) use ($communityIds) {
                $query->whereIn('community_id', $communityIds);
            });
        }

        $totalTithes = (clone $tithes)->sum('amount');
        $totalOfferings = (clone $offerings)->sum('amount');
        $totalDonations = (clone $donations)->sum('amount');
        $totalGiving = $totalTithes + $totalOfferings + $totalDonations;

        $transactionsCount = (clone $tithes)->count() + (clone $offerings)->count() + (clone $donations)->count();

        // Offerings by type
        $offeringTypes = (clone $offerings)
            ->select('offering_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('offering_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        // Donations by type
        $donationTypes = (clone $donations)
            ->select('donation_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('donation_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        // Combine offerings and donations by type (case-insensitive matching)
        // This shows total, offering amount, and donation amount separately
        $combinedByType = [];

        // Process offerings
        foreach ($offeringTypes as $offering) {
            $typeKey = strtolower($offering->offering_type);
            if (!isset($combinedByType[$typeKey])) {
                $combinedByType[$typeKey] = [
                    'type' => $offering->offering_type,
                    'offering_amount' => 0,
                    'donation_amount' => 0,
                    'offering_count' => 0,
                    'donation_count' => 0,
                ];
            }
            $combinedByType[$typeKey]['offering_amount'] = $offering->total_amount;
            $combinedByType[$typeKey]['offering_count'] = $offering->count;
        }

        // Process donations
        foreach ($donationTypes as $donation) {
            $typeKey = strtolower($donation->donation_type);
            if (!isset($combinedByType[$typeKey])) {
                $combinedByType[$typeKey] = [
                    'type' => $donation->donation_type,
                    'offering_amount' => 0,
                    'donation_amount' => 0,
                    'offering_count' => 0,
                    'donation_count' => 0,
                ];
            }
            $combinedByType[$typeKey]['donation_amount'] = $donation->total_amount;
            $combinedByType[$typeKey]['donation_count'] = $donation->count;
        }

        // Calculate totals and format for display
        foreach ($combinedByType as $key => &$data) {
            $data['total_amount'] = $data['offering_amount'] + $data['donation_amount'];
            $data['total_count'] = $data['offering_count'] + $data['donation_count'];
        }

        // Sort by total amount descending
        usort($combinedByType, function ($a, $b) {
            return $b['total_amount'] <=> $a['total_amount'];
        });

        // Top contributors (filtered to only requested types: Umoja, Jengo, Pledges)
        // Including tolerance for both pretty labels and slugs
        $topContributorsQuery = Member::select(
            'members.id',
            'members.full_name',
            DB::raw('(
                    COALESCE((SELECT SUM(community_offering_items.amount_umoja + community_offering_items.amount_jengo + community_offering_items.amount_ahadi) FROM community_offering_items 
                              JOIN community_offerings ON community_offering_items.community_offering_id = community_offerings.id
                              WHERE community_offering_items.member_id = members.id 
                              AND community_offerings.status = "completed"
                              AND community_offerings.offering_date BETWEEN "' . $start->format('Y-m-d') . '" AND "' . $end->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(pledge_payments.amount) FROM pledge_payments 
                                JOIN pledges ON pledge_payments.pledge_id = pledges.id
                                WHERE pledges.member_id = members.id 
                                AND pledge_payments.payment_date BETWEEN "' . $start->format('Y-m-d') . '" AND "' . $end->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(offerings.amount) FROM offerings 
                                WHERE offerings.member_id = members.id 
                                AND offerings.approval_status = "approved"
                                AND offerings.offering_type IN ("Sadaka ya Umoja", "Sadaka ya Jengo", "sadaka_umoja", "sadaka_jengo", "Ahadi ya Bwana", "sadaka_ahadi")
                                AND offerings.offering_date BETWEEN "' . $start->format('Y-m-d') . '" AND "' . $end->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(quantity_fulfilled) FROM ahadi_pledges 
                                WHERE ahadi_pledges.member_id = members.id 
                                AND (item_type LIKE "%Cash%" OR item_type LIKE "%Fedha%")
                                AND fulfillment_date BETWEEN "' . $start->format('Y-m-d') . '" AND "' . $end->format('Y-m-d') . '"), 0)
                ) as total_giving')
        );

        if ($isChurchElder && !empty($communityIds)) {
            $topContributorsQuery->whereIn('community_id', $communityIds);
        }

        $topContributors = $topContributorsQuery->orderByDesc('total_giving')->limit(10)->get();

        // Get community offerings (mid-week/special)
        $communityOfferingsQuery = CommunityOffering::whereBetween('offering_date', [$start, $end])
            ->where('status', 'completed');

        if ($isChurchElder && !empty($communityIds)) {
            $communityOfferingsQuery->whereIn('community_id', $communityIds);
        }

        $communityOfferings = $communityOfferingsQuery->get();
        $totalCommunityOfferings = $communityOfferings->sum('amount');

        // Specifically for the new special report integration
        // Support both naming conventions (slugs and full names)
        $specialOfferingsSummary = [
            'umoja' => $communityOfferings->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja'])->sum('amount') +
                $communityOfferings->where('offering_type', 'Sunday Offering')->sum('amount_umoja') +
                (clone $offerings)->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja'])->sum('amount'),

            'jengo' => $communityOfferings->whereIn('offering_type', ['Sadaka ya Jengo', 'sadaka_jengo'])->sum('amount') +
                $communityOfferings->where('offering_type', 'Sunday Offering')->sum('amount_jengo') +
                (clone $offerings)->whereIn('offering_type', ['Sadaka ya Jengo', 'sadaka_jengo'])->sum('amount'),

            'ahadi' => $communityOfferings->whereIn('offering_type', ['Ahadi ya Bwana', 'sadaka_ahadi'])->sum('amount') +
                $communityOfferings->where('offering_type', 'Sunday Offering')->sum('amount_ahadi') +
                (clone $offerings)->whereIn('offering_type', ['Ahadi ya Bwana', 'sadaka_ahadi'])->sum('amount'),

            'other' => $communityOfferings->whereNotIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja', 'Sadaka ya Jengo', 'sadaka_jengo', 'Ahadi ya Bwana', 'sadaka_ahadi', 'Sunday Offering'])->sum('amount') +
                $communityOfferings->where('offering_type', 'Sunday Offering')->sum('amount_other') +
                (clone $offerings)->whereNotIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja', 'Sadaka ya Jengo', 'sadaka_jengo', 'Ahadi ya Bwana', 'sadaka_ahadi'])->sum('amount'),
        ];

        // Get Pledges Total (Paid amount in the period)
        $pledgePaymentsQuery = \App\Models\PledgePayment::whereBetween('payment_date', [$start, $end]);
        if ($isChurchElder && !empty($communityIds)) {
            $pledgePaymentsQuery->whereHas('pledge.member', function ($q) use ($communityIds) {
                $q->whereIn('community_id', $communityIds);
            });
        }
        $totalPledgesPaid = $pledgePaymentsQuery->sum('amount');

        // Include AhadiPledge fulfillments (Cash only)
        $ahadiPledgesQuery = AhadiPledge::whereBetween('fulfillment_date', [$start, $end]);
        if ($isChurchElder && !empty($communityIds)) {
            $ahadiPledgesQuery->whereHas('member', function ($q) use ($communityIds) {
                $q->whereIn('community_id', $communityIds);
            });
        }

        $ahadiPledgesPaid = $ahadiPledgesQuery->get()
            ->filter(function ($p) {
                return str_contains(strtolower($p->item_type), 'cash') || str_contains(strtolower($p->item_type), 'fedha');
            })
            ->sum('quantity_fulfilled');

        $totalPledgesPaid += $ahadiPledgesPaid;
        $totalPledgesPaid += $specialOfferingsSummary['ahadi'];

        // Update total giving to include ALL collections (Tithes + Umoja + Jengo + Pledges + Other)
        $totalGiving = $totalTithes + $specialOfferingsSummary['umoja'] + $specialOfferingsSummary['jengo'] + $totalPledgesPaid + $specialOfferingsSummary['other'];

        // Update transaction count to reflect ALL approved collections
        $transactionsCount = (clone $tithes)->count() +
            $communityOfferingsQuery->count() +
            $pledgePaymentsQuery->count() +
            (clone $offerings)->count() +
            (clone $ahadiPledgesQuery)->count();

        // Convert combinedByType to collection for use in view (allows ->take())
        $combinedByType = collect($combinedByType)->sortByDesc('total_amount');

        return view('reports.overview', compact(
            'totalMembers',
            'newMembers30d',
            'totalTithes',
            'totalOfferings',
            'totalDonations',
            'totalGiving',
            'transactionsCount',
            'offeringTypes',
            'donationTypes',
            'combinedByType',
            'topContributors',
            'start',
            'end',
            'isChurchElder',
            'selectedCommunity',
            'communities',
            'communityOfferings',
            'totalCommunityOfferings',
            'specialOfferingsSummary',
            'totalPledgesPaid'
        ));
    }
    /**
     * Display financial reports dashboard
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear());
        $endDate = $request->get('end_date', Carbon::now()->endOfYear());

        // Get comprehensive financial summary
        $financialSummary = $this->getFinancialSummary($startDate, $endDate);

        $totalMembers = Member::count();
        return view('finance.reports.index', compact('totalMembers', 'financialSummary', 'startDate', 'endDate'));
    }

    /**
     * Get mapping of purpose types across pledges, offerings, and donations
     * This maps pledge types to corresponding offering types and donation types/purposes
     */
    private function getPurposeTypeMapping()
    {
        return [
            'building' => [
                'pledge_type' => 'building',
                'offering_types' => ['building_fund'],
                'donation_types' => ['building'],
                'donation_purposes' => ['building', 'building fund', 'building_fund']
            ],
            'mission' => [
                'pledge_type' => 'mission',
                'offering_types' => ['general'], // Missions typically use general offerings
                'donation_types' => ['mission'],
                'donation_purposes' => ['mission', 'missions']
            ],
            'special' => [
                'pledge_type' => 'special',
                'offering_types' => ['special'],
                'donation_types' => ['special'],
                'donation_purposes' => ['special', 'special project', 'special_project']
            ],
            'general' => [
                'pledge_type' => 'general',
                'offering_types' => ['general'],
                'donation_types' => ['general'],
                'donation_purposes' => ['general']
            ]
        ];
    }

    /**
     * Get combined financial data by purpose (combining pledges, offerings, and donations)
     */
    private function getCombinedByPurpose($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $mapping = $this->getPurposeTypeMapping();
        $combined = [];

        foreach ($mapping as $purpose => $types) {
            // Get pledges for this purpose
            $pledges = Pledge::whereBetween('pledge_date', [$start, $end])
                ->where('pledge_type', $types['pledge_type'])
                ->get();

            $pledgeAmount = $pledges->sum('pledge_amount');
            $pledgePaid = $pledges->sum('amount_paid');
            $pledgeCount = $pledges->count();

            // Get offerings for this purpose
            $offerings = Offering::whereBetween('offering_date', [$start, $end])
                ->where('approval_status', 'approved')
                ->whereIn('offering_type', $types['offering_types'])
                ->get();

            $offeringAmount = $offerings->sum('amount');
            $offeringCount = $offerings->count();

            // Get donations for this purpose (by type or purpose field)
            $donations = Donation::whereBetween('donation_date', [$start, $end])
                ->where('approval_status', 'approved')
                ->where(function ($query) use ($types) {
                    $query->whereIn('donation_type', $types['donation_types'])
                        ->orWhereIn('purpose', $types['donation_purposes']);
                })
                ->get();

            $donationAmount = $donations->sum('amount');
            $donationCount = $donations->count();

            // Combined totals
            $combined[$purpose] = [
                'purpose' => $purpose,
                'display_name' => ucfirst($purpose) . ($purpose === 'building' ? ' Fund' : ($purpose === 'special' ? ' Project' : '')),
                'pledges' => [
                    'total_pledged' => $pledgeAmount,
                    'total_paid' => $pledgePaid,
                    'outstanding' => $pledgeAmount - $pledgePaid,
                    'count' => $pledgeCount
                ],
                'offerings' => [
                    'total' => $offeringAmount,
                    'count' => $offeringCount
                ],
                'donations' => [
                    'total' => $donationAmount,
                    'count' => $donationCount
                ],
                'combined_total' => $pledgePaid + $offeringAmount + $donationAmount,
                'combined_pledged' => $pledgeAmount + $offeringAmount + $donationAmount
            ];
        }

        return $combined;
    }

    /**
     * Get comprehensive financial summary for all types
     */
    private function getFinancialSummary($startDate, $endDate)
    {
        // Parse dates
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // TITHES - Only approved
        $totalTithes = Tithe::whereBetween('tithe_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->sum('amount');
        $tithesCount = Tithe::whereBetween('tithe_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->count();
        $pendingTithes = Tithe::whereBetween('tithe_date', [$start, $end])
            ->where('approval_status', 'pending')
            ->sum('amount');

        // OFFERINGS - Only approved, with type breakdown
        $totalOfferings = Offering::whereBetween('offering_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->sum('amount');
        $offeringsCount = Offering::whereBetween('offering_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->count();
        $pendingOfferings = Offering::whereBetween('offering_date', [$start, $end])
            ->where('approval_status', 'pending')
            ->sum('amount');

        // Offering types breakdown
        $offeringTypes = Offering::whereBetween('offering_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->select('offering_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('offering_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        // DONATIONS - Only approved, with type breakdown
        $totalDonations = Donation::whereBetween('donation_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->sum('amount');
        $donationsCount = Donation::whereBetween('donation_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->count();
        $pendingDonations = Donation::whereBetween('donation_date', [$start, $end])
            ->where('approval_status', 'pending')
            ->sum('amount');

        // Donation types breakdown
        $donationTypes = Donation::whereBetween('donation_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->select('donation_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('donation_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        // PLEDGES - All pledges (not just approved)
        $totalPledged = Pledge::whereBetween('pledge_date', [$start, $end])
            ->sum('pledge_amount');
        $totalPledgePayments = Pledge::whereBetween('pledge_date', [$start, $end])
            ->sum('amount_paid');
        $pledgesCount = Pledge::whereBetween('pledge_date', [$start, $end])
            ->count();
        $outstandingPledges = $totalPledged - $totalPledgePayments;

        // Pledge types breakdown
        $pledgeTypes = Pledge::whereBetween('pledge_date', [$start, $end])
            ->select(
                'pledge_type',
                DB::raw('SUM(pledge_amount) as total_pledged'),
                DB::raw('SUM(amount_paid) as total_paid'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('pledge_type')
            ->orderBy('total_pledged', 'desc')
            ->get();

        // Get combined data by purpose
        $combinedByPurpose = $this->getCombinedByPurpose($startDate, $endDate);

        // EXPENSES - Match finance dashboard exactly: status='paid' AND approval_status='approved'
        // Use whereBetween with start/end of day to ensure all dates in range are included
        $expensesQuery = Expense::whereBetween('expense_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->where('status', 'paid')
            ->where('approval_status', 'approved');

        $totalExpenses = (clone $expensesQuery)->sum('amount');
        $expensesCount = (clone $expensesQuery)->count();

        // If no expenses found with exact match, try more flexible query
        if ($totalExpenses == 0) {
            $expensesQuery = Expense::whereBetween('expense_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->where(function ($query) {
                    $query->where('status', 'paid')
                        ->orWhere('approval_status', 'approved');
                })
                ->where('approval_status', '!=', 'rejected');

            $totalExpenses = (clone $expensesQuery)->sum('amount');
            $expensesCount = (clone $expensesQuery)->count();
        }
        $pendingExpenses = Expense::whereBetween('expense_date', [$start, $end])
            ->where(function ($query) {
                $query->where('approval_status', 'pending')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->where(function ($subQ) {
                                $subQ->whereNull('approval_status')
                                    ->orWhere('approval_status', '!=', 'approved');
                            });
                    });
            })
            ->sum('amount');

        // Calculate totals
        $totalIncome = $totalTithes + $totalOfferings + $totalDonations + $totalPledgePayments;
        $netIncome = $totalIncome - $totalExpenses;
        $totalPending = $pendingTithes + $pendingOfferings + $pendingDonations;

        return [
            'period' => [
                'start' => $start->format('M d, Y'),
                'end' => $end->format('M d, Y'),
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d')
            ],
            'tithes' => [
                'total' => $totalTithes,
                'count' => $tithesCount,
                'pending' => $pendingTithes
            ],
            'offerings' => [
                'total' => $totalOfferings,
                'count' => $offeringsCount,
                'pending' => $pendingOfferings,
                'types' => $offeringTypes
            ],
            'donations' => [
                'total' => $totalDonations,
                'count' => $donationsCount,
                'pending' => $pendingDonations,
                'types' => $donationTypes
            ],
            'pledges' => [
                'total_pledged' => $totalPledged,
                'total_paid' => $totalPledgePayments,
                'outstanding' => $outstandingPledges,
                'count' => $pledgesCount,
                'types' => $pledgeTypes
            ],
            'expenses' => [
                'total' => $totalExpenses,
                'count' => $expensesCount,
                'pending' => $pendingExpenses
            ],
            'summary' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'total_pending' => $totalPending
            ],
            'combined_by_purpose' => $combinedByPurpose
        ];
    }

    /**
     * Generate member giving report
     */
    public function memberGiving(Request $request)
    {
        try {
            \Log::info('memberGiving method called', ['request_params' => $request->all()]);

            $memberId = $request->get('member_id');

            // Normalize dates - handle both string and Carbon instances with error handling
            $startDateInput = $request->get('start_date');
            $endDateInput = $request->get('end_date');

            try {
                $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : Carbon::now()->startOfYear();
                $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : Carbon::now()->endOfYear();
            } catch (\Exception $e) {
                \Log::error('Date parsing error in memberGiving: ' . $e->getMessage());
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
            }

            // Ensure start date is before end date
            if ($startDate->gt($endDate)) {
                $temp = $startDate;
                $startDate = $endDate;
                $endDate = $temp;
            }

            if (!$memberId) {
                \Log::info('No member ID provided, showing member selection');
                try {
                    $members = Member::orderBy('full_name')->get();
                    $totalMembers = Member::count();

                    return view('finance.reports.member-giving', [
                        'members' => $members,
                        'member' => null,
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'totalMembers' => $totalMembers,
                        'tithes' => collect(),
                        'offerings' => collect(),
                        'donations' => collect(),
                        'pledges' => collect(),
                        'totalTithes' => 0,
                        'totalOfferings' => 0,
                        'totalDonations' => 0,
                        'totalPledged' => 0,
                        'totalPaid' => 0,
                        'totalGiving' => 0,
                        'monthlyData' => []
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error rendering view without member: ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());
                    throw $e;
                }
            }

            \Log::info('Fetching member data', ['member_id' => $memberId]);
            $member = Member::findOrFail($memberId);
            $members = Member::orderBy('full_name')->get();
            $totalMembers = Member::count();

            // Get member's financial data (only approved records)
            \Log::info('Fetching financial data', ['member_id' => $memberId, 'start_date' => $startDate, 'end_date' => $endDate]);

            // Offerings
            $offerings = Offering::where('member_id', $memberId)
                ->where('approval_status', 'approved')
                ->whereBetween('offering_date', [$startDate, $endDate])
                ->orderBy('offering_date', 'desc')
                ->get();

            // Include community offerings
            $communityOfferings = CommunityOfferingItem::where('member_id', $memberId)
                ->whereHas('offering', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('offering_date', [$startDate, $endDate])
                        ->where('status', 'completed');
                })
                ->get();

            // Map community items
            $mappedCommunityOfferings = $communityOfferings->map(function ($item) {
                return (object) [
                    'offering_date' => $item->offering->offering_date,
                    'amount' => $item->amount,
                    'offering_type' => $item->offering->offering_type,
                    'is_community' => true
                ];
            });

            // Combine for the table display
            $allOfferings = $offerings->concat($mappedCommunityOfferings)->sortByDesc('offering_date');

            // Pledges
            $pledges = Pledge::where('member_id', $memberId)
                ->whereBetween('pledge_date', [$startDate, $endDate])
                ->orderBy('pledge_date', 'desc')
                ->get();

            // Integrate Ahadi Pledges (from finance/ahadi-pledges)
            $ahadiPledges = AhadiPledge::where('member_id', $memberId)
                ->where('year', '>=', $startDate->year)
                ->where('year', '<=', $endDate->year)
                ->get();

            $mappedAhadiPledges = $ahadiPledges->map(function ($ahadi) {
                $isCash = str_contains(strtolower($ahadi->item_type), 'cash') || str_contains(strtolower($ahadi->item_type), 'fedha');
                $amount = $ahadi->estimated_value > 0 ? $ahadi->estimated_value : ($isCash ? $ahadi->quantity_promised : 0);
                $paid = $isCash ? $ahadi->quantity_fulfilled : 0;

                return new Pledge([
                    'pledge_date' => Carbon::createFromDate($ahadi->year, 1, 1),
                    'pledge_amount' => $amount,
                    'amount_paid' => $paid,
                    'status' => $ahadi->status == 'fully_fulfilled' ? 'completed' : 'active',
                    'pledge_type' => 'Ahadi: ' . $ahadi->item_type,
                    'description' => $ahadi->notes
                ]);
            });

            $pledges = $pledges->concat($mappedAhadiPledges)->sortByDesc('pledge_date');

            // Calculate totals
            $totalTithes = 0;
            $totalOfferings = $offerings->sum('amount') + $communityOfferings->sum('amount');
            $totalDonations = 0;
            $totalPledged = $pledges->sum('pledge_amount') ?? 0;
            $totalPaid = $pledges->sum('amount_paid') ?? 0;
            $totalGiving = $totalOfferings + $totalPaid;

            // Monthly breakdown
            $monthlyData = [];
            $current = $startDate->copy();
            $end = $endDate->copy();

            // Initialize empty collections for view
            $tithes = collect();
            $donations = collect();

            while ($current->lte($end)) {
                $monthStart = $current->copy()->startOfMonth();
                $monthEnd = $current->copy()->endOfMonth();

                // 1. Ahadi ya Bwana (Pledges Paid + Offerings of type 'Ahadi ya Bwana')
                $pledgePayments = \App\Models\PledgePayment::whereHas('pledge', function ($q) use ($memberId) {
                    $q->where('member_id', $memberId);
                })
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->sum('amount') ?? 0;

                $ahadiOfferings = Offering::where('member_id', $memberId)
                    ->where('approval_status', 'approved')
                    ->where('offering_type', 'Ahadi ya Bwana')
                    ->whereBetween('offering_date', [$monthStart, $monthEnd])
                    ->sum('amount') ?? 0;

                $monthAhadi = $pledgePayments + $ahadiOfferings;

                // Include AhadiPledge fulfillments (Cash only)
                $ahadiPledgePayments = AhadiPledge::where('member_id', $memberId)
                    ->whereBetween('fulfillment_date', [$monthStart, $monthEnd])
                    ->get()
                    ->filter(function ($p) {
                        return str_contains(strtolower($p->item_type), 'cash') || str_contains(strtolower($p->item_type), 'fedha');
                    })
                    ->sum('quantity_fulfilled');

                $monthAhadi += $ahadiPledgePayments;

                // 2. Sadaka ya Umoja
                $umojaOfferings = Offering::where('member_id', $memberId)
                    ->where('approval_status', 'approved')
                    ->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja'])
                    ->whereBetween('offering_date', [$monthStart, $monthEnd])
                    ->sum('amount') ?? 0;

                $umojaCommunity = CommunityOfferingItem::where('member_id', $memberId)
                    ->whereHas('offering', function ($q) use ($monthStart, $monthEnd) {
                        $q->whereBetween('offering_date', [$monthStart, $monthEnd])
                            ->where('status', 'completed')
                            ->whereIn('offering_type', ['Sadaka ya Umoja', 'sadaka_umoja']);
                    })
                    ->sum('amount') ?? 0;

                $monthUmoja = $umojaOfferings + $umojaCommunity;

                // 3. Sadaka ya Jengo
                $jengoOfferings = Offering::where('member_id', $memberId)
                    ->where('approval_status', 'approved')
                    ->whereIn('offering_type', ['Sadaka ya Jengo', 'sadaka_jengo'])
                    ->whereBetween('offering_date', [$monthStart, $monthEnd])
                    ->sum('amount') ?? 0;

                $jengoCommunity = CommunityOfferingItem::where('member_id', $memberId)
                    ->whereHas('offering', function ($q) use ($monthStart, $monthEnd) {
                        $q->whereBetween('offering_date', [$monthStart, $monthEnd])
                            ->where('status', 'completed')
                            ->whereIn('offering_type', ['Sadaka ya Jengo', 'sadaka_jengo']);
                    })
                    ->sum('amount') ?? 0;

                $monthJengo = $jengoOfferings + $jengoCommunity;

                $monthlyData[] = [
                    'month' => $current->format('M Y'),
                    'ahadi' => $monthAhadi,
                    'umoja' => $monthUmoja,
                    'jengo' => $monthJengo,
                    'total' => $monthAhadi + $monthUmoja + $monthJengo
                ];

                $current->addMonth();
            }

            \Log::info('Rendering view with member data', ['member_id' => $memberId]);

            return view('finance.reports.member-giving', [
                'member' => $member,
                'members' => $members,
                'totalMembers' => $totalMembers,
                'tithes' => $tithes,
                'offerings' => $allOfferings,
                'donations' => $donations,
                'pledges' => $pledges,
                'totalTithes' => $totalTithes,
                'totalOfferings' => $totalOfferings,
                'totalDonations' => $totalDonations,
                'totalPledged' => $totalPledged,
                'totalPaid' => $totalPaid,
                'totalGiving' => $totalGiving,
                'monthlyData' => $monthlyData,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in memberGiving method: ' . $e->getMessage());
            \Log::error('Error file: ' . $e->getFile() . ' Line: ' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return error response instead of redirect to see the actual error
            if (config('app.debug')) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }

            return redirect()->route('reports.member-giving')
                ->with('error', 'An error occurred while generating the report: ' . $e->getMessage());
        }
    }

    /**
     * Generate department giving report
     * This now combines pledges, offerings, and donations by purpose
     */
    public function departmentGiving(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear());
        $endDate = $request->get('end_date', Carbon::now()->endOfYear());

        // Get combined data by purpose (combines pledges, offerings, and donations)
        $combinedByPurpose = $this->getCombinedByPurpose($startDate, $endDate);

        // Also get individual breakdowns for reference
        $offeringTypes = Offering::whereBetween('offering_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->select('offering_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('offering_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        $donationTypes = Donation::whereBetween('donation_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->select('donation_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('donation_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        $pledgeTypes = Pledge::whereBetween('pledge_date', [$startDate, $endDate])
            ->select(
                'pledge_type',
                DB::raw('SUM(pledge_amount) as total_pledged'),
                DB::raw('SUM(amount_paid) as total_paid'),
                DB::raw('COUNT(*) as pledge_count')
            )
            ->groupBy('pledge_type')
            ->orderBy('total_pledged', 'desc')
            ->get();

        $totalMembers = Member::count();

        return view('finance.reports.department-giving', compact(
            'combinedByPurpose',
            'offeringTypes',
            'donationTypes',
            'pledgeTypes',
            'startDate',
            'endDate',
            'totalMembers'
        ));
    }

    /**
     * Generate income vs expenditure report
     */
    public function incomeVsExpenditure(Request $request)
    {
        try {
            // Check if month filter is selected
            if ($request->get('filter_type') === 'month' && $request->get('month')) {
                $month = $request->get('month'); // Format: YYYY-MM
                $startDate = Carbon::parse($month . '-01')->startOfMonth();
                $endDate = Carbon::parse($month . '-01')->endOfMonth();
            } else {
                $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->startOfYear();
                $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now()->endOfYear();

                // Ensure start date is before end date
                if ($startDate->gt($endDate)) {
                    $temp = $startDate;
                    $startDate = $endDate;
                    $endDate = $temp;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Date parsing error in incomeVsExpenditure: ' . $e->getMessage());
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        // Get income data
        $tithes = Tithe::whereBetween('tithe_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->sum('amount');

        // Offerings - Include standard Offerings, Community Offerings, and Session Collections
        $standardOfferings = Offering::whereBetween('offering_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->sum('amount');

        $communityOfferings = CommunityOffering::whereBetween('offering_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        $sessionOfferings = OfferingCollectionSession::whereBetween('collection_date', [$startDate, $endDate])
            ->where('status', 'received')
            ->sum('total_amount');

        $offerings = $standardOfferings + $communityOfferings + $sessionOfferings;

        // Ahadi ya Bwana (Combined Pledges and Ahadi Payments)
        $pledgePayments = \App\Models\PledgePayment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->sum('amount');

        $ahadiFulfillments = AhadiPledge::whereBetween('fulfillment_date', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('item_type', 'LIKE', '%Cash%')->orWhere('item_type', 'LIKE', '%Fedha%');
            })
            ->sum('quantity_fulfilled');

        $ahadiIncome = $pledgePayments + $ahadiFulfillments;

        $totalIncome = $tithes + $offerings + $ahadiIncome;

        // Get expenditure data
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->where('approval_status', 'approved')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        // Get expenses by category
        $expensesByCategory = $expenses->groupBy('expense_category')
            ->map(function ($categoryExpenses) {
                return [
                    'total' => $categoryExpenses->sum('amount'),
                    'count' => $categoryExpenses->count()
                ];
            })
            ->sortByDesc('total');

        // Monthly breakdown
        $monthlyData = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthTithes = Tithe::whereBetween('tithe_date', [$monthStart, $monthEnd])
                ->where('approval_status', 'approved')
                ->sum('amount');

            $mStandard = Offering::whereBetween('offering_date', [$monthStart, $monthEnd])
                ->where('approval_status', 'approved')
                ->sum('amount');
            $mCommunity = CommunityOffering::whereBetween('offering_date', [$monthStart, $monthEnd])
                ->where('status', 'completed')
                ->sum('amount');
            $mSession = OfferingCollectionSession::whereBetween('collection_date', [$monthStart, $monthEnd])
                ->where('status', 'received')
                ->sum('total_amount');

            $monthOfferings = $mStandard + $mCommunity + $mSession;

            $mPledgePayments = \App\Models\PledgePayment::whereBetween('payment_date', [$monthStart, $monthEnd])
                ->where('approval_status', 'approved')
                ->sum('amount');

            $mAhadiPayments = AhadiPledge::whereBetween('fulfillment_date', [$monthStart, $monthEnd])
                ->where(function ($q) {
                    $q->where('item_type', 'LIKE', '%Cash%')->orWhere('item_type', 'LIKE', '%Fedha%');
                })
                ->sum('quantity_fulfilled');

            $monthAhadiIncome = $mPledgePayments + $mAhadiPayments;

            $monthExpenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->where('status', 'paid')
                ->where('approval_status', 'approved')
                ->sum('amount');

            $monthlyIncome = $monthTithes + $monthOfferings + $monthAhadiIncome;

            $monthlyData[] = [
                'month' => $current->format('M Y'),
                'income' => $monthlyIncome,
                'expenses' => $monthExpenses,
                'net' => $monthlyIncome - $monthExpenses
            ];

            $current->addMonth();
        }

        $netIncome = $totalIncome - $totalExpenses;
        $totalMembers = Member::count();

        return view('finance.reports.income-vs-expenditure', compact(
            'tithes',
            'offerings',
            'ahadiIncome',
            'totalIncome',
            'totalExpenses',
            'netIncome',
            'expensesByCategory',
            'monthlyData',
            'startDate',
            'endDate',
            'totalMembers'
        ));
    }

    /**
     * Special Offerings Report (Summary List)
     * Shows all members who contributed to a specific special offering type in a given year.
     */
    public function specialOfferings(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $offeringType = $request->get('offering_type', 'Sadaka ya Umoja'); // Default to Umoja

        $start = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $end = Carbon::createFromDate($year, 12, 31)->endOfDay();

        // 1. Get community items
        $communityItems = CommunityOfferingItem::whereHas('offering', function ($query) use ($start, $end, $offeringType) {
            $query->whereBetween('offering_date', [$start, $end])
                ->where('offering_type', $offeringType)
                ->where('status', 'completed');
        })
            ->with(['member.community'])
            ->get();

        // 2. Get general offerings
        $generalItems = Offering::where('offering_type', $offeringType)
            ->whereBetween('offering_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->whereNotNull('member_id')
            ->with(['member.community'])
            ->get();

        // Consolidate data
        $combined = collect();

        foreach ($communityItems as $item) {
            $combined->push([
                'member_id' => $item->member_id,
                'member' => $item->member,
                'amount' => $item->amount,
            ]);
        }

        foreach ($generalItems as $item) {
            $combined->push([
                'member_id' => $item->member_id,
                'member' => $item->member,
                'amount' => $item->amount,
            ]);
        }

        // Group by member to calculate totals
        $memberStats = $combined->groupBy('member_id')->map(function ($memberItems) {
            $member = $memberItems->first()['member'];
            return [
                'member_id' => $member->id,
                'member_name' => $member->full_name,
                'envelope_number' => $member->envelope_number,
                'community_name' => $member->community->name ?? 'N/A',
                'total_amount' => $memberItems->sum('amount'),
                'transaction_count' => $memberItems->count(),
            ];
        })->sortByDesc('total_amount')->values();

        // Get available offering types for filter
        $availableTypes = CommunityOffering::select('offering_type')
            ->distinct()
            ->whereNotNull('offering_type')
            ->where('offering_type', '!=', 'General Offering') // Exclude general if mainly for special
            ->pluck('offering_type');

        // If list is empty, provide defaults
        if ($availableTypes->isEmpty()) {
            $availableTypes = ['Sadaka ya Umoja', 'Sadaka ya Jengo'];
        }

        return view('finance.reports.special-offerings.index', compact(
            'memberStats',
            'year',
            'offeringType',
            'availableTypes'
        ));
    }

    /**
     * Special Offering Member Card (Grid View)
     * Shows a printable 12-month x 5-week grid of contributions for a specific member.
     */
    public function specialOfferingMemberCard(Request $request, Member $member)
    {
        $year = $request->get('year', Carbon::now()->year);
        $offeringType = $request->get('offering_type', 'Sadaka ya Umoja');

        $start = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $end = Carbon::createFromDate($year, 12, 31)->endOfDay();

        // Fetch user's contributions for this specific type and year
        $contributions = CommunityOfferingItem::where('member_id', $member->id)
            ->whereHas('offering', function ($query) use ($start, $end, $offeringType) {
                $query->whereBetween('offering_date', [$start, $end])
                    ->where('offering_type', $offeringType)
                    ->where('status', 'completed');
            })
            ->with('offering')
            ->get();

        // Initialize 12x5 grid with zeros
        $grid = [];
        for ($m = 1; $m <= 12; $m++) {
            for ($w = 1; $w <= 5; $w++) {
                $grid[$m][$w] = 0;
            }
        }

        // Fill grid
        foreach ($contributions as $contribution) {
            $date = $contribution->offering->offering_date;
            $month = $date->month;

            // Calculate week of month: ceil(day / 7)
            // Example: Day 1-7 = Week 1, Day 8-14 = Week 2, ...
            $week = ceil($date->day / 7);

            // Handle edge case if a month has > 5 weeks logic (though day/7 max is 5 for 31 days)
            // Handle edge case if a month has > 5 weeks logic (though day/7 max is 5 for 31 days)
            if ($week > 5)
                $week = 5;

            // Add amount to the cell (in case multiple payments in same week range)
            $grid[$month][$week] += $contribution->amount;
        }

        return view('finance.reports.special-offerings.card', compact(
            'member',
            'grid',
            'year',
            'offeringType'
        ));
    }

    /**
     * Member Contribution Grid Report
     * Shows a printable 12-month x 5-week grid of contributions for Pledges or Special Offerings.
     */
    public function memberGridReport(Request $request, Member $member)
    {
        $year = $request->get('year', Carbon::now()->year);
        $type = $request->get('type', 'Sadaka ya Umoja'); // Options: 'Pledges', 'Sadaka ya Umoja', 'Sadaka ya Jengo'

        $start = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $end = Carbon::createFromDate($year, 12, 31)->endOfDay();

        // Initialize 12x5 grid with zeros
        $grid = [];
        for ($m = 1; $m <= 12; $m++) {
            for ($w = 1; $w <= 5; $w++) {
                $grid[$m][$w] = 0;
            }
        }

        $totalAmount = 0;

        if ($type === 'Pledges') {
            // Fetch pledge payments for this year
            $payments = \App\Models\PledgePayment::whereHas('pledge', function ($q) use ($member) {
                $q->where('member_id', $member->id);
            })
                ->whereBetween('payment_date', [$start, $end])
                ->get();

            foreach ($payments as $payment) {
                $date = $payment->payment_date;
                $month = $date->month;
                $week = ceil($date->day / 7);
                if ($week > 5)
                    $week = 5;
                $grid[$month][$week] += $payment->amount;
                $totalAmount += $payment->amount;
            }

            // Include AhadiPledge fulfillments (Cash only)
            $ahadiPledges = AhadiPledge::where('member_id', $member->id)
                ->whereBetween('fulfillment_date', [$start, $end])
                ->get()
                ->filter(function ($p) {
                    return str_contains(strtolower($p->item_type), 'cash') || str_contains(strtolower($p->item_type), 'fedha');
                });

            foreach ($ahadiPledges as $ahadi) {
                if ($ahadi->quantity_fulfilled > 0 && $ahadi->fulfillment_date) {
                    $date = Carbon::parse($ahadi->fulfillment_date);
                    $month = $date->month;
                    $week = ceil($date->day / 7);
                    if ($week > 5)
                        $week = 5;
                    $grid[$month][$week] += $ahadi->quantity_fulfilled;
                    $totalAmount += $ahadi->quantity_fulfilled;
                }
            }
        } else {
            // Special Offerings (Umoja/Jengo/Ahadi)
            // Community entries
            $communityItems = CommunityOfferingItem::where('member_id', $member->id)
                ->whereHas('offering', function ($query) use ($start, $end, $type) {
                    $query->whereBetween('offering_date', [$start, $end])
                        ->where(function ($q) use ($type) {
                            $q->where('offering_type', $type)
                                ->orWhere('offering_type', 'Sunday Offering');
                        })
                        ->where('status', 'completed');
                })
                ->get();

            foreach ($communityItems as $item) {
                $date = \Carbon\Carbon::parse($item->offering->offering_date);
                $month = $date->month;
                $week = ceil($date->day / 7);
                if ($week > 5)
                    $week = 5;

                $amt = $item->amount;
                if ($item->offering->offering_type === 'Sunday Offering') {
                    if ($type === 'Sadaka ya Umoja')
                        $amt = $item->amount_umoja;
                    elseif ($type === 'Sadaka ya Jengo')
                        $amt = $item->amount_jengo;
                    elseif ($type === 'Pledges' || $type === 'Ahadi ya Bwana')
                        $amt = $item->amount_ahadi;
                }

                $grid[$month][$week] += $amt;
                $totalAmount += $amt;
            }

            // General entries
            $generalOfferings = Offering::where('member_id', $member->id)
                ->whereBetween('offering_date', [$start, $end])
                ->where('offering_type', $type)
                ->where('approval_status', 'approved')
                ->get();

            foreach ($generalOfferings as $offering) {
                $date = \Carbon\Carbon::parse($offering->offering_date);
                $month = $date->month;
                $week = ceil($date->day / 7);
                if ($week > 5)
                    $week = 5;
                $grid[$month][$week] += $offering->amount;
                $totalAmount += $offering->amount;
            }
        }

        $totalPledgedAmount = 0;
        if ($type === 'Pledges') {
            // Calculate total promised amount for the year (Ahadi)
            // 1. Regular Pledges
            $totalPledgedAmount += \App\Models\Pledge::where('member_id', $member->id)
                ->whereBetween('pledge_date', [$start, $end])
                ->sum('pledge_amount');

            // 2. Ahadi Pledges (Cash only for this specific report context, or all?)
            // Usually valid to show all monetary value, but here we focus on cash/fedha
            $totalPledgedAmount += AhadiPledge::where('member_id', $member->id)
                ->where('year', $year)
                ->get()
                ->filter(function ($p) {
                    return str_contains(strtolower($p->item_type), 'cash') || str_contains(strtolower($p->item_type), 'fedha');
                })
                ->sum('quantity_promised');
        }

        return view('finance.reports.member-grid-report', compact(
            'member',
            'grid',
            'year',
            'type',
            'totalAmount',
            'totalPledgedAmount'
        ));
    }

    /**
     * Generate budget performance report
     */
    public function budgetPerformance(Request $request)
    {
        $budgetId = $request->get('budget_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfYear());
        $endDate = $request->get('end_date', Carbon::now()->endOfYear());

        if (!$budgetId) {
            return view('finance.reports.budget-performance', [
                'budgets' => Budget::orderBy('fiscal_year', 'desc')->get(),
                'budget' => null,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalMembers' => Member::count()
            ]);
        }

        $budget = Budget::findOrFail($budgetId);

        // Get expenses for this budget
        $expenses = Expense::where('budget_id', $budgetId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        // Get expenses by category
        $expensesByCategory = $expenses->groupBy('expense_category')
            ->map(function ($categoryExpenses) {
                return [
                    'total' => $categoryExpenses->sum('amount'),
                    'count' => $categoryExpenses->count(),
                    'avg' => $categoryExpenses->avg('amount')
                ];
            })
            ->sortByDesc('total');

        // Monthly breakdown
        $monthlyData = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthExpenses = Expense::where('budget_id', $budgetId)
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->where('status', 'paid')
                ->sum('amount');

            $monthlyData[] = [
                'month' => $current->format('M Y'),
                'spent' => $monthExpenses,
                'budget' => $budget->total_budget,
                'utilization' => $budget->total_budget > 0 ? round(($monthExpenses / $budget->total_budget) * 100, 2) : 0
            ];

            $current->addMonth();
        }

        $totalMembers = Member::count();

        // Get all budgets for the dropdown
        $budgets = Budget::orderBy('fiscal_year', 'desc')->get();

        return view('finance.reports.budget-performance', compact(
            'budget',
            'budgets',
            'expenses',
            'expensesByCategory',
            'monthlyData',
            'startDate',
            'endDate',
            'totalMembers'
        ));
    }

    /**
     * Generate fund breakdown report (includes both offerings and donations)
     */
    public function offeringFundBreakdown(Request $request)
    {
        // Parse dates - if provided as strings, convert to Carbon, otherwise use defaults
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->get('start_date'))
            : Carbon::now()->startOfYear();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->get('end_date'))
            : Carbon::now()->endOfYear();

        // Get all fund types from both offerings and donations (all approved, not just date range)
        // This ensures we show all fund types that have income, regardless of date
        $offeringTypes = Offering::select('offering_type')
            ->where('approval_status', 'approved')
            ->distinct()
            ->pluck('offering_type')
            ->unique()
            ->values();

        $donationTypes = Donation::select('donation_type')
            ->where('approval_status', 'approved')
            ->distinct()
            ->pluck('donation_type')
            ->unique()
            ->values();

        // Combine and get unique types (case-insensitive)
        $allFundTypes = $offeringTypes->concat($donationTypes)
            ->map(function ($type) {
                return strtolower($type);
            })
            ->unique()
            ->values();

        $fundBreakdown = [];

        foreach ($allFundTypes as $fundType) {
            // Get total income from offerings for this type
            $offeringIncome = Offering::whereRaw('LOWER(offering_type) = ?', [strtolower($fundType)])
                ->where('approval_status', 'approved')
                ->sum('amount');

            // Get total income from donations for this type
            $donationIncome = Donation::whereRaw('LOWER(donation_type) = ?', [strtolower($fundType)])
                ->where('approval_status', 'approved')
                ->sum('amount');

            // Combined total income (offerings + donations)
            $totalIncome = $offeringIncome + $donationIncome;

            // Get the original type name (prefer offering type if exists, otherwise donation type)
            $originalTypeName = Offering::whereRaw('LOWER(offering_type) = ?', [strtolower($fundType)])
                ->value('offering_type')
                ?? Donation::whereRaw('LOWER(donation_type) = ?', [strtolower($fundType)])
                    ->value('donation_type')
                ?? $fundType;

            // Get total used amount from budget allocations
            // BUT exclude amounts from expenses that have fund breakdown in approval_notes
            // (those are counted separately in usedFromExpenses to avoid double-counting)

            // First, get all expenses with fund breakdown that use this offering type
            // Include soft-deleted expenses to preserve used amounts even after deletion
            $expensesWithBreakdown = \App\Models\Expense::withTrashed()
                ->where('status', 'paid')
                ->where('approval_status', 'approved')
                ->whereNotNull('approval_notes')
                ->where(function ($query) {
                    $query->where('approval_notes', 'LIKE', '%Fund allocation%')
                        ->orWhere('approval_notes', 'LIKE', '%additional funding%');
                })
                ->get();

            // Calculate total amount used from these expenses for this offering type
            $amountFromExpensesWithBreakdown = 0;
            foreach ($expensesWithBreakdown as $expense) {
                // Try to extract fund breakdown
                if (preg_match('/:\s*(\[.*?\])/s', $expense->approval_notes, $matches)) {
                    $breakdown = json_decode($matches[1], true);
                    if (is_array($breakdown)) {
                        foreach ($breakdown as $funding) {
                            $fundingType = isset($funding['offering_type']) ?
                                strtolower(trim(str_replace([' ', '-'], '_', $funding['offering_type']))) : '';
                            $currentType = strtolower(trim(str_replace([' ', '-'], '_', $originalTypeName)));

                            if ($fundingType === $currentType && isset($funding['amount'])) {
                                $amountFromExpensesWithBreakdown += floatval($funding['amount']);
                            }
                        }
                    }
                }
            }

            // Get total used from allocations
            // Include soft-deleted budgets to preserve used amounts even after deletion
            // Check if deleted_at column exists first to avoid errors
            $hasDeletedAtColumn = false;
            try {
                $hasDeletedAtColumn = \Schema::hasColumn('budgets', 'deleted_at');
            } catch (\Exception $e) {
                // Column doesn't exist, continue without it
                $hasDeletedAtColumn = false;
            }

            $query = \DB::table('budget_offering_allocations')
                ->join('budgets', 'budget_offering_allocations.budget_id', '=', 'budgets.id')
                ->whereRaw('LOWER(budget_offering_allocations.offering_type) = ?', [strtolower($originalTypeName)]);

            if ($hasDeletedAtColumn) {
                // Include active budgets OR soft-deleted budgets (to preserve history)
                $query->where(function ($q) {
                    $q->where('budgets.status', 'active')
                        ->orWhereNotNull('budgets.deleted_at');
                });
            } else {
                // If deleted_at column doesn't exist, just use active budgets
                $query->where('budgets.status', 'active');
            }

            $totalUsedFromAllocations = $query->sum('budget_offering_allocations.used_amount');

            // Subtract the amount from expenses with breakdown to avoid double-counting
            $usedFromAllocations = max(0, $totalUsedFromAllocations - $amountFromExpensesWithBreakdown);

            // Also check expenses that are paid and linked to budgets with allocations from this offering type
            // This is a fallback for expenses that don't have approval_notes with fund breakdown
            $usedFromBudgetExpenses = 0;
            $budgetExpenseDetails = [];

            // Get all paid expenses that are linked to budgets
            // Only count expenses that are marked as paid by treasurer
            // Include soft-deleted expenses to preserve used amounts even after deletion
            $budgetExpenses = \App\Models\Expense::withTrashed()
                ->with('budget')
                ->where('status', 'paid')
                ->where('approval_status', 'approved')
                ->whereNotNull('budget_id')
                ->get();

            foreach ($budgetExpenses as $expense) {
                if (!$expense->budget)
                    continue;

                // IMPORTANT: Skip expenses that have approval_notes with fund breakdown
                // These should be handled by the main expense processing logic above
                if (!empty($expense->approval_notes)) {
                    continue;
                }

                // Check if this budget has allocations from the current fund type
                $budgetAllocations = \DB::table('budget_offering_allocations')
                    ->where('budget_id', $expense->budget_id)
                    ->whereRaw('LOWER(offering_type) = ?', [strtolower($originalTypeName)])
                    ->first();

                if ($budgetAllocations && $budgetAllocations->used_amount > 0) {
                    // Calculate how much of this expense was paid from this offering type
                    // This is an approximation - we'll use the proportion of the allocation
                    $totalAllocatedForBudget = \DB::table('budget_offering_allocations')
                        ->where('budget_id', $expense->budget_id)
                        ->sum('allocated_amount');

                    if ($totalAllocatedForBudget > 0) {
                        $proportion = $budgetAllocations->allocated_amount / $totalAllocatedForBudget;
                        $amountFromThisOffering = $expense->amount * $proportion;

                        // Only count if expense is paid
                        if ($expense->status === 'paid') {
                            $usedFromBudgetExpenses += $amountFromThisOffering;

                            $budgetExpenseDetails[] = [
                                'expense_id' => $expense->id,
                                'expense_name' => $expense->expense_name,
                                'expense_date' => $expense->expense_date,
                                'total_amount' => $expense->amount,
                                'offering_amount' => $amountFromThisOffering,
                                'budget_name' => $expense->budget->budget_name ?? 'No Budget',
                                'category' => $expense->expense_category
                            ];
                        }
                    }
                }
            }

            // Get used amount from paid expenses with additional funding
            // Expenses store additional funding in approval_notes as JSON
            $usedFromExpenses = 0;
            $expenseDetails = []; // Store details of expenses using this offering type
            $processedExpenseIds = []; // Track which expenses we've already processed for this offering type

            // Get only expenses that are marked as paid by treasurer
            // Expenses should only be counted after treasurer marks them as paid
            // Include soft-deleted expenses to preserve used amounts even after deletion
            $allExpenses = \App\Models\Expense::withTrashed()
                ->with('budget')
                ->where('status', 'paid')
                ->where('approval_status', 'approved')
                ->get();

            \Log::debug('Checking expenses for fund type', [
                'fund_type' => $originalTypeName,
                'total_all_expenses' => $allExpenses->count(),
                'expenses_with_notes' => $allExpenses->whereNotNull('approval_notes')->count()
            ]);

            foreach ($allExpenses as $expense) {
                // Skip if no approval_notes
                if (empty($expense->approval_notes)) {
                    \Log::debug('Expense has no approval_notes', [
                        'expense_id' => $expense->id,
                        'expense_name' => $expense->expense_name,
                        'status' => $expense->status,
                        'approval_status' => $expense->approval_status
                    ]);
                    continue;
                }

                // Check if approval_notes contains fund allocation information
                // Be more lenient - check for any JSON-like structure
                $hasFundInfo = (strpos($expense->approval_notes, 'additional funding') !== false ||
                    strpos($expense->approval_notes, 'Fund allocation') !== false ||
                    strpos($expense->approval_notes, 'offering_type') !== false ||
                    strpos($expense->approval_notes, '[') !== false);

                if ($hasFundInfo) {

                    // Try multiple regex patterns to extract JSON
                    $jsonFound = false;
                    $expenseFundBreakdown = null;
                    $matches = [];

                    // Pattern 1: "Fund allocation with additional funding: [JSON]"
                    if (preg_match('/Fund allocation with additional funding:\s*(\[.*?\])/s', $expense->approval_notes, $matches)) {
                        $jsonFound = true;
                    }
                    // Pattern 2: "Fund allocation: [JSON]"
                    elseif (preg_match('/Fund allocation:\s*(\[.*?\])/s', $expense->approval_notes, $matches)) {
                        $jsonFound = true;
                    }
                    // Pattern 3: Any JSON array after colon (greedy match to get full array)
                    elseif (preg_match('/:\s*(\[.*\])/s', $expense->approval_notes, $matches)) {
                        $jsonFound = true;
                    }
                    // Pattern 4: Find JSON array by matching balanced brackets (most reliable)
                    else {
                        // Find the first [ and then match until the corresponding ]
                        $startPos = strpos($expense->approval_notes, '[');
                        if ($startPos !== false) {
                            $bracketCount = 0;
                            $endPos = $startPos;
                            $notesLength = strlen($expense->approval_notes);
                            for ($i = $startPos; $i < $notesLength; $i++) {
                                $char = $expense->approval_notes[$i];
                                if ($char === '[') {
                                    $bracketCount++;
                                } elseif ($char === ']') {
                                    $bracketCount--;
                                    if ($bracketCount === 0) {
                                        $endPos = $i;
                                        break;
                                    }
                                }
                            }
                            if ($bracketCount === 0) {
                                $jsonString = substr($expense->approval_notes, $startPos, $endPos - $startPos + 1);
                                $testJson = json_decode($jsonString, true);
                                if (is_array($testJson) && !empty($testJson)) {
                                    // Check if it has the structure we expect (offering_type and amount)
                                    $hasValidStructure = false;
                                    foreach ($testJson as $item) {
                                        if (isset($item['offering_type']) && isset($item['amount'])) {
                                            $hasValidStructure = true;
                                            break;
                                        }
                                    }
                                    if ($hasValidStructure) {
                                        $matches[1] = $jsonString;
                                        $jsonFound = true;
                                    }
                                }
                            }
                        }
                    }

                    // Pattern 5: Try to find any JSON-like structure that might contain offering_type
                    if (!$jsonFound) {
                        // Look for patterns like {"offering_type":"general","amount":50000}
                        if (preg_match_all('/\{[^}]*"offering_type"[^}]*"amount"[^}]*\}/', $expense->approval_notes, $jsonMatches)) {
                            $combinedJson = '[' . implode(',', $jsonMatches[0]) . ']';
                            $testJson = json_decode($combinedJson, true);
                            if (is_array($testJson) && !empty($testJson)) {
                                $matches[1] = $combinedJson;
                                $jsonFound = true;
                            }
                        }
                    }

                    if ($jsonFound && isset($matches[1])) {
                        try {
                            $expenseFundBreakdown = json_decode($matches[1], true);
                            if (is_array($expenseFundBreakdown) && !empty($expenseFundBreakdown)) {
                                \Log::debug('Parsed fund breakdown from expense', [
                                    'expense_id' => $expense->id,
                                    'expense_name' => $expense->expense_name,
                                    'expense_total_amount' => $expense->amount,
                                    'fund_breakdown' => $expenseFundBreakdown,
                                    'fund_breakdown_sum' => array_sum(array_column($expenseFundBreakdown, 'amount')),
                                    'fund_type_being_checked' => $originalTypeName
                                ]);

                                // Validate that fund breakdown amounts sum to expense amount (with tolerance for rounding)
                                $breakdownSum = array_sum(array_column($expenseFundBreakdown, 'amount'));
                                $difference = abs($breakdownSum - $expense->amount);
                                if ($difference > 1) { // Allow 1 TZS difference for rounding
                                    \Log::warning('Fund breakdown sum does not match expense amount', [
                                        'expense_id' => $expense->id,
                                        'expense_amount' => $expense->amount,
                                        'breakdown_sum' => $breakdownSum,
                                        'difference' => $difference
                                    ]);
                                }

                                foreach ($expenseFundBreakdown as $funding) {
                                    // Normalize offering types for comparison (case-insensitive, handle spaces/underscores)
                                    $fundingOfferingType = isset($funding['offering_type']) ?
                                        strtolower(trim(str_replace([' ', '-'], '_', $funding['offering_type']))) : '';
                                    $currentOfferingType = strtolower(trim(str_replace([' ', '-'], '_', $originalTypeName)));

                                    \Log::debug('Comparing offering types', [
                                        'funding_offering_type' => $fundingOfferingType,
                                        'current_offering_type' => $currentOfferingType,
                                        'match' => $fundingOfferingType === $currentOfferingType,
                                        'funding_amount' => $funding['amount'] ?? 'not set'
                                    ]);

                                    if (
                                        $fundingOfferingType === $currentOfferingType &&
                                        isset($funding['amount'])
                                    ) {
                                        $amount = floatval($funding['amount']);

                                        // CRITICAL: Use the amount from the fund breakdown, NOT the expense total
                                        // The amount in the fund breakdown is the actual amount used from this specific offering type
                                        // Do NOT use $expense->amount here - that's the total expense amount

                                        // Validate: amount must be positive
                                        // The amount in the JSON should be the actual amount used from this offering type
                                        if ($amount > 0) {
                                            // Check if we've already processed this expense for this fund type
                                            $expenseKey = $expense->id . '_' . $originalTypeName;
                                            if (!in_array($expenseKey, $processedExpenseIds)) {
                                                $usedFromExpenses += $amount;
                                                $processedExpenseIds[] = $expenseKey;

                                                // Store expense details for display
                                                $expenseDetails[] = [
                                                    'expense_id' => $expense->id,
                                                    'expense_name' => $expense->expense_name,
                                                    'expense_date' => $expense->expense_date,
                                                    'total_amount' => $expense->amount, // Total expense amount
                                                    'offering_amount' => $amount, // Actual amount used from THIS offering type
                                                    'budget_name' => $expense->budget->budget_name ?? 'No Budget',
                                                    'category' => $expense->expense_category
                                                ];

                                                \Log::info('Found expense funding for fund type in report', [
                                                    'expense_id' => $expense->id,
                                                    'expense_name' => $expense->expense_name,
                                                    'fund_type' => $originalTypeName,
                                                    'funding_offering_type' => $funding['offering_type'],
                                                    'amount_from_this_fund' => $amount, // Amount used from this fund type
                                                    'total_expense_amount' => $expense->amount, // Total expense amount
                                                    'total_used_from_expenses' => $usedFromExpenses
                                                ]);
                                            } else {
                                                \Log::warning('Duplicate expense detected for fund type', [
                                                    'expense_id' => $expense->id,
                                                    'fund_type' => $originalTypeName,
                                                    'amount' => $amount
                                                ]);
                                            }
                                        } else {
                                            \Log::warning('Invalid amount in fund breakdown', [
                                                'expense_id' => $expense->id,
                                                'fund_type' => $originalTypeName,
                                                'amount' => $amount,
                                                'expense_amount' => $expense->amount
                                            ]);
                                        }
                                    }
                                }
                            } else {
                                \Log::warning('Parsed JSON is not a valid array', [
                                    'expense_id' => $expense->id,
                                    'parsed_result' => $expenseFundBreakdown
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Failed to parse additional funding from expense approval_notes in report', [
                                'expense_id' => $expense->id,
                                'expense_name' => $expense->expense_name,
                                'fund_type' => $originalTypeName,
                                'approval_notes' => substr($expense->approval_notes, 0, 500),
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    } else {
                        \Log::debug('Could not extract JSON from expense approval_notes', [
                            'expense_id' => $expense->id,
                            'expense_name' => $expense->expense_name,
                            'approval_notes' => substr($expense->approval_notes, 0, 500),
                            'has_fund_allocation_text' => (strpos($expense->approval_notes, 'Fund allocation') !== false ||
                                strpos($expense->approval_notes, 'additional funding') !== false)
                        ]);
                    }
                }
            }

            // Debug: Log detailed information for expenses with this fund type
            if (strtolower($originalTypeName) === 'general') {
                \Log::info('Debug: Detailed expense check for general fund', [
                    'fund_type' => $originalTypeName,
                    'total_all_expenses' => $allExpenses->count(),
                    'expenses_with_notes' => $allExpenses->whereNotNull('approval_notes')->count(),
                    'expenses_detail' => $allExpenses->map(function ($e) {
                        $hasFundAllocation = false;
                        $fundBreakdownPreview = null;
                        if ($e->approval_notes) {
                            $hasFundAllocation = (strpos($e->approval_notes, 'Fund allocation') !== false ||
                                strpos($e->approval_notes, 'additional funding') !== false);
                            if ($hasFundAllocation) {
                                // Try to extract JSON
                                if (preg_match('/:\s*(\[.*?\])/s', $e->approval_notes, $matches)) {
                                    $fundBreakdownPreview = $matches[1];
                                }
                            }
                        }
                        return [
                            'id' => $e->id,
                            'name' => $e->expense_name,
                            'status' => $e->status,
                            'approval_status' => $e->approval_status,
                            'has_notes' => !empty($e->approval_notes),
                            'has_fund_allocation' => $hasFundAllocation,
                            'notes_preview' => $e->approval_notes ? substr($e->approval_notes, 0, 150) : null,
                            'fund_breakdown_preview' => $fundBreakdownPreview
                        ];
                    })->toArray(),
                    'found_expense_details_count' => count($expenseDetails),
                    'used_from_expenses' => $usedFromExpenses
                ]);
            }

            \Log::info('Fund breakdown calculation', [
                'fund_type' => $originalTypeName,
                'offering_amount' => $offeringIncome,
                'donation_amount' => $donationIncome,
                'total_income' => $totalIncome,
                'used_from_allocations' => $usedFromAllocations,
                'used_from_expenses' => $usedFromExpenses,
                'total_used' => $usedFromAllocations + $usedFromExpenses,
                'all_expenses_count' => $allExpenses->count(),
                'expenses_with_fund_breakdown' => count($expenseDetails)
            ]);

            // Total used amount = allocations + expenses with additional funding + expenses from budget allocations (fallback)
            $usedAmount = $usedFromAllocations + $usedFromExpenses + $usedFromBudgetExpenses;

            // Merge expense details
            $allExpenseDetails = array_merge($expenseDetails, $budgetExpenseDetails);

            // Calculate available amount
            $availableAmount = $totalIncome - $usedAmount;

            // Calculate utilization percentage
            $utilizationPercentage = $totalIncome > 0 ? round(($usedAmount / $totalIncome) * 100, 2) : 0;

            $fundBreakdown[] = [
                'fund_type' => $originalTypeName,
                'offering_type' => $originalTypeName, // Keep for backward compatibility
                'display_name' => ucfirst(str_replace('_', ' ', $originalTypeName)),
                'total_income' => $totalIncome,
                'offering_amount' => $offeringIncome,
                'donation_amount' => $donationIncome,
                'used_amount' => $usedAmount,
                'used_from_allocations' => $usedFromAllocations,
                'used_from_expenses' => $usedFromExpenses + $usedFromBudgetExpenses,
                'expense_details' => $allExpenseDetails, // Detailed breakdown of expenses (from approval_notes + budget allocations)
                'available_amount' => $availableAmount,
                'utilization_percentage' => $utilizationPercentage,
                'status' => $availableAmount > 0 ? 'available' : 'depleted'
            ];
        }

        // Sort by total income descending
        usort($fundBreakdown, function ($a, $b) {
            return $b['total_income'] <=> $a['total_income'];
        });

        // Calculate totals
        $totalIncome = array_sum(array_column($fundBreakdown, 'total_income'));
        $totalUsed = array_sum(array_column($fundBreakdown, 'used_amount'));
        $totalAvailable = array_sum(array_column($fundBreakdown, 'available_amount'));

        return view('finance.reports.offering-fund-breakdown', compact(
            'fundBreakdown',
            'totalIncome',
            'totalUsed',
            'totalAvailable',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export report (handles dynamic format: pdf or excel)
     */
    public function exportReport(Request $request, $format)
    {
        if ($format === 'pdf') {
            return $this->exportPdf($request);
        } elseif ($format === 'excel') {
            return $this->exportExcel($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid export format. Use pdf or excel.'
        ], 400);
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $reportType = $request->get('report_type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $filterType = $request->get('filter_type');
        $month = $request->get('month');

        // Validate report type
        $validReportTypes = [
            'income-vs-expenditure',
            'member-giving',
            'department-giving',
            'budget-performance',
            'offering-fund-breakdown',
            'monthly-financial',
            'weekly-financial'
        ];

        if (!$reportType || !in_array($reportType, $validReportTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid report type. Valid types: ' . implode(', ', $validReportTypes)
            ], 400);
        }

        // Parse dates safely
        try {
            // Check if month filter is selected
            if ($filterType === 'month' && $month) {
                $start = Carbon::parse($month . '-01')->startOfMonth();
                $end = Carbon::parse($month . '-01')->endOfMonth();
            } else {
                $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfYear();
                $end = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfYear();
                if ($start->gt($end)) {
                    $tmp = $start;
                    $start = $end;
                    $end = $tmp;
                }
            }
        } catch (\Exception $e) {
            \Log::error('exportPdf date parse error: ' . $e->getMessage());
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        }

        // Route to appropriate report based on type
        switch ($reportType) {
            case 'income-vs-expenditure':
                return $this->exportIncomeVsExpenditurePdf($start, $end, $filterType, $month);

            case 'member-giving':
                return $this->exportMemberGivingPdf($request, $start, $end);

            case 'department-giving':
                return $this->exportDepartmentGivingPdf($start, $end);

            case 'budget-performance':
                return $this->exportBudgetPerformancePdf($request, $start, $end);

            case 'offering-fund-breakdown':
                return $this->exportOfferingFundBreakdownPdf($start, $end);

            case 'monthly-financial':
                return $this->exportMonthlyFinancialPdf($start, $end);

            case 'weekly-financial':
                return $this->exportWeeklyFinancialPdf($start, $end);

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'PDF export not yet implemented for this report type'
                ], 501);
        }
    }

    /**
     * Export Income vs Expenditure PDF
     */
    private function exportIncomeVsExpenditurePdf($start, $end, $filterType = null, $month = null)
    {
        // Build data for the report similar to the on-screen report
        $tithes = Tithe::whereBetween('tithe_date', [$start, $end])->sum('amount');
        $offerings = Offering::whereBetween('offering_date', [$start, $end])->sum('amount');
        $donations = Donation::whereBetween('donation_date', [$start, $end])->sum('amount');
        $pledgePayments = Pledge::whereBetween('updated_at', [$start, $end])->sum('amount_paid');
        $totalIncome = $tithes + $offerings + $donations + $pledgePayments;

        $expenses = Expense::whereBetween('expense_date', [$start, $end])
            ->where('status', 'paid')
            ->get();
        $totalExpenses = $expenses->sum('amount');

        $expensesByCategory = $expenses->groupBy('expense_category')
            ->map(function ($categoryExpenses) {
                return [
                    'total' => $categoryExpenses->sum('amount'),
                    'count' => $categoryExpenses->count(),
                ];
            })
            ->sortByDesc('total');

        // Monthly breakdown
        $monthlyData = [];
        $current = $start->copy()->startOfMonth();
        $endMonth = $end->copy()->endOfMonth();
        while ($current->lte($endMonth)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            $monthTithes = Tithe::whereBetween('tithe_date', [$monthStart, $monthEnd])->sum('amount');
            $monthOfferings = Offering::whereBetween('offering_date', [$monthStart, $monthEnd])->sum('amount');
            $monthDonations = Donation::whereBetween('donation_date', [$monthStart, $monthEnd])->sum('amount');
            $monthPledgePayments = Pledge::whereBetween('updated_at', [$monthStart, $monthEnd])->sum('amount_paid');
            $monthExpenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->where('status', 'paid')
                ->sum('amount');
            $monthlyData[] = [
                'month' => $current->format('M Y'),
                'income' => $monthTithes + $monthOfferings + $monthDonations + $monthPledgePayments,
                'expenses' => $monthExpenses,
                'net' => ($monthTithes + $monthOfferings + $monthDonations + $monthPledgePayments) - $monthExpenses,
            ];
            $current->addMonth();
        }

        $netIncome = $totalIncome - $totalExpenses;

        $filename = 'income-vs-expenditure-report-' . ($month ? $month : $start->format('Y-m-d') . '-to-' . $end->format('Y-m-d')) . '.html';

        return response()->view('finance.reports.pdf.income-vs-expenditure', [
            'reportType' => 'income-vs-expenditure',
            'start' => $start,
            'end' => $end,
            'tithes' => $tithes,
            'offerings' => $offerings,
            'donations' => $donations,
            'pledgePayments' => $pledgePayments,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netIncome' => $netIncome,
            'expensesByCategory' => $expensesByCategory,
            'monthlyData' => $monthlyData,
        ])->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export Member Giving PDF
     */
    private function exportMemberGivingPdf($request, $start, $end)
    {
        $memberId = $request->get('member_id');
        if (!$memberId) {
            return response()->json([
                'success' => false,
                'message' => 'Member ID is required for member giving report'
            ], 400);
        }

        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        $member = Member::findOrFail($memberId);

        // Get member's financial data (only approved records)
        $tithes = Tithe::where('member_id', $memberId)
            ->where('approval_status', 'approved')
            ->whereBetween('tithe_date', [$startDate, $endDate])
            ->orderBy('tithe_date', 'desc')
            ->get();

        $offerings = Offering::where('member_id', $memberId)
            ->where('approval_status', 'approved')
            ->whereBetween('offering_date', [$startDate, $endDate])
            ->orderBy('offering_date', 'desc')
            ->get();

        $donations = Donation::where('member_id', $memberId)
            ->where('approval_status', 'approved')
            ->whereBetween('donation_date', [$startDate, $endDate])
            ->orderBy('donation_date', 'desc')
            ->get();

        $pledges = Pledge::where('member_id', $memberId)
            ->whereBetween('pledge_date', [$startDate, $endDate])
            ->orderBy('pledge_date', 'desc')
            ->get();

        // Calculate totals
        $totalTithes = $tithes->sum('amount');
        $totalOfferings = $offerings->sum('amount');
        $totalDonations = $donations->sum('amount');
        $totalPledged = $pledges->sum('pledge_amount');
        $totalPaid = $pledges->sum('amount_paid');
        $totalGiving = $totalTithes + $totalOfferings + $totalDonations;

        // Monthly breakdown
        $monthlyData = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthTithes = Tithe::where('member_id', $memberId)
                ->where('approval_status', 'approved')
                ->whereBetween('tithe_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $monthOfferings = Offering::where('member_id', $memberId)
                ->where('approval_status', 'approved')
                ->whereBetween('offering_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $monthDonations = Donation::where('member_id', $memberId)
                ->where('approval_status', 'approved')
                ->whereBetween('donation_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $monthlyData[] = [
                'month' => $current->format('M Y'),
                'tithes' => $monthTithes,
                'offerings' => $monthOfferings,
                'donations' => $monthDonations,
                'total' => $monthTithes + $monthOfferings + $monthDonations
            ];

            $current->addMonth();
        }

        $filename = 'member-giving-report-' . $member->member_id . '-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.html';

        return response()->view('finance.reports.pdf.member-giving', compact(
            'member',
            'tithes',
            'offerings',
            'donations',
            'pledges',
            'totalTithes',
            'totalOfferings',
            'totalDonations',
            'totalPledged',
            'totalPaid',
            'totalGiving',
            'monthlyData',
            'startDate',
            'endDate'
        ))->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export Department Giving PDF
     */
    private function exportDepartmentGivingPdf($start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        // Get combined data by purpose (combines pledges, offerings, and donations)
        $combinedByPurpose = $this->getCombinedByPurpose($startDate, $endDate);

        // Also get individual breakdowns for reference
        $offeringTypes = Offering::whereBetween('offering_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->select('offering_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('offering_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        $donationTypes = Donation::whereBetween('donation_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->select('donation_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('donation_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        $pledgeTypes = Pledge::whereBetween('pledge_date', [$startDate, $endDate])
            ->select(
                'pledge_type',
                DB::raw('SUM(pledge_amount) as total_pledged'),
                DB::raw('SUM(amount_paid) as total_paid'),
                DB::raw('COUNT(*) as pledge_count')
            )
            ->groupBy('pledge_type')
            ->orderBy('total_pledged', 'desc')
            ->get();

        $filename = 'department-giving-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.html';

        return response()->view('finance.reports.pdf.department-giving', compact(
            'combinedByPurpose',
            'offeringTypes',
            'donationTypes',
            'pledgeTypes',
            'startDate',
            'endDate'
        ))->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export Budget Performance PDF (placeholder - to be implemented)
     */
    private function exportBudgetPerformancePdf($request, $start, $end)
    {
        $budgetId = $request->get('budget_id');
        if (!$budgetId) {
            return response()->json([
                'success' => false,
                'message' => 'Budget ID is required for budget performance report'
            ], 400);
        }

        // For now, redirect to the regular view
        return redirect()->route('reports.budget-performance', [
            'budget_id' => $budgetId,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d')
        ]);
    }

    /**
     * Export Offering Fund Breakdown PDF (placeholder - to be implemented)
     */
    private function exportOfferingFundBreakdownPdf($start, $end)
    {
        // For now, redirect to the regular view
        return redirect()->route('reports.offering-fund-breakdown', [
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d')
        ]);
    }

    /**
     * Export Monthly Financial Report PDF
     */
    private function exportMonthlyFinancialPdf($start, $end)
    {
        // Use the same data structure as the regular report
        $month = $start->format('Y-m');

        // Income Sources
        $tithes = Tithe::whereBetween('tithe_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->get();
        $totalTithes = $tithes->sum('amount');
        $tithesCount = $tithes->count();

        $offerings = Offering::whereBetween('offering_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->get();
        $totalOfferings = $offerings->sum('amount');
        $offeringsCount = $offerings->count();
        $offeringsByType = $offerings->groupBy('offering_type')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $donations = Donation::whereBetween('donation_date', [$start, $end])
            ->where('approval_status', 'approved')
            ->get();
        $totalDonations = $donations->sum('amount');
        $donationsCount = $donations->count();
        $donationsByType = $donations->groupBy('donation_type')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $pledgePayments = Pledge::whereBetween('updated_at', [$start, $end])
            ->where('amount_paid', '>', 0)
            ->get();
        $totalPledgePayments = $pledgePayments->sum('amount_paid');
        $pledgePaymentsCount = $pledgePayments->count();

        $totalIncome = $totalTithes + $totalOfferings + $totalDonations + $totalPledgePayments;

        // Expenses
        $expenses = Expense::whereBetween('expense_date', [$start, $end])
            ->where('status', 'paid')
            ->get();
        $totalExpenses = $expenses->sum('amount');
        $expensesCount = $expenses->count();
        $expensesByCategory = $expenses->groupBy('expense_category')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $netIncome = $totalIncome - $totalExpenses;

        // Daily breakdown
        $dailyData = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $dayTithes = Tithe::whereDate('tithe_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayOfferings = Offering::whereDate('offering_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayDonations = Donation::whereDate('donation_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayExpenses = Expense::whereDate('expense_date', $current->format('Y-m-d'))
                ->where('status', 'paid')
                ->sum('amount');

            if ($dayTithes > 0 || $dayOfferings > 0 || $dayDonations > 0 || $dayExpenses > 0) {
                $dailyData[] = [
                    'date' => $current->format('d M Y'),
                    'day' => $current->format('D'),
                    'income' => $dayTithes + $dayOfferings + $dayDonations,
                    'expenses' => $dayExpenses,
                    'net' => ($dayTithes + $dayOfferings + $dayDonations) - $dayExpenses
                ];
            }
            $current->addDay();
        }

        // Top contributors
        $topContributors = Member::select(
            'members.id',
            'members.full_name',
            'members.member_id',
            DB::raw('(
                    COALESCE((SELECT SUM(amount) FROM tithes WHERE tithes.member_id = members.id AND tithes.approval_status = "approved" AND tithes.tithe_date BETWEEN "' . $start->format('Y-m-d') . '" AND "' . $end->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(amount) FROM offerings WHERE offerings.member_id = members.id AND offerings.approval_status = "approved" AND offerings.offering_date BETWEEN "' . $start->format('Y-m-d') . '" AND "' . $end->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(amount) FROM donations WHERE donations.member_id = members.id AND donations.approval_status = "approved" AND donations.donation_date BETWEEN "' . $start->format('Y-m-d') . '" AND "' . $end->format('Y-m-d') . '"), 0)
                ) as total_giving')
        )
            ->having('total_giving', '>', 0)
            ->orderByDesc('total_giving')
            ->limit(20)
            ->get();

        $filename = 'monthly-financial-report-' . $month . '.html';

        return response()->view('finance.reports.pdf.monthly-financial', compact(
            'start',
            'end',
            'month',
            'totalTithes',
            'tithesCount',
            'totalOfferings',
            'offeringsCount',
            'offeringsByType',
            'totalDonations',
            'donationsCount',
            'donationsByType',
            'totalPledgePayments',
            'pledgePaymentsCount',
            'totalIncome',
            'totalExpenses',
            'expensesCount',
            'expensesByCategory',
            'netIncome',
            'dailyData',
            'topContributors'
        ))->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export Weekly Financial Report PDF
     */
    private function exportWeeklyFinancialPdf($start, $end)
    {
        // Calculate week start and end from the provided dates
        $startDate = Carbon::parse($start)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        // Income Sources
        $tithes = Tithe::whereBetween('tithe_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalTithes = $tithes->sum('amount');
        $tithesCount = $tithes->count();

        $offerings = Offering::whereBetween('offering_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalOfferings = $offerings->sum('amount');
        $offeringsCount = $offerings->count();
        $offeringsByType = $offerings->groupBy('offering_type')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $donations = Donation::whereBetween('donation_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalDonations = $donations->sum('amount');
        $donationsCount = $donations->count();
        $donationsByType = $donations->groupBy('donation_type')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $pledgePayments = Pledge::whereBetween('updated_at', [$startDate, $endDate])
            ->where('amount_paid', '>', 0)
            ->get();
        $totalPledgePayments = $pledgePayments->sum('amount_paid');
        $pledgePaymentsCount = $pledgePayments->count();

        $totalIncome = $totalTithes + $totalOfferings + $totalDonations + $totalPledgePayments;

        // Expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->get();
        $totalExpenses = $expenses->sum('amount');
        $expensesCount = $expenses->count();
        $expensesByCategory = $expenses->groupBy('expense_category')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $netIncome = $totalIncome - $totalExpenses;

        // Daily breakdown for the week
        $dailyData = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dayTithes = Tithe::whereDate('tithe_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayOfferings = Offering::whereDate('offering_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayDonations = Donation::whereDate('donation_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayExpenses = Expense::whereDate('expense_date', $current->format('Y-m-d'))
                ->where('status', 'paid')
                ->sum('amount');

            $dailyData[] = [
                'date' => $current->format('d M Y'),
                'day' => $current->format('D'),
                'income' => $dayTithes + $dayOfferings + $dayDonations,
                'expenses' => $dayExpenses,
                'net' => ($dayTithes + $dayOfferings + $dayDonations) - $dayExpenses
            ];
            $current->addDay();
        }

        // Top contributors for the week
        $topContributors = Member::select(
            'members.id',
            'members.full_name',
            'members.member_id',
            DB::raw('(
                    COALESCE((SELECT SUM(amount) FROM tithes WHERE tithes.member_id = members.id AND tithes.approval_status = "approved" AND tithes.tithe_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(amount) FROM offerings WHERE offerings.member_id = members.id AND offerings.approval_status = "approved" AND offerings.offering_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(amount) FROM donations WHERE donations.member_id = members.id AND donations.approval_status = "approved" AND donations.donation_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                ) as total_giving')
        )
            ->having('total_giving', '>', 0)
            ->orderByDesc('total_giving')
            ->limit(20)
            ->get();

        // Use PDF-specific view (without sidebar/topbar)
        $filename = 'weekly-financial-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.html';

        return response()->view('finance.reports.pdf.weekly-financial', compact(
            'startDate',
            'endDate',
            'totalTithes',
            'tithesCount',
            'totalOfferings',
            'offeringsCount',
            'offeringsByType',
            'totalDonations',
            'donationsCount',
            'donationsByType',
            'totalPledgePayments',
            'pledgePaymentsCount',
            'totalIncome',
            'totalExpenses',
            'expensesCount',
            'expensesByCategory',
            'netIncome',
            'dailyData',
            'topContributors'
        ))->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate comprehensive monthly financial report
     */
    public function monthlyFinancialReport(Request $request)
    {
        try {
            $month = $request->get('month', Carbon::now()->format('Y-m'));
            $startDate = Carbon::parse($month . '-01')->startOfMonth();
            $endDate = Carbon::parse($month . '-01')->endOfMonth();
        } catch (\Exception $e) {
            \Log::error('Date parsing error in monthlyFinancialReport: ' . $e->getMessage());
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // Income Sources
        $tithes = Tithe::whereBetween('tithe_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalTithes = $tithes->sum('amount');
        $tithesCount = $tithes->count();

        $offerings = Offering::whereBetween('offering_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalOfferings = $offerings->sum('amount');
        $offeringsCount = $offerings->count();
        $offeringsByType = $offerings->groupBy('offering_type')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $donations = Donation::whereBetween('donation_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalDonations = $donations->sum('amount');
        $donationsCount = $donations->count();
        $donationsByType = $donations->groupBy('donation_type')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $pledgePayments = Pledge::whereBetween('updated_at', [$startDate, $endDate])
            ->where('amount_paid', '>', 0)
            ->get();
        $totalPledgePayments = $pledgePayments->sum('amount_paid');
        $pledgePaymentsCount = $pledgePayments->count();

        $totalIncome = $totalTithes + $totalOfferings + $totalDonations + $totalPledgePayments;

        // Expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->get();
        $totalExpenses = $expenses->sum('amount');
        $expensesCount = $expenses->count();
        $expensesByCategory = $expenses->groupBy('expense_category')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $netIncome = $totalIncome - $totalExpenses;

        // Daily breakdown
        $dailyData = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dayTithes = Tithe::whereDate('tithe_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayOfferings = Offering::whereDate('offering_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayDonations = Donation::whereDate('donation_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayExpenses = Expense::whereDate('expense_date', $current->format('Y-m-d'))
                ->where('status', 'paid')
                ->sum('amount');

            if ($dayTithes > 0 || $dayOfferings > 0 || $dayDonations > 0 || $dayExpenses > 0) {
                $dailyData[] = [
                    'date' => $current->format('d M Y'),
                    'day' => $current->format('D'),
                    'income' => $dayTithes + $dayOfferings + $dayDonations,
                    'expenses' => $dayExpenses,
                    'net' => ($dayTithes + $dayOfferings + $dayDonations) - $dayExpenses
                ];
            }
            $current->addDay();
        }

        // Top contributors
        $topContributors = Member::select(
            'members.id',
            'members.full_name',
            'members.member_id',
            DB::raw('(
                    COALESCE((SELECT SUM(amount) FROM tithes WHERE tithes.member_id = members.id AND tithes.approval_status = "approved" AND tithes.tithe_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(amount) FROM offerings WHERE offerings.member_id = members.id AND offerings.approval_status = "approved" AND offerings.offering_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(amount) FROM donations WHERE donations.member_id = members.id AND donations.approval_status = "approved" AND donations.donation_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                ) as total_giving')
        )
            ->having('total_giving', '>', 0)
            ->orderByDesc('total_giving')
            ->limit(20)
            ->get();

        return view('finance.reports.monthly-financial', compact(
            'startDate',
            'endDate',
            'month',
            'totalTithes',
            'tithesCount',
            'totalOfferings',
            'offeringsCount',
            'offeringsByType',
            'totalDonations',
            'donationsCount',
            'donationsByType',
            'totalPledgePayments',
            'pledgePaymentsCount',
            'totalIncome',
            'totalExpenses',
            'expensesCount',
            'expensesByCategory',
            'netIncome',
            'dailyData',
            'topContributors'
        ));
    }

    /**
     * Generate comprehensive weekly financial report
     */
    public function weeklyFinancialReport(Request $request)
    {
        try {
            $weekStart = $request->get('week_start', Carbon::now()->startOfWeek()->format('Y-m-d'));
            $startDate = Carbon::parse($weekStart)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
        } catch (\Exception $e) {
            \Log::error('Date parsing error in weeklyFinancialReport: ' . $e->getMessage());
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        // Income Sources
        $tithes = Tithe::whereBetween('tithe_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalTithes = $tithes->sum('amount');
        $tithesCount = $tithes->count();

        $offerings = Offering::whereBetween('offering_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalOfferings = $offerings->sum('amount');
        $offeringsCount = $offerings->count();
        $offeringsByType = $offerings->groupBy('offering_type')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $donations = Donation::whereBetween('donation_date', [$startDate, $endDate])
            ->where('approval_status', 'approved')
            ->get();
        $totalDonations = $donations->sum('amount');
        $donationsCount = $donations->count();
        $donationsByType = $donations->groupBy('donation_type')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $pledgePayments = Pledge::whereBetween('updated_at', [$startDate, $endDate])
            ->where('amount_paid', '>', 0)
            ->get();
        $totalPledgePayments = $pledgePayments->sum('amount_paid');
        $pledgePaymentsCount = $pledgePayments->count();

        $totalIncome = $totalTithes + $totalOfferings + $totalDonations + $totalPledgePayments;

        // Expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->get();
        $totalExpenses = $expenses->sum('amount');
        $expensesCount = $expenses->count();
        $expensesByCategory = $expenses->groupBy('expense_category')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('total');

        $netIncome = $totalIncome - $totalExpenses;

        // Daily breakdown for the week
        $dailyData = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dayTithes = Tithe::whereDate('tithe_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayOfferings = Offering::whereDate('offering_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayDonations = Donation::whereDate('donation_date', $current->format('Y-m-d'))
                ->where('approval_status', 'approved')
                ->sum('amount');
            $dayExpenses = Expense::whereDate('expense_date', $current->format('Y-m-d'))
                ->where('status', 'paid')
                ->sum('amount');

            $dailyData[] = [
                'date' => $current->format('d M Y'),
                'day' => $current->format('D'),
                'income' => $dayTithes + $dayOfferings + $dayDonations,
                'expenses' => $dayExpenses,
                'net' => ($dayTithes + $dayOfferings + $dayDonations) - $dayExpenses
            ];
            $current->addDay();
        }

        // Top contributors for the week
        $topContributors = Member::select(
            'members.id',
            'members.full_name',
            'members.member_id',
            DB::raw('(
                    COALESCE((SELECT SUM(amount) FROM tithes WHERE tithes.member_id = members.id AND tithes.approval_status = "approved" AND tithes.tithe_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(amount) FROM offerings WHERE offerings.member_id = members.id AND offerings.approval_status = "approved" AND offerings.offering_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                    + COALESCE((SELECT SUM(amount) FROM donations WHERE donations.member_id = members.id AND donations.approval_status = "approved" AND donations.donation_date BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '"), 0)
                ) as total_giving')
        )
            ->having('total_giving', '>', 0)
            ->orderByDesc('total_giving')
            ->limit(20)
            ->get();

        return view('finance.reports.weekly-financial', compact(
            'startDate',
            'endDate',
            'totalTithes',
            'tithesCount',
            'totalOfferings',
            'offeringsCount',
            'offeringsByType',
            'totalDonations',
            'donationsCount',
            'donationsByType',
            'totalPledgePayments',
            'pledgePaymentsCount',
            'totalIncome',
            'totalExpenses',
            'expensesCount',
            'expensesByCategory',
            'netIncome',
            'dailyData',
            'topContributors'
        ));
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $reportType = $request->get('report_type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // This would integrate with Laravel Excel (Maatwebsite)
        // For now, return a placeholder response
        return response()->json([
            'message' => 'Excel export functionality will be implemented with Laravel Excel',
            'report_type' => $reportType,
            'date_range' => $startDate . ' to ' . $endDate
        ]);
    }

    /**
     * Generate member giving receipt
     */
    public function generateMemberReceipt($memberId, Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear());
        $endDate = $request->get('end_date', Carbon::now()->endOfYear());

        $member = Member::findOrFail($memberId);

        // Get member's financial data for the period
        $tithes = Tithe::where('member_id', $memberId)
            ->whereBetween('tithe_date', [$startDate, $endDate])
            ->orderBy('tithe_date', 'desc')
            ->get();

        $offerings = Offering::where('member_id', $memberId)
            ->whereBetween('offering_date', [$startDate, $endDate])
            ->orderBy('offering_date', 'desc')
            ->get();

        $donations = Donation::where('member_id', $memberId)
            ->whereBetween('donation_date', [$startDate, $endDate])
            ->orderBy('donation_date', 'desc')
            ->get();

        $pledges = Pledge::where('member_id', $memberId)
            ->whereBetween('pledge_date', [$startDate, $endDate])
            ->orderBy('pledge_date', 'desc')
            ->get();

        // Calculate totals
        $totalTithes = $tithes->sum('amount');
        $totalOfferings = $offerings->sum('amount');
        $totalDonations = $donations->sum('amount');
        $totalPledged = $pledges->sum('pledge_amount');
        $totalPaid = $pledges->sum('amount_paid');
        $totalGiving = $totalTithes + $totalOfferings + $totalDonations;

        // Church information (used in member receipt header)
        $churchInfo = [
            'name' => 'KKKT Ushirika wa Longuo',
            'address' => 'P.O. Box 8765, Moshi, Kilimanjaro, Tanzania',
            'phone' => '+255 756 330 509',
            'email' => 'info@wauminilink.org',
            'website' => 'www.wauminilink.org'
        ];

        return view('finance.reports.member-receipt', compact(
            'member',
            'tithes',
            'offerings',
            'donations',
            'pledges',
            'totalTithes',
            'totalOfferings',
            'totalDonations',
            'totalPledged',
            'totalPaid',
            'totalGiving',
            'startDate',
            'endDate',
            'churchInfo'
        ));
    }
}
