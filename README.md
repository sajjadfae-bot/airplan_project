# Airplan — API مدیریت پرواز

همان ساختار `example-app` (Laravel 10) با سه قابلیت اضافه:

-مدیریت ناوگان هواپیما (`airplanes`)
- ثبت و مدیریت اطلاعات خلبان (`pilots`)
- احراز هویت با **Sanctum Bearer Token** — فقط خلبانان میتوانند اطلاعات را ببینند

---

## 1. اجرای پروژه

```bash
cd C:\Users\Raya-Bit\Desktop\n_lar\airplan
php artisan serve
```

آدرس.base: `http://127.0.0.1:8000/api/v1`

دیتابیس پیشفرض **SQLite** است و فایل آن `database/database.sqlite` میباشد (مigrations اجرا شدهاند).
برای شروع کاملاً تمیز:

```bash
php artisan migrate:fresh
```

### اگر خواستید MySQL باشد

در `.env` جایگزیر کنید:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=airplan
DB_USERNAME=root
DB_PASSWORD=
```

سپس دیتابیس `airplan` را بسازید و `php artisan migrate` بزنید. ساختار جدولها فرقی نمیکند.

---

## 2. اتصال به Postman

1. Postman → **Import** → فایل `postman/Airplan.postman_collection.json` را انتخاب کنید.
2. در تب **Variables** مقدار `baseUrl` برابر `http://127.0.0.1:8000/api/v1` است.
3. ابتدا درخواست **«ثبت خلبان»** یا **«ورود خلبان»** را اجرا کنید.
   اسکریپت داخل_collection بهطور خودکار token را در متغیر `token` ذخیره میکند.
4. بقیه درخواستها از هدر زیر استفاده میکنند (خودکار توسط Collection اعمال میشود):

```http
Authorization: Bearer <token>
Accept: application/json
```

برای تست دستی:

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" http://127.0.0.1:8000/api/v1/airplane
```

> نکته: هر بار که خلبان لاگین میکند، tokenهای قبلی او باطل میشوند (`$pilot->tokens()->delete()`).

---

## 3. لیست کامل مسیرها

| متد | مسیر | توضیح | token لازم است؟ |
|---|---|---|---|
| POST | `/pilot/store` | ثبت خلبان + صدور token | خیر |
| POST | `/pilot/login` | ورود با `license_number` + `password` + صدور token | خیر |
| GET | `/pilot/me` | خلبان فعلی (تست token) | بله |
| GET | `/pilot` | لیست خلبانها | بله |
| GET | `/pilot/{pilot}/show` | نمایش یک خلبان | بله |
| PUT | `/pilot/{pilot}/update` | ویرایش خلبان | بله |
| DELETE | `/pilot/{pilot}/destroy` | حذف خلبان | بله |
| GET | `/airplane` | لیست ناوگان | بله |
| POST | `/airplane/store` | ثبت هواپیما | بله |
| GET | `/airplane/{airplane}/show` | نمایش یک هواپیما | بله |
| PUT | `/airplane/{airplane}/update` | ویرایش هواپیما | بله |
| DELETE | `/airplane/{airplane}/destroy` | حذف هواپیما | بله |

---

## 4. نمونه بدنه درخواستها

**ثبت خلبان** (`POST /pilot/store`)

```json
{
  "name": "Captain Raya",
  "license_number": "PIL-1001",
  "phone": "09120000000",
  "password": "secret123",
  "status": "active"
}
```

پاسخ:

```json
{
  "message": "Pilot registered successfully",
  "token": "1|xxxxxxxxxxxxxxxx",
  "data": { "id": 1, "name": "Captain Raya", "license_number": "PIL-1001", "phone": "09120000000", "status": "active" }
}
```

**ثبت هواپیما** (`POST /airplane/store`)

```json
{
  "name": "Boeing 737",
  "model": "737-800",
  "registration_code": "EP-ARA",
  "capacity": 189,
  "status": "active"
}
```

قوانین اعتبارسنجی: `status` فقط `active` یا `inactive`، `capacity` عدد و حداقل 1،
`license_number` و `registration_code` یکتا، `password` حداقل 6 کاراکتر.
در صورت خطا Laravel پاسخ `422` با جزئیات برمیگرداند.

---

## 5. نگاشت ساختار به پروژه اصلی

| پروژه اصلی (`example-app`) | این پروژه (`airplan`) |
|---|---|
| `routes/v1/api.php` + prefix `api/v1` در `RouteServiceProvider` | دقیقاً همان، با روتهای خلبان/هواپیما |
| `app/Models/product.php` | `app/Models/airplane.php`, `app/Models/pilot.php` (مدل `pilot` از `HasApiTokens` استفاده میکند) |
| `app/Http/Requests/ProductStoreValidation.php` | `AirplaneStoreValidation.php`, `PilotStoreValidation.php`, `PilotLoginValidation.php` |
| `app/Http/Resources/ProductStoreResource.php` | `AirplaneStoreResource.php`, `PilotStoreResource.php` |
| `app/Http/Controllers/productController.php` | `airplaneController.php`, `pilotController.php` |
| migration با `id` + فیلدها + `timestamps()` | `airplanes` و `pilots` دقیقاً با همان الگو |
| `personal_access_tokens` (Sanctum) | همان جدول، محل ذخیره توکنها |

نامگذاری مدلها و کنترلرها (`airplane`, `pilot`, `pilotController`) عمداً مثل پروژه اصلی کوچک نوشته شده تا ساختار یکسان بماند.

---

## 6. عیب یابی

- **401 Unauthorized** → token ارسال نشده یا باطل شده است؛ دوباره لاگین کنید.
- **422 Unprocessable Entity** → یکی از قوانین اعتبارسنجی شکسته شده؛ بدنه پاسخ را بخوانید.
- **404** → آدرس را چک کنید؛ تمام روتها زیر `api/v1` هستند.
- لاگ خطاها: `storage/logs/laravel.log`.
