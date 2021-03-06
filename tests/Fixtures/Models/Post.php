<?php

namespace TestModel;

use Akibatech\Crud\Fields\DatePickerField;
use Akibatech\Crud\Fields\FileUploadField;
use Akibatech\Crud\Fields\RadioField;
use Akibatech\Crud\Fields\SelectRelationField;
use Akibatech\Crud\Fields\TextareaField;
use Akibatech\Crud\Fields\TextField;
use Akibatech\Crud\Fields\TinymceField;
use Akibatech\Crud\Services\CrudFields;
use Akibatech\Crud\Services\CrudManager;
use Akibatech\Crud\Traits\Crudable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Crudable;

    public function getCrudFields()
    {
        return CrudFields::make([
            TextField::handle('title', 'required|min:3')->withPlaceholder('Title of the post'),
            TextareaField::handle('introduction', 'required|min:3')->withPlaceholder('Short introduction to the post'),
            TinymceField::handle('content', 'required|min:3')->withPlaceholder('Your content !'),
            FileUploadField::handle('illustration')->withMaxSize(1024 * 1024)->withTypes('jpeg,png'),
            SelectRelationField::handle('category_id')->withRelation('category')->withLabel('Category'),
            RadioField::handle('status', 'required')->withOptions(['draft' => 'Draft', 'live' => 'Live'], 'live'),
            DatePickerField::handle('published_at')->withDateFormat('Y-m-d')->withMinDate(Carbon::now()),
        ]);
    }

    public function getCrudManager()
    {
        return CrudManager::make()
            ->setNamePrefix('posts')
            ->setUriPrefix('crud/posts')
            ->setName('Post');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
