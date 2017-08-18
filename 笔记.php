<?php


// 安装laravel环境*****************************

    1.安装phpstudy环境(laravel5.4要求php>5.6.4,,,推荐php7.0)  #需要开启openssl扩展
    2.安装composer , 安装完成后在cmd执行'composer',有内容则安装成功
    3.composer安装laravel :   composer create-project laravel/laravel lar54
    4.在lar54目录执行'php artisan serve', 然后浏览器访问'localhost:8000',有内容则laravel安装成功



// 配置****************************************
    //配置时区  :
        在config/app.php中将timezone的值设置为prc
    //配置mysql :
        #1.创建测试数据
            create database learn_laravel charset utf8;use learn_laravel;create table stus(id int,name varchar(10));insert into stus values (1,'张三');
        #2.修改laravel框架配置
            #修改.env文件中以下mysql配置
                DB_CONNECTION=mysql
                DB_HOST=127.0.0.1
                DB_PORT=3306
                DB_DATABASE=learn_laravel
                DB_USERNAME=root
                DB_PASSWORD=root
        #3.重新执行 :
            php artisan serve    #如果你给laravel配置了虚拟主机,可省去此步骤
        #4.在routes/web.php文件添加以下内容 :
                Route::get('/a', function () {
                    echo date('H:i:s');
                    return DB::select('select * from stus');
                });
        #然后, 访问http://localhost:8000/a  , 有内容,则laravel中mysql配置成功,\-_-/
    //配置mysql编码 :
        #找到AppServiceProvider.php文件,添加以下代码 :
            use Illuminate\Support\Facades\Schema;
            Schema::defaultStringLength(191);   #这一句添加在boot方法里
        #说明 : laravel5.4默认使用utf8mb4编码,如不配置,数据库在mysql5.7.7版本以下时,php artisan migrate会报错





// 目录结构*************************************
    public : 包括入口文件index.php和前端资源css,js等
    routes : 路由目录
    app    : model文件默认存储于此目录下, 如app/User.php (为什么没有model目录?--因为老外觉得有歧义)
    app/Http/Controllers : controllers控制器目录
    resources/views      : view视图目录
    #前期,知道以上目录,足矣




// MVC******************************************
    //mvc流程图 :
        tp版MVC       : 浏览器访问www.a.cn?a=home&c=index&m=index ---------->controller控制器-------->model模型
                                                                                    |
                                                                                    |
                                                                                    ∨
                                                                                view视图显示


        laravel版MVC  : 浏览器访问www.a.cn/a ---------->routes路由---------->controller控制器-------->model模型
                                                                                    |
                                                                                    |
                                                                                    ∨
                                                                                view视图显示

    //跑通MVC :
        #1.创建路由 : 在routes/web.php文件添加以下内容 :
            Route::get('/stu/index', 'StuController@index');
        #2.创建controller控制器 :
            命令行执行 : php artisan make:controller StuController  #创建StuController.php控制器
            #修改StuController.php控制器后代码为 :
                namespace App\Http\Controllers;
                use Illuminate\Http\Request;
                use App\Stu;     #引用model
                class StuController extends Controller
                {
                    #测试方法
                    public function index(){
                        echo "11<br/>";
                        $data = Stu::first();
                        print_r($data);
                        return view('stu/index',['data'=>$data]);
                    }
                }
        #3.创建model模型 :
            命令行执行 : php artisan make:model Stu  #创建Stu.php模型,注意:模型为Stu.php而数据库为stus(之前创建的)
        #4.创建view视图  :
            命令行执行 : cd resources/views && mkdir Stu && cd Stu && echo '{{$data->name}}' >> index.blade.php
            #呃...这个步骤可以不用命令行, 只是为了做笔记方便 -_-|

        #最后, 访问http://localhost:8000/stu/index , 有内容,则MVC流程跑通,\-_-/





// laravel命令行*********************************
    php artisan                     #查看所有命令
    php artisan -h route:list       #查看route:list的用法












