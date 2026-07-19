# Footer & SEO structure plan — cheapvinreport.com

Статус: черновик на согласование. Метки для партнёра: `STAGING:` / `EN (dev handoff)`.

## Стратегия
carfaxcheaper.com вышел в SEO-топ почти БЕЗ внешних ссылок → у них решает on-page +
бесплатные инструменты + контент, а не ссылочная масса. Вывод: повторяем весь их набор
инструментов и делаем ШИРЕ. Каждый инструмент = отдельная индексируемая страница под
свой запрос, в конце — воронка к покупке отчёта. Дёшево масштабируется (особенно
бренд-декодеры: одна механика — десятки страниц).

Итоговое нижнее меню — 4 колонки + нижняя юридическая строка.

## Колонка 1 — Company (траст)
Контакты переносим сюда. Отзывы — отдельной страницей здесь же.
- About Us — компания за проектом
- Are We Legit? — снимает страх «развод ли это»
- How It Works — прозрачность (VIN → отчёт)
- Sample Report — образец отчёта = доказательство продукта
- Reviews — Etsy-отзывы + ссылка на Trustpilot
- FAQ — закрывает возражения
- Blog — экспертный контент
- Contact & Support — support@cheapvinreport.email

## Колонка 2 — Free Tools (10 строк в футере, порядок = порядок сборки)
Делаем VIN-данные + калькуляторы. Бренд-декодеры и стикер/плейт — ОТЛОЖЕНЫ (в хаб /tools позже,
т.к. стикер/плейт = данные из платного отчёта, ещё не подключены).

1. VIN Check-Digit Validator — алгоритм, без API [самый простой]
2. Car Payment Calculator — JS
3. Fuel Cost Calculator — JS
4. Car Affordability Calculator — JS
5. Depreciation Calculator — JS
6. Lease vs Buy Calculator — JS
7. Cost of Ownership Calculator — JS (паритет с carfaxcheaper)
8. Model Year Decoder — VIN 10-й символ, мини-таблица
9. VIN Decoder — NHTSA API
10. Vehicle Specs Lookup — NHTSA API

ОТЛОЖЕНО (помним, делаем позже на /tools): бренд-декодеры (Toyota/Ford/Chevrolet/Honda/
Nissan/BMW/Mercedes/Jeep/Hyundai/Kia/VW/Porsche…), Country of Origin (WMI), Engine/Trim
Decoder, Refinance, Early Payoff, Sales Tax & Fees, Trade-in Estimator, License Plate Lookup,
Window Sticker by VIN.

## Колонка 3 — Vehicle Tools (VIN-чек лендинги → воронка к Carfax/Autocheck)
Твои 10. Каждая под запрос, с VIN-полем, ведёт к покупке отчёта.
- Title Check by VIN
- Lien Check by VIN
- Odometer Check
- Accident History
- Auction Records
- Recall Lookup (можно сделать реально бесплатным через NHTSA)
- Theft Check
- Owner History
- Service & Maintenance
- Market Value Estimate

## Колонка 4 — Compare (сравнения «мы vs …»)
Белые бренды / поставщики данных:
- vs Carfax.com
- vs AutoCheck
- vs EpicVIN
- vs Bumper
- vs ClearVIN
- vs carVertical

Другие продавцы дешёвого карфакса (прямые конкуренты):
- vs CarfaxCheaper (carfaxcheaper.com)
- vs CheapCarfax (cheapcarfax.net)
- vs CarfaxDeals (carfaxdeals.com)

Хаб:
- Best Cheap Carfax Alternatives (ловит запрос «carfax alternative»)

## Нижняя юридическая строка (как сейчас)
Terms of Service · Privacy Policy · Refund Policy · © 2026 + дисклеймер + иконки оплаты.

---
### Заметки
- Все ссылки — через LocaleRoute (5 языков), общая шапка/подвал.
- Приоритет по частотке (проверить инструментом): vs Carfax.com, vs AutoCheck,
  VIN Decoder, Carfax Alternatives, бренд-декодеры — наибольший спрос; делаем первыми.
- Много инструментов → в футере только топ-8 + хаб /tools со всем списком.
