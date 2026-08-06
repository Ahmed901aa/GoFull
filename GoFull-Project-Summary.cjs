const fs = require("fs");
const { Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
        Header, Footer, AlignmentType, HeadingLevel, BorderStyle, WidthType,
        ShadingType, PageNumber, PageBreak, LevelFormat } = require("docx");

const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
const borders = { top: border, bottom: border, left: border, right: border };
const cellMargins = { top: 80, bottom: 80, left: 120, right: 120 };
const greenColor = "004B3B";
const lightGreen = "E8F5E9";

function heading(text, level = HeadingLevel.HEADING_1) {
  return new Paragraph({ heading: level, spacing: { before: 300, after: 150 },
    children: [new TextRun({ text, bold: true, font: "Cairo", size: level === HeadingLevel.HEADING_1 ? 32 : level === HeadingLevel.HEADING_2 ? 28 : 24, color: greenColor })] });
}

function para(text, opts = {}) {
  return new Paragraph({ spacing: { after: 120 }, alignment: opts.align || AlignmentType.RIGHT,
    children: [new TextRun({ text, font: "Cairo", size: 22, color: opts.color || "333333", bold: opts.bold || false, ...opts.run })] });
}

function bulletItem(text) {
  return new Paragraph({ numbering: { reference: "bullets", level: 0 }, spacing: { after: 80 }, alignment: AlignmentType.RIGHT,
    children: [new TextRun({ text, font: "Cairo", size: 22, color: "333333" })] });
}

function tableRow(cells, isHeader = false) {
  return new TableRow({
    children: cells.map((text, i) => new TableCell({
      borders, margins: cellMargins,
      width: { size: i === 0 ? 2500 : 6860, type: WidthType.DXA },
      shading: isHeader ? { fill: greenColor, type: ShadingType.CLEAR } : { fill: "FFFFFF", type: ShadingType.CLEAR },
      children: [new Paragraph({ alignment: AlignmentType.RIGHT,
        children: [new TextRun({ text, font: "Cairo", size: 20, color: isHeader ? "FFFFFF" : "333333", bold: isHeader })] })]
    }))
  });
}

function apiTableRow(cells, isHeader = false) {
  const widths = [1500, 3200, 2300, 2360];
  return new TableRow({
    children: cells.map((text, i) => new TableCell({
      borders, margins: cellMargins,
      width: { size: widths[i] || 2360, type: WidthType.DXA },
      shading: isHeader ? { fill: greenColor, type: ShadingType.CLEAR } : { fill: i % 2 === 0 ? "F9F9F9" : "FFFFFF", type: ShadingType.CLEAR },
      children: [new Paragraph({ alignment: AlignmentType.RIGHT,
        children: [new TextRun({ text, font: "Cairo", size: 18, color: isHeader ? "FFFFFF" : "333333", bold: isHeader })] })]
    }))
  });
}

