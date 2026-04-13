# GoFull — ملخص جميع التعديلات

## 1. ربط ملف السائق (Provider) بالبيانات الحقيقية

### Backend — `app/Http/Controllers/API/ProfileController.php`
- أصبح endpoint العام `/api/profile` يُرجع إحصائيات مزود الخدمة (completed_orders, average_rating, total_ratings, service_type, vehicle_make, vehicle_model, vehicle_plate, is_available, verification_status)
- حساب `$ratingStats` باستخدام `Rating::whereHas('serviceRequest', ...)` مع `provider_id`

### Backend — `app/Http/Controllers/API/Provider/ProfileController.php`
- إضافة حقل `total_income` محسوب من `$profile->serviceRequests()->where('status', 'completed')->sum('total')`
- استخدام نمط clone لتجنب تعديل الاستعلام الأصلي

### Backend — `app/Http/Controllers/API/Provider/AnalyticsController.php`
- إضافة فحص null safety: إرجاع 404 إذا لم يُعثر على ملف مزود الخدمة
- إصلاح خطأ SQL Cardinality Violation: تغيير `where('request_id', subquery)` إلى `whereIn('request_id', subquery)` لأن الاستعلام الفرعي يُرجع أكثر من صف واحد

---

## 2. القائمة الجانبية لمزود الخدمة (Provider Drawer)

### Flutter — `lib/features/driver_home/presentation/widgets/driver_drawer.dart`
- إعادة كتابة كاملة من StatelessWidget إلى StatefulWidget
- جلب البيانات الحقيقية من `GET /provider/profile` عند initState
- عرض الاسم الحقيقي من API مع fallback للبيانات المخزنة مؤقتاً
- عرض نوع الخدمة الديناميكي، التقييم الحقيقي مع عدد التقييمات، إجمالي الدخل الحقيقي
- مؤشرات تحميل أثناء جلب البيانات
- عرض الأحرف الأولى بدلاً من أيقونة الشخص العامة في الصورة الرمزية

---

## 3. حقل totalIncome في كيان مزود الخدمة

### Flutter — `lib/features/provider/domain/entities/provider_profile_entity.dart`
- إضافة حقل `final double totalIncome` بقيمة افتراضية 0
- تحديث `props` ليشمل جميع الحقول الـ 16 للمقارنة الصحيحة في Equatable

### Flutter — `lib/features/provider/data/models/provider_profile_model.dart`
- إضافة `super.totalIncome` للمُنشئ
- إضافة `totalIncome: double.tryParse('${json['total_income'] ?? ''}') ?? 0` في fromJson

### Flutter — `lib/features/provider/data/datasources/provider_data_source.dart`
- إضافة `totalIncome: 0` للبيانات التجريبية

---

## 4. صفحة التقارير لتطبيق مزود الخدمة (Sponsor Reports)

### Flutter — `lib/features/driver_profile/presentation/screens/driver_reports_screen.dart`
- صفحة كاملة (607 سطر) تحتوي على:
  - 4 بطاقات إحصائية: إجمالي الطلبات، إجمالي الدخل، متوسط التقييم، دخل اليوم
  - مخطط أعمدة أسبوعي للطلبات (Custom Paint)
  - مخطط مساحي لمعدل القبول (Custom Paint)
  - سحب للتحديث (Pull-to-Refresh) مع RefreshIndicator
  - معالجة أخطاء محسّنة: التقاط DioException مع رسائل مخصصة (403 = حسابك قيد المراجعة، 404 = لم يُعثر على بيانات)
- المسار مسجل في `route_generator.dart` والقائمة الجانبية

### Backend — `app/Http/Controllers/API/Provider/AnalyticsController.php`
- Controller كامل يُرجع: total_orders, total_income, today_orders, today_income, orders_change, income_change, average_rating, total_ratings, weekly_orders, weekly_acceptance
- مسار API: `GET /provider/analytics`

---

## 5. صفحة الملف الشخصي — إزالة الإحصائيات من تطبيق العميل

### Flutter — `lib/features/profile/presentation/screens/profile_screen.dart`
- اكتشاف دور مزود الخدمة وعرض `_ProviderInfoSection` (نوع الخدمة، شارة التحقق، شارة التوفر، معلومات المركبة)
- إزالة widget `_ProfileStatsRow` بالكامل (التقييم، الطلبات المكتملة، إجمالي التقييمات)
- إزالة المتغيرات غير المستخدمة: `completedOrders`, `averageRating`, `totalRatings`

---

## 6. صورة SVG في صفحة الدعم الفني

### Flutter — `lib/core/resources/assets_manager.dart`
- إضافة `static const String helpUser = '$_path/help_user.svg';` في SvgAssets

### Flutter — `lib/features/profile/presentation/screens/support_screen.dart` (تطبيق العميل)
- استبدال التدرج والأيقونة بـ `SvgPicture.asset(SvgAssets.helpUser)` بعرض كامل الشاشة مع `BoxFit.cover`

