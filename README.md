# Print-on-demand storefront — backend

Laravel backend for a shop that sells personalised printed goods (tote bags, lunch bags, bento boxes,
daycare drink bottles, sticker labels). The buyer picks a product, size, design, colour, font and
options, types text lines onto the artwork, and orders; the shop produces print-ready files from the
saved personalisation.

The storefront is a separate Nuxt application: [maylogic/sfe2](https://github.com/maylogic/sfe2).

## Stack

| Layer      | Technology |
|------------|------------|
| Runtime    | PHP 8.4, Laravel 11 |
| API        | GraphQL — [Lighthouse 6](https://lighthouse-php.com), schema in `graphql/schema.graphql` |
| Admin      | Filament 3, 11 resources (products, orders, designs, fonts, colours, bases, coupons, sales rules, reviews, banners, previews) |
| Database   | MySQL 8 |
| Queues     | database driver (`php artisan queue:work`) |
| Payments   | T-Bank (Tinkoff) merchant API v2 — `app/Services/TBankMerchantAPI.php` |
| Local env  | Sail-style `docker-compose.yml`: nginx + PHP 8.4 runtime + MySQL 8 |

## Domain model in one screen

- **Catalog** — `Product` with `ProductSize`, `ProductOption` / `ProductOptionItem`, `ProductGalleryItem`,
  and `ProductLineDefinition` (text inputs with `max_length`, `required`, `hidden`, `sort`).
  A `Design` is a base artwork bound to allowed `Font`, `Color` and `Base` records.
- **Cart** — `Cart` / `CartItem` / `CartItemTextLine` / option items. Guest carts keyed by session
  `anon_id`, user carts by `user_id`; on login `CartService::mergeCarts()` sums identical
  personalisations, moves distinct ones, carries coupons over and deletes the guest cart.
- **Pricing** — `PriceService` + `SalesRuleService` + `RuleEvaluator`.
  Price of an item = product price + size price + option prices, then active sales rules are applied in
  sort order, then coupons. A rule is a row with `amount`, `is_percentage`, `product` / `shipping` flags
  and an optional **Symfony Expression Language** condition evaluated against a typed context
  (`ProductPriceContext`, `ShippingPriceContext`), e.g. `product.price > 500 and cart_subtotal.regular >= 3000`.
  Money never travels as a bare number: `App\Data\Price`, `App\Data\Totals`.
- **Coupons** — percentage codes with an optional stock (`quantity`), reserved on apply and returned on
  remove inside a transaction with `lockForUpdate()`.
- **Loyalty** — `Balance` / `BalanceTransaction`; the cart can spend points once, `apply_balance` flag.
- **Orders** — `OrderService::placeOrder()` copies cart items, text lines and option items into
  `Order` / `OrderItem` inside a single transaction and freezes `price_regular` / `price_discounted`
  and all totals, so later price changes cannot rewrite history.
- **Order lifecycle** — state machine in `app/States/Order`:
  `PENDING → PRINTING → PACKING → DELIVERY → COMPLETED`, with `CANCELED` available from any
  non-terminal state. `Order::process()` walks the pipeline, `Order::cancel()` stops it.
- **Delivery** — `ShippingMethod` with a provider enum (CDEK, Boxberry, Post, Post 1st class);
  `ShippingService` returns price and lead time, `ShippingInfo`.

## Requirements

- PHP 8.4 with `ext-curl`, `ext-dom`, `ext-libxml`, `ext-mbstring`, `ext-pdo_mysql`
- Composer 2
- Node 20 + npm (admin panel assets)
- Docker (optional, for MySQL)

`composer.json` also requires `ext-http`, which the application code never uses — a leftover from the
skeleton. On a machine without that extension install with:

```bash
composer install --ignore-platform-req=ext-http
```

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# Option A — SQLite, fastest way to click through
touch database/database.sqlite          # DB_CONNECTION=sqlite in .env

# Option B — MySQL 8 from the bundled compose file
./vendor/bin/sail up -d                 # nginx + PHP 8.4 + mysql:8.0
# then set DB_CONNECTION=mysql, DB_HOST=mysql, DB_DATABASE, DB_USERNAME, DB_PASSWORD

php artisan migrate
npm install && npm run build            # Vite manifest for the admin panel theme
php artisan serve
```

| What | Where |
|------|-------|
| GraphQL endpoint | `POST http://localhost:8000/graphql` |
| GraphQL explorer | `http://localhost:8000/graphiql` |
| Admin panel | `http://localhost:8000/bJjc` (Filament login) |
| Queue worker | `php artisan queue:work` |

Admin access is role-based (`App\Enum\UserRole`: `ADMIN`, `MANAGER`, `CUSTOMER`) — a user needs
`adminPanelAllowed()` to enter the panel.

Environment variables that matter beyond the Laravel defaults:

```
TBANK_MERCHANT_ID, TBANK_SECRET_KEY   # payments; without them the payment link resolver fails
TBANK_TAXATION, TBANK_FFD12, ...      # receipt data
FONTS_INSTALL_DIR                     # target for `php artisan fonts:install` (system fonts for print)
```

`php artisan fonts:install` copies the font files registered in the `fonts` table into
`FONTS_INSTALL_DIR`, so the print pipeline can resolve the same family names the browser used.

The `downloads/` directory holds ~3.7k exported print assets (SVG/PNG/JSON) and is used by
`download-files.js` and `check-cutlines.py` for batch exports. It is data, not application code —
cloning it costs ~135 MB.

## Quick check

Two calls that prove the API is wired up (both verified against a running instance):

```bash
# catalog, with the active sales rule already applied
curl -s localhost:8000/graphql -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d '{"query":"query { products(first: 3) { data { sku name price { regular is_discounted discounted } } } }"}'
```

```graphql
# put a personalised item into the session cart
mutation {
  addCartItem(
    productId: "1"
    quantity: 1
    personalize: { sizeId: "1", options: [], textLines: [{ productLineDefinitionId: "1", value: "Маша" }] }
  ) {
    items { quantity text_lines { value } price { regular discounted } }
    totals { subtotal grand_total { regular discounted } }
  }
}
```

## Tests

```bash
vendor/bin/phpunit --testdox         # 40 tests, 114 assertions, all green
```

`php artisan test` runs the same suite; on PHP 8.5 the framework's own config triggers
`PDO::MYSQL_ATTR_SSL_CA` deprecations, so it reports them alongside the results.

40 tests, 114 assertions, no external services: SQLite in memory, queue and session on `array`.

| File | What it pins down |
|------|-------------------|
| `tests/Unit/RuleEvaluatorTest.php` | expression rules: property access on typed contexts, compound conditions, and the rule that a broken expression means "no match", never a 500 |
| `tests/Feature/PricingTest.php` | product price with size and options, percentage and fixed rules, floor at zero, inactive and expired rules ignored, product vs shipping rules, coupon applied after rules, shipping discount |
| `tests/Feature/GraphqlCartTest.php` | the cart contract as the Nuxt client sees it: `addCartItem` with personalisation, unknown product validation, `updateCartItemQuantity` to zero, item counter, coupon apply/remove with quantity reservation |
| `tests/Feature/PlaceOrderTest.php` | `canPlaceOrder` guards, cart → order copy (items, text lines, options, price snapshots, totals), order lookup by email + id, full purchase flow through GraphQL |
| `tests/Feature/OrderStateMachineTest.php` | the printing pipeline, cancellation, and the two terminal states refusing to move |
| `tests/Feature/CartMergeTest.php` | guest → user merge: identical items summed, distinct items moved, coupon carried over, guest cart attached when the user has none |

Tests run against SQLite and therefore catch ambiguous SQL that MySQL would reject as well.

## Release / ops

- `composer install --optimize-autoloader --no-dev`, `npm run build`, `php artisan config:cache route:cache view:cache`
- `php artisan migrate --force`
- supervisord config for the queue worker lives in `docker/runtimes/8.4/supervisord.conf`

## Known gaps

- No CI pipeline: tests are run manually.
- The repo has no `.github` workflows and `composer.json` still carries the `laravel/laravel` skeleton name.
- On PHP 8.5 the framework emits deprecations (`PDO::MYSQL_ATTR_SSL_CA`); the target runtime is PHP 8.4.
- `public/build` is gitignored, and the admin panel theme asset is only registered when a manifest exists
  (`app/Providers/Filament/BJjcPanelProvider.php`), so `php artisan` works on a fresh clone without
  `npm run build`.
