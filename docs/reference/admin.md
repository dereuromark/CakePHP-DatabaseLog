# Admin interface

With plugin routes enabled, the dashboard is `/admin/database-log` and the entry list is `/admin/database-log/logs`. The dashboard shows row counts and activity charts; merged occurrence counts are shown on individual entries. Charts count stored rows rather than summing merged occurrences.

## Access control

`DatabaseLog.adminAccess` is required in both normal and standalone modes. It must be a closure returning the boolean `true` for permitted requests. Missing configuration, non-closures, other return values, and exceptions deny access with 403. Host controller responses, including authentication redirects, are preserved before the plugin's check runs.

```php
'DatabaseLog' => [
    'adminAccess' => function (\Cake\Http\ServerRequest $request): bool {
        $identity = $request->getAttribute('identity');

        return $identity !== null && in_array('admin', (array)$identity->roles, true);
    },
],
```

Adapt the rule to your application. It covers destructive admin actions as well as viewing logs. The plugin records its decision with an available Authorization component or request authorization service. This does not replace authentication middleware or add separate per-action policies.

## Standalone mode

```php
'DatabaseLog' => [
    'standalone' => true,
],
```

Merge this setting with the required access callback. Standalone mode skips the host AppController's initialization and lifecycle callbacks, including `beforeFilter`, `beforeRender`, `beforeRedirect`, and `afterFilter`. The plugin loads Flash, its helpers, and its configured layout. Application middleware still runs.

If the host controller normally authenticates or authorizes access, implement that rule in middleware or the plugin's access callback before using standalone mode. Delegate with a callback returning `true` only when another guard actually enforces the complete rule upstream.

## Routes and presentation

| Setting | Default | Behavior |
| --- | --- | --- |
| `routePath` | `/database-log` | Mount path under the Admin prefix; `/logs` gives `/admin/logs`. Set before routes load. |
| `adminLayout` | `null` | Plugin layout; `false` selects the application layout, or supply a layout name. |
| `dashboardAutoRefresh` | `0` | Refresh interval in seconds; zero disables refresh. |
| `adminBackUrl` | Unset | Optional header link accepting a CakePHP URL array or URL string. |
| `adminBackLabel` | `Back to App` | Translated label for that link. |
| `isSearchEnabled` | `null` | Auto-detect the loaded Search plugin; `false` disables advanced search. |

For an application link:

```php
'DatabaseLog' => [
    'adminBackUrl' => [
        'plugin' => false,
        'prefix' => 'Admin',
        'controller' => 'Overview',
        'action' => 'index',
    ],
],
```

Without Search, type filtering remains available. To enable summary search, install and load the [Search plugin](https://github.com/FriendsOfCake/search) compatible with your application before using the admin controllers. Setting `isSearchEnabled = true` does not install the dependency.

The plugin uses core helpers unless Tools is loaded, in which case it selects the Tools helpers. The default layout supplies its own presentation. Applications using a custom layout must load any styles and scripts their templates need.

## Type labels

`DatabaseLog.typeMap` maps types to CSS classes, and `typeDefaultClass` defaults to `secondary`. The default mapping uses `danger` for `error` and `cli-error`, `warning` for `warning` and `notice`, and `info` for `info`.

`typeTemplate` defaults to `<span class="badge badge-%s bg-%s">%s</span>`. Its placeholders receive the CSS class twice and the escaped type. Set `typeMap = false` to render only the escaped type text.