### Flutter — `lib/features/driver_support/presentation/screens/driver_support_screen.dart` (تطبيق مزود الخدمة)
- استبدال `_SupportIllustration` القائمة على الأيقونات بـ `SvgPicture.asset(SvgAssets.helpUser)` بعرض كامل edge-to-edge
- نقل padding من ScrollView إلى DirectCallSection فقط لجعل SVG بدون هوامش

---

## 7. إصلاح خطأ لوحة التحكم الإدارية (Admin Dashboard)

### Backend — `bootstrap/app.php`
- إضافة `$middleware->redirectGuestsTo(fn () => route('admin.login'))` لإصلاح خطأ "Route [login] not defined" عند انتهاء الجلسة

### Backend — `app/Http/Controllers/API/Provider/AnalyticsController.php`
- تغيير `Rating::where('request_id', function...)` إلى `Rating::whereIn('request_id', function...)` لإصلاح خطأ SQL "Subquery returns more than 1 row"

---

## 8. تغيير نوع السيارة في طلب الونش من قائمة إلى حقل نصي

### Flutter — `lib/features/towing/presentation/widgets/towing_car_details_form.dart`
- استبدال `ServiceDropdown` بـ `ServiceInputField` لحقل نوع السيارة
- تبسيط خصائص الـ Widget إلى controller فقط بدلاً من قائمة وقيمة محددة

### Flutter — `lib/features/towing/presentation/screens/towing_screen.dart`
- استبدال `_selectedCarType` (String?) و `_carTypes` (List) بـ `_carTypeCtrl` (TextEditingController)
- تحديث التحقق من الصحة: `_carTypeCtrl.text.trim().isNotEmpty` بدلاً من `_selectedCarType != null`
- تحديث الإرسال: `carType: _carTypeCtrl.text.trim()` بدلاً من `_selectedCarType!`
- إضافة listener و dispose لـ `_carTypeCtrl`

---

## 9. تغيير تقييم الونش من Bottom Sheet إلى Inline (مثل الوقود)

### Flutter — `lib/features/towing/presentation/screens/towing_trip_details_screen.dart`
- إعادة كتابة كاملة من StatelessWidget إلى StatefulWidget
- إزالة `showRatingBottomSheet` (Bottom Sheet) واستبداله بتقييم مضمن داخل الصفحة
- نفس نمط صفحة إكمال الوقود: زر "تقييم الخدمة" → نموذج تقييم مضمن مع تمرير تلقائي → إرسال → رسالة شكر
- 3 حالات للزر السفلي: (1) تقييم الخدمة + تخطي (2) إرسال التقييم + تخطي (3) العودة للرئيسية
- إضافة عرض نوع السيارة في تفاصيل السيارة

---

## 10. تغيير لون حاويات GIF وإضافة صور GIF لتطبيق مزود الخدمة

### Flutter — `lib/core/widgets/dotted_circle_container.dart`
- تغيير لون الدائرة من `#004B3B` إلى `#336F62`

### Flutter — `lib/features/towing/presentation/widgets/gif_circle.dart`
- تغيير بداية التدرج من `#004B3B` إلى `#336F62`

### Flutter — `lib/features/driver_service/presentation/screens/driver_navigate_screen.dart` (تطبيق مزود الخدمة)
- إضافة صورة `tank_truck.gif` داخل `DottedCircleContainer` في اللوحة السفلية لطلبات الوقود

### Flutter — `lib/features/driver_service/presentation/screens/driver_refueling_screen.dart` (تطبيق مزود الخدمة)
- استبدال الأيقونة النابضة بـ `DottedCircleContainer(imagePath: 'assets/images/refuel.gif')`

---

## 11. تغيير صورة GIF في شاشة "في الطريق لك" بتطبيق العميل

### Flutter — `lib/features/towing/presentation/screens/driver_found_screen.dart`
- تغيير صورة GIF عند حالة `en_route` لطلبات الوقود من `magnifying_glass.gif` إلى `tank_truck.gif`
- طلبات الونش تبقى تعرض `magnifying_glass.gif`

---

## 12. إزالة تقييم Bottom Sheet من طلبات الوقود في تطبيق العميل

### Flutter — `lib/features/home/presentation/screens/home_screen.dart`
- إضافة شرط `if (order.isFuelDelivery)` لتخطي عرض Bottom Sheet — طلبات الوقود تستخدم التقييم المضمن في `fuel_complete_screen`

### Flutter — `lib/features/shell/presentation/screens/bottom_nav_shell.dart`
- إضافة نفس الشرط `if (order.isFuelDelivery) return;` قبل عرض Bottom Sheet عند استئناف التطبيق

---

## ملاحظات مهمة

- **حساب مزود الخدمة يحتاج للموافقة**: يجب تشغيل الأمر التالي لتفعيل الحساب:
  ```bash
  php artisan tinker --execute '$p = \App\Models\ProviderProfile::first(); $p->update(["verification_status" => "approved"]); echo "Done";'
  ```
- **تسجيل الدخول**: رقم الهاتف يجب أن يكون 9 أحرف على الأقل، وكلمة المرور 6 أحرف على الأقل
- **middleware `provider.approved`**: يمنع جميع مسارات API لمزود الخدمة بخطأ 403 إذا لم يكن الحساب مُوافقاً عليه
