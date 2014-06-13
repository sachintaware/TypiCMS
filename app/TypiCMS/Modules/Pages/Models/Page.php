<?php
namespace TypiCMS\Modules\Pages\Models;

use Request;

use TypiCMS\Models\Base;
use TypiCMS\NestedCollection;

use Input;

class Page extends Base
{

    use \Dimsav\Translatable\Translatable;
    use \TypiCMS\Presenters\PresentableTrait;

    protected $presenter = 'TypiCMS\Modules\Pages\Presenters\ModulePresenter';

    protected $fillable = array(
        'meta_robots_no_index',
        'meta_robots_no_follow',
        'position',
        'parent',
        'rss_enabled',
        'comments_enabled',
        'is_home',
        'css',
        'js',
        'template',
        // Translatable fields
        'title',
        'slug',
        'uri',
        'status',
        'body',
        'meta_title',
        'meta_keywords',
        'meta_description',
    );

    /**
     * Translatable model configs.
     *
     * @var array
     */
    public $translatedAttributes = array(
        'title',
        'slug',
        'uri',
        'status',
        'body',
        'meta_title',
        'meta_keywords',
        'meta_description',
    );

    /**
     * The default route for admin side.
     *
     * @var string
     */
    public $route = 'pages';

    /**
     * lists
     */
    public $order = 'position';
    public $direction = 'asc';

    /**
     * For nested collection
     *
     * @var array
     */
    public $children = array();

    /**
     * return 'active' if this is the current page
     * 
     * @return string 'active' or ''
     */
    public function activeClass()
    {
        return Request::is($this->uri) ? 'active' : '';
    }

    /**
     * Relations
     */
    public function menulinks()
    {
        return $this->hasMany('TypiCMS\Modules\Menulinks\Models\Menulink');
    }

    /**
     * Scope from
     */
    public function scopeFrom($query, $relid)
    {
        return $query;
    }

    /**
     * Custom collection
     *
     * @return InvoiceCollection object
     */
    public function newCollection(array $models = array())
    {
        return new NestedCollection($models);
    }

    /**
     * Observers
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // set is_home = 0 on previous homepage
            if ($model->is_home) {
                static::where('is_home', 1)
                    ->update(array('is_home' => 0));
            }
        });

        static::updating(function ($model) {
            // set is_home = 0 on previous homepage
            if ($model->is_home) {
                static::where('is_home', 1)
                    ->where('id', '!=', $model->id)
                    ->update(array('is_home' => 0));
            }
        });
    }
}
