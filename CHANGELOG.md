# Changelog

## Version 2.0.0
- BREAKING CHANGE: Changed namespace from `\Mathrix\Lumen` to `\Mathrix\Lumen\Zero`
- BREAKING CHANGE: Removed `\Mathrix\Lumen\Base` namespace in favor of specific namespaces.
- BREAKING CHANGE: Removed `BaseListener`, `BaseNotification`, `BaseObserver` and `BasePolicy` since they are empty useless classes.
- BREAKING CHANGE: `POST`, `GET`, `PATCH` standard actions will now return the models data wrapped in the data key:
```json
{
  "id": 17,
  "slug": "test"
}
```
becomes:
```json
{
  "data": {
    "id": 17,
    "slug": "test"
  }
}
```
- BREAKING CHANGE: `BaseController` `index`, `post`, `get`, `patch` and `delete` actions has been renamed:

| Before   | After            |
|----------|------------------|
| `index`  | `standardIndex`  |
| `post`   | `standardPost`   |
| `get`    | `standardGet`    |
| `patch`  | `standardPatch`  |
| `delete` | `standardDelete` |

- BREAKING CHANGE: Lumen has been upgraded to version 5.8. See the consequences: [Laravel 5.8 changelog](https://laravel.com/docs/5.8/releases#laravel-5.8)
- BREAKING CHANGE: `ClassResolver` has now a single method, `getModelClass` which will do best effort to resolve the model class. Other methods have been delete.
- Using [haydenpierce/class-finder](https://gitlab.com/hpierce1102/ClassFinder), the `ObserverServiceProvider`, `PolicyServiceProvider` and `RegistrarServiceProvider` have been rewritten.
- Model attributes aliases have been added, which allow renaming an attributes in the model.