// route路由*************************************
    // 常用路由 :
        Route::get('/aa/{id}', 'StuController@aa');     #get方式访问localhost:8000/aa/2时触发
        Route::match(['get','post','put','delete'],'/bb', 'StuController@bb'); #get,post,put,delete方式访问时触发
        Route::resource('/stu','StuController');  #RESTful资源路由【推荐】
    // 路由群组 :
        Route::group(['prefix' => 'admin','namespace' => 'Admin'], function () {
            Route::get('/a','AController@index'); #群组里可写多个路由
        });
        #此示例会匹配 : localhost:8000/admin/a   -->  Admin/AController.php的index方法
    //注 : php artisan route:list 可查看已定义路由










// 数据库迁移************************************
    // 概念 : 就是使用laravel来创建及修改数据表
    // 创建表 :
        #1.创建表文件 :
            命令行执行 : php artisan make:migration create_product_table  #将在database/migrations文件夹生成文件
            #此文件包含两个函数up和down, up用于新增表/列/索引, down相反,用于删除表/列/索引
        #2.修改up和down方法 :
            public function up(){
                Schema::create('product', function (Blueprint $table) {
                    $table->increments('id');  #数据库自增id
                    $table->string('name',33)->nullable()->default('')->comment('产品名')->index();
                    $table->integer('user_id');
                    $table->timestamps();      #添加 created_at 和 updated_at 列
                    $table->softDeletes();     #创建deleted_at列,用于软删除
                });
            }
            public function down(){
                Schema::drop('product');
            }
        #3.执行命令 :
            php artisan migrate     # 将执行up函数
            #撤销上一次操作 : php artisan migrate:rollback         #将执行down函数
    // 修改表 :
        #1.同上(文件名需更换)
        #2.修改up和down方法 :
            public function up(){
                Schema::table('product', function (Blueprint $table) {
                    $table->string('email')->nullable();
                });
            }
            public function down(){
                Schema::table('product', function (Blueprint $table) {
                    $table->dropColumn('email');
                });
            }
        #3.同上




// 数据库填充************************************
    //步骤:
        #1.创建填充文件 :
            命令行执行 : php artisan make:seeder test_product
        #2.修改run方法 :
            public function run(){
                for ($i=0; $i <100 ; $i++) {
                    DB::table('product')->insert(['name'=>"xiaoming_{$i}",'user_id'=>1]);
                }
            }
        #3.执行命令
            php artisan db:seed --class=test_product









// 数据的增删改查*********************************
    //1. DB方式(不需要创建model)
        use Illuminate\Support\Facades\DB;   #引入命名空间
        #1.1原生语句
            DB::insert('insert into stus values (?, ?)', [1, 'Dayle']);            #增
            $deleted  = DB::delete('delete from stus');                            #删
            $affected = DB::update('update stus set name=2 where id = ?', ['1']);  #改
            DB::select('select * from stus');                                      #查
        #1.2查询构造器
            DB::table('stus')->insert(['id' => '11', 'name' =>'aa']);              #增
            DB::table('stus')->where('id','>',5)->delete();                        #删
            DB::table('stus')->where('id',1)->update(['name'=>22]);                #改
            DB::table('stus')->where('id','>',5)->get();                           #查(多条:get,一条:first)

    //2.Eloquent ORM(每张表对应相应model)
        #2.1创建model :
            php artisan make:model Product
        #2.2在model中添加以下内容 :
            protected $table = 'product';       #指定表名,默认为类名+s(英文复数形式)
            protected $primaryKey = 'id';       #指定主键,默认为id
            protected $fillable = ['name'];     #白名单,只允许修改name列
        #2.3增删改查操作 :
            use App\Product;     #引入命名空间
            Product::create(['name' => 'iphone5']);                #增
            Product::where('id',1)->delete();                      #删(分真正删除及软删除,软删除需在模型use SoftDeletes)
            Product::where('id',2)->update(['name' => 'iphone6']); #改
            Product::where('id','>',2)->first();                   #查(多条:get/all,一条:first/find)





