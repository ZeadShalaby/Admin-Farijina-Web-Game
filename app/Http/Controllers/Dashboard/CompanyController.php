<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Coupon;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComapnyRequest;
use App\Http\Requests\UpdateComapnyRequest;

class CompanyController extends Controller
{
    //
    public function index()
    {
        $companies = Company::withCount([
            'coupons as total_coupons',
            'coupons as coupons_last_month' => function ($query) {
                $query->where('created_at', '>=', now()->subMonth());
            },
            'coupons as total_usages' => function ($query) {
                $query->join('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id');
            },
            'coupons as usages_last_month' => function ($query) {
                $query->join('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id')
                    ->where('coupon_usages.created_at', '>=', now()->subMonth());
            },
        ])->get();
      

        $bestCompany = $companies->sortByDesc('total_usages')->first();
        if ($bestCompany) {
            $bestCompany->usage_rate = $bestCompany->total_usages / max($bestCompany->total_coupons, 1);
        }

        // ثاني أفضل شركة من حيث الاستخدامات
        $secondBestCompany = $companies->sortByDesc('total_usages')->skip(1)->first();
        if ($secondBestCompany) {
            $secondBestCompany->usage_rate = $secondBestCompany->total_usages / max($secondBestCompany->total_coupons, 1);
        }

        $bestPartner = $companies->sortByDesc('total_coupons')->first();

        // شركات مميزة: أي شركة عندها معدل استخدام > 0
        $topCompaniesCount = $companies->filter(function ($company) {
            $usageRate = $company->total_usages / max($company->total_coupons, 1);
            return $usageRate > 0;
        })->count();

        // الشركات النشطة: أي شركة عندها كوبونات خلال آخر شهر > 1
        $activeCompaniesCount = $companies->filter(function ($company) {
            return $company->coupons_last_month > 1;
        })->count();


        $totalCompanies = $companies->count();
         
        return view('dashboard.companies.index', compact(
            'companies',
            'totalCompanies',
            'topCompaniesCount',
            'activeCompaniesCount',
            'bestCompany',
            'secondBestCompany',
            'bestPartner',
        ));
    }


    public function CompanyCoupon(Request $request, $company)
    {
        try {
            $companys = Company::all();
            $coupons = Coupon::where('company_id', $company)->get();
            return view('dashboard.coupon.index', compact('coupons', 'companys'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض القسائم: ' . $e->getMessage());
        }
    }

    public function store(StoreComapnyRequest $request)
    {
        try {
            $validatedData = $request->validated();
            Company::create($validatedData);
            session()->flash('Add', '   تمت اضافه الشركه بنجاح  : ' . $validatedData['name'] . ' بنجاح ');
            return redirect()->route('companies.index')->with('success', 'تمت اضافه الشركه بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الشركة: ' . $e->getMessage());
        }
    }


    public function update(UpdateComapnyRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $company = Company::findOrFail($request->id);
            $company->update($validatedData);
            session()->flash('edit', '  تم تعديل شركه : ' . $company->name . ' بنجاح ');
            return redirect()->route('companies.index')->with('success', 'تم تحديث الشركة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الشركة: ' . $e->getMessage());
        }
        // Logic to show the form for creating a new company
    }

    public function delete(Request $request)
    {
        try {
            $company = Company::findOrFail($request->id);
            session()->flash('delete', '  تم حذف شركه : ' . $company->name . ' بنجاح ');
            $company->delete();
            return redirect()->route('companies.index')->with('delete', 'تم حذف الشركة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الشركة: ' . $e->getMessage());
        }
        //? Logic to delete a company

    }


    public function analysis(Company $company)
    {
        $batches = $company->coupons()
            ->whereNotNull('batch')
            ->select('batch')
            ->groupBy('batch')
            ->get()
            ->map(function ($batch) use ($company) {
                $coupons = $company->coupons()->where('batch', $batch->batch)->get();

                return [
                    'batch' => $batch->batch,
                    'total_coupons' => $coupons->count(),
                    'usages' => $coupons->sum(fn($c) => $c->usages()->count()),
                    'last_month_usages' => $coupons->sum(
                        fn($c) => $c->usages()->where('created_at', '>=', now()->subMonth())->count()
                    ),
                    'usage_rate' => $coupons->count() > 0
                        ? round(($coupons->sum(fn($c) => $c->usages()->count()) / $coupons->count()) * 100, 2)
                        : 0,
                    'start_date' => $coupons->min('start_date')->format('Y-m-d'),
                    'end_date' => $coupons->max('end_date')->format('Y-m-d'),
                ];
            });

        return response()->json([
            'company' => $company->name,
            'batches' => $batches,
        ]);
    }






}
