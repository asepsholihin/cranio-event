## Public Open API

<table>
<thead>
<tr>
<th>Endpoints</th>
<th>Description</th>
<th>Request</th>
<th>Response</th>
</tr>
</thead>
<tbody>
<tr>
<td>

`GET /api/public/web-settings/general`

</td>
<td>Display current general web settings</td>
<td></td>
<td>

```json
{
    "web_logo": "https://s3.amazonaws.com/<bucket>/<dir>/logo.jpg",
    "web_favicon": "https://s3.amazonaws.com/<bucket>/<dir>/favicon.jpg",
    "web_title": "Travel Umroh Paket Umroh Terbaik di Jakarta Depok Tangerang Bekasi",
    "web_email": "cs@jejakimani.com",
    "meta_keywords": "umroh, paket umroh, travel, travel umroh, umroh indonesia",
    "meta_description": "Jejak Imani Merupakan Travel Umroh Paket Umroh terbaik dan Berkualitas melayani Info biaya Umroh di Jakarta Bogor Depok Tangerang Bekasi",
    "lang_id": null,
    "phone_number": "+62 851 5705 0220",
    "wa_number_1": "+62 821-821-821",
    "wa_number_2": "+62 821-821-821",
    "copyright_text": "Copyright © 2019 Jejak Imani. All Rights Reserved.",
    "cta_button_text": "Daftar Sekarang",
    "footer_consultation": "Hubungi Kami",
    "footer_location": "Jl Siliwangi No.4, Pondok Benda, Kec. Pamulang, Kota Tangerang Selatan, Banten 15417"
}
```

</td>
</tr>

<tr>
<td>

`GET /api/public/web-settings/index-page`

</td>
<td>Display current index page settings</td>
<td></td>
<td>

```json
{
    "why_us_title": "Mengapa Kami",
    "about_image": "https://s3.amazonaws.com/<bucket>/<dir>/about.jpg",
    "about_title": "Tentang Kami",
    "profile_ustadz_image": "https://s3.amazonaws.com/<bucket>/<dir>/about.jpg",
    "profile_ustadz_title": "Profil Ustadz",
    "tour_package_title": "Paket Umroh",
    "article_title": "Artikel",
    "partner_title": "Partner",
    "lang_id": null,
    "video_url": "https://www.youtube.com/embed/86ihAFXmNEM"
}
```

</td>
</tr>

<tr>
<td>

`GET /api/public/catalog/categories`

</td>
<td>Display pagination list of catalog categories</td>
<td>
Query Params:

`q` (optional) (default=`null`) value to search

`page` (optional) (default=`1`) page to fetch

`perPage` (optional) (default=`10`) list data per page to show

`sortBy` (optional) (default=`id`) sort by key

`sortDesc` (optional) (default=`false`) enable sort by descending

</td>
<td>

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "lang_id": null,
            "name": "dadu",
            "description": "lorema",
            "active": 0,
            "order": 1,
            "status": 1,
            "deleted": 0,
            "sub_categories": []
        }
    ],
    "first_page_url": "/catalog/categories?perPage=10&sortBy=id&sortDesc=true&page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "/catalog/categories?perPage=10&sortBy=id&sortDesc=true&page=3",
    "links": [],
    "next_page_url": "/catalog/categories?perPage=10&sortBy=id&sortDesc=true&page=2",
    "path": "/catalog/categories",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

</td>
</tr>

<tr>
<td>

`GET /api/public/catalog/categories/:id`

</td>
<td>Display detail of catalog category by id</td>
<td></td>
<td>

```json
{
    "id": 1,
    "lang_id": null,
    "name": "dadud",
    "description": "lorema",
    "active": 0,
    "order": 2,
    "status": 1,
    "deleted": 0
}
```

</td>
</tr>

<tr>
<td>

`GET /api/public/catalog/sub-categories`

</td>
<td>Display pagination list of catalog sub categories</td>
<td>
Query Params:

`q` (optional) (default=`null`) value to search

`page` (optional) (default=`1`) page to fetch

`perPage` (optional) (default=`10`) list data per page to show

`sortBy` (optional) (default=`id`) sort by key

`sortDesc` (optional) (default=`false`) enable sort by descending