// 数据库关联关系*********************************
    //用法 :
        #1.1 user模型里加上如下代码 :
            public function my_product(){
                // 1对1: hasOne    1对多: hasMany    多对多: belongsToMany
                return $this->hasOne('App\Product','user_id','id');
            }
        #1.2 控制器测试 :
            dd(User::find(1)->my_product);


        #2.1 product模型加上如下代码 :
            public function my_user(){
                // 1对1/1对多: belongsTo     多对多: belongsToMany
                return $this->belongsTo('App\User','user_id','id');
            }
        #2.2 控制器测试 :
            dd(Product::find(1)->my_user);
            #注 : hasOne,hasMany,belongsTo函数用法类似, 多对多belongsToMany用法不同,自查手册











// view视图及blade模板引擎用法********************
    //用法 :
        #1.流程语句及显示变量
            #1.控制器代码 :
                $arr = [['name'=>'张三','age'=>28],['name'=>'李四','age'=>55]];
                return view('stu/index',compact('arr'));
            #2.视图index.blade.php代码 :
                <div>
                    @foreach($arr as $v)
                        <p>{{$v['name']}}</p>
                        <p>{{$v['age']}}</p>
                    @endforeach
                </div>
                #if,for,while语句类似

        #2.公共模板
            #1.公共模板common.blade.php代码 :
                <div>我是公共头部</div>
                @yield('content')
                <div>我是公共尾部</div>
            #2.控制器模板index.blade.php代码 :
                @extends('layouts.common')        #代表layouts目录下的common.blade.php
                @section('content')
                    <div>我是正文部分</div>
                @endsection
                #另外,@include指令类似PHP的include,也挺常用









// 数据验证******************************************
    // 编写测试数据 :
        #1. 编写路由 :
            Route::get('/val', function () { return view('val'); });
            Route::post('/val/index','valController@index');
        #2. 编写val.blade.php页面表单 :
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Document</title>
            </head>
            <body>
                <form action="{{url('val/index')}}" method="post">
                    {{csrf_field()}}
                    <input type="text" name="name"><br>
                    <input type="text" name="age"><br>
                    <input type="submit" value="提交">
                </form>
                {{-- 错误信息 --}}
                @if($errors->any())
                    <ul class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </body>
            </html>
        #3. 创建控制器 :
            php artisan make:controller valController
    // 验证 :
        //方法1 :
            #编写valController.php的index方法 :
                public function index(Request $request){
                    $this->validate($request, [
                        'name' => 'required',
                        'age' => 'required|max:3',
                    ],[
                        'name.required' => '姓名不能为空',
                        'age.required'  => '年龄不能为空',
                        'age.max'       => '年龄不能超过3位',
                    ]);
                    echo '只有验证成功,才会执行这一句';
                }
        //方法2 : 【推荐】
            #1.命令行执行 :
                php artisan make:request valRequest
            #2.修改valRequest.php文件如下 :
                public function authorize(){
                    #此函数用来验证是否有权限做修改,比如:用户只能删除自己的文章,你可以做如下判断 :
                    # return '当前文章属于当前用户' ? true : false;
                    return true;
                }
                #验证规则
                public function rules(){ return [ 'name' => 'required', 'age' => 'required|max:3']; }

                #错误信息,    此函数可不写,可修改resources/lang/en/validation.php文件,全局生效
                public function messages(){
                    return [ 'name.required' => '姓名不能为空', 'age.required' => '年龄不能为空', 'age.max' => '年龄不能超过3位',];
                }
            #3.编写valController.php的index方法 :
                public function index(\App\Http\Requests\valRequest $request){
                    echo '只有验证成功,才会执行这一句';
                }









