# z-repository

[![Latest Stable Version](https://poser.pugx.org/zhaohehe/zrepository/v/stable)](https://packagist.org/packages/zhaohehe/zrepository)
[![License](https://poser.pugx.org/zhaohehe/zrepository/license)](https://packagist.org/packages/zhaohehe/zrepository)

z-repository is a package for Laravel 5 which is used to abstract the database layer. This makes applications much easier to maintain.

## Installation

Run the following command from you terminal:


 ```bash
 composer require "zhaohehe/zrepository"
 ```

In your ```config/app.php``` add  ```Zhaohehe\Repositories\Providers\RepositoryProvider::class```to the end of the providers array:

```php
'providers' => [
    ...
    Zhaohehe\Repositories\Providers\RepositoryProvider::class,
],
```

Publish Configuration
```bash
php artisan vendor:publish
```


## Usage

First, create your repository class. Note that your repository class MUST extend ```Zhaohehe\Repositories\Eloquent\Repository``` and implement model() method

```php
<?php namespace App\Repositories;

use Zhaohehe\Repositories\Eloquent\Repository;

class PoemRepository extends Repository {

    public function model() {
        return 'App\Models\Poem';
    }
}
```

By implementing ```model()``` method you telling repository what model class you want to use. Now, create ```App\Poem``` model:

```php
<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Poem extends Model {

}
```

And finally, use the repository in the controller:

```php
<?php namespace App\Http\Controllers;

use App\Repositories\PoemRepository as Poem;

class PoemController extends Controller {

    private $poem;

    public function __construct(Poem $poem) {

        $this->poem = $poem;
    }

    public function index() {
        return \Response::json($this->poem->all());
    }
}
```
Or you can run the following command to create repository and criteria class automatic
```bash
php artisan make:repository Poem 
php artisan make:criteria Poem
```

## Available Methods

The following methods are available:

##### Zhaohehe\Repositories\Contracts\RepositoryInterface

```php
public function all($columns = ['*']);

public function paginate($perPage = 15, $columns = ['*']);

public function create(array $data);

public function save(array $data);

public function delete($id);

public function update(array $data, $id);

public function find($id, $columns = ['*']);

public function findBy($field, $value, $columns = ['*']);

public function findWhere($where, $columns = ['*']);
```

##### Zhaohehe\Repositories\Contracts\CriteriaInterface

```php
public function skipCriteria($status = true);

public function getCriteria();

public function getByCriteria(Criteria $criteria);
    
public function pushCriteria(Criteria $criteria);

public function applyCriteria();
```

### Example usage


Create a new Poem in repository:

```php
$this->Poem->create(Input::all());
```

Update existing Poem:

```php
$this->Poem->update(Input::all(), $id);
```

Delete Poem:

```php
$this->poem->delete($id);
```

Find Poem by id;

```php
$this->poem->find($id);
```

you can also chose what columns to fetch:

```php
$this->poem->find($id, ['title', 'description', 'author']);
```

Get a single row by a single column criteria.

```php
$this->poem->findBy('title', $title);
```

Or you can get all rows by a single column criteria.
```php
$this->poem->findAllBy('author', $author_id);
```

Get all results by multiple fields

```php
$this->poem->findWhere([
    'author' => $author_id,
    ['year','>',$year]
]);
```

## Criteria

Criteria is a simple way to apply specific condition, or set of conditions to the repository query. Your criteria class MUST extend the abstract ```Zhaohehe\Repositories\Criteria\Criteria``` class.

Here is a simple criteria:

```php
<?php namespace App\Repositories\Criteria\Poem;

use Zhaohehe\Repositories\Criteria\Criteria;
use Zhaohehe\Repositories\Contracts\RepositoryInterface as Repository;

class CreatedInTangDynasty extends Criteria {

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * 
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->where('dynasty', '=', '唐朝');
        return $model;
    }
}
```

Now, inside you controller class you call pushCriteria method:

```php
<?php namespace App\Http\Controllers;

use App\Repositories\Criteria\CreatedInTangDynasty;
use App\Repositories\PoemRepository as Poem;

class PoemController extends Controller {

    /**
     * @var Poem
     */
    private $poem;

    public function __construct(Poem $poem) {

        $this->poem = $poem;
    }

    public function index() {
        $this->poem->pushCriteria(new CreatedInTangDynasty());
        return \Response::json($this->poem->all());
    }
}
```


## Credits

This package is largely inspired by [this](https://github.com/prettus/l5-repository) great package by @andersao . But I don't like it :).