</td>
<td>

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "lang_id": null,
            "category_id": 24,
            "name": "ducimusajs",
            "description": "Facere error et quidem et minus quos. Ea possimus qui voluptatem suscipit ipsa velit dolorem quo. Possimus aut officiis aut culpa eum.",
            "active": 0,
            "order": 3,
            "status": 1,
            "deleted": 0,
            "category": {
                "id": 24,
                "lang_id": null,
                "name": "in",
                "description": "Totam dolor similique autem adipisci ut qui natus. Commodi velit soluta aperiam facilis similique aut. Ut quos praesentium laudantium odit. Ipsum aperiam voluptas suscipit dignissimos.",
                "active": 1,
                "order": 1,
                "status": 1,
                "deleted": 0
            }
        }
    ],
    "first_page_url": "/catalog/sub-categories?perPage=10&sortBy=id&sortDesc=true&page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "/catalog/sub-categories?perPage=10&sortBy=id&sortDesc=true&page=3",
    "links": [],
    "next_page_url": "/catalog/sub-categories?perPage=10&sortBy=id&sortDesc=true&page=2",
    "path": "/catalog/sub-categories",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

</td>
</tr>

<tr>
<td>

`GET /api/public/catalog/sub-categories/:id`

</td>
<td>Display detail of catalog sub category by id</td>
<td></td>
<td>

```json
{
    "id": 43,
    "lang_id": null,
    "category_id": 24,
    "name": "ducimusajs",
    "description": "Facere error et quidem et minus quos. Ea possimus qui voluptatem suscipit ipsa velit dolorem quo. Possimus aut officiis aut culpa eum.",
    "active": 0,
    "order": 3,
    "status": 1,
    "deleted": 0,
    "category": {
        "id": 24,
        "lang_id": null,
        "name": "in",
        "description": "Totam dolor similique autem adipisci ut qui natus. Commodi velit soluta aperiam facilis similique aut. Ut quos praesentium laudantium odit. Ipsum aperiam voluptas suscipit dignissimos.",
        "active": 1,
        "order": 1,
        "status": 1,
        "deleted": 0
    }
}
```

</td>
</tr>

<tr>
<td>

`GET /api/public/catalog/products`

</td>
<td>Display pagination list of catalog products</td>
<td>
Query Params:

`q` (optional) (default=`null`) value to search

`page` (optional) (default=`1`) page to fetch

`perPage` (optional) (default=`10`) list data per page to show

`sortBy` (optional) (default=`id`) sort by key

`sortDesc` (optional) (default=`false`) enable sort by descending

</td>
<td>

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "lang_id": null,
            "sub_category_id": 26,
            "package_type": 2,
            "trip_id": 1,
            "name": "hua",
            "description": "asd",
            "flights": 2,
            "image_thumbnail": "0",
            "image_background": "0",
            "package_price": "10000",
            "price_start_from": "10000",
            "departure_date": "2022-08-20 00:00:00",
            "counter": 0,
            "status": 1,
            "deleted": 0,
            "sub_category": {
                // detail from sub category
                "id": 26,
                "name": "nihil",
                "category": {
                    // detail from category
                    "id": 23,
                    "name": "magni"
                }
            },
            "trip": {
                // detail from trip
                "id": 1,
                "title": "a"
            }
        }
    ],
    "first_page_url": "/catalog/products?perPage=10&sortBy=id&sortDesc=true&page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "/catalog/products?perPage=10&sortBy=id&sortDesc=true&page=3",
    "links": [],
    "next_page_url": "/catalog/products?perPage=10&sortBy=id&sortDesc=true&page=2",
    "path": "/catalog/products",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

</td>
</tr>

<tr>
<td>

`GET /api/public/catalog/products/:id`

</td>
<td>Display detail of catalog product by id</td>
<td></td>
<td>

```json
{
    "id": 1,
    "lang_id": null,
    "sub_category_id": 26,
    "package_type": 2,
    "trip_id": 1,
    "name": "hua",
    "description": "asd",
    "flights": 2,
    "image_thumbnail": "0",
    "image_background": "0",
    "package_price": "10000",
    "price_start_from": "10000",
    "departure_date": "2022-08-20 00:00:00",
    "counter": 0,
    "status": 1,
    "deleted": 0,
    "sub_category": {
        // detail from sub category
        "id": 26,
        "name": "nihil",
        "category": {
            // detail from category
            "id": 23,
            "name": "magni"
        }
    },
    "trip": {
        // detail from trip
        "id": 1,
        "title": "a"
    }
}
```

</td>
</tr>
</tbody>

</table>
