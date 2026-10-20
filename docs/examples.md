# Worker examples

## Entry script

Use the [quick start](../README.md#quick-start) to create `web/index.php` in an existing Yii application. It binds the
four HttpSoft factories before the runner resolves the bridge creator and emitter. Set the request's secret
`cookieValidationKey` in `config/web.php` and keep the application's routes and identity configuration.

The runner owns `handle(request)` -> `SapiEmitter::emit(response, body)` -> `finalize(success)`. It passes `false`
for the body on HEAD and finalizes exactly once in `finally`. After-send runs only after successful emission.
Do not call `finalize()` again from application event handlers or mix Yii `run()`/`Response::send()` into this loop.

## Request limits and memory

The constructor limit takes priority over `$_ENV['MAX_REQUESTS']`. When that entry is absent, the runner reads the
process environment with `getenv('MAX_REQUESTS')`. A missing or nonnumeric value defaults to 1000:

```php
$app = new \yii2\extensions\psrbridge\http\Application($config);
$app->setMemoryLimit(256 * 1024 * 1024);

$runner = new \yii2\extensions\frankenphp\FrankenPHP($app, maxRequests: 500);
$runner->run();
```

`clean()` triggers recycling at 90% of the configured memory threshold. Without `setMemoryLimit()`, the bridge
reads PHP's `memory_limit`; an unlimited setting maps to `PHP_INT_MAX`. The runner returns `ServerExitCode::OK`
for the memory threshold, and `ServerExitCode::REQUEST_LIMIT` for the request limit or native worker stop signal.
A non-positive constructor limit still stops after the first request; it does not select an unlimited loop.

## Sessions and persistent components

Set bridge lifecycle properties on the application instance before starting the runner:

```php
$app = new \yii2\extensions\psrbridge\http\Application($config);
$app->useSession = false;
$app->persistentComponents = ['db', 'cache'];

(new \yii2\extensions\frankenphp\FrankenPHP($app))->run();
```

Lazy sessions remain isolated per visitor. Keep session, user, request, response, and error handler out of persistent
components. Keep cookie validation enabled for browser sessions. Use the bridge's
[configuration guide](https://github.com/yii2-extensions/psr-bridge/blob/main/docs/configuration.md) for strict session
IDs, JSON parsing from globals, and cookie settings.

## Uploads and streaming downloads

Retrieve uploads through `yii2\extensions\psrbridge\http\UploadedFile`, validate their content and size with the
application's upload model, and save them under server-generated names outside the public directory. Do not use
client filenames as storage paths. Check `saveAs()` before reporting success.

Keep file response streams open through emission. The bridge emitter reads bounded chunks. Check the returned bytes and `Content-Range`
when using partial responses; after-send handlers may release resources after successful emission. See the bridge's
[upload and download examples](https://github.com/yii2-extensions/psr-bridge/blob/main/docs/examples.md).

[Back to documentation](../README.md#documentation)
