# z-repository

>z-repository是一个为laravel5提供的数据库抽象层，目的是为了将应用的数据库操作和核心的业务逻辑分离开，保证controller的精致。

## 简介
>z-repository提供了criteria和transformer来接管数据库的查询和查询结果的展示，使得各部分分离开来，解开耦合。同时repository接管model层，使得model层专注于数据模型本身的定义，比如relationship，fillable等

## 安装

在终端中输入以下命令，通过composer来安装


 ```bash
 composer require "zhaohehe/zrepository:1.1.0"
 ```

然后，打开laravel的```config/app.php``` 文件，增加```Zhaohehe\Repositories\Providers\RepositoryProvider::class``` 到你的providers数组

```php
'providers' => [
    ...
    Zhaohehe\Repositories\Providers\RepositoryProvider::class,
],
```

最后，发布，这会在你的```config```目录下生成一个```repository.php```文件，用来配置repository
```bash
php artisan vendor:publish
```


## 使用

### repository

首先，创建你的repository类，你可以在命令行中使用如下命令自动生成该类
```bash
php artisan make:repository Poem --model Poem
```
其中，--model Poem 是可选的，用来指定repository中model的名称，默认情况下，会根据repository的名称自动产生model名，你可以在repository.php配置文件中设置该类的命名空间等，后面的criteria和transforme的自动生成也是这样，生成的文件如下：

```php
<?php namespace App\Repositories;

use Zhaohehe\Repositories\Eloquent\Repository;

class PoemRepository extends Repository 
{

    public function model() 
    {
        return 'App\Models\Poem';
    }
}
```
当然，你也可以手动创建repository类，该类务必继承自```Zhaohehe\Repositories\Eloquent\Repository``` ，且实现model()方法，该方法用来指定该repository对应的数据模型。
现在，来创建你的```App\Poem``` 数据模型：


```php
<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Poem extends Model 
{

}
```

最后，在你的controller中使用repository

```php
<?php namespace App\Http\Controllers;

use App\Repositories\PoemRepository as Poem;

class PoemController extends Controller 
{

    protected $poem;

    public function __construct(Poem $poem) 
    {

        $this->poem = $poem;
    }

    public function index() 
    {
        return $this->poem->all();
    }
}
```

#### 暂时可用的方法

下面这些方法是暂时可以使用的，显然不够，后面会陆续添加

##### Zhaohehe\Repositories\Contracts\RepositoryInterface

```php
public function all($columns = ['*']);    //获取所有记录

public function paginate($perPage = 15, $columns = ['*']);    //分页，默认每页15条

public function create(array $data);    //创建一条记录

public function save(array $data);    //保存

public function delete($id);    //删除一条记录

public function update(array $data, $id);    //更新记录

public function find($id, $columns = ['*']);    //按id查找

public function findBy($field, $value, $columns = ['*']);    //按指定字段查找

public function findWhere($where, $columns = ['*']);    //按多个条件查找
```


#### 例子


创建一条记录:

```php
$this->Poem->create(Input::all());
```

更新记录:

```php
$this->Poem->update(Input::all(), $id);
```

删除记录:

```php
$this->poem->delete($id);
```

按id查找;

```php
$this->poem->find($id);
```

你可以指定要查询的字段:

```php
$this->poem->find($id, ['title', 'description', 'author']);
```

根据指定字段的值来查找.

```php
$this->poem->findBy('title', $title);
```

多个条件查找

```php
$this->poem->findWhere([
    'author' => $author_id,
    ['year','>',$year]
]);
```

### Criteria

>Criteria是一个让你可以根据具体的或者一系列复杂的条件来向你的repository发起查询的方式，你可以将一些可能会在多个接口或者情况下用到的查询条件放到这里，到达复用的目的，而且可以将复杂的查询条件从你的controller中抽离出来，精简代码的同时，也使得各部分之间的耦合更加松散，你的criteria类必须继承自```Zhaohehe\Repositories\Criteria\Criteria``` 抽象类。

一个简单的例子，比如你要查询所有唐代的诗词，（实际情况下，查询的条件可能要复杂的多，否则就没必要将它抽离出来了）:

```php
<?php namespace App\Repositories\Criteria\Poem;

use Zhaohehe\Repositories\Criteria\Criteria;
use Zhaohehe\Repositories\Contracts\RepositoryInterface as Repository;

class CreatedInTangDynasty extends Criteria 
{

    public function apply($model, Repository $repository)
    {
        $model = $model->where('dynasty', '=', '唐朝');
        return $model;
    }
}
```

现在，在你的controller里面，你可以调用repository的```pushCriteria```方法:

```php
<?php namespace App\Http\Controllers;

use App\Repositories\Criteria\CreatedInTangDynasty;
use App\Repositories\PoemRepository as Poem;

class PoemController extends Controller 
{
    private $poem;

    public function __construct(Poem $poem) 
    {
        $this->poem = $poem;
    }

    public function index() 
    {
        $this->poem->pushCriteria(new CreatedInTangDynasty());
        return $this->poem->all();
    }
}
```