// 中间件****************************************
    //步骤 :
        #1.执行命令行 :
            php artisan make:middleware loginMid
        #2.修改loginMid.php :
            public function handle($request, Closure $next){
                echo '我在之前执行';
                $response = $next($request);
                echo '我在之后执行';
                return $response;
            }
        #3.在kernel.php中$routeMiddleware属性加上 :
            'loginMid' => \App\Http\Middleware\loginMid::class,
        #4.创建路由 :
            Route::get('/aaaa',function () { echo '1111'; })->middleware('loginMid');









// 登录注册**************************************
    //方法1 :  (laravel默认方式)
        php artisan make:auth    #只此一句,登录注册完成,访问http://localhost:8000/register测试
    //方法2 : (自定义登录)
        // 编写测试数据 :
            #1. 编写路由 :
                Route::match(['get','post'],'/login','loginController@login');
            #2. 编写login.blade.php页面表单 :
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Document</title>
                </head>
                <body>
                    <form action="{{url('login')}}" method="post">
                        {{csrf_field()}}
                        <input type="text" name="email"><br>
                        <input type="password" name="password"><br>
                        <input type="submit" value="提交">
                    </form>
                </body>
                </html>
            #3. 命令行执行 :
                php artisan make:controller loginController
        //验证 :
            #在loginController.php添加如下代码 :
                use Illuminate\Support\Facades\Auth;

                public function login(Request $request){
                    if($request->isMethod('post')){
                        if (Auth::attempt(['email'=>$request->get('email'), 'password'=>$request->get('password')])) {
                            echo '登录成功';
                        }
                    }
                    return view('login/login');
                }













/************************************实例:*****************************************/

//1.前期准备***************************
    #1.新建laravel项目:
        composer create-project laravel/laravel laravel_article
    #2.配置(略,数据库名为:laravel_article)

//2.创建数据库及测试数据***************
    //创建数据库 :
        #1. 创建表文件 :
            命令行执行 : php artisan make:migration create_artisan_table
        #2.修改up和down方法 :
            public function up(){
                Schema::create('article', function (Blueprint $table) {
                    $table->increments('id');  #数据库自增id
                    $table->string('title',33)->nullable()->default('')->comment('title')->index();
                    $table->text('content');
                    $table->integer('user_id');
                    $table->timestamps();      #添加 created_at 和 updated_at 列
                    $table->softDeletes();     #创建deleted_at列,用于软删除
                });
            }
            public function down(){
                Schema::drop('product');
            }
        #3.执行命令 :
            php artisan migrate     # 将执行up函数
            #手动添加测试数据
//3.登录注册
    php artisan make:auth

//4.创建路由,控制器及模型 :
    #1. 添加路由 :
        Route::resource('/article','ArticleController');
    #2. 创建控制器 :
        命令行执行 : php artisan make:controller ArticleController -r
    #3. 创建模型 :
        #1.命令行执行 :
            php artisan make:model Http/Models/Article
        #2.修改Article.php文件:
            use Illuminate\Database\Eloquent\SoftDeletes;

            use SoftDeletes;     #注意,这一行代码写在类内部
            protected $table      = 'article';
            protected $primaryKey = 'id';
            protected $fillable   = ['title','content','user_id'];

//5.显示文章列表
    #1.修改ArticleController的index方法 :
        public function index(){
            $data = Article::all();
            return view('article/index',compact('data'));
        }
    #2.新建views/article/index.blade.php文件,内容如下:
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>显示文章</title>
        </head>
        <body>
            <a href="{{url('article/create')}}">添加文章</a><hr>
            <ul>
                @foreach($data as $v)
                    <li art_id="{{$v->id}}">
                        <a href="{{url('article',['id'=>$v->id])}}"><h3>{{$v->title}}</h3></a>
                        <i>作者:11</i>
                        <span><a href="{{url('article/'.$v->id.'/edit')}}">修改</a><button class="del" art_id="{{$v->id}}">删除</button></span>
                    </li>
                @endforeach
            </ul>
        </body>
        </html>

