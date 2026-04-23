WITH product_list AS (
    -- Сначала фильтруем продукты по категории для уменьшения объема данных
    SELECT p.product_id, p.product_name, m.manufacturer_name
    FROM product p
    JOIN manufacturer m ON p.manufacturer_id = m.manufacturer_id
    WHERE p.category_id = :category_id
),
base_prices AS (
    -- Получаем последнюю цену вне периода (базовая цена)
    SELECT DISTINCT ON (price.product_id)
        price.product_id,
        price as base_price,
        price_date as base_price_date
    FROM price
    -- todo JOIN product_list ON price.product_id = product_list.product_id
    WHERE
        -- todo лучше join или where? или вовсе не фильтровать?
        -- todo product_id IN (SELECT product_id FROM product_list) AND
        price.price_date < CURRENT_DATE - INTERVAL '7 days'
    ORDER BY price.product_id, price.price_date DESC
),
last_week_price AS (
    -- нашёл min/max цены (и даты) за последнюю неделю
    SELECT DISTINCT ON (p.product_id)
        p.product_id,
        first_value(price) OVER (PARTITION BY p.product_id ORDER BY p.price ASC, p.price_date ASC) as min_price,
        first_value(price_date) OVER (PARTITION BY p.product_id ORDER BY p.price ASC, p.price_date ASC) as min_price_date,
        first_value(price) OVER (PARTITION BY p.product_id ORDER BY p.price DESC, p.price_date ASC) as max_price,
        first_value(price_date) OVER (PARTITION BY p.product_id ORDER BY p.price DESC, p.price_date ASC) as max_price_date
    FROM price p
    WHERE p.price_date >= CURRENT_DATE - INTERVAL '7 days'
)
SELECT
    pl.manufacturer_name,
    pl.product_name,
    ROUND(COALESCE(lp.min_price, bp.base_price), 2) as min_price,
    COALESCE(lp.min_price_date, bp.base_price_date) as min_price_date,
    ROUND(COALESCE(lp.max_price, bp.base_price), 2) as max_price,
    COALESCE(lp.max_price_date, bp.base_price_date) as max_price_date
FROM product_list pl
    LEFT JOIN base_prices bp ON pl.product_id = bp.product_id
    LEFT JOIN last_week_price lp ON pl.product_id = lp.product_id
