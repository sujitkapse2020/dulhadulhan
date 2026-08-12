# TODO: Implement Login Functionality

## Steps

- [x] Plan created and approved
- [x] 1. Install Laravel Sanctum (`composer require laravel/sanctum`) and publish/run migration
- [x] 2. Add `HasApiTokens` trait to `User` model
- [x] 3. Create `LoginDTO`
- [x] 4. Create `LoginUserRequest` (validation)
- [x] 5. Create `InvalidCredentialsException`
- [x] 6. Add `login()` to `AuthServiceInterface` + `AuthService`
- [x] 7. Create `LoginHistoryRepository` + interface, bind in `RepositoryServiceProvider`
- [x] 8. Implement `LoginController::__invoke`
- [x] 9. Create feature test `tests/Feature/Auth/LoginTest.php`
- [x] 10. Verify all changes are consistent and run tests

## Note

- Fixed pre-existing migration ordering issue: renamed `2026_07_19_145905_create_conversations_table.php` → `2026_07_19_145805_create_conversations_table.php` so `conversations` is created before `messages` (which has a FK to it).
- Added `$fillable` to `LoginHistory` model (was missing, causing mass-assignment failure).
- `RegisterTest` failure is pre-existing (test payload `+1234567890` / `terms_accepted=true` does not match `RegisterUserRequest` rules `digits:10` / `"accepted"`). Unrelated to login.
- `SendWelcomeEmailTest` failure is pre-existing and unrelated to login.

