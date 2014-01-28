### Summary ###

A basic RESTful API framework written in PHP. Allows you to create a web service fast & easy
Please do not hesitate to contact for any bug report & merge request.

* Micro-framework style route definitions such as $app->get('/', function($request,$response){ });
* Supports HTTP method override via short route definitions, useful while implementing ACL.
* Supports MongoDB as an data source. Any collection can be assigned to a spcified route via App/ClassName.
* Supports Mysql as an data source. Any collection can be assigned to a spcified route via App/ClassName  [BUGGY] (1)
* Supports Sqlite as an data source. Any collection can be assigned to a spcified route via App/ClassName  [BUGGY] (2)
* Memcached support via route queries, Default 3 seconds to evade high load.
* Automagical content negotiation via Headers. Looks for http accepts header.
* Output support application/json (default), text/csv and application/xml.
* Json output support a standart output type for all handlers.
* CSV output supports standart output type as HTTP headers, such as X-Api-Response, X-Api-Message, X-Api-Keys.
* Supports automatic form enctype detection to capture posted values. raw POST body decodes only JSON format.


Notes: 1,2 - MySQL & Sqlite support removed due to unstable output & rookie implementation. (Sorry will be added soon)

#### TODOS ####
* Authentication support via Basic and Digest Access & OAuth2
* Connect host limitation via configuration. must check real & forwarded ip addresses of client to protect against hostname forgery
* text/html output type as an automatic scaffolding & make available basic CRUD operations via route.


Have fun, Peace!