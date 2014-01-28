## Restima - A RESTful PHP Micro-framework ##
[http://restima.evrima.net/](http://restima.evrima.net/)

#### Summary ####
Allows you to create a reliable RESTful webservice (really!) fast & easy,
Please do not hesitate to contact for any bug report & merge request.

Supports:
* Micro-framework style route definitions such as $app->get('/', function($request,$response){ });
* Supports HTTP method override via short route definitions, useful while implementing ACL.
* Supports MongoDB as an data source. Any collection can be assigned to a spcified route via App/ClassName.
* Supports Mysql as an data source. Any collection can be assigned to a spcified route via Class [BUGGY](#1)
* Supports Sqlite as an data source. Any collection can be assigned to a spcified route via Class [BUGGY](#1)
* Memcached support via route queries, Default 3 seconds to evade high load.
* Automagical content negotiation via Headers. Looks for http accepts header.
* Output support application/json (default), text/csv and application/xml.
* Laravel style variable routes as /my_route/{my_variable}
* Json output support a standart output type for all handlers.
* CSV output supports standart output type as HTTP headers, such as X-Api-Response, X-Api-Message, X-Api-Keys.
* Supports automatic form enctype detection to capture posted values. raw POST body decodes only JSON format.
* supports multiple configuration by Environment name or developer name. useful to apply different config sets while working within a team!

Notes: 1 - MySQL and Sqlite support removed due to unstable output & rookie implementation. (Sorry, it will be added soon.)

#### How to Install #####
* Clone git repo to your working directory.
* Apache Httpd: you can use .htaccess
* Nginx / php-fpm: Use the [snippet here](https://gist.github.com/risyasin/8677470).


#### Configuration #####
Framework reads the file config/default.php by default.
Also looks for extra configuration files via SAPI / FastCGI environment variables.
Precedence order follows as below.
* Default => default.php -Required-
* APPLICATION_ENV => 'your_environment' supposed to be used as Env. switch. Filename without extension (php)
* APPLICATION_DEV => 'your_name' supposed to be used as Developer switch. Filename without extension (php)


#### Usage #####
This framework aimed to be quick design of a [RESTful webservice](https://blog.apigee.com/detail/restful_api_design) with MongoDB via built-in Adapter. MongoDB Adapter you can requires only collection name. (Database name must be in configuration)
A short example for /books route to connect a collection named my_book_list below.
First create a new file under the controllers directory named app/
Create your class proper to filename, use the skeleton below.

``` php
    class books extends \DataSources\MongoAdapter
    {
        public function __construct($req, $resp, $config)
        {
            $this->init($req, $resp, $config);
            $this->set_collection('my_book_list');
        }
    }
```
Then open your route [test-route](http://localhost/restima/books/)!
All RESTful methods will be applied by Adapter for you, such as:
* GET /books => to fetch all books in collection.
* GET /books/[id] => to fetch a specific book with mongoId with [id].
* POST /books => to create a new book in collection.
* PUT /books/[id] => to update a specific book with mongoId with [id].
* DELETE /books/[id] => to delete a specific book with mongoId with [id].

You can use pagination, sorting, filtering via url parameters.
* GET /books?page=4&sort=_id => to fetch all books in collection.

Optionally you can set a public var in class body named $cache_expire to initialize memcached.

To create a fixed/variable route binding in index.php;
 ``` php
     $app->get('/', function($request,$response){
         $response->send(array('status'=>'RESTful API is running!'));
     });

     $app->get('/help/{subject}', function($request,$response){
        if($request->vars['subject']){  /* Handle variable route */ }
         $response->send('Testing!');
     });

     $app->post('/my_route', function($request,$response){
        if($request->params){  /* Handle variable route */ }
         $response->send('Testing!');
     });
 ```


#### Todo List ####
* Authentication support via Basic and Digest Access & OAuth2
* Connect host limitation via configuration. must check real & forwarded ip addresses of client to protect against hostname forgery
* text/html output type as an automatic scaffolding & make available basic CRUD operations via route.
* Better documentation & Code samples.


Have fun, Peace!