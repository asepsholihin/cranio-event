<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\LogProductActivity;
use Carbon\Carbon;
use Illuminate\Support\Str;

class WebProduct extends Model
{
    use HasFactory, Sluggable;
    use QueryCacheable;

    const DIR_FILE = 'web/catalog/files';

    protected $table = 'web_products';

    protected static $flushCacheOnUpdate = true;

    public $cacheFor = 3600;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'lang_id',
        'sub_category_id',
        'package_type',
        'trip_id',
        'name',
        'slug',
        'description',
        'flights',
        'image_thumbnail',
        'image_background',
        'package_price',
        'price_start_from',
        'departure_date',
        'counter',
        'status',
        'deleted',
        'created_by',
        'updated_by',
        'image_itinerary',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'canonical',
        'alt_image_thumbnail',
        'title_image_thumbnail',
        'alt_image_background',
        'title_image_background',
        'meta_index',
        'title',
        'total_days',
        'hotel_star',
        'product_name',
        'change_price_start_from',
        'itinerary_description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'total_days',
        'star',
        'low_star',
        'destinations',
    ];

    public function getStarAttribute()
    {

        $hotel = DB::table('hotel_umroh_trips')->select([
            DB::raw('MAX(GREATEST(star::integer)) as star')
        ])->where('umroh_trip_id', $this->trip_id)->first();

        if ($hotel->star) {
            return $hotel->star ?? 3;
        }

        $package = DB::table('package_umroh_trips')->select([
            DB::raw('MAX(GREATEST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as star')
        ])
            ->where('umroh_trip_id', $this->trip_id)
            ->first();

        return $package->star ?? 3;
    }

    public function getLowStarAttribute()
    {
        $hotel = DB::table('hotel_umroh_trips')->select([
            DB::raw('MIN(LEAST(star::integer)) as star')
        ])->where('umroh_trip_id', $this->trip_id)->first();
        if ($hotel) {
            return $hotel->star ?? 3;
        }

        $package = DB::table('package_umroh_trips')->select([
            DB::raw('MIN(LEAST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as star')
        ])
            ->where('umroh_trip_id', $this->trip_id)
            ->first();

        return $package->star ?? 3;
    }

    public function getDestinationsAttribute()
    {
        $umrohTrip = DB::table('umroh_trips')->select('location_destination_id')->find($this->trip_id);
        $destinations = array();

        if ($umrohTrip) {
            if ($umrohTrip->location_destination_id) {
                $location_ids = Str::replace('[', '', $umrohTrip->location_destination_id);
                $location_ids = Str::replace(']', '', $location_ids);
                $location_ids = explode(',', $location_ids);
                if(!$location_ids) {
                    $destinations = City::select(['name'])
                    ->whereIn('id', $location_ids)
                    ->pluck('name');
                }
            }
        }

        return $destinations;
    }

    public function getTotalDaysAttribute()
    {
        $umrohTrip = DB::table('umroh_trips')->select('total_days')->where('id', $this->trip_id)->first();
        $days = "";
        if ($umrohTrip) {
            $days = $umrohTrip->total_days;
        }

        return $days;
    }

    public function imageThumbnail(): Attribute
    {
        // if (App::environment('production') && request()->is('api/public/**')) {
        //     return Attribute::make(
        //         get: fn ($value, $attributes) =>
        //             ($attributes['image_thumbnail'] == null) ? ($attributes['sub_category_id'] == 2) ? asset('images/thumbnail_umrah_sapphire.webp') : (($attributes['sub_category_id'] == 1) ? asset('images/thumbnail_umrah_ruby.webp') : asset('images/paket-umroh.png')) : $attributes['image_thumbnail']
        //     );
        // }

        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['image_thumbnail'] == null) {
                    $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                    $package = PackageUmrohTrip::find($attributes['package_type']);
                    if($package) {
                        if ($package->name == "Ruby") {
                            $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                        }
                        if ($package->name == "Emerald") {
                            $thumbnail = asset('images/thumbnail_umrah_emerald.webp');
                        }
                        if ($package->name == "Sapphire") {
                            $thumbnail = asset('images/thumbnail_umrah_sapphire.webp');
                        }
                        if ($package->name == "Plus Bintang 4") {
                            $thumbnail = asset('images/thumbnail_umrah_emerald.webp');
                        }
                        if ($package->name == "Plus Bintang 5") {
                            $thumbnail = asset('images/thumbnail_umrah_sapphire.webp');
                        }
                        if ($package->name == "Lebih Hemat") {
                            $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                        }
                    }
                    return $thumbnail;
                } else {
                    if (App::environment('production') && request()->is('api/public/**')) {
                        return $attributes['image_thumbnail'];
                    } else {
                        return StorageAttributes::getTempUrl($attributes['image_thumbnail'] ?? null);
                    }
                }
            }
        );
    }

    public function imageBackground(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => ($attributes['image_background'] == null) ? asset('images/background_umrah.webp') : $attributes['image_background'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => ($attributes['image_background'] == null) ? asset('images/background_umrah.webp') : StorageAttributes::getTempUrl(
                $attributes['image_background'] ?? null
            ),
        );
    }

    public function imageIcon(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) =>
                $attributes['image_icon'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_icon'] ?? null
            ),
        );
    }

    public function imageItinerary(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) =>
                $attributes['image_itinerary'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_itinerary'] ?? null
            ),
        );
    }

    public function airlineLogo(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) =>
                $attributes['airline_logo'] ?? null,
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['airline_logo'] ?? null
            ),
        );
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function sub_category()
    {
        return $this->belongsTo(WebSubcategory::class, 'sub_category_id');
    }

    public function trip()
    {
        return $this->belongsTo(UmrohTrip::class, 'trip_id')->withTrashed();
    }

    public function package()
    {
        return $this->belongsTo(PackageUmrohTrip::class, 'package_type');
    }

    public function images()
    {
        return $this->hasMany(WebProductImage::class, 'web_product_id');
    }

    public static function boot()
    {
        parent::boot();

        self::created(function (WebProduct $item) {
            $message = Auth::user()->name . " membuat katalog product baru " . $item->title;
            LogProductActivity::create(['product_id' => $item->id, 'user_id' => Auth::id(), 'log_type' => 'create', 'message' => $message]);
        });

        self::updated(function (WebProduct $item) {
            if(Auth::user()) {
                $message = Auth::user()->name . " melakukan perubahan pada product " . $item->title;
                LogProductActivity::create(['product_id' => $item->id, 'user_id' => Auth::id(), 'log_type' => 'update', 'message' => $message]);
            }
        });

        self::deleted(function (WebProduct $item) {
            $message = Auth::user()->name . " menghapus product " . $item->title;
            LogProductActivity::create(['product_id' => $item->id, 'user_id' => Auth::id(), 'log_type' => 'delete', 'message' => $message]);
        });
    }

    public function scopeTableSearch($query)
    {
        $query->leftJoin('package_umroh_trips', 'package_umroh_trips.id', 'web_products.package_type');
        $query->leftJoin('packages', 'packages.name', 'package_umroh_trips.name');
        $query->join('umroh_trips', 'umroh_trips.id', 'web_products.trip_id');
        $query->with(['sub_category', 'sub_category.category', 'trip', 'trip.packages', 'package']);
        $query->select('web_products.*', 'packages.image_icon', 'location_destination_id');
        if (!empty(request()->query('subcategoryId'))) {
            if (request()->query('subcategoryId') == "2_5") {
                $query->whereIn('web_products.sub_category_id', array(2, 5));
            } else if (request()->query('subcategoryId') == "7_8") {
                $query->whereIn('web_products.sub_category_id', array(7, 8));
            } else {
                $query->where('web_products.sub_category_id', request()->query('subcategoryId'));
            }
        }

        if (!empty(request()->query('packageType'))) {
            $packageTypes = explode(",", request()->query('packageType'));
            $query->where(function ($queries) use ($packageTypes) {
                foreach ($packageTypes as $packageType) {
                    $queries->orWhere('package_umroh_trips.name', 'like', '%' . $packageType . '%');
                }
            });
        }

        if (empty(request()->query('q', ''))) {
            return $query;
        }
        $search = '%' . request()->query('q') . '%';
        return $query->where('web_products.name', 'like', $search)
            ->orWhere('web_products.description', 'like', $search);
    }

    public function scopeTableSearchPublic($query)
    {
        $query->leftJoin('package_umroh_trips', 'package_umroh_trips.id', 'web_products.package_type');
        $query->leftJoin('packages', 'packages.name', 'package_umroh_trips.name');
        $query->leftjoin('airlines', 'airlines.id', 'web_products.flights');
        $query->join('umroh_trips', 'umroh_trips.id', 'web_products.trip_id');
        $query->with(['sub_category', 'sub_category.category', 'trip', 'trip.packages', 'package']);
        $query->select('web_products.*', 'packages.image_icon', 'airlines.logo as airline_logo', 'location_destination_id');
        $query->whereColumn('umroh_trips.taken_seats', '<>', 'umroh_trips.number_of_seats');

        if (request()->is('api/*')) {
            $query->where('web_products.status', 1);
            $query->whereDate('web_products.departure_date', '>=', now());
        }

        if (!empty(request()->query('categoryId'))) {
            $query->whereHas('sub_category', function ($query) {
                $query->where('category_id', request()->categoryId);
            });
        }

        if (!empty(request()->query('subcategoryId'))) {
            if (request()->query('subcategoryId') == "2_5") {
                $query->whereIn('web_products.sub_category_id', array(2, 5));
            } else if (request()->query('subcategoryId') == "7_8") {
                $query->whereIn('web_products.sub_category_id', array(7, 8));
            } else {
                $query->where('web_products.sub_category_id', request()->query('subcategoryId'));
            }
        }

        if (!empty(request()->query('departureDate'))) {
            $departureDate = Carbon::parse(request()->query('departureDate'))->format('Y-m-d');
            $query->where('departure_date', $departureDate);
        }


        if (!empty(request()->query('packageType'))) {
            $packageTypes = explode(",", request()->query('packageType'));
            $query->where(function ($queries) use ($packageTypes) {
                foreach ($packageTypes as $packageType) {
                    $queries->orWhere('package_umroh_trips.name', 'like', '%' . $packageType . '%');
                }
            });
        }

        if (!empty(request()->query('productId'))) {
            $query->where('web_products.id', '<>', request()->query('productId'));
        }

        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where('web_products.name', 'like', $search)
            ->orWhere('web_products.description', 'like', $search);
    }
}
