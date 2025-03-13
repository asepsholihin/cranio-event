<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\App;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Support\Facades\Auth;
use App\Models\LogArticleActivity;

class Article extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;
    use Sluggable; //, QueryCacheable;

    protected $table = 'web_blogs';

    // protected static $flushCacheOnUpdate = true;

    // public $cacheFor = 3600;

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    const CREATED_FROM_SPA = 1;
    const CREATED_FROM_MOBILE = 2;

    const IMAGE = 'image';
    const DIR_IMAGE = 'web/articles/';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'lang_id',
        'category_id',
        'title',
        'slug',
        'keywords',
        'content',
        'image_url',
        'order',
        'status',
        'created_by',
        'updated_by',
        'meta_title',
        'meta_description',
        'canonical',
        'meta_index',
        'written_by',
        'reviewed_by',
        'seo_score',
        'seo_checks',
        'count_internal_link',
        'count_external_link',
        'thumbnail_url',
        'show_in_page',
        'type',
        'asatidz_id',
        'youtube_link',
        'views_count',
        'views_count_visible',
        'written_by_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'seo_checks'
    ];

    protected $appends = [
        'url'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function getUrlAttribute()
    {
        return "https://www.jejakimani.com/artikel/" . $this->slug;
    }

    public function imageUrl(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) =>
                $attributes['image_url'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_url'] ?? null
            ),
        );
    }

    // public function thumbnailUrl(): Attribute
    // {
    //     if (App::environment('production') && request()->is('api/public/**')) {
    //         return Attribute::make(
    //             get: fn ($value, $attributes) =>
    //             $attributes['thumbnail_url'],
    //         );
    //     }

    //     return Attribute::make(
    //         get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
    //             $attributes['thumbnail_url'] ?? null
    //         ),
    //     );
    // }

    public function scopeTableSearch($query)
    {
        $query
            ->join('users', 'users.id', '=', 'web_blogs.created_by')
            ->leftjoin('web_blog_categories', 'web_blog_categories.id', '=', 'web_blogs.category_id')
            ->select('web_blogs.*', 'users.name as created_by_name', 'web_blog_categories.name as category');

        if (request()->is('api/*')) {
            $query->where('status', 1);
        }

        if (request()->categoryId) {
            $query->where('web_blogs.category_id', request()->categoryId);
        }

        if (request()->category) {
            $query->where('web_blog_categories.slug', request()->category);
        }

        if (request()->showInPage) {
            $query->where('web_blogs.show_in_page', request()->showInPage);
        }

        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where(function($q) use($search) {
            $q->where('title',  request()->query('q'))
            ->orWhere('title', 'like', $search)
            ->orWhere('keywords', 'like', $search);
        });
    }

    public static function boot()
    {
        parent::boot();

        self::created(function(Article $item){
            $message = Auth::user()->name." membuat artikel baru " . $item->title;
            LogArticleActivity::create(['article_id'=>$item->id, 'user_id'=>Auth::id(), 'log_type'=>'create', 'message'=>$message]);
        });

        self::updated(function(Article $item){
            if(Auth::user()) {
                $message = Auth::user()->name." melakukan perubahan pada artikel " . $item->title;
                LogArticleActivity::create(['article_id'=>$item->id, 'user_id'=>Auth::id(), 'log_type'=>'update', 'message'=>$message]);
            }
        });

        self::deleted(function(Article $item){
            $message = Auth::user()->name." menghapus artikel " . $item->title;
            LogArticleActivity::create(['article_id'=>$item->id, 'user_id'=>Auth::id(), 'log_type'=>'delete', 'message'=>$message]);
        });
    }
}