//6.显示文章详情
    #1. 修改ArticleController的show方法 :
        public function show($id){
            $info = Article::find($id);
            return view('article/show',compact('info'));
        }
    #2. 新建views/article/show.blade.php文件,内容如下:
        <h2>{{$info->title}}</h2>
        <p>{{$info->content}}</p>


//7.添加文章
    //1. 显示页面 :
        #1. 修改ArticleController的create方法 :
            public function create(){
                return view('article/create');
            }
        #2. 新建views/article/create.blade.php文件,内容如下:
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Document</title>
            </head>
            <body>
                <form action="{{url('article')}}" method="post">
                    {{csrf_field()}}
                    <input type="text" name="title">
                    <textarea name="content"></textarea>
                    <input type="hidden" name="user_id" value="1">
                    <input type="submit" value="提交">
                </form>
                {{-- 错误信息 --}}
                @if($errors->any())
                    <ul class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </body>
            </html>
    //2.验证数据 :
        #1. 创建验证文件 :
            命令行执行 : php artisan make:request articleRequest
        #2. 修改articleRequest.php文件 :
            public function authorize(){
                return true;
            }
            #验证规则
            public function rules(){ return [ 'title' => 'required', 'content' => 'required|min:3']; }
        #3. 修改ArticleController的store方法 :
            public function store(\App\Http\Requests\ArticleRequest $request){
                echo '只有验证成功,才会执行这一句';
            }
    //3.处理逻辑 :
        #修改ArticleController的store方法 :
            public function store(\App\Http\Requests\ArticleRequest $request){
                $data = Article::create($request->all());
                return empty($data) ? '未知错误' : redirect('article');
            }

//8.使用软件包LaravelCollective/html替代原生html表单语法
    #1.安装软件包 :
        命令行执行 : composer require "laravelcollective/html":"^5.4.0"
    #2.配置config/app.php :
        'providers' => [
            // ...
            // 插件laravelcollective/html
            Collective\Html\HtmlServiceProvider::class,
            // ...
        ],
        'aliases' => [
            // ...
            // 插件laravelcollective/html
            'Form' => Collective\Html\FormFacade::class,
            'Html' => Collective\Html\HtmlFacade::class,
            // ...
        ],
    #3.修改create.blade.php文件的form表单部分如下 :
        {!! Form::model(null,['url'=>url('article'),'method'=>'post']) !!}
            {!! Form::text('title', null, ['class' => 'form-control','autocomplete'=>'off']) !!}
            {!! Form::text('content', null, ['class' => 'form-control','autocomplete'=>'off']) !!}
            {!! Form::hidden('user_id', 1, ['autocomplete'=>'off']) !!}
            {!! Form::submit('提交',['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}

//9.修改文章
    //1.显示页面
        #1. 修改ArticleController的edit方法 :
            public function edit($id){
                $info = Article::find($id);
                return view('article/edit',compact('info'));
            }
        #2. 新建views/article/edit.blade.php文件,内容如下:
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Document</title>
            </head>
            <body>
                {!! Form::model($info,['url'=>url('article',['id'=>$info->id]),'method'=>'PUT']) !!}
                    {!! Form::text('title', null, ['class' => 'form-control','autocomplete'=>'off']) !!}
                    {!! Form::text('content', null, ['class' => 'form-control','autocomplete'=>'off']) !!}
                    {!! Form::hidden('user_id', 1, ['autocomplete'=>'off']) !!}
                    {!! Form::submit('提交',['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
                {{-- 错误信息 --}}
                @if($errors->any())
                    <ul class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </body>
            </html>
    //2.验证数据及处理逻辑 :
        #修改ArticleController的update方法 :
            public function update(\App\Http\Requests\ArticleRequest $request, $id){
                $data = Article::where('id',$id)->update($request->only('title','content','user_id'));
                return $data==false ? '未知错误' : redirect("article/$id");
            }