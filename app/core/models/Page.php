<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\Media;

/**
 * Class Page
 * Model representing a page in the application.
 */
class Page extends AModel
{
    protected $table = 'pages';
    protected $fillable = ['title', 'content', 'slug', 'media_id'];

    /**
     * Page constructor.
     *
     * @param int|null $id The ID of the page to load.
     */
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    /**
     * Get the title of the page.
     *
     * @return string
     */
    public function title()
    {
        return $this->data->title;
    }

    /**
     * Get the content of the page.
     *
     * @return string
     */
    public function content()
    {
        return $this->data->content;
    }

    /**
     * Get the slug of the page.
     *
     * @return string
     */
    public function slug()
    {
        return $this->data->slug;
    }

    /**
     * Get the media associated with the page.
     *
     * @return Media
     */
    public function media()
    {
        return new Media($this->data->media_id);
    }

    /**
     * Get the validation rules for the page.
     *
     * @return array
     */
    public function validation()
    {
        return [
            'title' => 'required|min:3|max:255',
            'content' => 'required',
            'slug' => 'required|min:3|max:255|alpha_dash|unique:pages',
            'media_id' => 'required|exists:medias,id'
        ];
    }
}
