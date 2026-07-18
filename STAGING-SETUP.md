# CheapVINReport — STAGING (тестовая копия)

Это рабочая копия сайта для доработки под SEO. Живёт отдельно от боевого сайта,
закрыта от индексации, платежи в тестовом режиме, ключи поставщика отчётов —
рабочие (отчёты реально тянутся). Когда доведём — отдаёшь партнёру-программисту
готовый репозиторий с комментариями.

---

## 1. Что уже сделано в этой копии (для тебя и для программиста)

**Данные / безопасность:**
- База переведена на **SQLite** (`database/staging-seed.sqlite`) — отдельный сервер
  MySQL не нужен. При первом запуске она копируется на постоянный диск Railway.
- Из базы **удалены чужие заказы, сессии и реальные пользователи** (там были личные
  email/VIN клиентов). Оставлены только настройки (цены/валюты).
- Заведён отдельный админ:
  - **Логин:** `admin@staging.local`
  - **Пароль:** `Staging2026!`
  - Зайти в админку: `/dashboard` (или `/login` → затем `/dashboard`).

**Закрытие от индексации (прод не задет):**
- `app/Http/Middleware/ForceNoIndex.php` — новый middleware, шлёт заголовок
  `X-Robots-Tag: noindex, nofollow` на **любом окружении кроме production**.
- `bootstrap/app.php` — middleware зарегистрирован глобально (`$middleware->append(...)`).
- `resources/views/header.blade.php` — в условие `noindex`-меты добавлен `staging`.
- Всё это **no-op на боевом сайте** (`APP_ENV=production`), поэтому изменения
  безопасно вливать в основной репозиторий.

**Деплой:**
- `Dockerfile` — сборка образа (PHP 8.4 + расширения, Composer, Node для Vite/Tailwind).
- `docker/entrypoint.sh` — при старте сидит БД на диск, применяет миграции, поднимает сайт.
- `railway.json` — говорит Railway собирать через Dockerfile.
- Платежи Stripe уже на **тестовых** ключах (`pk_test`/`sk_test`), Platon/Hutko — в sandbox.

> ⚠️ **Про секреты.** Реальные ключи (поставщик, почта, Monobank) НЕ лежат в этом
> репозитории — они в отдельном файле `.env.staging`, который вставляется в Railway
> вручную. В git секретов нет. Файл `.env.staging` дала тебе я отдельно — храни его
> в надёжном месте и не коммить.

---

## 2. Как развернуть на Railway (пошагово, ~15 минут)

### Шаг 1. Залить код на GitHub
1. Создай **приватный** репозиторий на github.com (например `cheapvinreport-staging`).
2. Из папки проекта выполни (репозиторий уже инициализирован, есть первый коммит):
   ```bash
   git remote add origin https://github.com/ТВОЙ-ЛОГИН/cheapvinreport-staging.git
   git branch -M main
   git push -u origin main
   ```
   *(Нет опыта с командной строкой? Можно поставить GitHub Desktop и залить папку через него.)*

### Шаг 2. Создать проект на Railway
1. Зайди на **railway.com** → войди через GitHub.
2. **New Project → Deploy from GitHub repo** → выбери свой репозиторий.
3. Railway увидит `Dockerfile` и начнёт сборку. Первая сборка идёт 3–6 минут.

### Шаг 3. Подключить постоянный диск (volume) для базы
1. В сервисе: **Settings → Volumes → New Volume**.
2. Mount path: **`/data`** (именно так — туда пишется `database.sqlite`).

### Шаг 4. Вставить переменные окружения
1. В сервисе: **Variables → Raw Editor**.
2. Вставь **всё содержимое файла `.env.staging`** (я передала его отдельно).
3. Нажми **Deploy**.

### Шаг 5. Включить публичный URL и вписать его
1. **Settings → Networking → Generate Domain** — получишь адрес вида
   `https://cheapvinreport-staging-production.up.railway.app`.
2. Скопируй этот адрес в переменную **`APP_URL`** (Variables) и снова **Deploy**.
3. Готово — открывай URL. Сайт работает, в Google не попадёт.

---

## 3. Как мы дорабатываем сайт дальше

Рабочий цикл простой:
1. Я готовлю изменения (новые страницы сравнений, «are we legit», schema и т.д.).
2. Ты коммитишь и пушишь в GitHub (`git push`) — **или я отдаю тебе готовые файлы**.
3. Railway сам пересобирает и обновляет копию за пару минут.
4. Смотрим результат на staging-URL, правим, повторяем.

Когда всё готово — отдаёшь партнёру ссылку на репозиторий: там код + все мои
комментарии (ищи пометки `STAGING:` и блоки `EN (dev handoff)`), он вливает в прод.

---

## 4. Локальный запуск (если захочешь у себя на компе)

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# положить staging-seed.sqlite как рабочую базу:
cp database/staging-seed.sqlite database/database.sqlite
# в .env: DB_CONNECTION=sqlite и DB_DATABASE=абсолютный путь к database/database.sqlite
php artisan migrate
php artisan serve
```
