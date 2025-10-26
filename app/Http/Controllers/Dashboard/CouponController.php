<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::whereNull('company_id')
            ->withCount([
                'usages as total_usage' => function ($query) {
                    $query->select(DB::raw("count(*)"));
                },
                'usages as unique_users' => function ($query) {
                    $query->select(DB::raw("count(distinct user_id)"));
                },
            ])
            ->get()
            ->map(function ($coupon) {
                // ? remaining usage
                $coupon->remaining_usage = max(0, $coupon->usage_limit - $coupon->total_usage);

                // ? usage percentage
                $coupon->usage_percentage = $coupon->usage_limit > 0
                    ? round(($coupon->total_usage / $coupon->usage_limit) * 100, 2)
                    : 0;

                // ? Check if the coupon is expired
                $coupon->is_expired = $coupon->end_date && $coupon->end_date < now();

                return $coupon;
            });
        $companys = Company::all();
      

        return view('dashboard.coupon.index', compact('coupons', 'companys'));
    }



    public function create()
    {
        $companys = Company::all();
        return view('dashboard.coupon.store', compact('companys'));
    }

    public function store(Request $request)
    {
        // ? تحديد نوع الفورم
        $isCompanyCoupon = $request->filled('company');
        if ($isCompanyCoupon) {
            //? Validation للشركة والكوبونات
            $validatedData = $request->validate([
                'company' => 'sometimes|required|exists:companies,id', // ID الشركة
                'coupon_number' => 'sometimes|required|integer|min:1',
                'num_code' => 'sometimes|required|integer|min:5',
                'type' => 'required|in:discount,free_games',
                'value' => 'nullable|required_if:type,discount|numeric',
                'discount_type' => 'nullable|required_if:type,discount|in:percentage,fixed',
                'total_games' => 'nullable|required_if:type,free_games|integer',
                'usage_limit' => 'required|integer',
                'usage_per_user' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'user_id' => 'exists:users,id',
                'batch' => 'nullable|unique:coupons,batch|string|max:255',
            ]);

            //? إنشاء عدد الكوبونات المطلوبة
            for ($i = 0; $i < $validatedData['coupon_number']; $i++) {
                $coupon = new Coupon();
                $coupon->company_id = $validatedData['company']; //? ربط بالكومباني
                $coupon->type = $validatedData['type'];

                if ($validatedData['type'] === 'discount') {
                    $coupon->value = $validatedData['value'];
                    $coupon->discount_type = $validatedData['discount_type'];
                } else {
                    $coupon->total_games = $validatedData['total_games'];
                }

                $coupon->usage_limit = $validatedData['usage_limit'];
                $coupon->usage_per_user = $validatedData['usage_per_user'];
               $coupon->start_date = $validatedData['start_date'];
               $coupon->end_date = $validatedData['end_date'];
             
                $coupon->user_id = $validatedData['user_id'];
                $coupon->code = strtoupper(Str::random($validatedData['num_code'])); // توليد كود عشوائي لكل كوبون
                $coupon->batch = $validatedData['batch'];
                $coupon->save();
            }

        } else {
            //? Validation للفورم العادي
            $validatedData = $request->validate([
                'code' => 'required|unique:coupons',
                'type' => 'required|in:discount,free_games',
                'value' => 'nullable|required_if:type,discount|numeric',
                'discount_type' => 'nullable|required_if:type,discount|in:percentage,fixed',
                'total_games' => 'nullable|required_if:type,free_games|integer',
                'usage_limit' => 'required|integer',
                'usage_per_user' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'user_id' => 'exists:users,id',
            ]);

            $coupon = new Coupon();
            $coupon->code = $validatedData['code'];
            $coupon->type = $validatedData['type'];
            $coupon->company_id = null; //? الفورم العادي بدون شركة

            if ($validatedData['type'] === 'discount') {
                $coupon->value = $validatedData['value'];
                $coupon->discount_type = $validatedData['discount_type'];
            } else {
                $coupon->total_games = $validatedData['total_games'];
            }

            $coupon->usage_limit = $validatedData['usage_limit'];
            $coupon->usage_per_user = $validatedData['usage_per_user'];
           $coupon->start_date = $validatedData['start_date'];
           $coupon->end_date = $validatedData['end_date'];
     
            $coupon->user_id = $validatedData['user_id'];
            $coupon->save();
        }

        session()->flash('Add', 'تم اضافة القسيمة بنجاح');
        return redirect()->route('coupons')->with('success', 'Coupon created successfully.');
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|unique:coupons,code,' . $request->id,
            'type' => 'required|in:discount,free_games',
            'value' => 'nullable|required_if:type,discount|numeric',
            'discount_type' => 'nullable|required_if:type,discount|in:percentage,fixed,free_shipping,bogo',
            'total_games' => 'nullable|required_if:type,free_games|integer',
            'usage_limit' => 'required|integer',
            'usage_per_user' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'user_id' => 'exists:users,id',
        ], [
            'code.required' => 'حقل الكود مطلوب',
            'code.unique' => 'قيمة الكود موجودة مسبقاً',
            'type.required' => 'حقل نوع القسيمة مطلوب',
            'value.required_if' => 'حقل قيمة الخصم مطلوب للقسائم الخصم',
            'value.numeric' => 'حقل قيمة الخصم يجب أن يكون رقمي',
            'discount_type.required_if' => 'حقل نوع الخصم مطلوب للقسائم الخصم',
            'total_games.required_if' => 'حقل عدد الألعاب مطلوب للقسائم المجانية',
            'total_games.integer' => 'حقل عدد الألعاب يجب أن يكون رقمياً',
            'usage_limit.required' => 'حقل الحد الأقصى للاستخدام مطلوب',
            'usage_per_user.required' => 'حقل الحد الأقصى للاستخدام لكل مستخدم مطلوب',
            'start_date.required' => 'حقل تاريخ البدء مطلوب',
            'start_date.date' => 'حقل تاريخ البدء يجب أن يكون تاريخاً',
            'end_date.required' => 'حقل تاريخ الانتهاء مطلوب',
            'end_date.date' => 'حقل تاريخ الانتهاء يجب أن يكون تاريخاً',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'user_id.exists' => 'رقم المستخدم غير موجود'
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $validatedData['code'];
        $coupon->type = $validatedData['type'];

        if ($validatedData['type'] === 'discount') {
            $coupon->value = $validatedData['value'];
            $coupon->discount_type = $validatedData['discount_type'];
            $coupon->total_games = null;
        } elseif ($validatedData['type'] === 'free_games') {
            $coupon->total_games = $validatedData['total_games'];
            $coupon->value = null;
            $coupon->discount_type = null;
        }

        $coupon->usage_limit = $validatedData['usage_limit'];
        $coupon->usage_per_user = $validatedData['usage_per_user'];
        $coupon->start_date = Carbon::parse($validatedData['start_date'])->subHours(5);
        $coupon->end_date = Carbon::parse($validatedData['end_date']);
        $coupon->user_id = $validatedData['user_id'];
        $coupon->save();

        session()->flash('Add', 'تم تعديل القسيمة بنجاح ');
        return redirect()->route('coupons')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Request $request)
    {
        $coupon = Coupon::find($request->id);
        $coupon->delete();
        session()->flash('delete', 'تم حذف القسيمة بنجاح ');
        return redirect()->route('coupons')->with('success', 'Coupon deleted successfully');
    }
  
   public function batch(Request $request)
    {
        try {
            $batches = Coupon::where('company_id', $request->company_id)
                ->whereNotNull('batch')
                ->select('batch')
                ->groupBy('batch')
                ->pluck('batch'); 

            return response()->json([
                'status' => true,
                'batches' => $batches
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض القسائم: ' . $e->getMessage());
        }
    }
}
