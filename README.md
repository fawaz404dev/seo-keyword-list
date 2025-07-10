# ترتيب الكلمات المفتاحية في جوجل

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.0%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.0-orange.svg)]()

## 📋 الوصف

إضافة ووردبريس احترافية لتتبع ترتيب الكلمات المفتاحية في محرك البحث جوجل. تسمح هذه الإضافة بإنشاء قائمة بالكلمات المفتاحية المهمة لموقعك وتتبع ترتيبها في نتائج البحث بمرور الوقت مع إمكانية تصدير البيانات وتحليل الأداء.

## ✨ المميزات

- 🔍 **إضافة كلمات مفتاحية** مع ترتيبها الأولي
- 📊 **تحديث الترتيب الحالي** للكلمات المفتاحية
- 📈 **عرض تغيير الترتيب** مقارنة بالشهر السابق
- 🔄 **تحديث تلقائي** لترتيب الشهر السابق في بداية كل شهر
- 📁 **تصدير البيانات** إلى ملف CSV
- 🎨 **واجهة مستخدم** سهلة الاستخدام ومتجاوبة
- 🌐 **دعم اللغة العربية** بالكامل
- 🔒 **آمان عالي** مع حماية من الوصول المباشر

## 🚀 التثبيت

### التثبيت اليدوي
1. قم بتحميل ملفات الإضافة إلى مجلد `/wp-content/plugins/seo-keyword-list/`
2. قم بتفعيل الإضافة من خلال قائمة 'الإضافات' في لوحة تحكم ووردبريس
3. استخدم الإضافة من خلال قائمة 'الكلمات المفتاحية' في لوحة التحكم

### التثبيت من GitHub
```bash
cd wp-content/plugins/
git clone https://github.com/fjomah/seo-keyword-list.git
```

## 📖 الاستخدام

### إضافة كلمة مفتاحية جديدة
1. انتقل إلى قائمة **'الكلمات المفتاحية'** في لوحة التحكم
2. أدخل الكلمة المفتاحية والترتيب الأولي لها
3. انقر على زر **'إضافة الكلمة المفتاحية'**

### تحديث ترتيب كلمة مفتاحية
1. انقر على زر **'تعديل'** بجانب الكلمة المفتاحية
2. أدخل الترتيب الجديد
3. انقر على زر **'تحديث'**

### حذف كلمة مفتاحية
1. انقر على زر **'حذف'** بجانب الكلمة المفتاحية
2. قم بتأكيد الحذف

### تصدير الكلمات المفتاحية إلى ملف CSV
1. انقر على زر **'تصدير إلى CSV'** أعلى قائمة الكلمات المفتاحية
2. سيتم تنزيل ملف CSV يحتوي على جميع الكلمات المفتاحية وترتيبها

## 🗂️ هيكل المشروع

```
seo-keyword-list/
├── assets/
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
├── includes/
│   ├── class-seo-keyword-list.php
│   ├── class-seo-keyword-list-admin.php
│   └── update-db.php
├── languages/
│   ├── seo-keyword-list-ar.mo
│   ├── seo-keyword-list-ar.po
│   └── seo-keyword-list.pot
├── templates/
│   └── admin-page.php
├── seo-keyword-list.php
└── README.md
```

## ⚙️ المتطلبات

- **ووردبريس:** 5.0 أو أحدث
- **PHP:** 7.0 أو أحدث
- **MySQL:** 5.6 أو أحدث
- **الذاكرة:** 64MB على الأقل

## 🛠️ التطوير والمساهمة

### إعداد بيئة التطوير
1. استنسخ المستودع:
   ```bash
   git clone https://github.com/fjomah/seo-keyword-list.git
   ```
2. انتقل إلى مجلد المشروع:
   ```bash
   cd seo-keyword-list
   ```
3. قم بتثبيت الإضافة في بيئة ووردبريس للاختبار

### المساهمة
1. قم بعمل Fork للمستودع
2. أنشئ فرع جديد للميزة: `git checkout -b feature/new-feature`
3. قم بالتعديلات والالتزام: `git commit -am 'Add new feature'`
4. ادفع التغييرات: `git push origin feature/new-feature`
5. أنشئ Pull Request

## 📝 سجل التغييرات

### الإصدار 1.0.0
- الإصدار الأولي
- إضافة وإدارة الكلمات المفتاحية
- تتبع ترتيب الكلمات المفتاحية
- تصدير البيانات إلى CSV
- دعم اللغة العربية

## 🐛 الإبلاغ عن الأخطاء

إذا واجهت أي مشاكل أو أخطاء، يرجى:
1. التحقق من [الأسئلة الشائعة](#الأسئلة-الشائعة)
2. البحث في [Issues الموجودة](https://github.com/fjomah/seo-keyword-list/issues)
3. إنشاء [Issue جديد](https://github.com/fjomah/seo-keyword-list/issues/new) مع تفاصيل المشكلة

## ❓ الأسئلة الشائعة

**س: هل تدعم الإضافة محركات بحث أخرى غير جوجل؟**
ج: حالياً الإضافة مصممة خصيصاً لجوجل، لكن يمكن تطويرها لدعم محركات أخرى.

**س: هل يمكن تتبع أكثر من موقع؟**
ج: الإضافة مصممة لتتبع موقع واحد، لكن يمكن استخدامها على مواقع متعددة منفصلة.

**س: كم عدد الكلمات المفتاحية التي يمكن إضافتها؟**
ج: لا يوجد حد أقصى محدد، لكن الأداء يعتمد على موارد الخادم.

## 👨‍💻 معلومات المطور

**المطور:** [فوزي جمعة](https://fjomah.com)

**الموقع الشخصي:** [https://fjomah.com](https://fjomah.com)

**البريد الإلكتروني:** [info@fjomah.com](mailto:info@fjomah.com)

**الهاتف:** +201111933193

**GitHub:** [https://github.com/fjomah](https://github.com/fjomah)

### خبرات المطور
- 🌐 تطوير مواقع الويب والتطبيقات
- 🔍 خبير في تحسين محركات البحث (SEO)
- 💻 مطور ووردبريس معتمد
- 📱 تطوير التطبيقات المتجاوبة

## 🤝 الدعم

للحصول على الدعم الفني:
- 📧 **البريد الإلكتروني:** [support@fjomah.com](mailto:support@fjomah.com)
- 📱 **واتساب:** +201111933193
- 🌐 **الموقع:** [https://fjomah.com/contact](https://fjomah.com/contact)
- 💬 **GitHub Issues:** [إنشاء تذكرة دعم](https://github.com/fjomah/seo-keyword-list/issues)

## 📄 الترخيص

هذا المشروع مرخص تحت رخصة **GPL v2** أو أحدث.

```
Copyright (C) 2024 فوزي جمعة

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## 🌟 شكر وتقدير

شكراً لجميع المساهمين والمستخدمين الذين يساعدون في تطوير هذه الإضافة.

---

**إذا أعجبتك هذه الإضافة، لا تنس إعطائها ⭐ على GitHub!**