### Transformer
>Transformers 的作用是按照接口的需要来包装你从数据库查询出来的结果，你可以在这里方便的设置你需要哪些字段，每一个字段的数据类型，或者你要联查多个表来组成接口所需要的数据时，你可以在这里利用eloquent的relationship方便的完成，每一个Transformer都需要继承自```League\Fractal\TransformerAbstract```抽象类，这是一个第三方的包，需要你用composer引入

 ```composer require league/fractal```

###### 用以下命令创建一个Transformer

```bash
php artisan make:transformer Poem
```
这会创建下面这样的一个Transformer类，比如你需要从接口返回一首诗的id，title，和author，而你的Poem表里面只存有author_id，你可以用Model的relationship方便的做到，这需要你在Poem Model中去定义和Author Model之间的关系，这部分详情请看官方文档的介绍：

```php
use App\Models\Poem;
use League\Fractal\TransformerAbstract;

class PoemTransformer extends TransformerAbstract
{
    public function transform(Poem $model)
    {
        return [
            'id'      => (int) $model->id,
            'title'   => $model->title,
            'author'  => $model->author->name
        ];
    }
}
```
###### 使用repository

在repository中使用
```php
namespace App\Repositories;
use Zhaohehe\Repositories\Eloquent\Repository;

class PoemRepository extends Repository
{
    public function model()
    {
        return 'App\Poem';
    }
    
    public function transfomer()
    {
        return "App\\Transformers\\PoemTransformer";
    }
}
```
你也可以在controller中调用```setTransformer```方法来使用
```php
$this->poem->setTransformer("App\\Transformers\\PoemTransformer");
```

###### 在Model后使用
当你在对repository使用了transformer之后，也许在某些场景下你不希望查询结果自动的被transform掉，你可以调用repository的```skipTransformer()```方法来跳过转换。这个时候你可以让你的Model去实现```Zhaohehe\Repositories\Contracts\Transformable```接口，这样你就可以在你想要的时候直接调用Model的```transform```方法来灵活地呈现你的查询结果的样式，当你的Model实现了```Zhaohehe\Repositories\Contracts\Transformable```接口之后，你必须要使用```Zhaohehe\Repositories\Traits\TransformableTraits```来赋予Model相应的功能，代码如下：

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Zhaohehe\Repositories\Contracts\Transformable;
use Zhaohehe\Repositories\Traits\TransformableTraits;

class Poem extends Model implements Transformable
{
    use TransformableTraits;
}

```
现在，你可以随心所欲地transform查询结果了：
```php
$repository = app('App\Repositories\PoemRepository');
$repository->setTransformer("App\\Transformers\\PoemTransformerr");

$poem = $repository->find(1);    //获取被转换过的查询结果

dd( $poem );    //这会返回一个按照Poemtransformer定义过的数组

...

$poem = $repository->skipTransformer()->find(1);    //跳过transformer，返回原始的数据模型查询结果

dd( $poem );    //这会返回一个普通model的查询结果
dd( $posem->transform() );    //调用$poem的```transform```方法，返回转换过的结果
```
### EventObserver

>事件观察者允许你在repository中方便的针对数据库操作流程中的某一个具体的节点绑定你想要执行的事件

#### 在Model中实现接口
如果你想开启该功能，你需要在你的Model类中实现```Zhaohehe\Repositories\Contracts\ModelEventInterface```接口，并使用```Zhaohehe\Repositories\Traits\ModelEventTraits```来赋予你的数据模型该功能，你的Model类大概会长成这样：
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zhaohehe\Repositories\Traits\ModelEventTraits;
use Zhaohehe\Repositories\Contracts\ModelEventInterface;

class Poem extends Model implements ModelEventInterface
{
    use ModelEventTraits;

    protected $fillable = [
   
    ];

}
```
#### 在repository中注册observer

比如你想在每一次新建一条Poem记录之后，将当前的某些信息记录到操作日志中，你可以在PoemRepository中重载```onCreated```方法，代码如下:

```php
<?php

namespace App\Repositories;

use Zhaohehe\Repositories\Eloquent\Repository;

class PoemRepository extends Repository
{
    public function model()
    {
        return 'App\Models\Poem';
    }

    public function onCreated()
    {
        //do something
    }
}
```
这样，当你在调用repository的```create```方法创建新的记录的时候，```onCreated```会在创建的动作结束后被调用。

#### 可用的方法

```php
function onCreating();
function onCreated();

function onUpdating();
function onUpdated();

function onSaving();
function onSaved();

function onDeleting();
function onDeleted();
```
以上方法会在相应的节点被激活。

## 最后

以上的思路和代码是在阅读了很多大神的代码之后产生的，尤其是[这个](https://github.com/prettus/l5-repository)，很荣幸可以站在巨人们的肩膀上。