const doc = new Document({
  styles: {
    default: { document: { run: { font: "Cairo", size: 22 } } },
    paragraphStyles: [
      { id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 32, bold: true, font: "Cairo", color: greenColor },
        paragraph: { spacing: { before: 300, after: 150 }, outlineLevel: 0 } },
      { id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 28, bold: true, font: "Cairo", color: greenColor },
        paragraph: { spacing: { before: 240, after: 120 }, outlineLevel: 1 } },
      { id: "Heading3", name: "Heading 3", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 24, bold: true, font: "Cairo", color: greenColor },
        paragraph: { spacing: { before: 200, after: 100 }, outlineLevel: 2 } },
    ]
  },
  numbering: {
    config: [{
      reference: "bullets",
      levels: [{ level: 0, format: LevelFormat.BULLET, text: "●", alignment: AlignmentType.LEFT,
        style: { paragraph: { indent: { left: 720, hanging: 360 } } } }]
    }]
  },
  sections: [{
    properties: {
      page: {
        size: { width: 12240, height: 15840 },
        margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 }
      }
    },
    headers: {
      default: new Header({
        children: [new Paragraph({ alignment: AlignmentType.CENTER, border: { bottom: { style: BorderStyle.SINGLE, size: 6, color: greenColor, space: 4 } },
          children: [
            new TextRun({ text: "GO FULL", font: "Cairo", size: 20, bold: true, color: greenColor }),
            new TextRun({ text: "  —  ", font: "Cairo", size: 20, color: "999999" }),
            new TextRun({ text: "ملخص المشروع التقني", font: "Cairo", size: 20, color: "666666" }),
          ]
        })]
      })
    },
    footers: {
      default: new Footer({
        children: [new Paragraph({ alignment: AlignmentType.CENTER,
          children: [
            new TextRun({ text: "© 2026 GO FULL  —  ", font: "Cairo", size: 16, color: "999999" }),
            new TextRun({ text: "صفحة ", font: "Cairo", size: 16, color: "999999" }),
            new TextRun({ children: [PageNumber.CURRENT], font: "Cairo", size: 16, color: "999999" }),
          ]
        })]
      })
    },
    children: [
      // ═══ COVER ═══
      new Paragraph({ spacing: { before: 2000 }, alignment: AlignmentType.CENTER,
        children: [new TextRun({ text: "GO FULL", font: "Cairo", size: 56, bold: true, color: greenColor })] }),
      new Paragraph({ spacing: { after: 200 }, alignment: AlignmentType.CENTER,
        children: [new TextRun({ text: "منصة إدارة خدمات إمداد الوقود والساحبة", font: "Cairo", size: 28, color: "555555" })] }),
      new Paragraph({ spacing: { after: 600 }, alignment: AlignmentType.CENTER, border: { bottom: { style: BorderStyle.SINGLE, size: 4, color: greenColor, space: 8 } },
        children: [new TextRun({ text: "ملخص تقني شامل", font: "Cairo", size: 24, color: "888888" })] }),

      // Info table
      new Table({
        width: { size: 9360, type: WidthType.DXA }, columnWidths: [4680, 4680],
        rows: [
          tableRow(["المطور", "Ahmed Ahmeid"]),
          tableRow(["التاريخ", "2026"]),
          tableRow(["GitHub", "github.com/Ahmed901aa/GoFull"]),
          tableRow(["الدولة", "ليبيا — بنغازي"]),
        ]
      }),

      new Paragraph({ children: [new PageBreak()] }),

      // ═══ 1. OVERVIEW ═══
      heading("١. نبذة عامة"),
      para("GO FULL هو تطبيق موبايل لخدمات توصيل الوقود وسحب السيارات في ليبيا. يربط بين العملاء (أصحاب السيارات) ومزودي الخدمة (سائقي الوقود والساحبات). يشمل المشروع تطبيق موبايل (Flutter) وباك اند (Laravel API) ولوحة تحكم إدارية (Tailwind CSS)."),

      heading("٢. التقنيات المستخدمة"),
      heading("الباك اند (Backend)", HeadingLevel.HEADING_3),
      bulletItem("PHP 8.4 + Laravel 13"),
      bulletItem("Laravel Sanctum v4 — مصادقة API بالتوكن"),
      bulletItem("Laravel Reverb v1 — WebSocket للتحديثات اللحظية"),
      bulletItem("MySQL — قاعدة البيانات"),
      bulletItem("Tailwind CSS v4 — لوحة التحكم الإدارية"),

      heading("الفرونت اند (Mobile)", HeadingLevel.HEADING_3),
      bulletItem("Flutter (Dart) — تطبيق موبايل واحد للعميل والمزود"),
      bulletItem("BLoC / Cubit — إدارة الحالة"),
      bulletItem("Dio — الاتصال بالـ API"),
      bulletItem("Google Maps Flutter — الخرائط وتتبع الموقع"),
      bulletItem("GetIt — Dependency Injection"),
      bulletItem("دعم كامل للعربية (RTL) والإنجليزية"),
      bulletItem("وضع فاتح / داكن (Light/Dark Mode)"),

      heading("الأدوات", HeadingLevel.HEADING_3),
      bulletItem("Git / GitHub — التحكم بالإصدارات"),
      bulletItem("Postman — اختبار الـ API"),
      bulletItem("Figma — التصاميم"),
      bulletItem("Railway — استضافة السيرفر"),

      new Paragraph({ children: [new PageBreak()] }),

      // ═══ 3. ROLES ═══
      heading("٣. أدوار المستخدمين"),
      new Table({
        width: { size: 9360, type: WidthType.DXA }, columnWidths: [2500, 6860],
        rows: [
          tableRow(["الدور", "الوصف"], true),
          tableRow(["العميل (Driver)", "صاحب السيارة اللي يطلب خدمة وقود أو سحب"]),
          tableRow(["المزود (Provider)", "سائق شاحنة الوقود أو سائق الساحبة"]),
          tableRow(["المدير (Admin)", "صلاحيات كاملة على لوحة التحكم"]),
          tableRow(["الموظف (Employee)", "صلاحيات محدودة حسب التخصص (وقود/سحب)"]),
        ]
      }),

      // ═══ 4. ORDER FLOW ═══
      heading("٤. دورة حياة الطلب"),
      para("كل طلب يمر بهذه المراحل بالترتيب (لا يمكن تخطي مرحلة):"),
      bulletItem("قيد الانتظار (pending) → العميل أنشأ الطلب"),
      bulletItem("مقبول (accepted) → المزود قبل الطلب"),
      bulletItem("في الطريق (en_route) → المزود متجه للعميل"),
      bulletItem("وصل (arrived) → المزود وصل للموقع"),
      bulletItem("جاري (in_progress) → الخدمة بدأت"),
      bulletItem("مكتمل (completed) → الخدمة انتهت + الدفع"),
      para("يمكن إلغاء الطلب في أي مرحلة مع تسجيل من ألغى والسبب."),

      // ═══ 5. SERVICES ═══
      heading("٥. الخدمات"),
      heading("إمداد الوقود", HeadingLevel.HEADING_3),
      bulletItem("العميل يختار: نوع الوقود (بنزين/ديزل) + الكمية (1-200 لتر) + الموقع"),
      bulletItem("التسعير: سعر اللتر × الكمية + رسوم الخدمة (15 د.ل)"),
      bulletItem("الأسعار: بنزين 0.75 د.ل/لتر — ديزل 0.85 د.ل/لتر"),

      heading("خدمة الساحبة (الونش)", HeadingLevel.HEADING_3),
      bulletItem("العميل يختار: موقع السيارة + وجهة السحب + نوع السيارة + رقم اللوحة"),
      bulletItem("التسعير: سعر أساسي 50 د.ل + رسوم الخدمة (15 د.ل)"),
      bulletItem("الدفع: نقدي فقط (العملة: دينار ليبي)"),

      new Paragraph({ children: [new PageBreak()] }),

      // ═══ 6. API ENDPOINTS ═══
      heading("٦. نقاط الـ API الرئيسية"),
      heading("طرف العميل", HeadingLevel.HEADING_3),
      new Table({
        width: { size: 9360, type: WidthType.DXA }, columnWidths: [1500, 3200, 2300, 2360],
        rows: [
          apiTableRow(["الطريقة", "المسار", "الوظيفة", "الملاحظات"], true),
          apiTableRow(["POST", "/api/auth/register", "تسجيل حساب", "عميل أو مزود"]),
          apiTableRow(["POST", "/api/auth/login", "تسجيل دخول", "هاتف + كلمة مرور"]),
          apiTableRow(["GET", "/api/home", "الصفحة الرئيسية", "بانرات + طلب نشط"]),
          apiTableRow(["POST", "/api/driver/requests/fuel", "طلب وقود", "موقع + نوع + كمية"]),
          apiTableRow(["POST", "/api/driver/requests/towing", "طلب سحب", "موقعين + سيارة"]),
          apiTableRow(["PATCH", "/api/driver/requests/{id}/cancel", "إلغاء طلب", "مع السبب"]),
          apiTableRow(["POST", "/api/driver/requests/{id}/rate", "تقييم", "1-5 نجوم"]),
        ]
      }),
      new Paragraph({ spacing: { after: 200 } }),

      heading("طرف المزود", HeadingLevel.HEADING_3),
      new Table({
        width: { size: 9360, type: WidthType.DXA }, columnWidths: [1500, 3200, 2300, 2360],
        rows: [
          apiTableRow(["الطريقة", "المسار", "الوظيفة", "الملاحظات"], true),
          apiTableRow(["PATCH", "/api/provider/profile/availability", "تغيير الحالة", "متصل/غير متصل"]),
          apiTableRow(["PATCH", "/api/provider/profile/location", "تحديث الموقع", "GPS كل 10 ثواني"]),
          apiTableRow(["GET", "/api/provider/requests", "طلبات معلقة", "حسب نوع الخدمة"]),
          apiTableRow(["PATCH", "/api/provider/requests/{id}/accept", "قبول طلب", ""]),
          apiTableRow(["PATCH", "/api/provider/requests/{id}/status", "تحديث الحالة", "تسلسلي"]),
          apiTableRow(["GET", "/api/provider/analytics", "إحصائيات", "دخل + طلبات + تقييم"]),
        ]
      }),

      new Paragraph({ children: [new PageBreak()] }),

      // ═══ 7. DATABASE ═══
      heading("٧. قاعدة البيانات"),
      para("المشروع يحتوي على 10 جداول رئيسية:"),
      new Table({
        width: { size: 9360, type: WidthType.DXA }, columnWidths: [2500, 6860],
        rows: [
          tableRow(["الجدول", "الوصف"], true),
          tableRow(["users", "المستخدمين (4 أدوار: عميل/مزود/مدير/موظف)"]),
          tableRow(["provider_profiles", "ملفات المزودين (سيارة + موقع + تقييم + حالة التوثيق)"]),
          tableRow(["provider_documents", "وثائق المزود (هوية/رخصة/تأمين)"]),
          tableRow(["service_requests", "الطلبات (موقع + حالة + تسعير + دفع)"]),
          tableRow(["ratings", "تقييمات الخدمة (1-5 نجوم)"]),
          tableRow(["fuel_prices", "أسعار الوقود (بنزين/ديزل + ضريبة)"]),
          tableRow(["banners", "البانرات الترويجية"]),
          tableRow(["app_settings", "إعدادات التطبيق (رسوم/عملة/دعم)"]),
          tableRow(["notifications", "الإشعارات"]),
          tableRow(["driver_vehicles", "سيارات العملاء"]),
        ]
      }),

      new Paragraph({ children: [new PageBreak()] }),

      // ═══ 8. ADMIN PANEL ═══
      heading("٨. لوحة التحكم الإدارية"),
      bulletItem("لوحة معلومات (Dashboard) — إحصائيات + رسوم بيانية + أفضل المزودين"),
      bulletItem("توثيق المزودين — مراجعة الوثائق + تحديد موعد + قبول/رفض"),
      bulletItem("إدارة المستخدمين — بحث + تعليق/تفعيل + حذف"),
      bulletItem("مراقبة الخدمات — طلبات نشطة لحظياً"),
      bulletItem("تحليلات متقدمة — إيرادات + ساعات ذروة + اتجاهات 30 يوم"),
      bulletItem("أسعار الوقود — تعديل الأسعار والضرائب"),
      bulletItem("إعدادات التطبيق — رسوم الخدمة"),
      bulletItem("إدارة الموظفين — إضافة/حذف موظفين"),

      // ═══ 9. FLUTTER SCREENS ═══
      heading("٩. شاشات التطبيق"),
      heading("شاشات العميل", HeadingLevel.HEADING_3),
      bulletItem("الرئيسية — بانرات + خدمات + عروض + بطاقة طلب حي"),
      bulletItem("طلب وقود — اختيار النوع + الكمية + الموقع"),
      bulletItem("طلب سحب — موقع السيارة + الوجهة + بيانات السيارة"),
      bulletItem("بحث عن مزود — رسوم متحركة أثناء البحث"),
      bulletItem("تتبع الطلب — خريطة حية + حالة الطلب"),
      bulletItem("التقييم — 5 نجوم + تعليق"),
      bulletItem("سجل الطلبات — جميع الطلبات السابقة"),
      bulletItem("الملف الشخصي + الإعدادات + الدعم"),

      heading("شاشات المزود", HeadingLevel.HEADING_3),
      bulletItem("الرئيسية — تفعيل/إيقاف + طلبات معلقة + طلب نشط"),
      bulletItem("قبول/رفض الطلب — تفاصيل الطلب + أزرار"),
      bulletItem("الملاحة — خريطة Google Maps للعميل"),
      bulletItem("التوثيق — التقاط صور"),
      bulletItem("تحصيل الدفع — تأكيد الاستلام"),
      bulletItem("التقارير — إحصائيات الدخل والطلبات"),

      new Paragraph({ children: [new PageBreak()] }),

      // ═══ 10. ARCHITECTURE ═══
      heading("١٠. الهيكلية البرمجية"),
      para("المشروع يستخدم Clean Architecture مع تنظيم Feature-First:"),
      bulletItem("data/ — مصادر البيانات (API) + النماذج + المستودعات"),
      bulletItem("domain/ — الكيانات + حالات الاستخدام + واجهات المستودعات"),
      bulletItem("presentation/ — BLoC + الشاشات + الويجتات"),
      para("كل ميزة (Feature) لها مجلد مستقل فيه الطبقات الثلاث."),

      // ═══ 11. SECURITY ═══
      heading("١١. الأمان"),
      bulletItem("Sanctum Bearer Token — كل طلب API يحتاج توكن"),
      bulletItem("Middleware — تحقق من الدور + حالة التوثيق"),
      bulletItem("Form Request Validation — تحقق من كل مدخل"),
      bulletItem("CSRF Protection — للوحة الإدارية"),
      bulletItem("تسلسل الحالة — لا يمكن تخطي مراحل الطلب"),

      // ═══ 12. TEST DATA ═══
      heading("١٢. بيانات الاختبار"),
      new Table({
        width: { size: 9360, type: WidthType.DXA }, columnWidths: [2500, 3430, 3430],
        rows: [
          new TableRow({ children: [
            new TableCell({ borders, margins: cellMargins, width: { size: 2500, type: WidthType.DXA }, shading: { fill: greenColor, type: ShadingType.CLEAR },
              children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "الدور", font: "Cairo", size: 20, color: "FFFFFF", bold: true })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, shading: { fill: greenColor, type: ShadingType.CLEAR },
              children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "الهاتف", font: "Cairo", size: 20, color: "FFFFFF", bold: true })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, shading: { fill: greenColor, type: ShadingType.CLEAR },
              children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "كلمة المرور", font: "Cairo", size: 20, color: "FFFFFF", bold: true })] })] }),
          ]}),
          new TableRow({ children: [
            new TableCell({ borders, margins: cellMargins, width: { size: 2500, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "العميل", font: "Cairo", size: 20 })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "0911111111", font: "Cairo", size: 20 })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "12345678", font: "Cairo", size: 20 })] })] }),
          ]}),
          new TableRow({ children: [
            new TableCell({ borders, margins: cellMargins, width: { size: 2500, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "مزود وقود", font: "Cairo", size: 20 })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "0923663333", font: "Cairo", size: 20 })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "12345678", font: "Cairo", size: 20 })] })] }),
          ]}),
          new TableRow({ children: [
            new TableCell({ borders, margins: cellMargins, width: { size: 2500, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "مزود ساحبة", font: "Cairo", size: 20 })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "0923664444", font: "Cairo", size: 20 })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "12345678", font: "Cairo", size: 20 })] })] }),
          ]}),
          new TableRow({ children: [
            new TableCell({ borders, margins: cellMargins, width: { size: 2500, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "المدير", font: "Cairo", size: 20 })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "0910406699", font: "Cairo", size: 20 })] })] }),
            new TableCell({ borders, margins: cellMargins, width: { size: 3430, type: WidthType.DXA }, children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: "12345678", font: "Cairo", size: 20 })] })] }),
          ]}),
        ]
      }),

      new Paragraph({ spacing: { before: 400 }, alignment: AlignmentType.CENTER,
        children: [new TextRun({ text: "— نهاية الملخص —", font: "Cairo", size: 22, color: "999999", italics: true })] }),
    ]
  }]
});

Packer.toBuffer(doc).then(buf => {
  fs.writeFileSync("/sessions/charming-awesome-ramanujan/mnt/GoFull/GoFull-Project-Summary.docx", buf);
  console.log("Done: GoFull-Project-Summary.docx");
});
