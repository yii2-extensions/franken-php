# Migrate an existing worker

Use the [upgrade guide](../UPGRADE.md) for dependency and API changes. Verify the application's behavior after updating.

## Request isolation

Run successive requests in one worker. Keep request, response, error handler, session, and user out of
`persistentComponents`; the bridge defaults retain `db` and `cache` only.

## Cookies and sessions

Keep native PHP session cookies disabled before opening sessions. Configure the request cookie validation secret and
retain `syncCookieValidation=true` when response cookies must be signed.

Test two visitors, a missing cookie, and tampered or malformed cookies. `useSession=false` skips eager session opening
around bootstrap; it still synchronizes session IDs for lazy session access. Preserve that synchronization in any
overridden bridge hooks.

## Response lifecycle

Confirm `EVENT_AFTER_SEND` runs once after successful emission and does not run if emission fails. The runner owns
`finalize()`; remove application hooks that call it again or invoke Yii `Response::send()`.

Confirm an after-send exception propagates without a second finalization. Keep download streams open until emission
completes. Check HEAD status and headers with an empty body, GET response bytes, partial downloads, and an upload
followed by a request with no files. Use the bridge's `http\UploadedFile` when retrieving model uploads.

## JSON bodies

For JSON from SAPI globals, check the PSR parsed body before relying on a Yii parser. The bridge creator supplies
`$_POST`, which can be `[]`; parsing runs only when the PSR parsed body is `null`. See the bridge's
[body parsing guide](https://github.com/yii2-extensions/psr-bridge/blob/main/docs/configuration.md).

Run the application through FrankenPHP as well as the package's mocked unit suite. A passing unit suite does not verify
the native request reset, streaming, TLS, or worker restart behavior.

[Back to documentation](../README.md#documentation)
